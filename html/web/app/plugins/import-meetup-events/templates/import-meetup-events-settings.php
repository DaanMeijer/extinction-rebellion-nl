<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
$meetup_options = get_option( IME_OPTIONS );
$meetup_oauth_key = isset( $meetup_options['meetup_oauth_key'] ) ? $meetup_options['meetup_oauth_key'] : '';
$meetup_oauth_secret = isset( $meetup_options['meetup_oauth_secret'] ) ? $meetup_options['meetup_oauth_secret'] : '';

$ime_user_token_options = get_option( 'ime_user_token_options', array() );
$ime_authorized_user = get_option( 'ime_authorized_user', array() );
?>
<div class="ime_container">
    <div class="ime_row">
        <h3 class="setting_bar"><?php esc_attr_e( 'Meetup Settings', 'import-meetup-events' ); ?></h3>

        <?php
        $site_url = get_home_url();
        if( !isset( $_SERVER['HTTPS'] ) && false === stripos( $site_url, 'https' ) && $meetup_oauth_key != '' && $meetup_oauth_secret != '' && empty($ime_authorized_user) ) {
            ?>
            <div class="widefat ime_settings_error">
                <?php _e( "It looks like you don't have HTTPS enabled on your website. Please enable it. HTTPS is required for authorize your meetup account.","import-meetup-events" ); ?>
            </div>
        <?php
        } ?>
        
        <div class="widefat ime_settings_notice">
            <?php printf( '<b>%1$s</b> %2$s <b><a href="https://secure.meetup.com/meetup_api/oauth_consumers/create" target="_blank">%3$s</a></b> %4$s',  __( 'Note : ','import-meetup-events' ), __( 'You have to create a Meetup OAuth Consumer before filling the following details.','import-meetup-events' ), __( 'Click here','import-meetup-events' ),  __( 'to create new OAuth Consumer','import-meetup-events' ) ); ?>
            <br/>
            <?php _e( 'For detailed step by step instructions ', 'import-meetup-events' ); ?>
            <strong><a href="http://docs.xylusthemes.com/docs/import-meetup-events/creating-oauth-consumer/" target="_blank"><?php _e( 'Click here', 'import-meetup-events' ); ?></a></strong>.
            <br/>
            <?php _e( '<strong>Set the Application Website as : </strong>', 'import-meetup-events' ); ?>
            <span style="color: green;"><?php echo get_site_url(); ?></span>
            <br/>
            <?php _e( '<strong>Set Redirect URI : </strong>', 'import-meetup-events' ); ?>
            <span style="color: green;"><?php echo admin_url( 'admin-post.php?action=ime_authorize_callback' ); ?></span>
        </div>

        <?php
        if( $meetup_oauth_key != '' && $meetup_oauth_secret != '' ){
            ?>
            <h4 class="setting_bar"><?php esc_attr_e( 'Connect your Meetup Account', 'import-meetup-events' ); ?></h4>
            <div class="ime_authorize">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <?php _e( 'Meetup Authorization','import-meetup-events' ); ?> :
                            </th>
                            <td>
                                <?php
                                if( !empty($ime_authorized_user) && isset($ime_authorized_user->name) ) {
                                    $image = isset($ime_authorized_user->photo->thumb_link) ? $ime_authorized_user->photo->thumb_link : '';
                                    $email = isset($ime_authorized_user->email) ? $ime_authorized_user->email : '';
                                    $name = $ime_authorized_user->name;
                                    ?>
                                    <div class="ime_connection_wrapper">
                                        <div class="img_wrap">
                                            <img src="<?php echo $image; ?>"  alt="<?php echo $name; ?>">
                                        </div>
                                        <div class="name_wrap">
                                            <?php printf( __('Connected as: %s', 'import-meetup-events'), '<strong>'.$name.'</strong>' ); ?>
                                            <br/>
                                            <?php echo $email; ?>
                                            <br/>
                                            <a href="<?php echo admin_url('admin-post.php?action=ime_deauthorize_action'); ?>">
                                                <?php _e('Remove Connection', 'import-meetup-events'); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <?php
                                }else{
                                    ?>
                                    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                                        <input type="hidden" name="action" value="ime_authorize_action"/>
                                        <?php wp_nonce_field('ime_authorize_action', 'ime_authorize_nonce'); ?>
                                        <?php
                                        $button_value = __('Connect', 'import-meetup-events');
                                        ?>
                                        <input type="submit" class="button" name="ime_authorize" value="<?php echo $button_value; ?>" />
                                    </form>
                                    <span class="ime_small">
                                        <?php _e( 'Please connect your meetup account for import meetup events.','import-meetup-events' ); ?>
                                    </span>
                                <?php } ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php
        }
        ?>
        <form method="post" id="ime_setting_form">
            <table class="form-table">
                <tbody>
                    <tr>
                        <td colspan="2" style="padding: 0px;">
                            
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <?php _e( 'Meetup OAuth Key','import-meetup-events' ); ?> :
                        </th>
                        <td>
                            <input class="meetup_api_key" name="meetup[meetup_oauth_key]" type="text" value="<?php echo $meetup_oauth_key; ?>" />
                            <span class="xtei_small">
                                <?php printf('%s <a href="https://secure.meetup.com/meetup_api/oauth_consumers/" target="_blank">%s</a>', __( 'Insert your meetup.com OAuth Key you can get it from', 'import-meetup-events' ), __( 'here', 'import-meetup-events' ) ); ?>
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <?php _e( 'Meetup OAuth Secret','import-meetup-events' ); ?> :
                        </th>
                        <td>
                            <input class="meetup_api_key" name="meetup[meetup_oauth_secret]" type="text" value="<?php echo $meetup_oauth_secret; ?>" />
                            <span class="xtei_small">
                                <?php printf('%s <a href="https://secure.meetup.com/meetup_api/oauth_consumers/" target="_blank">%s</a>', __( 'Insert your meetup.com OAuth Secret you can get it from', 'import-meetup-events' ), __( 'here', 'import-meetup-events' ) ); ?>
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" style="text-align: center" colspan="2">
                            <?php _e( ' - OR -', 'import-meetup-events' ); ?>
                        </th>
                    </tr>

                    <tr>
                        <th scope="row">
                            <?php _e( 'Meetup API key','import-meetup-events' ); ?> :
                        </th>
                        <td>
                            <input class="meetup_api_key" name="meetup[meetup_api_key]" type="text" value="<?php if ( isset( $meetup_options['meetup_api_key'] ) ) { echo $meetup_options['meetup_api_key']; } ?>" />
                            <span class="xtei_small">
                                <?php printf('%s <a href="https://secure.meetup.com/meetup_api/key/" target="_blank">%s</a>', __( 'Insert your meetup.com API key you can get it from', 'import-meetup-events' ), __( 'here', 'import-meetup-events' ) ); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" style="text-align: center" colspan="2">
                            
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Update existing events', 'import-meetup-events' ); ?> : 
                        </th>
                        <td>
                            <?php
                            $update_meetup_events = isset( $meetup_options['update_events'] ) ? $meetup_options['update_events'] : 'no';
                            ?>
                            <input type="checkbox" name="meetup[update_events]" value="yes" <?php if( $update_meetup_events == 'yes' ) { echo 'checked="checked"'; } ?> />
                            <span class="xtei_small">
                                <?php _e( 'Check to updates existing events.', 'import-meetup-events' ); ?>
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <?php _e('Direct link to Meetup', 'import-meetup-events'); ?> :
                        </th>
                        <td>
                            <?php
                            $direct_link = isset($meetup_options['direct_link']) ? $meetup_options['direct_link'] : 'no';
                            ?>
                            <input type="checkbox" name="meetup[direct_link]" value="yes" <?php if ($direct_link == 'yes') { echo 'checked="checked"'; }if (!ime_is_pro()) {echo 'disabled="disabled"'; } ?> />
                            <span>
                                <?php _e('Check to enable direct event link to Meetup instead of event detail page.', 'import-meetup-events'); ?>
                            </span>
                            <?php do_action('ime_render_pro_notice'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Advanced Synchronization', 'import-meetup-events' ); ?> : 
                        </th>
                        <td>
                            <?php
                            $advanced_sync = isset( $meetup_options['advanced_sync'] ) ? $meetup_options['advanced_sync'] : 'no';
                            ?>
                            <input type="checkbox" name="meetup[advanced_sync]" value="yes" <?php if( $advanced_sync == 'yes' ) { echo 'checked="checked"'; } if( !ime_is_pro()){ echo 'disabled="disabled"'; } ?> />
                            <span>
                                <?php _e( 'Check to enable advanced synchronization, this will delete events which are removed from Meetup. Also, it deletes passed events.', 'import-meetup-events' ); ?>
                            </span>
                            <?php do_action( 'ime_render_pro_notice' ); ?>
                        </td>
                    </tr>

                    <tr>
						<th scope="row">
							<?php esc_attr_e( 'Accent Color', 'import-meetup-events' ); ?> :
						</th>
						<td>
						<?php
						$accent_color = isset( $meetup_options['accent_color'] ) ? $meetup_options['accent_color'] : '#039ED7';
						?>
						<input class="ime_color_field" type="text" name="meetup[accent_color]" value="<?php echo esc_attr( $accent_color ); ?>"/>
						<span class="ime_small">
							<?php esc_attr_e( 'Choose accent color for front-end event grid and event widget.', 'import-meetup-events' ); ?>
						</span>
						</td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <?php _e('Event Slug', 'import-meetup-events'); ?> :
                        </th>
                        <td>
                            <?php
                            $event_slug = isset($meetup_options['event_slug']) ? $meetup_options['event_slug'] : 'meetup-event';
                            ?>
                            <input type="text" name="meetup[event_slug]" value="<?php if ( $event_slug ) { echo $event_slug; } ?>" <?php if (!ime_is_pro()) { echo 'disabled="disabled"'; } ?> />
                            <span class="ime_small">
                                <?php _e('Slug for the event.', 'import-meetup-events'); ?>
                            </span>
                            <?php do_action('ime_render_pro_notice'); ?>
                        </td>
                    </tr>
						<th scope="row">
							<?php esc_attr_e( 'Event Display Time Format', 'import-meetup-events' ); ?> :
						</th>
						<td>
						<?php
                        $time_format = isset( $meetup_options['time_format'] ) ? $meetup_options['time_format'] : '12hours';
						?>
                        <select name="meetup[time_format]">
								<option value="12hours" <?php selected('12hours', $time_format); ?>><?php esc_attr_e( '12 Hours', 'import-meetup-events' );  ?></option>
                                <option value="24hours" <?php selected('24hours', $time_format); ?>><?php esc_attr_e( '24 Hours', 'import-meetup-events' ); ?></option>						
                                <option value="wordpress_default" <?php selected('wordpress_default', $time_format); ?>><?php esc_attr_e( 'WordPress Default', 'import-meetup-events' ); ?></option>
                        </select>
						<span class="ime_small">
							<?php esc_attr_e( 'Choose event display time format for front-end.', 'import-meetup-events' ); ?>
						</span>
						</td>
					</tr>
                    
                </tbody>
            </table>
            <br/>

            <div class="ime_element">
                <input type="hidden" name="ime_action" value="ime_save_settings" />
                <?php wp_nonce_field( 'ime_setting_form_nonce_action', 'ime_setting_form_nonce' ); ?>
                <input type="submit" class="button-primary xtei_submit_button" style=""  value="<?php esc_attr_e( 'Save Settings', 'import-meetup-events' ); ?>" />
            </div>
            </form>
    </div>
</div>
