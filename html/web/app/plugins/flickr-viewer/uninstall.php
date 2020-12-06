<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

	//$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
	//check_admin_referer( "deactivate-plugin_{$plugin}" );

	global $wpdb;
	
	// Delete options
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'cws_fgp_%';");