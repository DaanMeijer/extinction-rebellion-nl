<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPPicasa Admin Functions
 * 
 * Loads main functions used by admin menu and front-end.
 * 
 * Copyright (c) 2011, cheshirewebsolutions.com, Ian Kennerley (info@cheshirewebsolutions.com).
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/ 


/**
 *
 *  Allow redirection, even if my theme starts to send output to the browser
 *
 */	

add_action( 'init', 'cws_fgp_do_output_buffer' );
function cws_fgp_do_output_buffer() {
	ob_start();
}

// Retrieve and display the URL parameter
function cws_fgp_output_album_id() {
	global $wp_query;
	
	if( isset( $wp_query->query_vars['album_id'] ) ) {
		return $wp_query->query_vars['album_id'];
	}
}
/*
function cws_fgp_query_vars_filter($vars) {
	$vars[] = 'cws_page';
	$vars[] .= 'cws_album';
	$vars[] .= 'cws_album_title'; // pass album title to results pages, expander, grid, list
  $vars[] .= 'cws_debug'; // add for simple way to enable debugging via address bar

  return $vars;
}
add_filter( 'query_vars', 'cws_fgp_query_vars_filter' );
*/

function cws_fgp_query_vars_filter($vars) {
  $vars[] .= 'cws_debug'; // add for simple way to enable debugging via address bar

  return $vars;
}
add_filter( 'query_vars', 'cws_fgp_query_vars_filter' );






function cws_fgp_getWPPM() {

  if ( ! did_action('wp_loaded') ) {
    $msg = 'Please call cws_fgp_getCurrentUser after wp_loaded is fired.';
    return new WP_Error( 'to_early_for_user', $msg );
  }

  static $wp_pm = NULL;

  if ( is_null( $wp_pm ) ) {
    $wp_pm = new CWS_FGP_WP_PM( new CWS_FGP_WP_PM_User( get_current_user_id() ) );
  }

  return $wp_pm;
}


function cws_fgp_getCurrentUser() {

  $wppm = cws_fgp_getWPPM();

  if ( is_wp_error( $wppm ) ) return $wppm;

  $user = $wppm->getUser();

  if ( $user instanceof CWS_FGP_WP_PM_User ) return $user;
}

add_action( 'wp_loaded', 'cws_fgp_getCurrentUser' );



function cws_fgp_displayUpgradeID() {

  $current_user = cws_fgp_getCurrentUser();
  if ( $current_user instanceof CWS_FGP_WP_PM_User ) {
    // $plugin = new CWS_Flickr_Gallery_Pro( $plugin_name, $version, $isPro );
    $plugin = new CWS_Flickr_Gallery_Pro();
    $plugin_admin = new CWS_Flickr_Gallery_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro() );
    $plugin_admin->cws_fgp_admin_installed_notice($current_user);
    $plugin_admin->cws_fgp_ignore_upgrade($current_user);

    // $plugin_admin->cws_fgp_admin_migrate_notice($current_user);

  } else { //echo 'No one logged in'; 
  }
}

// This shows/hides the Admin Upgrade Notice
add_action( 'wp_loaded', 'cws_fgp_displayUpgradeID', 30 );


//enqueues our external font awesome stylesheet
function enqueue_our_required_stylesheets(){
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'); 
}
add_action('wp_enqueue_scripts','enqueue_our_required_stylesheets');
