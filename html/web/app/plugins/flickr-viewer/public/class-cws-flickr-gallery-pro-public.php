<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://cheshirewebsolutions.com/
 * @since      2.0.0
 *
 * @package    CWS_Flickr_Gallery_Pro
 * @subpackage CWS_Flickr_Gallery_Pro/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    CWS_Flickr_Gallery_Pro
 * @subpackage CWS_Flickr_Gallery_Pro/public
 * @author     Ian Kennerley <info@cheshirewebsolutions.com>
 */
class CWS_Flickr_Gallery_Pro_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $isPro ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->isPro = $isPro;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in CWS_Flickr_Gallery_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The CWS_Flickr_Gallery_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cws-flickr-gallery-pro-public.css', array(), $this->version, 'all' );

		// TODO: make the inclusion of this conditional?
		wp_enqueue_style( 'lightbox', plugin_dir_url( __FILE__ ) . 'css/lightbox/lightbox.css', array(), $this->version, 'all' );
				
		wp_enqueue_style( 'cws_fgp_thumbnail_grid_css', plugin_dir_url( __FILE__ ) . 'css/default.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'cws_fgp_thumbnail_grid_css1', plugin_dir_url( __FILE__ ) . 'css/component.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in CWS_Flickr_Gallery_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The CWS_Flickr_Gallery_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cws-flickr-gallery-pro-public.js', array( 'jquery' ), $this->version, false );

	}

}
