<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       http://xylusthemes.com
 * @since      1.0.0
 *
 * @package    Import_Meetup_Events
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove options
delete_option( IME_OPTIONS );

// Remove schduled Imports
$scheduled_import_args = array(
		'post_type'      => 'ime_scheduled_import',
		'posts_per_page' => -1,
	);
$scheduled_imports = get_posts( $scheduled_import_args );
if( !empty( $scheduled_imports ) ){
	foreach ( $scheduled_imports as $import ) {
		if( $import->ID != '' ){
			wp_delete_post( $import->ID, true );
		}		
	}
}

// Remove Import History
$ime_import_history_args = array(
	'post_type'      => 'ime_import_history',
	'posts_per_page' => -1,
);
$ime_import_histories = get_posts( $ime_import_history_args );
if( !empty( $ime_import_histories ) ){
	foreach ( $ime_import_histories as $ime_import_history ) {
		if( $ime_import_history->ID != '' ){
			wp_delete_post( $ime_import_history->ID, true );
		}		
	}
}
