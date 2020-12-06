<?php
namespace EM_OAuth;
use EM_Options, EM_Exception, stdClass;

/**
 * Class OAuth_API_Client
 * @package EM_OAuth
 * @property-read $service_name
 * @property-read $option_name
 * @property-read $option_dataset
 * @property-read $authorization_scope
 * @property-read $multiple_tokens
 * @property-read $token_class
 */
class OAuth_API_Client {
	/**
	 * The base class for this package of classes, used for accessing infromation such as $option_name, etc.
	 * If naming conventions are followed, for example SomeService_API > SomeService_API_Client, this will be automatically deduced.
	 * @var OAuth_API
	 */
	protected static $api_class;
	/**
	 * @var string
	 */
	public $id;
	/**
	 * @var string
	 */
	public $secret;
	/**
	 * @var string
	 */
	public $scope;
	/**
	 * @var
	 */
	public $client;

	/**
	 * @var OAuth_API_Token
	 */
	public $token;
	/**
	 * @var string
	 */
	public $user_id;
	/**
	 * @var bool
	 */
	public $authorized = false;
	
	/**
	 * The URL without trailing slash for the API base URL, to which endpoints can be appended to.
	 * @var string
	 */
	public $api_base = 'https://api.oauth.com';
	/**
	 * The URL that'll be used to request an authorization code from the user. Can include strings CLIENT_ID, ACCESS_SCOPE, REDIRECT_URI, and STATE which will be replaced dynamically.
	 * @var string
	 */
	public $oauth_authorize_url = 'https://api.oauth.com/authorize';
	/**
	 * Required by child class unless it overrides the request_access_token() method.
	 * @var string
	 */
	public $oauth_request_token_url = 'https://api.oauth.com/token';
	/**
	 * Defaults to $oauuth_request_token_url if not set.
	 * @var string
	 */
	public $oauth_refresh_token_url = null;
	/**
	 * Required by child class unless it overrides the verify_access_token() method.
	 * @var string
	 */
	public $oauth_verification_url = 'https://api.oauth.com/verify_token';
	/**
	 * Required by child class unless it overrides the revoke_access_token() method.
	 * @var string
	 */
	public $oauth_revoke_url = 'https://api.oauth.com/revoke';
	public $oauth_authentication = 'parameters';
	/**
	 * Whether or not an OAuth Service should pass on the state param for security check
	 * @var bool
	 */
	public $oauth_state = true;

	/**
	 * OAuth_API_Client constructor.
	 *
	 * @throws EM_Exception
	 */
	public function __construct(){
		// check credentials
		$creds = array(
			'id' => EM_Options::get( $this->option_name. '_app_id', '', $this->option_dataset),
			'secret' => EM_Options::get( $this->option_name. '_app_secret', '', $this->option_dataset)
		);
		foreach( array('id', 'secret', 'scope') as $k ){
			if( !empty($creds[$k]) ){
				$this->$k = $creds[$k];
			}elseif( empty($this->$k) ) { // constructors can be overriden to add any of the above
				throw new EM_Exception( __('OAuth application information incomplete.', 'events-manager') );
			}
		}
		if( !$this->oauth_refresh_token_url ){
			$this->oauth_refresh_token_url = $this->oauth_request_token_url;
		}
	}
	
	/**
	 * Shortcut for base class properties.
	 * @param $name
	 * @return mixed
	 */
	public function __get( $name ){
		$api = static::get_api_class();
		if( $name == 'option_name' ){
			return $api::get_option_name();
		}elseif( $name == 'option_dataset' ){
			return $api::get_option_dataset();
		}elseif( $name == 'authorization_scope' ){
			return $api::get_authorization_scope();
		}elseif( $name == 'multiple_tokens' ){
			return $api::supports_multiple_tokens();
		}elseif( $name == 'token_class' ){
			return $api::get_token_class();
		}
		return null;
	}
	
	/**
	 * @return OAuth_API
	 */
	public static function get_api_class(){
		// set default API class name if not defined by parent
		if( self::$api_class === static::$api_class && class_exists(str_replace('_Client', '', get_called_class())) ){
			static::$api_class = str_replace('_Client', '', get_called_class());
		}
		return static::$api_class;
	}

	/**
	 * @return mixed
	 */
	public function get_oauth_url(){
		$scope = is_array($this->scope) ? urlencode(implode('+', $this->scope)) : $this->scope;
		$state_nonce = wp_create_nonce($this->option_name.'_authorize');
		$replacements = array( urlencode($this->id), $scope, urlencode(static::get_oauth_callback_url()), $state_nonce );
		$return = str_replace( array('CLIENT_ID','ACCESS_SCOPE','REDIRECT_URI', 'STATE'), $replacements, $this->oauth_authorize_url );
		if( $this->oauth_state ){
			// check there's a STATE value in the authorize url, otherwise add it proactively
			if( !preg_match('/STATE/', $this->oauth_authorize_url) ){
				$return = add_query_arg('state', $state_nonce, $return);
			}
		}
		return $return;
	}

	/**
	 * @return string
	 */
	public static function get_oauth_callback_url(){
		$redirect_base_uri = defined('OAUTH_REDIRECT') ? OAUTH_REDIRECT : admin_url('admin-ajax.php'); // you can completely replace the oauth redirect link for testing locally via a proxy for example
		if( defined('EM_OAUTH_TUNNEL') ){ // for local development or other reasons, you can replace the domain with a tunnel domain, which should be with http(s):// included
			$redirect_base_uri = str_replace(get_home_url(), EM_OAUTH_TUNNEL, $redirect_base_uri);
		}
		$api = static::get_api_class();
		$callback_action = 'em_oauth_'. $api::get_option_name();
		return add_query_arg(array('action'=>strtolower($callback_action), 'callback'=>'authorize'), $redirect_base_uri);
	}

	/**
	 * Returns a native client for this service, in the event we want to load an SDK provided by the service.
	 * @return stdClass
	 */
	public function client(){
		return new stdClass();
	}
	
	// GET, POST, PUT, PATCH, DELETE functions
	
	/**
	 * @param $endpoint
	 * @param array $request_args
	 * @param bool $json_decode
	 * @return array
	 * @throws EM_Exception
	 */
	public function http_request( $endpoint, array $request_args = array(), $json_decode = true ){
		// clean up whether endpoint or full URL is provided
		$endpoint = str_replace($this->api_base, '', $endpoint);
		$request_url = $this->api_base. $endpoint;
		//$request_url = add_query_arg('access_token', $this->token->access_token, $request_url);
		// add oauth and method heaeders
		if( empty($request_args['headers']) ) $request_args['headers'] = array();
		$request_args['headers']['authorization'] = 'Bearer '.$this->token->access_token;
		$request_args['method'] = in_array($request_args['method'], array('GET','POST','PUT','PATCH','DELETE')) ? $request_args['method'] : 'GET';
		// prepare JSON format if sending via that content type
		if( !empty($request_args['headers']['Content-Type']) && $request_args['headers']['Content-Type'] == 'application/json' ){
			if( !empty($request_args['body']) && (is_array($request_args['body']) || is_object($request_args['body'])) ){
				$request_args['body'] = json_encode($request_args['body']);
			}
		}
		// request and parse
		$response = wp_remote_request($request_url, $request_args); /* @var \Requests_Response_Headers $response['headers'] */
		if( is_wp_error($response) ){
			throw new EM_Exception($response);
		}elseif( $response['response']['code'] >= 300 ){ //anything not 20x will indicate an issue
			$errors = json_decode($response['body']);
			if( is_array($errors) ){
				$error = current($errors);
			}elseif( !empty($errors->code) ){
				$error = $errors;
			}else{
				$error = (object) array('code' => $response['response']['code'], 'message' => $response['body']);
			}
			throw new EM_Exception($error->message, $error->code);
		}
		if( $json_decode ){
			$response['body'] = json_decode($response['body']);
		}
		return $response;
	}
	
	/**
	 * Fetches event data from the given endpoint with supplied arguments according to Meetup API v3
	 *
	 * @param string $endpoint Full URL or endpoint accepted.
	 * @param array $args
	 * @param array $request_args
	 * @return array
	 * @throws EM_Exception
	 */
	public function get($endpoint, array $args = array(), array $request_args = array() ){
		$request_url = add_query_arg( $args, $this->api_base.$endpoint );
		$request_args['method'] = 'GET';
		return static::http_request( $request_url, $request_args );
	}
	
	/**
	 * @param $endpoint
	 * @param array $vars
	 * @param array $request_args
	 * @param bool $json            Shorthand for setting Content-Type in headers to application/json
	 * @return array|mixed
	 * @throws EM_Exception
	 */
	public function post($endpoint, array $vars = array(), array $request_args = array(), $json = false ){
		$request_args['body'] = $vars;
		$request_args = array_merge(array('method' => 'POST'), $request_args);
		if( $json ){
			if( empty($request_args['headers'])) $request_args['headers'] = array();
			$request_args['headers']['Content-Type'] = 'application/json';
		}
		return static::http_request($endpoint, $request_args, $json);
	}
	
	/**
	 * @param $endpoint
	 * @param array $vars
	 * @param array $request_args
	 * @param bool $json
	 * @return array|mixed
	 * @throws EM_Exception
	 */
	public function patch($endpoint, array $vars = array(), array $request_args = array(), $json = false ){
		$request_args['method'] = 'PATCH';
		return static::post($endpoint, $vars, $request_args, $json);
	}
	
	/**
	 * @param $endpoint
	 * @param array $vars
	 * @param array $request_args
	 * @param bool $json
	 * @return array|mixed
	 * @throws EM_Exception
	 */
	public function put($endpoint, array $vars = array(), array $request_args = array(), $json = false ){
		$request_args['method'] = 'PUT';
		return static::post($endpoint, $vars, $request_args, $json);
	}
	
	/**
	 * @param $endpoint
	 * @param array $args
	 * @param array $request_args
	 * @return array|mixed
	 * @throws EM_Exception
	 */
	public function delete($endpoint, array $args = array(), array $request_args = array() ){
		$request_url = add_query_arg( $args, $this->api_base.$endpoint );
		$request_args['method'] = 'DELETE';
		return static::http_request( $request_url, $request_args );
	}

	// Baseic OAuth interaction functions, loading a token into client as well as requesting, refreshing, verifying and revoking tokens.

	/**
	 * @param int $user_id
	 * @param int $account_id
	 * @throws EM_Exception
	 */
	public function load_token( $account_id = null, $user_id = null ){
		if( $this->authorization_scope !== 'user' ) $user_id = null; // user id is not relevant
		// return value if already authorized
		if( $this->authorized && $this->authorized = $user_id.'|'.$account_id && $this->user_id == $user_id && $this->token->id == $account_id) return;
		// not authorized, re/load token
		$this->authorized = $this->token = false;
		$this->user_id = $user_id;
		// get token information from user account
		$this->get_access_token( $account_id );
		// renew token if expired
		if ( $this->token->is_expired() ) {
			// Refresh the token if it's expired and update WP user meta.
			$this->refresh();
		}else{
			$this->authorized = $user_id .'|'. $this->token->id;
		}
	}

	/**
	 * Requests an access token from the supplied authorization code. The access token is further verified and populated with service account meta.
	 * If successful, token and meta information is saved for the user $user_id or current user if not specified.
	 * Throws an EM_Exception if unsuccessful at any stage in this process.
	 *
	 * @var string $code
	 * @var int $user_id
	 * @throws EM_Exception
	 */
	public function request($code, $user_id = null ){
		if( $this->authorization_scope == 'user' ){
			$this->user_id = empty($user_id) ? get_current_user_id() : $user_id; // used in $this->save_access_token()
		}else{
			$this->user_id = null;
		}
		$access_token = $this->request_access_token($code);
		$this->token = new OAuth_API_Token($access_token);
		if( $this->token->refresh_token === true ) $this->token->refresh_token = false; // if no token was provided, we may be able to obtain it here, otherwise validation will fail upon refresh.
		// verify the access token so we can establish the id of this account and then save it to user profile
		$access_token_meta = $this->verify_access_token();
		// now, check for previous tokens and save to it instead of overwriting (we do this in case ppl reauthorize the same account and get a new token with no refresh_token)
		if( empty($this->token->id) ){
			$token = $this->token;
			try{
				$this->get_access_token($access_token_meta['id']);
				$this->token->refresh( $token->to_array() ); // merge in new token info to old token
			} catch ( EM_Exception $ex ){
				$this->token = $token; // revert back to new token
			}
		}
		// refresh current or new token with the meta info and save
		$this->token->refresh( $access_token_meta );
		$this->save_access_token();
	}

	/**
	 * @throws EM_Exception
	 */
	public function refresh(){
		if ( $this->token->refresh_token ) {
			try{
				$access_token = $this->refresh_access_token();
				$this->token->refresh($access_token, true);
				$this->save_access_token();
				$this->authorized = $this->user_id .'|'. $this->token->id;
			}catch( EM_Exception $ex ){
				throw new EM_Exception( array(
					$this->option_name.'-error' => sprintf(esc_html__( 'There was an error connecting to %s: %s', 'events-manager' ), $this->service_name, "<code>{$ex->getMessage()}</code>"),
					$this->option_name.'-token-expired' => $this->reauthorize_error_string()
				));
			}
		}else{
			throw new EM_Exception( $this->reauthorize_error_string() );
		}
	}

	/**
	 * Verify the token for this client by obtaining meta data of the account associated to this token and saving it to the current token.
	 * @var boolean $update_token
	 * @return boolean
	 * @throws EM_Exception
	 */
	public function verify( $update_token = true ){
		$access_token_meta = $this->verify_access_token();
		// refresh current or new token with the meta info and save
		$updated = $this->token->refresh( $access_token_meta );
		if( $updated && $update_token ) $this->save_access_token();
		return true; // if we get here, verification passed.
	}

	/**
	 * @return boolean
	 * @throws EM_Exception
	 */
	public function revoke(){
		return $this->revoke_access_token();
	}

	/* START OVERRIDABLE FUNCTIONS - THESE FUNCTIONS COULD BE OVERRIDDEN TO SPECIFICALLY DEAL WITH PARTICULAR OAUTH PROVIDERS */

	/**
	 * Specific function which requests the access token from the API Service and returns the access token array, an error array if service replies.
	 * Throws an EM_Exception if there are any other connection issues.
	 *
	 * @var string $code
	 * @return array
	 * @throws EM_Exception
	 */
	public function request_access_token( $code ){
		$args = array(
			'body' => array(
				'client_id' => $this->id,
				'grant_type' => 'authorization_code',
				'redirect_uri' => static::get_oauth_callback_url(),
				'code' => $code
			)
		);
		return $this->oauth_request( 'post', $this->oauth_request_token_url, $args );
	}

	/**
	 * @return array
	 * @throws EM_Exception
	 */
	public function refresh_access_token(){
		$args = array(
			'body' => array(
				'grant_type' => 'refresh_token',
				'refresh_token' => $this->token->refresh_token,
			)
		);
		return $this->oauth_request('post', $this->oauth_refresh_token_url, $args);
	}

	/**
	 * Verifies an access token by obtaining further meta data about the account associated with that token.
	 * Expected return is an associative array containing the id (service account id the token belongs to), name, photo and email (optional).
	 *
	 * @return array
	 * @throws EM_Exception
	 */
	public function verify_access_token(){
		$request_url = str_replace('ACCESS_TOKEN', $this->token->access_token, $this->oauth_verification_url);
		$access_token = $this->oauth_request('get', $request_url);
		return $access_token; // we may want to override this depending on what's returned
	}

	/**
	 * @return bool
	 * @throws EM_Exception
	 */
	public function revoke_access_token(){
		if( empty($this->oauth_revoke_url) ) return false;
		$request_url = str_replace('ACCESS_TOKEN', $this->token->access_token, $this->oauth_revoke_url);
		return $this->oauth_request('get', $request_url); // we may want to override this depending on what's returned
	}
	
	/**
	 * @param string $method
	 * @param $request_url
	 * @param array $args
	 * @return mixed
	 * @throws EM_Exception
	 */
	public function oauth_request($method, $request_url, $args = array() ){
		$args = array_merge( array('headers' => array(), 'body' => array()), $args );
		if( $this->oauth_authentication == 'basic' ){
			$args['headers']['authorization'] = 'Basic '.base64_encode($this->id.':'.$this->secret);
		}
		if( $method === 'get'){
			if( $this->oauth_authentication == 'parameters' ){
				// add client params to URL if using get
				$request_url = add_query_arg( array('client_id' => $this->id), $request_url );
			}
			$response = wp_remote_get($request_url, $args);
		}elseif( $method === 'post' ){
			if( $this->oauth_authentication == 'parameters' ){
				// add auth params to body if using post
				$args['body']['client_id'] = $this->id;
				$args['body']['client_secret'] = $this->secret;
			}
			if( empty($args['Content-Type'])){
				// by defaulut post will send this content type
				$args['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
			}
			$response = wp_remote_post($request_url, $args);
		}else{
			throw new EM_Exception('Unknown request method.');
		}
		if( is_wp_error($response) ){
			throw new EM_Exception($response->get_error_messages());
		}elseif( $response['response']['code'] != '200' ){
			$errors = json_decode($response['body']);
			$error = current($errors);
			if( !empty($error->message) ){
				$message = $error->message;
			}elseif( !empty($error->error) ){
				$message = $error->error;
			}elseif( !empty($error->reason) ){
				$message = $error->reason;
			}elseif( is_string($error) ){
				$message = $error;
			}elseif( is_string($errors) ){
				$message = $errors;
			}else{
				$message = var_export($errors);
			}
			$error_code = !empty($error->code) ? $error->code : 'oauth-error';
			throw new EM_Exception($message, $error_code);
		}
		return json_decode($response['body'], true); // we may want to override this depending on what's returned
	}

	/* END OVERRIDABLE FUNCTIONS */

	/**
	 * @param int $api_user_id
	 * @return string
	 */
	public function reauthorize_error_string($api_user_id = 0 ){
		$settings_page_url = '<a href="'.admin_url('admin.php?page=events-manager-options').'">'. esc_html__('settings page', 'events-manager-google').'</a>';
		if( !$api_user_id && !empty($this->token->id) ){
			$api_user_id = !empty($this->token->email) ? $this->token->email : $this->token->id;
		}
		if( $api_user_id ){
			return sprintf(__('You need to reauthorize access to account %s by visiting the %s page.', 'events-manager-google'), $api_user_id, $settings_page_url);
		}
		return sprintf(__('You need to authorize access to your %s account by visiting the %s page.', 'events-manager-google'), $this->service_name, $settings_page_url);
	}

	/**
	 * Gets an access token for a specific account, or provides first account user has available, if any. If no access token is available, an EM_Exception is thrown.
	 *
	 * @param int $api_user_id The ID (e.g. number or email) of the OAuth account
	 * @return OAuth_API_Token
	 * @throws EM_Exception
	 */
	public function get_access_token( $api_user_id = 0 ){
		if( $this->authorization_scope == 'site' ){
			$site_tokens = EM_Options::get($this->option_name.'_token', array(), $this->option_dataset);
			if( !empty($site_tokens) ){
				if( $this->multiple_tokens && !empty($api_user_id) && !empty($site_tokens[$api_user_id]) ){
					$token_data = $site_tokens[$api_user_id];
					$token_data['id'] = $api_user_id;
				}else{
					$token_data = current($site_tokens);
					$token_data['id'] = key($site_tokens);
				}
			}
		}elseif( $this->authorization_scope == 'user' ){
			$user_tokens = get_user_meta( $this->user_id, $this->option_dataset.'_'.$this->option_name, true );
			if( !empty($user_tokens) ){
				if( $api_user_id ){
					if( !empty($user_tokens[$api_user_id]) ){
						$token_data = $user_tokens[$api_user_id];
						$token_data['id'] = $api_user_id;
					}
				}elseif( !empty($user_tokens) ){
					$token_data = current($user_tokens);
					$token_data['id'] = key($user_tokens);
				}
			}
		}
		if( empty($token_data) ) throw new EM_Exception( $this->reauthorize_error_string($api_user_id) );
		$this->token = new $this->token_class($token_data);
		return $this->token;
	}

	/**
	 * Sets the access token to the user meta storage where all connected accounts for the user of that token are stored.
	 */
	public function save_access_token(){
		if( $this->authorization_scope == 'site' ){
			$token = $this->token->to_array();
			if( $this->multiple_tokens ){
				$site_tokens = EM_Options::get($this->option_name.'_token', array(), $this->option_dataset);
			}
			if( empty($site_tokens) ) $site_tokens = array();
			$site_tokens[$this->token->id] = $token;
			EM_Options::set($this->option_name.'_token', $site_tokens, $this->option_dataset);
		}elseif( $this->authorization_scope == 'user' ){
			if( $this->multiple_tokens ){
				$user_tokens = get_user_meta($this->user_id, $this->option_dataset.'_'.$this->option_name, true);
			}
			if( empty($user_tokens) ) $user_tokens = array();
			$token = $this->token->to_array();
			$user_tokens[$this->token->id] = $token;
			update_user_meta($this->user_id, $this->option_dataset.'_'.$this->option_name, $user_tokens);
		}
	}
}