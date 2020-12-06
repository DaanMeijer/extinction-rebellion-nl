<?php
/*
Plugin Name: Admin CSS MU
Plugin URI: http://millionclues.com
Description: A plugin to load a CSS file to style the admin side. Works with Multisite.
Author: Arun Basil Lal
Author URI: http://millionclues.com
Version: 2.5
Text Domain: admin-css-mu
Domain Path: /languages
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * ::TODO::
 * - Update version number ADMINCSSMU_VERSION_NUM on every update
 */
 
// Exit If Accessed Directly
if ( ! defined( 'ABSPATH' ) ) exit;

/*------------------------------------------*/
/*			Plugin Setup Functions			*/
/*------------------------------------------*/


// Add Admin Sub Menu: Appearance > Admin CSS MU
function admincssmu_add_menu_links() {
	if ( is_multisite() ) {
		if ( get_current_blog_id() == SITE_ID_CURRENT_SITE ) {
			add_theme_page ( __('Admin CSS MU','admin-css-mu'), __('Admin CSS MU','admin-css-mu'), 'update_core', 'admin-css-mu','admincssmu_admin_interface_render'  );
		}
	}
	else {
		add_theme_page ( __('Admin CSS MU','admin-css-mu'), __('Admin CSS MU','admin-css-mu'), 'update_core', 'admin-css-mu','admincssmu_admin_interface_render'  );
	}
}
add_action( 'admin_menu', 'admincssmu_add_menu_links' );


// Print Direct Link To Admin CSS MU Options Page In Plugins List
function admincssmu_settings_link( $links ) {
	return array_merge(
		array(
			'settings' => '<a href="' . admin_url( 'themes.php?page=admin-css-mu' ) . '">' . __( 'Add CSS', 'admin-css-mu' ) . '</a>'
		),
		$links
	);
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'admincssmu_settings_link' );


// Add Links to Plugins list
function admincssmu_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'admincssmu.php' ) !== false ) {
		$new_links = array(
				'donate' => '<a href="http://millionclues.com/donate/" target="_blank">Donate</a>',
				'hireme' 	=> '<a href="http://millionclues.com/portfolio/" target="_blank">Hire Me For A Project</a>',
				);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'admincssmu_plugin_row_meta', 10, 2 ); 


// Load Text Domain
function admincssmu_load_plugin_textdomain() {
    load_plugin_textdomain( 'admin-css-mu', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'admincssmu_load_plugin_textdomain' );


// Register Settings
function admincssmu_register_settings() {
	register_setting( 'admincssmu_settings_group', 'admincssmu_custom_css', 'admincssmu_clean_css_with_csstidy' );
}
add_action( 'admin_init', 'admincssmu_register_settings' );

/**
 * Add plugin version to database
 *
 * @since 		2.4
 * @constant 	ADMINCSSMU_VERSION_NUM		the version number of the current version
 * @refer		https://codex.wordpress.org/Creating_Tables_with_Plugins#Adding_an_Upgrade_Function
 */
if (!defined('ADMINCSSMU_VERSION_NUM'))
    define('ADMINCSSMU_VERSION_NUM', '2.4');
// update_option('abl_admincssmu_version', ADMINCSSMU_VERSION_NUM);	// Disabled to set default values for Load Admin CSS checkbox

/**
 * Set default values for Load CSS checkboxes
 *
 * @since	2.4
 */
if ( is_multisite() ) {
	$installed_ver = get_blog_option( SITE_ID_CURRENT_SITE, 'abl_admincssmu_version' );
}
else {
	$installed_ver = get_option( 'abl_admincssmu_version' );
}

if ($installed_ver == '' ) {
	if ( is_multisite() ) {
		$admincssmu_custom_css_option = get_blog_option( get_current_blog_id(), 'admincssmu_custom_css' );
	}
	else {
		$admincssmu_custom_css_option = get_option( 'admincssmu_custom_css' );
	}
	
	// All CSS loaded by default
	$admincssmu_custom_css_option['load_css'] = 1;
	
	if ( is_multisite() ) {
		update_blog_option(SITE_ID_CURRENT_SITE, 'admincssmu_custom_css', $admincssmu_custom_css_option);
		update_blog_option(SITE_ID_CURRENT_SITE, 'abl_admincssmu_version', ADMINCSSMU_VERSION_NUM);
	}
	else {
		update_option('admincssmu_custom_css', $admincssmu_custom_css_option);
		update_option('abl_admincssmu_version', ADMINCSSMU_VERSION_NUM);
	}
}


// Delete Options During Uninstall
function admincssmu_uninstall_plugin() {
	delete_option( 'admincssmu_custom_css' );
	delete_option( 'abl_admincssmu_version' );
}
register_uninstall_hook(__FILE__, 'admincssmu_uninstall_plugin' );


/**
 * Admin footer text
 *
 * @since	2.3
 */
function admincssmu_footer_text($default) {
    
	// Retun default on non-plugin pages
	$screen = get_current_screen();
	if ( $screen->id !== "appearance_page_admin-css-mu" ) {
		return $default;
	}
	
    $admincssmu_footer_text = sprintf( __( 'If you like this plugin, please <a href="%s" target="_blank">make a donation</a> or leave a <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating to support continued development. Thanks a bunch!', 'admin-css-mu' ), 
								'http://millionclues.com/donate/',
								'https://wordpress.org/support/plugin/admin-css-mu/reviews/?rate=5#new-post' 
						);
	
	return $admincssmu_footer_text;
}
add_filter('admin_footer_text', 'admincssmu_footer_text');


/*--------------------------------------*/
/*			Admin Options Page			*/
/*--------------------------------------*/

// Load Syntax Highlighter
function admincssmu_register_highlighter( $hook ) {
	if ( 'appearance_page_admin-css-mu' === $hook ) {
		wp_enqueue_style( 'highlighter-css', plugins_url( basename( dirname( __FILE__ ) ) . '/highlighter/codemirror.css' ) );
		wp_enqueue_script( 'highlighter-js', plugins_url( basename( dirname( __FILE__ ) ) . '/highlighter/codemirror.js' ), array(), '20140329', true );
		wp_enqueue_script( 'highlighter-css-js', plugins_url( basename( dirname( __FILE__ ) ) . '/highlighter/css.js' ), array(), '20140329', true );
	}
}
add_action( 'admin_enqueue_scripts', 'admincssmu_register_highlighter' );


// Sanitize CSS with CSS Tidy - Uses CSS Tidy Modified By The Jetpack Team. 
function admincssmu_clean_css_with_csstidy ( $input ) {
	$input['admincssmu_admin_css'] 		= admincssmu_csstidy_helper ( $input['admincssmu_admin_css'] );
	return $input;
}

// Scrub And Clean With CSS Tidy
function admincssmu_csstidy_helper ( $css, $minify=false ) {
	
	include_once('csstidy/class.csstidy.php');
	
	$csstidy = new csstidy();
	$csstidy->set_cfg( 'remove_bslash',              false );
	$csstidy->set_cfg( 'compress_colors',            false );
	$csstidy->set_cfg( 'compress_font-weight',       false );
	$csstidy->set_cfg( 'optimise_shorthands',        0 );
	$csstidy->set_cfg( 'remove_last_;',              false );
	$csstidy->set_cfg( 'case_properties',            false );
	$csstidy->set_cfg( 'discard_invalid_properties', true );
	$csstidy->set_cfg( 'css_level',                  'CSS3.0' );
	$csstidy->set_cfg( 'preserve_css',               true );
	
	if ($minify === false) {
		$csstidy->set_cfg( 'template', dirname( __FILE__ ) . '/csstidy/wordpress-standard.tpl' );
	} else {
		$csstidy->set_cfg( 'template', 'highest');
	}
	
	$css = preg_replace( '/\\\\([0-9a-fA-F]{4})/', '\\\\\\\\$1', $css );
	$css = str_replace( '<=', '&lt;=', $css );
	$css = wp_kses_split( $css, array(), array() );
	$css = str_replace( '&gt;', '>', $css ); // kses replaces lone '>' with &gt;
	$css = strip_tags( $css );
	
	$csstidy->parse( $css );
	$css = $csstidy->print->plain();

	return $css;
}

// Admin Interface: Appearance > Admin CSS MU
function admincssmu_admin_interface_render () {

	if ( is_multisite() ) {
		$admincssmu_custom_css_option = get_blog_option( get_current_blog_id(), 'admincssmu_custom_css' );
	}
	else {
		$admincssmu_custom_css_option = get_option( 'admincssmu_custom_css' );
	}
	
	$admincssmu_admin_css_content = isset( $admincssmu_custom_css_option['admincssmu_admin_css'] ) && ! empty( $admincssmu_custom_css_option['admincssmu_admin_css'] ) ? $admincssmu_custom_css_option['admincssmu_admin_css'] : __( "/* Enter Your Custom Admin CSS Here */\r\n", 'admin-css-mu' );

?>
	<div class="wrap">
		<h1><?php _e('Admin CSS MU','admin-css-mu') ?></h1>
		<p><?php _e('Enter your custom CSS below and it will be loaded on all Admin Pages.','admin-css-mu') ?></p>
		
		<form method="post" action="options.php" enctype="multipart/form-data">
		
			<?php settings_fields( 'admincssmu_settings_group' ); ?>

			<textarea rows="10" class="large-text code" id="admincssmu_custom_css[admincssmu_admin_css]" name="admincssmu_custom_css[admincssmu_admin_css]"><?php echo esc_textarea( $admincssmu_admin_css_content ); ?></textarea><br>
			
			<div style="margin-top: 5px;">
				<input type="checkbox" name="admincssmu_custom_css[load_css]" id="admincssmu_custom_css[load_css]" value="1" 
				<?php if ( isset( $admincssmu_custom_css_option['load_css'] ) ) { checked( '1', $admincssmu_custom_css_option['load_css'] ); } ?>>
				<label for="admincssmu_custom_css[load_css]" style="vertical-align: unset;"><?php _e('Load Admin CSS', 'admin-css-mu') ?></label>
			</div>
			
			<div style="margin-top: 5px;">
				<input type="checkbox" name="admincssmu_custom_css[minfy_css]" id="admincssmu_custom_css[minfy_css]" value="1"
					<?php if ( isset( $admincssmu_custom_css_option['minfy_css'] ) ) { checked( '1', $admincssmu_custom_css_option['minfy_css'] ); } ?>>
					<label for="admincssmu_custom_css[minfy_css]" style="vertical-align: unset;"><?php _e('Minify CSS', 'admin-css-mu') ?></label>
			</div>
			
			<?php submit_button( __( 'Save CSS', 'admin-css-mu' ), 'primary', 'submit', true ); ?>
		</form>
		
		<?php // Highlighter ?>
		<script language="javascript">
			jQuery( document ).ready( function() {
				var editor_admin_css = CodeMirror.fromTextArea( document.getElementById( "admincssmu_custom_css[admincssmu_admin_css]" ), {lineNumbers: true, lineWrapping: true} );
			});
		</script>
	</div><?php
}



/*----------------------------------*/
/*			Load CSS Styles			*/
/*----------------------------------*/

// Load Admin CSS Submited Via Admin
function admincssmu_load_admin_css_from_admin() { 
	
	if ( is_multisite() ) {
		$admincssmu_custom_css_option = get_blog_option( SITE_ID_CURRENT_SITE, 'admincssmu_custom_css' );
	}
	else {
		$admincssmu_custom_css_option = get_option( 'admincssmu_custom_css' );
	}
	
	// Check if Load CSS is checked
	if ( !((isset($admincssmu_custom_css_option['load_css'])) && (boolval($admincssmu_custom_css_option['load_css']))) ) {
		return;
	}
	
	$admincssmu_admin_css_content = isset( $admincssmu_custom_css_option['admincssmu_admin_css'] ) && ! empty( $admincssmu_custom_css_option['admincssmu_admin_css'] ) ? $admincssmu_custom_css_option['admincssmu_admin_css'] : '' ; 
	
	$admincssmu_admin_css_content = wp_kses( $admincssmu_admin_css_content, array( '\'', '\"' ) );
	$admincssmu_admin_css_content = str_replace( '&gt;', '>', $admincssmu_admin_css_content );	
	
	// Minify
	if ( (isset($admincssmu_custom_css_option['minfy_css'])) && (boolval($admincssmu_custom_css_option['minfy_css'])) ) {
		$admincssmu_admin_css_content = admincssmu_csstidy_helper($admincssmu_admin_css_content, true);
	} ?>
    <style type="text/css">
        <?php echo $admincssmu_admin_css_content; ?>
    </style><?php 
}
add_filter( 'admin_enqueue_scripts' , 'admincssmu_load_admin_css_from_admin' );


// Load Admin CSS From /wp-content/themes/custom_admin.css For Backward Compatibility With Version 1.0
if ( file_exists ( get_theme_root() . '/custom_admin.css' )) {
	function admincssmu_load_custom_admin_css() {
		wp_enqueue_style('admincssmu_custom_css', get_theme_root_uri() . '/custom_admin.css');
	}
	add_action( 'admin_enqueue_scripts', 'admincssmu_load_custom_admin_css' );
}

/**
 * Get the boolean value of a variable
 *
 * @param 	mixed 	The scalar value being converted to a boolean.
 * @return 	boolean The boolean value of var.
 * @refer	https://millionclues.com/wordpress-tips/solved-fatal-error-call-to-undefined-function-boolval/
 */
if( !function_exists('boolval')) {
  
  function boolval($var){
    return !! $var;
  }
}