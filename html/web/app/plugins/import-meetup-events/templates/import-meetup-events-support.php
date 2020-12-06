<?php
// If this file is called directly, abort.
// Icon Credit: Icon made by Freepik and Vectors Market from www.flaticon.com
if ( ! defined( 'ABSPATH' ) ) exit;
global $ime_events;
$open_source_support_url = 'https://wordpress.org/support/plugin/import-meetup-events/';
$support_url = 'https://xylusthemes.com/support/?utm_source=insideplugin&utm_medium=web&utm_content=sidebar&utm_campaign=freeplugin';

$review_url = 'https://wordpress.org/support/plugin/import-meetup-events/reviews/?rate=5#new-post';
$facebook_url = 'https://www.facebook.com/xylusinfo/';
$twitter_url = 'https://twitter.com/XylusThemes/';

?>
<div class="wpea_container">
    <div class="wpea_row">
        <div class="wpea-column support_well">
        	<h3 class="setting_bar"><?php esc_attr_e( 'Getting Support', 'import-meetup-events' ); ?></h3>
            <div class="ime-support-features">
				<div class="ime-support-features-card">
					<div class="ime-support-features-img">
						<img class="ime-support-features-icon" src="<?php echo IME_PLUGIN_URL.'assets/images/document.svg'; ?>" alt="<?php esc_attr_e( 'Looking for Something?', 'import-meetup-events' ); ?>">
					</div>
					<div class="ime-support-features-text">
						<h3 class="ime-support-features-title"><?php esc_attr_e( 'Looking for Something?', 'import-meetup-events' ); ?></h3>
						<p><?php esc_attr_e( 'We have documentation of how to import meetup events.', 'import-meetup-events' ); ?></p>
						<a target="_blank" class="button button-primary" href="http://docs.xylusthemes.com/docs/import-meetup-events/"><?php esc_attr_e( 'Plugin Documentation', 'import-meetup-events' ); ?></a>
					</div>
				</div>
				<div class="ime-support-features-card">
					<div class="ime-support-features-img">
						<img class="ime-support-features-icon" src="<?php echo IME_PLUGIN_URL.'assets/images/call-center.svg'; ?>" alt="<?php esc_attr_e( 'Need Any Assistance?', 'import-meetup-events' ); ?>">
					</div>
					<div class="ime-support-features-text">
						<h3 class="ime-support-features-title"><?php esc_attr_e( 'Need Any Assistance?', 'import-meetup-events' ); ?></h3>
						<p><?php esc_attr_e( 'Our EXPERT Support Team is always ready to Help you out.', 'import-meetup-events' ); ?></p>
						<a target="_blank" class="button button-primary" href="https://xylusthemes.com/support/"><?php esc_attr_e( 'Contact Support', 'import-meetup-events' ); ?></a>
					</div>
				</div>
				<div class="ime-support-features-card">
					<div class="ime-support-features-img">
						<img class="ime-support-features-icon"  src="<?php echo IME_PLUGIN_URL.'assets/images/bug.svg'; ?>" alt="<?php esc_attr_e( 'Found Any Bugs?', 'import-meetup-events' ); ?>" />
					</div>
					<div class="ime-support-features-text">
						<h3 class="ime-support-features-title"><?php esc_attr_e( 'Found Any Bugs?', 'import-meetup-events' ); ?></h3>
						<p><?php esc_attr_e( 'Report any Bug that you Discovered, Get Instant Solutions.', 'import-meetup-events' ); ?></p>
						<a target="_blank" class="button button-primary" href="https://github.com/xylusthemes/import-meetup-events"><?php esc_attr_e( 'Report to GitHub', 'import-meetup-events' ); ?></a>
					</div>
				</div>
				<div class="ime-support-features-card">
					<div class="ime-support-features-img">
						<img class="ime-support-features-icon" src="<?php echo IME_PLUGIN_URL.'assets/images/tools.svg'; ?>" alt="<?php esc_attr_e( 'Require Customization?', 'import-meetup-events' ); ?>" />
					</div>
					<div class="ime-support-features-text">
						<h3 class="ime-support-features-title"><?php esc_attr_e( 'Require Customization?', 'import-meetup-events' ); ?></h3>
						<p><?php esc_attr_e( 'We would Love to hear your Integration and Customization Ideas.', 'import-meetup-events' ); ?></p>
						<a target="_blank" class="button button-primary" href="https://xylusthemes.com/what-we-do/"><?php esc_attr_e( 'Connect Our Service', 'import-meetup-events' ); ?></a>
					</div>
				</div>
				<div class="ime-support-features-card">
					<div class="ime-support-features-img">
						<img class="ime-support-features-icon" src="<?php echo IME_PLUGIN_URL.'assets/images/like.svg'; ?>" alt="<?php esc_attr_e( 'Like The Plugin?', 'import-meetup-events' ); ?>" />
					</div>
					<div class="ime-support-features-text">
						<h3 class="ime-support-features-title"><?php esc_attr_e( 'Like The Plugin?', 'import-meetup-events' ); ?></h3>
						<p><?php esc_attr_e( 'Your Review is very important to us as it helps us to grow more.', 'import-meetup-events' ); ?></p>
						<a target="_blank" class="button button-primary" href="https://wordpress.org/support/plugin/import-meetup-events/reviews/?rate=5#new-post"><?php esc_attr_e( 'Review Us on WP.org', 'import-meetup-events' ); ?></a>
					</div>
				</div>
			</div>
        </div>

        <?php 
        $plugins = array();
        $plugin_list = $ime_events->admin->get_xyuls_themes_plugins();
        if( !empty( $plugin_list ) ){
            foreach ($plugin_list as $key => $value) {
                $plugins[] = $ime_events->admin->get_wporg_plugin( $key );
            }
        }
        ?>
        <div class="" style="margin-top: 20px;">
            <h3 class="setting_bar"><?php _e( 'Plugins you should try','import-meetup-events' ); ?></h3>
            <?php 
            if( !empty( $plugins ) ){
                foreach ($plugins as $plugin ) {
                    ?>
                    <div class="plugin_box">
                        <?php if( $plugin->banners['low'] != '' ){ ?>
                            <img src="<?php echo $plugin->banners['low']; ?>" class="plugin_img" title="<?php echo $plugin->name; ?>">
                        <?php } ?>                    
                        <div class="plugin_content">
                            <h3><?php echo $plugin->name; ?></h3>

                            <?php wp_star_rating( array(
                            'rating' => $plugin->rating,
                            'type'   => 'percent',
                            'number' => $plugin->num_ratings,
                            ) );?>

                            <?php if( $plugin->version != '' ){ ?>
                                <p><strong><?php _e( 'Version:','import-meetup-events' ); ?> </strong><?php echo $plugin->version; ?></p>
                            <?php } ?>

                            <?php if( $plugin->requires != '' ){ ?>
                                <p><strong><?php _e( 'Requires:','import-meetup-events' ); ?> </strong> <?php _e( 'WordPress ','import-meetup-events' ); echo $plugin->requires; ?>+</p>
                            <?php } ?>

                            <?php if( $plugin->active_installs != '' ){ ?>
                                <p><strong><?php _e( 'Active Installs:','import-meetup-events' ); ?> </strong><?php echo $plugin->active_installs; ?>+</p>
                            <?php } ?>

                            <?php //print_r( $plugin ); ?>
                            <a class="button button-secondary" href="<?php echo admin_url( 'plugin-install.php?tab=plugin-information&plugin='. $plugin->slug.'&TB_iframe=1&width=772&height=600'); ?>" target="_blank">
                                <?php _e( 'Install Now','import-meetup-events' ); ?>
                            </a>
                            <a class="button button-primary" href="<?php echo $plugin->homepage . '?utm_source=crosssell&utm_medium=web&utm_content=supportpage&utm_campaign=freeplugin'; ?>" target="_blank">
                                <?php _e( 'Buy Now','import-meetup-events' ); ?>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            <div style="clear: both;">
        </div>
    </div>
</div>
