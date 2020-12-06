<?php
namespace EM_OAuth;
use EM_Exception, EM_Notices, EM_Options;

class OAuth_API {
	/**
	 * The name of this service to be displayed to the end user in notices etc.
	 * @var string
	 */
	protected static $service_name = 'EM OAuth 2.0';
	/**
	 * @var string
	 */
	protected static $service_url = 'http://example.com';
	/**
	 * Ths option/key name used to differentiate this from other OAuth objects stored in the database tables.
	 * @var string
	 */
	protected static $option_name = 'oauth';
	/**
	 * Where data about this API is stored, which is by default in a serialized array with option name 'em_oauth'
	 * @var string
	 */
	protected static $option_dataset = 'dbem_oauth';
	/**
	 * Allows overriding the default client class to be loaded, set this to the token class name you'd like to use instead.
	 * By default, the extended classname preceded by _Client will be used if it exists, otherwise OAuth_API_Client.
	 * @var string
	 */
	protected static $client_class;
	/**
	 * Allows overriding the default token class to be loaded, set this to the token class name you'd like to use instead.
	 * By default, the extended classname preceded by _Token will be used if it exists, otherwise OAuth_API_Token.
	 * @var string
	 */
	protected static $token_class;
	/**
	 * Defines whether authorization tokens are stored at a site, user or (eventually) network level.
	 * @var string 'site' or 'user' level (future consideration for 'network')
	 */
	protected static $authorization_scope = 'site';
	/**
	 * Whether or not storage destination supports multiple accounts (e.g. multiple accounts for a site or a user)
	 * @var bool
	 */
	protected static $multiple_tokens = false;
	
	/**
	 * @return string
	 */
	public static function get_service_name() {
		return static::$service_name;
	}
	
	/**
	 * @return string
	 */
	public static function get_service_url() {
		return static::$service_url;
	}
	
	/**
	 * @return string
	 */
	public static function get_option_name() {
		return static::$option_name;
	}
	
	/**
	 * @return string
	 */
	public static function get_option_dataset() {
		return static::$option_dataset;
	}
	
	/**
	 * @return OAuth_API_Client|string String representation of token class, used for instantiation or static function/property reference
	 */
	public static function get_client_class() {
		if( static::$client_class !== null && class_exists(static::$client_class) ) return static::$client_class;
		if( static::$client_class === null && class_exists(get_called_class().'_Client') ){
			static::$client_class = get_called_class().'_Client';
			return static::$client_class;
		}
		return 'EM_OAuth\OAuth_API_Client';
	}
	
	/**
	 * @return OAuth_API_Token|string String representation of token class, used for instantiation or static function/property reference
	 */
	public static function get_token_class() {
		if( static::$token_class !== null && class_exists(static::$token_class) ) return static::$token_class;
		if( static::$token_class === null && class_exists(get_called_class().'_Token') ){
			static::$token_class = get_called_class().'_Token';
			return static::$token_class;
		}
		return 'EM_OAuth\OAuth_API_Token';
	}
	
	/**
	 * @return string
	 */
	public static function get_authorization_scope() {
		return static::$authorization_scope;
	}
	
	/**
	 * @return bool
	 */
	public static function supports_multiple_tokens() {
		return static::$multiple_tokens;
	}

	/**
	 * Loads the service credentials into an abstract client api object. If a user ID is supplied and there's an issue retrieving an access token, an exception will be returned.
	 * @param int $user_id The User ID in WordPress
	 * @param int $api_user_id The ID of the account in Google (i.e. the email)
	 * @return OAuth_API_Client
	 * @throws EM_Exception
	 */
	public static function get_client( $user_id = 0, $api_user_id = 0 ) {
		//set up the client
		$client_class = static::get_client_class();
		$client = new $client_class(); /* @var OAuth_API_Client $client */
		//load user access token
		if( $user_id !== false ) {
			if ( empty($user_id) ) $user_id = get_current_user_id();
			$client->load_token( $user_id, $api_user_id );
		}
		return $client;
	}
	
	public static function get_user_tokens( $user_id = false ){
		if( static::$authorization_scope !== 'user' ) return array();
		if( empty($user_id) ) $user_id = get_current_user_id();
		$user_tokens = get_user_meta( $user_id, static::$option_dataset.'_'.static::$option_name, true );
		if( empty($user_tokens) ) $user_tokens = array();
		return $user_tokens;
	}
	
	/**
	 * @return array[OAuth_API_Token]
	 */
	public static function get_site_tokens(){
		if( static::$authorization_scope !== 'site' ) return array();
		$site_tokens = EM_Options::get(static::$option_name.'_token', array(), static::$option_dataset);
		if( empty($site_tokens) ) $site_tokens = array();
		return $site_tokens;
	}

	/**
	 * Includes and calls the code required to handle a callback from FB to store user auth token.
	 */
	public static function oauth_authorize() {
		global $EM_Notices;
		if( !empty($EM_Notices) ) $EM_Notices = new EM_Notices();
		if( !empty($_REQUEST['code']) ){
			try{
				$client = static::get_client(false);
				if( !$client->oauth_state || (!empty($_REQUEST['state']) && !wp_verify_nonce( $_REQUEST['state'], static::$option_name.'_authorize')) ){
					$EM_Notices->add_error( sprintf( esc_html__( 'There was an error connecting to %s: %s', 'events-manager' ), static::$service_name, '<code>No State Provided</code>'), true );
				}else{
					try {
						$client->request( $_REQUEST['code'] );
						$EM_Notices->add_confirm( sprintf( esc_html__( 'Your account has been successfully connected with %s!', 'events-manager' ), static::$service_name ), true);
					} catch ( EM_Exception $e ){
						$EM_Notices->add_error( sprintf( esc_html__( 'There was an error connecting to %s: %s', 'events-manager' ), static::$service_name, '<code>'.$e->getMessage().'</code>' ), true );
					}
				}
			} catch ( EM_Exception $ex ){
				$EM_Notices->add_error($ex->get_messages(), true);
			}
		}else{
			$EM_Notices->add_error( sprintf( esc_html__( 'There was an error connecting to %s: %s', 'events-manager' ), static::$service_name, '<code>No Authorization Code Provided</code>'), true );
		}
		// Redirect to settings page
		$query_args = array( 'page' => 'events-manager-options' );
		$url = add_query_arg( $query_args, admin_url( 'admin.php' ) );
		wp_redirect( $url );
		die();
	}

	/**
	 * Handles disconnecting a user from one or all their connected Google accounts, attempting to revoke their key in the process.
	 */
	public static function oauth_disconnect(){
		global $EM_Notices;
		if( !empty($EM_Notices) ) $EM_Notices = new EM_Notices();

		if( static::$authorization_scope == 'user' ){
			$account_tokens = static::get_user_tokens();
		}else{
			$account_tokens = static::get_site_tokens();
		}
		$accounts_to_disconnect = array();
		if( empty($_REQUEST['user']) && !empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'em-oauth-'. static::$option_name .'-disconnect') ){
			$accounts_to_disconnect = array_keys($account_tokens);
		}elseif( !empty($_REQUEST['account']) && !empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'em-oauth-'. static::$option_name .'-disconnect-'.$_REQUEST['account']) ){
			if( !empty($account_tokens[$_REQUEST['account']]) ){
				$accounts_to_disconnect[] = $_REQUEST['account'];
			}
		}else{
			$EM_Notices->add_error('Missing nonce, please contact your administrator.', true);
		}
		if( !empty($accounts_to_disconnect) ){
			$errors = $disconnected_accounts = array();
			foreach( $accounts_to_disconnect as $account_id ){
				try{
					$client = static::get_client( get_current_user_id(), $account_id);
					$client->revoke();
				} catch ( EM_Exception $ex ){
					$account_name = !empty( $client->token->email ) ? $client->token->email : $client->token->name;
					$errors[] = "<em>$account_name</em> - " . $ex->getMessage();
				} finally{
					$disconnected_accounts[] = $account_id;
					unset($account_tokens[$account_id]);
				}
			}
			if( !empty($disconnected_accounts) ){
				if( static::$authorization_scope == 'user' ){
					if( empty($account_tokens) ){
						delete_user_meta( get_current_user_id(), 'em_oauth_'.static::$option_name );
					}else{
						update_user_meta( get_current_user_id(), 'em_oauth_'.static::$option_name, $account_tokens );
					}
				}else{
					EM_Options::set(static::$option_name.'_token', $account_tokens, static::$option_dataset);
				}
				$success = _n('You have successfully disconnected from your %s account.', 'You have successfully disconnected from your %s accounts.', count($accounts_to_disconnect), 'events-manager');
				$EM_Notices->add_confirm(sprintf($success, static::$service_name), true);
			}
			if( !empty($errors) ){
				$error_msg = sprintf( esc_html__('There were some issues whilst disconnecting from your %s account(s) :', 'events-manager'), static::$service_name );
				array_unshift( $errors, $error_msg );
				$EM_Notices->add_error( $errors, true );
			}
		}

		// Redirect to settings page
		$query_args = array( 'page' => 'events-manager-options' );
		$url = add_query_arg( $query_args, admin_url( 'admin.php' ) );
		wp_redirect( $url );
		die();
	}
}
//include dependents
include('oauth-api-token.php');
include('oauth-api-client.php');
if( is_admin() ){
	include('oauth-admin-settings.php');
}