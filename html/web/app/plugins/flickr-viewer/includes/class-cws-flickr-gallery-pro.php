<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://cheshirewebsolutions.com/
 * @since      1.0.0
 *
 * @package    CWS_Flickr_Gallery_Pro
 * @subpackage CWS_Flickr_Gallery_Pro/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    cws_flickr_gallery_pro
 * @subpackage cws_flickr_gallery_pro/includes
 * @author     Ian Kennerley <info@cheshirewebsolutions.com>
 */
class CWS_Flickr_Gallery_Pro {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      CWS_Flickr_Gallery_Pro_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        
		$this->plugin_name = 'cws-flickr-gallery-pro';
		$this->version = '1.1.8';
		$this->isPro = 0;
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		// set installed flag so can determine whether or not to show upgrade message
		update_option( 'cws_fgp_installed', 1 );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - CWS_Flickr_Gallery_Pro_Loader. Orchestrates the hooks of the plugin.
	 * - CWS_Flickr_Gallery_Pro_i18n. Defines internationalization functionality.
	 * - CWS_Flickr_Gallery_Pro_Admin. Defines all hooks for the admin area.
	 * - CWS_Flickr_Gallery_Pro_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cws-flickr-gallery-pro-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cws-flickr-gallery-pro-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cws-flickr-gallery-pro-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cws-flickr-gallery-pro-public.php';

		/**
		 * The class responsible for all Flickr API Authorization functionality.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/api-libs/phpFlickr/phpFlickr.php';   

		/**
		 * The class responsible for all Flickr API functionality.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/api-libs/tbPhpFlickr/tbPhpFlickr.php'; 
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/api-libs/tbPhpFlickr/bootstrap.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/api-libs/tbPhpFlickr/CacheManager.php';
		//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/api-libs/tbPhpFlickr/Credentials.php';

		$this->loader = new CWS_Flickr_Gallery_Pro_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new CWS_Flickr_Gallery_Pro_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new CWS_Flickr_Gallery_Pro_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_isPro() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        
        // Add the options page - Just stores Google AccessCode in prep to return it for AccessToken

        // comment out below to see if it stops error log...
		///$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );
 
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_page' );

		// Added 1.0.1 to allow exra page with details of how to get started...
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_gs_page' );

		// If Pro add Shortcode Snippet page to admin menu
		// if( $this->get_isPro() )
		{
			$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_sc_page' );
		}

        // Register and define the settings
        $this->loader->add_action( 'admin_init', $plugin_admin, 'register_plugin_settings' );  
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new CWS_Flickr_Gallery_Pro_Public( $this->get_plugin_name(), $this->get_version(), $this->get_isPro() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    CWS_Flickr_Gallery_Pro_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Chesk if this is Pro version of plugin.
	 *
	 * @since     1.0.0
	 * @return    bool    The Pro flag for the plugin.
	 */
	public function get_isPro() {
		return $this->isPro;
	}	

}