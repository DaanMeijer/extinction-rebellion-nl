<?php
namespace EM_OAuth;
use EM_Options, EM_Exception;
use Matrix\Exception;

class OAuth_API_Admin_Settings {

	public static $option_name = 'em_oauth';
	public static $service_name = 'EM OAuth 2.0';
	/**
	 * @var OAuth_API Class name of base API class for this service type, used for static variable referencing
	 */
	public static $api_class = 'EM_OAuth\OAuth_API';
	public static $service_url = 'http://example.com';
	public static $icon_url = '';


	public static function init(){
		$class = get_called_class(); //get child class name to call
		self::$icon_url = plugin_dir_url(__FILE__). 'icon.png';
		//handle service app creds
		add_action('em_options_page_footer', "$class::em_settings_apps");
		add_action('em_options_save', "$class::em_settings_save_apps");
	}
	
	/**
	 * @return OAuth_API
	 */
	public static function get_api_class(){
		//set default API class name if not defined by parent
		if( self::$api_class === static::$api_class && class_exists(str_replace('_Admin_Settings', '', get_called_class())) ){
			static::$api_class = str_replace('_Admin_Settings', '', get_called_class());
		}
		return static::$api_class;
	}

	public static function em_settings_save_apps(){
		$api = static::get_api_class();
		if( $api::get_option_dataset() == 'dbem_oauth' ) return;
		$option_names = array($api::get_option_name().'_app_id', $api::get_option_name().'_app_secret');
		foreach( $option_names as $option_name ){
			$value = !empty($_REQUEST[$api::get_option_dataset()][$option_name]) ? $_REQUEST[$api::get_option_dataset()][$option_name] : '';
			EM_Options::set($option_name, $value, $api::get_option_dataset());
		}
	}
	
	public static function em_settings_apps_header(){
		// override this for extra content above settings meta box
	}

	public static function em_settings_apps(){
		$desc = esc_html__("You'll need to create an App with %s and obtain the App credentials by going to %s.", 'events-manager');
		$desc_url = esc_html__('You will also need supply an OAuth redirect url to %s. Your url for this site is : %s', 'events-manager');
		$api = static::get_api_class(); /* @var OAuth_API $api */
		$api_client_class = $api::get_client_class();
		$callback_url = $api_client_class::get_oauth_callback_url();
		$service_name = $api::get_service_name();
		?>
		<div  class="postbox em-oaut-connect em-oauth-connect-<?php echo esc_attr($api::get_authorization_scope()); ?>" id="em-opt-<?php echo esc_attr($api::get_option_name()); ?>-app" >
			<div class="handlediv" title="<?php __('Click to toggle', 'events-manager'); ?>"><br /></div><h3><span><?php echo esc_html($api::get_service_name()) ?></span></h3>
			<div class="inside">
				<?php static::em_settings_apps_header(); ?>
				<h4><?php esc_html_e('Server API Credentials', 'events-manager-zoom'); ?></h4>
				<p><?php printf( $desc, $service_name, '<a href="'. $api::get_service_url() .'">'. $api::get_service_url() .'</a>'); ?></p>
				<p><?php printf( $desc_url, $service_name, "<code>$callback_url</code>") ?></p>
				<p><?php printf( esc_html__('Once you have entered valid API information, you will see a button below to connect your site to %s.', 'events-manager-zoom'), $service_name); ?></p>
				<table class='form-table'>
					<?php
					em_options_input_text(sprintf(__('%s App ID', 'events-manager'), $service_name), $api::get_option_dataset().'['.$api::get_option_name().'_app_id]', '');
					em_options_input_text(sprintf(__('%s App Secret', 'events-manager'), $service_name), $api::get_option_dataset().'['.$api::get_option_name().'_app_secret]', '');
					?>
				</table>
				<?php
				static::em_settings_user_auth();
				static::em_settings_apps_footer();
				?>
			</div> <!-- . inside -->
		</div> <!-- .postbox -->
		<?php
	}
	
	public static function em_settings_apps_footer(){
		// override this for extra content above settings meta box
	}
	
	public static function em_settings_user_auth(){
		//a fresh client with no token for generating oauth links
		$api = static::get_api_class(); /* @var OAuth_API $api */
		try{
			$api_client = $api::get_client(false); /* @var OAuth_API_Client $api_client */
			$service_name = $api::get_service_name();
			$option_name = $api::get_option_name();
			//get tokens if client is configured
	        if( !is_wp_error($api_client) ){ //oauth is not configured correctly...
	            $oauth_url = $api_client->get_oauth_url();
	            //we don't need to verify connections at this point, we just need to know if there are any
		        if( $api_client->authorization_scope == 'user' ){
	                $user_id = get_current_user_id();
			        $access_tokens = $api::get_user_tokens();
		        }else{
		        	$user_id = null;
					$access_tokens = $api::get_site_tokens();
		        }
		        $oauth_accounts = array();
		        $connected = $reconnect_required = false;
	            foreach( $access_tokens as $account_id => $oauth_account ){
	                try {
	                    $api_client->load_token( $account_id, $user_id );
	                    $verification = true;
	                } catch ( EM_Exception $e ) {
	                    $verification = false;
	                }
	                $oauth_account['id'] = !empty($oauth_account['email']) ? $oauth_account['email'] : $account_id;
	                $disconnect_url_args = array( 'action' => 'em_oauth_'. $api::get_option_name(), 'callback' => 'disconnect', 'account' => $account_id, 'nonce' => wp_create_nonce('em-oauth-'. $option_name .'-disconnect-'.$account_id) );
	                $oauth_account['disconnect'] = add_query_arg( $disconnect_url_args, admin_url( 'admin-ajax.php' ) );
	                if( !$verification ){
	                    $oauth_account['reconnect'] = true;
	                    $reconnect_required = true;
	                }else{
	                    $connected = true;
	                }
	                $oauth_accounts[] = $oauth_account;
	            }
	            if( $connected ){
	                $button_url = add_query_arg( array( 'action' => 'em_oauth_'. $option_name, 'callback' => 'disconnect', 'nonce' => wp_create_nonce('em-oauth-'. $option_name .'-disconnect') ), admin_url( 'admin-ajax.php' ) );
	                $button_text = count($oauth_accounts) > 1 ? __('Disconnect All', 'events-manager') : __('Disonnect', 'events-manager');
	                $button_class = 'button-secondary';
	            }else{
	                $button_url = $oauth_url;
	                $button_text = __('Connect', 'events-manager');
	                $button_class = 'button-primary';
	            }
	        }
			?>
			<div class="em-oauth-service-info">
				<?php if( $api::get_authorization_scope() == 'user'): ?>
				<h4><?php echo $service_name; ?></h4>
				<?php else: ?>
				<h4><?php esc_html_e('Account Connection', 'events-manager-zoom'); ?></h4>
				<?php endif; ?>
				<?php if( $connected || $reconnect_required ): ?>
					<p><?php echo esc_html(sprintf(_n('You are successfully connected to the following %s account:', 'You are successfully connected to the following %s accounts:', count($oauth_accounts), 'events-manager-zoom'), $service_name)); ?></p>
					<ul clss="em-oauth-service-accounts">
						<?php foreach ( $oauth_accounts as $oauth_account ): ?>
							<li class="em-oauth-service-account em-oauth-account-<?php echo empty($oauth_account['reconnect']) ? 'connected':'disconnected'; ?>">
								<img src="<?php echo esc_url($oauth_account['photo']); ?>" width="25" height="25">
								<div class="em-oauth-account-description">
                                <span class="em-oauth-account-label">
                                    <?php if( !empty($oauth_account['reconnect']) ): ?><span class="dashicons dashicons-warning"></span><?php endif; ?>
	                                <?php echo esc_html($oauth_account['name']) .' <em>('. esc_html($oauth_account['id']) .')</em>'; ?>
                                </span>
									<span class="em-oauth-account-actions">
                                    <?php if( count($oauth_accounts) > 1 ): ?>
	                                    <a href="<?php echo esc_url($oauth_account['disconnect']); ?>"><?php esc_html_e('Disconnect', 'events-manager'); ?></a>
                                    <?php elseif( !empty($oauth_account['reconnect']) ): ?>
	                                    <a href="<?php echo esc_url($oauth_url); ?>"><?php esc_html_e('Reconnect', 'events-manager'); ?></a> |
                                        <a href="<?php echo esc_url($oauth_account['disconnect']); ?>"><?php esc_html_e('Remove', 'events-manager'); ?></a>
                                    <?php endif; ?>
                                </span>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
					<p>
						<a class="<?php echo $button_class; ?>  em-oauth-connect-button" href="<?php echo esc_url($button_url); ?>"><?php echo esc_html($button_text); ?></a>
						<?php if( $api::supports_multiple_tokens() ): ?>
						<a class="button-secondary" href="<?php echo esc_url($oauth_url); ?>"><?php esc_html_e('Connect additional account') ?></a>
						<?php endif; ?>
					</p>
					<?php do_action('em_settings_user_auth_after_connect_additional_'.$option_name); ?>
					<p><em><?php esc_html_e('If you are experiencing errors when trying to use any of these accounts, try disconnecting and connecting again.', 'events-manager'); ?></em></p>
				<?php else: ?>
					<p><em><?php echo sprintf(esc_html__('Connect to import events and locations from %s.','events-manager'), $service_name); ?></em></p>
					<a class="<?php echo $button_class; ?>  em-oauth-connect-button" href="<?php echo esc_url($button_url); ?>"><?php echo esc_html($button_text); ?></a>
				<?php endif; ?>
			</div>
			<?php
		}catch( EM_Exception $ex ){
			?>
			<div class="em-oauth-service-info">
				<p><em><?php echo $ex->get_message(); ?></em></p>
			</div>
			<?php
		}
	}
}