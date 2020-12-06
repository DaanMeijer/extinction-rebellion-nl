<?php
/**
 * class for Meetup User Authorization
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    Import_Meetup_Events
 * @subpackage Import_Meetup_Events/includes
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Import_Meetup_Events_Authorize {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'admin_post_ime_authorize_action', array( $this, 'ime_authorize_user' ) );
		add_action( 'admin_post_ime_deauthorize_action', array( $this, 'ime_deauthorize_user' ) );
		add_action( 'admin_post_ime_authorize_callback', array( $this, 'ime_authorize_user_callback' ) );
	}

	/*
	* Authorize Meetup user to get access token
	*/
    function ime_authorize_user() {
		if ( ! empty($_POST) && wp_verify_nonce($_POST['ime_authorize_nonce'], 'ime_authorize_action' ) ) {
			$meetup_options = get_option( IME_OPTIONS );
			$meetup_oauth_key = isset( $meetup_options['meetup_oauth_key'] ) ? $meetup_options['meetup_oauth_key'] : '';
			$meetup_oauth_secret = isset( $meetup_options['meetup_oauth_secret'] ) ? $meetup_options['meetup_oauth_secret'] : '';
			$redirect_url = admin_url( 'admin-post.php?action=ime_authorize_callback' );
			$param_url = urlencode($redirect_url);
			if( $meetup_oauth_key != '' && $meetup_oauth_secret != '' ){

				$dialog_url = "https://secure.meetup.com/oauth2/authorize?client_id="
				        . $meetup_oauth_key . "&response_type=code&redirect_uri=" . $param_url;
				header("Location: " . $dialog_url);
			}else{
				die( __( 'Please insert Meetup Oauth Key and Secret.', 'import-meetup-events' ) );
			}
        } else {
            die( __('You have not access to doing this operations.', 'import-meetup-events' ) );
        }
    }

    /*
	* Remove Meetup user connection
	*/
    function ime_deauthorize_user() {
    	delete_option('ime_authorized_user');
    	delete_option('ime_user_token_options');
		$redirect_url = admin_url('admin.php?page=meetup_import&tab=settings');
	    wp_redirect($redirect_url);
	    exit();
    }

    /*
	* Authorize meetup user on callback to get access token
	*/
    function ime_authorize_user_callback() {
		global $ime_success_msg;
		if ( isset( $_GET['code'] ) && !empty( $_GET['code'] ) ) {

				$code = sanitize_text_field($_GET['code']);
				$meetup_options = get_option( IME_OPTIONS );
				$meetup_oauth_key = isset( $meetup_options['meetup_oauth_key'] ) ? $meetup_options['meetup_oauth_key'] : '';
				$meetup_oauth_secret = isset( $meetup_options['meetup_oauth_secret'] ) ? $meetup_options['meetup_oauth_secret'] : '';
				$redirect_url = admin_url('admin-post.php?action=ime_authorize_callback');
				$param_url = urlencode($redirect_url);
				
				if( $meetup_oauth_key != '' && $meetup_oauth_secret != '' ){

					$token_url = 'https://secure.meetup.com/oauth2/access';
					$args = array(
						'method' => 'POST',
						'headers' => array( 'content-type' => 'application/x-www-form-urlencoded'),
						'body'    => "client_id={$meetup_oauth_key}&client_secret={$meetup_oauth_secret}&grant_type=authorization_code&redirect_uri={$param_url}&code={$code}"
					);
					$access_token = "";
					$ime_user_token_options = $ime_authorized_user = array();
					$response = wp_remote_post( $token_url, $args );
					$body = wp_remote_retrieve_body( $response );
					$body_response = json_decode( $body );
					if ($body != '' && isset( $body_response->access_token ) ) {
						delete_transient('ime_meetup_auth_token');
						$access_token = $body_response->access_token;
					    update_option('ime_user_token_options', $body_response);

						$profile_call= wp_remote_get("https://api.meetup.com/members/self?access_token=$access_token");
						$profile = wp_remote_retrieve_body( $profile_call );
						$profile = json_decode( $profile );
						update_option('ime_authorized_user', $profile );

						$redirect_url = admin_url('admin.php?page=meetup_import&tab=settings&m_authorize=1');
					    wp_redirect($redirect_url);
					    exit();
					}else{
						$redirect_url = admin_url('admin.php?page=meetup_import&tab=settings&m_authorize=0');
					    wp_redirect($redirect_url);
					    exit();
					}
				} else {
					$redirect_url = admin_url('admin.php?page=meetup_import&tab=settings&m_authorize=2');
					wp_redirect($redirect_url);
					exit();
				}

            } else {
				die( __('You have not access to doing this operations.', 'import-meetup-events' ) );
            }
    }
}