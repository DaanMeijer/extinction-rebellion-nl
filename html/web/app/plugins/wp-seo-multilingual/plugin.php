<?php
/**
 * Plugin Name: Yoast SEO Multilingual
 * Plugin URI: https://wpml.org/
 * Description: Compatibility layer for WordPress SEO and WPML | <a href="https://wpml.org/documentation/plugins-compatibility/using-wordpress-seo-with-wpml/">Documentation</a>
 * Author: OnTheGoSystems
 * Author URI: http://www.onthegosystems.com/
 * Version: 1.2.1
 * Plugin Slug: wp-seo-multilingual
 *
 * @package wpml/wpseo
 */

use WPML\WPSEO\Loaders;

// Check if we are already active.
if ( defined( 'WPSEOML_VERSION' ) ) {
	return;
}

define( 'WPSEOML_VERSION', '1.2.1' );
define( 'WPML_WPSEO_VERSION', WPSEOML_VERSION ); // TODO: remove this when WPML 4.3 is released (see wpmlwpseo-28).
define( 'WPSEOML_PLUGIN_PATH', dirname( __FILE__ ) );

require_once WPSEOML_PLUGIN_PATH . '/vendor/autoload.php';

// We have to do this early because wordpress-seo does it early too.
if ( apply_filters( 'wpml_setting', false, 'setup_complete' ) ) {
	$redirector = new WPML_WPSEO_Redirection();
	if ( $redirector->is_redirection() ) {
		add_filter( 'wpml_skip_convert_url_string', '__return_true' );
	}
}

/**
 * Initialize plugin when WPML has loaded.
 */
function wpml_wpseo_init() {
	// Dont load if Yoast SEO is not active.
	if ( ! defined( 'WPSEO_VERSION' ) ) {
		return;
	}

	// WPML prior to 4.2.5 runs its own compatibility classes.
	if ( version_compare( ICL_SITEPRESS_VERSION, '4.2.5', '<' ) ) {
		return;
	}

	$actions_filters_loader = new WPML_Action_Filter_Loader();
	$actions_filters_loader->load( Loaders::get( WPSEO_VERSION ) );
}
add_action( 'wpml_loaded', 'wpml_wpseo_init' );

/**
 * We need to do the redirection checks before wordpress-seo loads.
 * To resolve this we move ourselves first in the plugins list.
 * By using priority 1 we will go after WPML core.
 */
function wpml_wpseo_loads_first() {
	$path    = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
	$plugins = get_option( 'active_plugins' );
	$key     = array_search( $path, (array) $plugins, true );
	if ( $plugins && $key ) {
		array_splice( $plugins, $key, 1 );
		array_unshift( $plugins, $path );
		update_option( 'active_plugins', $plugins );
	}
}
add_action( 'activated_plugin', 'wpml_wpseo_loads_first', 1 );
