<?php
/**
 * Sidebar for Admin Pages
 *
 * @package     Import_Meetup_Events
 * @subpackage  Import_Meetup_Events/templates
 * @copyright   Copyright (c) 2016, Dharmesh Patel
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="upgrade_to_pro">
	<h2><?php esc_html_e( 'Upgrade to Pro', 'import-meetup-events' ); ?></h2>
	<p><?php esc_html_e( 'Unlock more power to events import operation, enable scheduled imports today, Upgrade today!!','import-meetup-events'); ?></p>
	<a class="button button-primary upgrade_button" href="<?php echo esc_url( IME_PLUGIN_BUY_NOW_URL ); ?>" target="_blank">
		<?php esc_html_e( 'Upgrade to Pro','import-meetup-events'); ?>
	</a>
</div>

<div class="upgrade_to_pro">
	<h2><?php esc_html_e( 'Custom WordPress Development Services','import-meetup-events'); ?></h2>
	<p><?php esc_html_e( "From small blog to complex web apps, we push the limits of what's possible with WordPress.","import-meetup-events" ); ?></p>
	<a class="button button-primary upgrade_button" href="<?php echo esc_url('https://xylusthemes.com/contact/?utm_source=insideplugin&utm_medium=web&utm_content=sidebar&utm_campaign=freeplugin'); ?>" target="_blank">
		<?php esc_html_e( 'Hire Us','import-meetup-events'); ?>
	</a>
</div>

<div>
	<p style="text-align:center">
		<strong><?php esc_html_e( 'Would you like to remove these ads?','import-meetup-events'); ?></strong><br>
		<a href="<?php echo esc_url( IME_PLUGIN_BUY_NOW_URL ); ?>" target="_blank">
			<?php esc_html_e( 'Get Premium','import-meetup-events'); ?>
		</a>
	</p>
</div>