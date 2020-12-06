<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://cheshirewebsolutions.com/
 * @since      1.0.0
 *
 * @package    CWS_Flickr_Gallery_Pro
 * @subpackage CWS_FLickr_Gallery_Pro/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class CWS_Flickr_Gallery_Pro_Admin {

    var $debug = false;
    
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The is user authenticated with Google.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      int    $authenticated    Check is user authenticated with Google.
	 */    
    private $authenticated = 0;

    /**
     * The check is this a Pro version.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $isPro    Check is if this is pro version.
     */    
    private $isPro ;  

    /**
     * User Id used to check ignore upgrade notice.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $userId    User Id of loggedin user.
     */ 
    private $userId;

    /**
     * The check is this site is in Development (i.e. NOT Production).
     *
     * @since    1.0.1
     * @access   private
     * @var      int    $isDev    Check is if this isite is in Development.
     */    
    public $isDev;      
    
    var $client; // Used for Google Client
    
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since      1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $isPro, $isDev = false ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->isPro = $isPro;

        // Include required files
        $this->includes();

        $this->isDev = $isDev;
    }


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function includes() {

		if( $this->debug ) error_log( 'Inside: CWS_WPPicasaPro::includes()' );
		
		if ( is_admin() ) $this->admin_includes();
		
		include_once( dirname(__FILE__) . '/../cws-fgp-functions.php' );				// TODO: split file out in admin and non-admin functions
		include_once( dirname(__FILE__) . '/../shortcodes/shortcode-init.php' );		// Init the shortcodes
        include_once( dirname(__FILE__) . '/../widgets/widget-init.php' );				// Widget classes		

        if( $this->isPro ==1 ) 
        {
            include_once( dirname(__FILE__) . '/../shortcodes/pro/shortcode-photoset.php' );  // Moved this here from shortcode-init.php, as was causing error, need to check!

            // TODO: change this into an include for Pro shortcodes...
            add_shortcode( 'cws_fgp_photoset', 'cws_fgp_shortcode_photoset' );  // display photoset by specified id
        }
	}

	public function admin_includes() {
	
		if( $this->debug ) error_log( 'Inside: CWS_WPPicasaPro::admin_includes()' );

		include_once( dirname(__FILE__) . '/../cws-fgp-functions.php' );				// TODO: split file out in admin and non-admin functions
	}    
    

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cws-flickr-gallery-pro-admin.css', array(), $this->version, 'all' );
	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cws-flickr-gallery-pro-admin.js', array( 'jquery' ), $this->version, false );
	}
    

    /**
     * Check if the plugin is a Pro version.
     *
     * @since    1.0.0
     */  
    private function get_Pro( $isPro ) {

        if( $isPro == 1 ){ return "Pro"; }
        return;
    }


    /**
     * Register the Top Level Menu Page for the admin area.
     *
     * @since    1.0.0
     */    
    public function add_menu_page() {

        $strIsPro = $this->get_Pro( $this->isPro );
        add_menu_page( 'Page Title', 'Flickr Viewer', 'manage_options', 'cws_fgp', array( $this, 'options_page') );
        add_submenu_page( 'cws_fgp', 'Flickr Viewer Settings', 'Settings', 'manage_options', 'cws_fgp', array( $this, 'options_page') );
    }    


    /**
     * Register the Options for the admin area.
     *
     * @since    1.0.0
     */    
    public function add_options_sc_page() {

        $strIsPro = $this->get_Pro( $this->isPro );
        add_submenu_page( 'cws_fgp', 'Flickr Viewer Shortcodes', 'Shortcodes', 'manage_options', 'cws_sc', array( $this, 'options_page_sc') );
    }
 

    /**
     * Register the Getting Started Options for the admin area.
     *
     * @since    1.0.1
     */    
    public function add_options_gs_page() {

        //$strIsPro = $this->get_Pro( $this->isPro );
        add_submenu_page( 'cws_fgp', 'Flickr Gallery Getting Started', 'Getting Started', 'manage_options', 'cws_gs', array( $this, 'options_page_gs') );
    }


    /**
     * Draw the Options page for the admin area. Getting started information.
     *
     * @since    2.3.0
     */
    public function options_page_gs() {
 ?>
        <div class="wrap">
        <?php screen_icon(); ?>
            <h2>Getting Started</h2>

            <!-- <div class="widget-liquid-left"> -->
            <div>

                <form action="options.php" method="post">

                <?php 
                // Step 1:  The user has not authenticated we give them a link to login    
                $plugin = new CWS_Flickr_Gallery_Pro();
                $plugin_admin = new CWS_Flickr_Gallery_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro() );

                $code = get_option( 'cws_fgp_code' );
                $ftoken = get_option( 'cws_fgp_token' );

                // set this to false in production!
                $flickr = new phpFlickr( $code['api_key'], $code['api_secret'], $this->isDev );
             ?>

                <form action="options.php" method="post">

                <?php 
                // Step 1: The user has not authenticated we give them a link to login    
                if( !get_option( 'cws_fgp_oauthToken' ) || !get_option( 'cws_fgp_oauthSecretToken' ) ) {

                    $url = admin_url( "options-general.php?page=cws_fgp" );
                    echo "<p>You appear to not be authenticated with Flickr.</p>";                        
                    echo "<a href=\"$url\">Click here to go to settings page.</a><br>";
                ?>
                    </form> 
                    <?php
                } else {
                ?>
                <style>
                span.sc-highlight{
                    background-color:#f7f7f7;padding:6px;border:1px solid #c2c2c2; border-radius:4px;
                }
                </style>
                <div style="width: 95%;" class="postbox-container">

                <?php
                    #----------------------------------------------------------------------------
                    # Photostream
                    #----------------------------------------------------------------------------
                ?>
                    <h2>How to display your Photostream</h2>
                    <p>Below is a shortcode example to display your Photostream in a grid format.</p>
                    <p><span class="sc-highlight">[cws_fgp_photostream theme=grid show_title=1 thumb_size=square_150]</span></p>

                    <?php
                    if( $plugin->isPro == 1 ) {
                        ?>
                        <h4>Display as Grid or Justified Image Gallery</h4>
                        <p>The display can be changed using options <span class="sc-highlight">theme=grid or theme=projig</span></p>
                    <?php
                    }    
                    ?>

                    <h4>Show / Hide the Title</h4>
                    <p>The album title can be hidden using options <span class="sc-highlight">show_title=0 or show_title=1</span></p>

                    <h4>Override Default Thumbnail Size</h4>
                    <p>The default thumbnail size can be overridden using options <span class="sc-highlight">thumb_size=square_150</span></p>
                    <p>Valid values are 

                    <span class="sc-highlight">square</span>, 
                    <span class="sc-highlight">square_75</span>, 
                    <span class="sc-highlight">square_150</span>, 
                    <span class="sc-highlight">thumbnail</span>, 
                    <span class="sc-highlight">small</span>, 
                    <span class="sc-highlight">small_240</span>, 
                    <span class="sc-highlight">small_320</span>, 
                    <span class="sc-highlight">medium</span>, 
                    <span class="sc-highlight">medium_500</span>, 
                    <span class="sc-highlight">medium_640</span>, 
                    <span class="sc-highlight">medium_800</span>
                    </p>
                    <br>

                <?php
                    #----------------------------------------------------------------------------
                    # Favourites
                    #----------------------------------------------------------------------------
                ?>
                    <h2>How to display your Favourites</h2>
                    <p>Below is a shortcode example to display your Favourites in a grid format.</p>
                    <?php
                    $strFavoutites = '[cws_fgp_favourites theme=grid thumb_size=square_150 show_title=1 ';
                    if( $plugin->isPro == 1 ) {
                        $strFavoutites .= 'enable_download=1]';
                    } else {
                        $strFavoutites .= ']';
                    }
                    ?>
                    <p><span class="sc-highlight"><?php echo $strFavoutites; ?></span></p>

                    <?php
                    if( $plugin->isPro == 1 ) {
                        ?>
                        <h4>Display as Grid or Justified Image Gallery</h4>
                        <p>The display can be changed using options <span class="sc-highlight">theme=grid or theme=projig</span></p>
                    <?php
                    }    
                    ?>

                    <h4>Show / Hide the Title</h4>
                    <p>The album title can be hidden using options <span class="sc-highlight">show_title=0 or show_title=1</span></p>

                    <h4>Override Default Thumbnail Size</h4>
                    <p>The default thumbnail size can be overridden using options <span class="sc-highlight">thumb_size=square_150</span></p>
                    <p>Valid values are 

                    <span class="sc-highlight">square</span>, 
                    <span class="sc-highlight">square_75</span>, 
                    <span class="sc-highlight">square_150</span>, 
                    <span class="sc-highlight">thumbnail</span>, 
                    <span class="sc-highlight">small</span>, 
                    <span class="sc-highlight">small_240</span>, 
                    <span class="sc-highlight">small_320</span>, 
                    <span class="sc-highlight">medium</span>, 
                    <span class="sc-highlight">medium_500</span>, 
                    <span class="sc-highlight">medium_640</span>, 
                    <span class="sc-highlight">medium_800</span>
                    </p>

                    <?php
                    if( $plugin->isPro == 1 ) {
                    ?>
                        <h4>Show / Hide Download Icon</h4>
                        <p>The download icon can be hidden using options <span class="sc-highlight">enable_download=0 or enable_download=1</span></p>
                    <?php
                    }
                    ?>
                    <br>

                <?php
                    #----------------------------------------------------------------------------
                    # Gallery
                    #----------------------------------------------------------------------------
                ?>
                    <h2>How to display your Gallery</h2>
                    <p>Below is a shortcode example to display your Gallery in a grid format.</p>
                    <p>Click here to <a href="admin.php?page=cws_sc">get shortcodes with your gallery ids</a>
                    <?php
                    $strFavoutites = '[cws_fgp_galleries gallery_id=72157678104065683 theme=grid thumb_size=square_150 show_title=1 ';
                    if( $plugin->isPro == 1 ) {
                        $strFavoutites .= 'enable_download=1]';
                    } else {
                        $strFavoutites .= ']';
                    }
                    ?>
                    <p><span class="sc-highlight"><?php echo $strFavoutites; ?></span></p>

                    <?php
                    if( $plugin->isPro == 1 ) {
                        ?>
                        <h4>Display as Grid or Justified Image Gallery</h4>
                        <p>The display can be changed using options <span class="sc-highlight">theme=grid or theme=projig</span></p>
                    <?php
                    }    
                    ?>

                    <h4>Show / Hide the Title</h4>
                    <p>The album title can be hidden using options <span class="sc-highlight">show_title=0 or show_title=1</span></p>

                    <h4>Override Default Thumbnail Size</h4>
                    <p>The default thumbnail size can be overridden using options <span class="sc-highlight">thumb_size=square_150</span></p>
                    <p>Valid values are 

                    <span class="sc-highlight">square</span>, 
                    <span class="sc-highlight">square_75</span>, 
                    <span class="sc-highlight">square_150</span>, 
                    <span class="sc-highlight">thumbnail</span>, 
                    <span class="sc-highlight">small</span>, 
                    <span class="sc-highlight">small_240</span>, 
                    <span class="sc-highlight">small_320</span>, 
                    <span class="sc-highlight">medium</span>, 
                    <span class="sc-highlight">medium_500</span>, 
                    <span class="sc-highlight">medium_640</span>, 
                    <span class="sc-highlight">medium_800</span>
                    </p>

                    <?php
                    if( $plugin->isPro == 1 ) {
                    ?>
                        <h4>Show / Hide Download Icon</h4>
                        <p>The download icon can be hidden using options <span class="sc-highlight">enable_download=0 or enable_download=1</span></p>
                    <?php
                    }
                    ?>

                <?php
                    #----------------------------------------------------------------------------
                    # Albums
                    #----------------------------------------------------------------------------
                ?>
<br>

                    <h2>How to display your Albums</h2>
                    <p>Below is a shortcode example to display your Albums in a grid format.</p>

                    <?php
                    $strAlbums = '[cws_fgp_albums theme=grid thumb_size=small show_title=1 album_thumb_size=50 num_album_results=3 num_image_results=9 ';
                    if( $plugin->isPro == 1 ) {
                        $strAlbums .= 'enable_download=1]';
                    } else {
                        $strAlbums .= ']';
                    }
                    ?>

                    <p><span class="sc-highlight"><?php echo $strAlbums; ?></span></p>

                    <h4>Album Cover Size</h4>
                    <p>Album Cover size is not adjustable, it will be in a future release.</p>

                    <?php
                    if( $plugin->isPro == 1 ) {
                        ?>
                        <h4>Display as Grid or Justified Image Gallery</h4>
                        <p>The display can be changed using options <span class="sc-highlight">theme=grid or theme=projig</span></p>
                    <?php
                    }    
                    ?>

                    <h4>Show / Hide the Title</h4>
                    <p>The album title can be hidden using options <span class="sc-highlight">show_title=0 or show_title=1</span></p>

                    <h4>Override Default Thumbnail Size</h4>
                    <p>The default thumbnail size can be overridden using options <span class="sc-highlight">thumb_size=square_150</span></p>
                    <p>Valid values are 

                    <span class="sc-highlight">square</span>, 
                    <span class="sc-highlight">square_75</span>, 
                    <span class="sc-highlight">square_150</span>, 
                    <span class="sc-highlight">thumbnail</span>, 
                    <span class="sc-highlight">small</span>, 
                    <span class="sc-highlight">small_240</span>, 
                    <span class="sc-highlight">small_320</span>, 
                    <span class="sc-highlight">medium</span>, 
                    <span class="sc-highlight">medium_500</span>, 
                    <span class="sc-highlight">medium_640</span>, 
                    <span class="sc-highlight">medium_800</span>
                    </p>

                    <?php
                    if( $plugin->isPro == 1 ) {
                    ?>
                        <h4>Show / Hide Download Icon</h4>
                        <p>The download icon can be hidden using options <span class="sc-highlight">enable_download=0 or enable_download=1</span></p>
                    <?php
                    }
                    ?>

                    <br>


                <?php
                    #----------------------------------------------------------------------------
                    # Specific Album by ID
                    #----------------------------------------------------------------------------
                ?>
                    <h2>How to display images in a Specific Album </h2>
                    <p>Below is a shortcode example to display images from a specific album in a grid format.</p>                    

                    <?php
                    if( $plugin->isPro == 0 ) {
                    ?>
                        <p><strong>This feature is only available in the Pro version</strong></p>
                    <?php
                    }
                    ?> 

                    <p>Click here to <a href="admin.php?page=cws_sc">get shortcodes with your album ids</a>

                    <?php
                    $strAlbumByID = '[cws_fgp_photoset id=72157660838162428 theme=grid thumb_size=square_150 ';
                    if( $plugin->isPro == 1 ) {
                        $strAlbumByID .= 'enable_download=1]';
                    } else {
                        $strAlbumByID .= ']';
                    }
                    ?>

                    <p><span class="sc-highlight"><?php echo $strAlbumByID; ?></span></p>

                    <h4>Show / Hide the Title</h4>
                    <p>The image title can be hidden using options <span class="sc-highlight">show_title=0 or show_title=1</span></p>

                    <h4>Override Default Thumbnail Size</h4>
                    <p>The default thumbnail size can be overridden using options <span class="sc-highlight">thumb_size=square_150</span></p>
                    <p>Valid values are 

                    <span class="sc-highlight">square</span>, 
                    <span class="sc-highlight">square_75</span>, 
                    <span class="sc-highlight">square_150</span>, 
                    <span class="sc-highlight">thumbnail</span>, 
                    <span class="sc-highlight">small</span>, 
                    <span class="sc-highlight">small_240</span>, 
                    <span class="sc-highlight">small_320</span>, 
                    <span class="sc-highlight">medium</span>, 
                    <span class="sc-highlight">medium_500</span>, 
                    <span class="sc-highlight">medium_640</span>, 
                    <span class="sc-highlight">medium_800</span>
                    </p>

                    <?php
                    if( $plugin->isPro == 1 ) {
                    ?>
                        <h4>Show / Hide Download Icon</h4>
                        <p>The download icon can be hidden using options <span class="sc-highlight">enable_download=0 or enable_download=1</span></p>
                    <?php
                    }
                    ?>

                    <?php
                    if( $plugin->isPro == 1 ) {
                        ?>
                        <h4>Display as Grid or Justified Image Gallery</h4>
                        <p>The display can be changed using options <span class="sc-highlight">theme=grid or theme=projig</span></p>
                    <?php
                    }    
                    ?>
                        <div class="metabox-holder">
              
                            <div class="postboxxxx" id="settingsxxx">
                                <?php
                                // Only display shortcode snippets to Pro users...
                                if( $plugin->isPro == 1 ) {

                                    // Grab options stored in db
                                    $options = get_option( 'cws_fgp_options' );
                                    ?>



                                    <!-- put some PRO helpful info here -->

                            </div> <!-- / . postbox -->

                        </div> <!-- / meta holder -->
                    </div> <!-- / .postbox-container -->
            <?php   } else { ?>
            <?php       // Display upgrade content if not Pro
                        // echo $plugin_admin->cws_gpp_upgrade_content(); 
                    }

               } 

                ?>

            </div><!-- / left -->
        </div>
        <?php
        } // end function options_page_gs()




    
    /**
     * Draw the Options page for the admin area. This contains simple shortcode snippets for *** PRO ONLY ***
     *
     * @since    1.0.0
     */
    public function options_page_sc() {
 ?>
        <div class="wrap">
        <?php screen_icon(); ?>
            <h2>Flickr Gallery <?php echo $this->get_Pro( $this->isPro );?> Settings</h2>
            <!-- <div class="widget-liquid-left"> -->
            <div>
            <?php 

                $plugin = new CWS_Flickr_Gallery_Pro();
                $plugin_admin = new CWS_Flickr_Gallery_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro() );

                $code = get_option( 'cws_fgp_code' );
                $oauth_token = get_option( 'cws_fgp_oauthToken' );
                $oauth_secret = get_option( 'cws_fgp_oauthSecretToken' );

                // set this to false in production!
                $flickr = new phpFlickr( $code['api_key'], $code['api_secret'], $this->isDev );
                $tbPhpFlickr = new tbPhpFlickr( $myCache = new CacheManager(), false  );
                //$tbPhpFlickr = new tbPhpFlickr( null, false  );

    $options['enable_cache'] = isset($options['enable_cache']) ? $options['enable_cache'] : false;

    // set File system cache, if option is set in settings page and is Pro
    if( $options['enable_cache'] == true && $plugin->isPro == 1 && $myCache == true ) 
    {
        $myCache->enableCache( "fs", dirname(__FILE__) . "/../cache" );
    }
/*
    echo '<pre>';
    print_r($tbPhpFlickr);
    echo '</pre>';
*/

                //$flickr->setToken( $ftoken );
                //$token = $flickr->auth_checkToken();
                $flickr->setOauthToken($oauth_token, $oauth_secret);
             ?>

                <form action="options.php" method="post">

                <?php 
                // Step 1: The user has not authenticated we give them a link to login    
                if( !get_option( 'cws_fgp_oauthToken' ) || !get_option( 'cws_fgp_oauthSecretToken' ) ) {

                    $url = admin_url( "options-general.php?page=cws_fgp" );
                    echo "<p>You appear to not be authenticated with Flickr.</p>";                        
                    echo "<a href=\"$url\">Click here to go to settings page.</a><br>";
                ?>
                    </form> 
                    <?php
                } else {
                    /*
                        echo "<pre>" . print_r($token, true) . "</pre>";
                        echo "ftoken: $ftoken<br>";
                        echo "<pre>" . print_r( $flickr, true ) . "</pre>";
                    */
                ?>
                <div style="width: 95%;" class="postbox-container">
                    <h3>Shortcode Usage Examples</h3>

                        <div class="metabox-holder">

                            <!-- Photostream -->
                            <div class="postbox" id="settings">
                                <p style="padding:10px;"><strong>Display your Photostream</strong></p>

                                    <table class="wp-list-table widefat fixed posts">

                                        <thead>
                                         <tr valign="top">
                                              <th scope="row" width="100">Theme</th>
                                              <th scope="row">Example shortcode</th>
                                         </tr>
                                        </thead>

                                        <tbody data-wp-lists="list:post" id="the-list">
                                                <tr>
                                                    <td class="shortcode column-view">Grid</td>
                                                    <td class="shortcode column-shortcode">
                                                        <input size="80" type="text" class="shortcode-in-list-table wp-ui-text-highlight code" value="[cws_fgp_photostream theme=grid thumb_size=square_150  show_title=0]" readonly="readonly" onfocus="this.select();">                                     
                                                    </td>
                                                </tr>

                                                <?php
                                                if( $plugin->isPro == 1 ) {
                                                    ?>
                                                <tr>
                                                    <td class="shortcode column-view">Justified Image Grid</td>                                                    
                                                    <td class="shortcode column-shortcode">
                                                        <input size="80" type="text" class="shortcode-in-list-table wp-ui-text-highlight code" value="[cws_fgp_photostream theme=projig thumb_size=square_150  show_title=0]" readonly="readonly" onfocus="this.select();">                                     
                                                    </td>
                                                </tr><?php
                                                }
                                                ?>

                                            </tr>

                                         </tbody>                                            
                                    </table> 
                            </div>

                            <!-- Favourites -->
                            <div class="postbox" id="settings">
                                <p style="padding:10px;"><strong>Display your Favourites</strong></p>

                                    <table class="wp-list-table widefat fixed posts">

                                        <thead>
                                         <tr valign="top">
                                              <th scope="row" width="100">Theme</th>
                                              <th scope="row">Example shortcode</th>
                                         </tr>
                                        </thead>

                                        <tbody data-wp-lists="list:post" id="the-list">
                                            <tr>
                                                <td class="shortcode column-view">Grid</td>
                                                <td class="shortcode column-shortcode">
                                                    <input size="80" type="text" class="shortcode-in-list-table wp-ui-text-highlight code" value="[cws_fgp_favourites theme=grid thumb_size=square_150  show_title=0]" readonly="readonly" onfocus="this.select();">                                     
                                                </td>
                                            </tr>

                                                <?php
                                                if( $plugin->isPro == 1 ) {
                                                    ?>
                                                <tr>
                                                    <td class="shortcode column-view">Justified Image Grid</td>                                                    
                                                    <td class="shortcode column-shortcode">
                                                        <input size="80" type="text" class="shortcode-in-list-table wp-ui-text-highlight code" value="[cws_fgp_photostream theme=projig thumb_size=square_150  show_title=0]" readonly="readonly" onfocus="this.select();">                                     
                                                    </td>
                                                </tr><?php
                                                }
                                                ?>

                                         </tbody>                                            
                                    </table> 
                            </div>                            

                            <!-- Galleries -->

                            <div class="postbox" id="settings">

                                    <p style="padding:10px;"><strong>Display your Galleries</strong></p>
                                    <?php
                                        // Get list of users galleries from Flickr
                                        $user = get_option( 'cws_fgp_user' );
                                        $photos = $tbPhpFlickr->galleries_getList ( $user );
                                    ?>

                                    <table class="wp-list-table widefat fixed posts">
                                        <thead>
                                         <tr valign="top">
                                              <th scope="row" width="100">Gallery Name</th>
                                              <!--<th scope="row" width="70">Published</th> -->
                                              <th scope="row" width="50">Num Photos </th>
                                              <th scope="row" width="100">Gallery ID </th>
                                              <th scope="row" width="100">Theme</th>
                                              <th scope="row">Example shortcode</th>
                                         </tr>
                                        </thead>
                                    <?php
                                        // loop over the albums
                                        foreach ( $photos['galleries']['gallery'] as $gallery ) {

                                            // Get the gallery id
                                            $gid = explode('-', $gallery['id'] );

                                            // get the data we need  
                                            ?>
                                                <tbody data-wp-lists="list:post" id="the-list">

                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong><?php echo $gallery['title'];?></strong>
                                                        </td>
                                                        <td class="shortcode column-deescription"><?php echo $gallery['count_photos'];?></td>                                                        
                                                        <td class="shortcode column-deescription"><?php echo $gid[1];?></td>
                                                        <td class="shortcode column-theme">Grid</td>
                                                        <td class="shortcode column-shortcode">
                                                            <input size="80" type="text" class="shortcode-in-list-table wp-ui-text-highlight code" value="[cws_fgp_galleries gallery_id=<?php echo $gid[1];?> theme=grid thumb_size=square_150]" readonly="readonly" onfocus="this.select();">                                     
                                                    </tr>


                                                <?php
                                                if( $plugin->isPro == 1 ) {
                                                    ?>
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong><?php echo $gallery['title'];?></strong>
                                                        </td>
                                                        <td class="shortcode column-deescription"><?php echo $gallery['count_photos'];?></td>                                                        
                                                        <td class="shortcode column-deescription"><?php echo $gid[1];?></td>
                                                        <td class="shortcode column-theme">Justified Image Grid</td>
                                                        <td class="shortcode column-shortcode">
                                                            <input size="80" type="text" class="shortcode-in-list-table wp-ui-text-highlight code" value="[cws_fgp_galleries gallery_id=<?php echo $gid[1];?> theme=projig thumb_size=square_150]" readonly="readonly" onfocus="this.select();">                                     
                                                    </tr><?php
                                                }
                                                ?>              

                                                 </tbody>
                                            <?php
                                        } // end foreach
                                    ?>
                                            <foot>
                                             <tr valign="top">
                                                  <th scope="row">Gallery Name </th>
                                                  <!-- <th scope="row">Published</th> -->   
                                                  <th scope="row">Num Photos </th>                            
                                                  <th scope="row">Gallery ID </th>
                                                  <th scope="row" width="50">Theme</th>
                                                  <th scope="row">Example shortcode </th>
                                             </tr>
                                            </foot>                                  
                                        </table>  

                            </div>


              
                            <div class="postbox" id="settings">

                                <p style="padding:10px;"><strong>Display your Photosets</strong></p>

                                <?php
                                // $plugin = new CWS_Flickr_Gallery_Pro( $plugin_name, $version, $isPro );
                                $plugin = new CWS_Flickr_Gallery_Pro();
                                $plugin_admin = new CWS_Flickr_Gallery_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro() );

                                // Look up username of authentcated user and store in db               
                                $base_url   = $flickr->urls_getUserPhotos();
                                $user = $flickr->urls_lookupUser( $base_url );
                                update_option( 'cws_fgp_user', $user['id'] );

                                $photosets = $tbPhpFlickr->photosets_getList( 0,0, get_option( 'cws_fgp_user' ) );
                                $options['enable_cache'] = isset($options['enable_cache']) ? $options['enable_cache'] : false;

                                // set File system cache, if option is set in settings page and is Pro
                                //if( $options['enable_cache'] == true && $plugin->isPro == 1 && $myCache == true ) 
                                {
                                    $myCache->enableCache( "fs", dirname(__FILE__) . "/../cache" );
                                }

                                /*echo '<pre>';
                                print_r($photosets);
                                echo '</pre>';
                                */
                                
                                // Only display shortcode snippets to Pro users...
                                //if( $plugin->isPro == 1 ) {
                                if( 1 == 1 ) {

                                    ?>
                                        <table class="wp-list-table widefat fixed posts">
                                            <thead>
                                             <tr valign="top">
                                                  <th scope="row" width="100">Album Name </th>
                                                  <!--<th scope="row" width="70">Published</th> -->
                                                  <th scope="row" width="50">Num Photos </th>
                                                  <th scope="row" width="100">Album ID </th>
                                                  <th scope="row" width="100">Theme </th>
                                                  <th scope="row">Example shortcode</th>
                                             </tr>
                                            </thead>
                                    <?php
                                        // loop over the albums
                                        //foreach( $xml->entry as $feed ) {
                                        foreach ( $photosets['photoset'] as $album ) {

                                            // get the data we need  
                                            ?>
                                                <tbody data-wp-lists="list:post" id="the-list">

                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong><?php echo $album['title'];?></strong>
                                                        </td>
                                                        <td class="shortcode column-deescription"><?php echo $album['photos'];?></td>                                                        
                                                        <td class="shortcode column-deescription"><?php echo $album['id'];?></td>
                                                        <td class="shortcode column-theme">Grid</td>
                                                        <td class="shortcode column-shortcode">
                                                            <input size="80" type="text" class="shortcode-in-list-table wp-ui-text-highlight code" value="[cws_fgp_albums id=<?php echo $album['id'];?> theme=grid thumb_size=square_150]" readonly="readonly" onfocus="this.select();">                                     
                                                    </tr>

                                                <?php
                                                if( $plugin->isPro == 1 ) {
                                                    ?>
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong><?php echo $album['title'];?></strong>
                                                        </td>
                                                        <td class="shortcode column-deescription"><?php echo $album['photos'];?></td>                                                        
                                                        <td class="shortcode column-deescription"><?php echo $album['id'];?></td>
                                                        <td class="shortcode column-theme">Justified Image Grid</td>
                                                        <td class="shortcode column-shortcode">
                                                            <input size="80" type="text" class="shortcode-in-list-table wp-ui-text-highlight code" value="[cws_fgp_albums id=<?php echo $album['id'];?> theme=projig thumb_size=square_150]" readonly="readonly" onfocus="this.select();">                                     
                                                    </tr><?php
                                                }
                                                ?>  


                                                 </tbody>
                                            <?php
                                        } // end foreach
                                    ?>
                                            <foot>
                                             <tr valign="top">
                                                  <th scope="row">Album Name </th>
                                                  <!-- <th scope="row">Published</th> -->   
                                                  <th scope="row">Num Photos </th>                            
                                                  <th scope="row">Album ID </th>
                                                  <th scope="row" width="100">Theme </th>                                                  
                                                  <th scope="row">Example shortcode </th>
                                             </tr>
                                            </foot>                                  
                                        </table>






                                    <?php
                                    //} // end else
                                    ?>
                            </div> <!-- / . postbox -->
                            
                        </div> <!-- / meta holder -->
                        
                    </div> <!-- / .postbox-container -->
                    
            <?php   } else { ?>
            <?php       // Display upgrade content if not Pro
                        echo $plugin_admin->cws_fgp_upgrade_content(); 
                    }

               } 

                ?>

            </div><!-- / left -->
        </div>
        <?php
        } // end function options_page_sc()



	/**
	 * Draw the Options page for the admin area.
	 *
	 * @since    1.0.0
	 */
    public function options_page() {

        if( $this->deauthorizeFlickrAccount() ) {
            // TODO: finsish this delete_option unset()
            delete_option( 'cws_fgp_reset' );
            delete_option( 'cws_fgp_code' );
            delete_option( 'cws_fgp_oauthToken' );
            delete_option( 'cws_fgp_oauthSecretToken' );
            delete_option( 'cws_fgp_user' );
        } ?>

        <div class="wrap">
        <?php screen_icon(); ?>
            <h2>Flickr Gallery <?php echo $this->get_Pro( $this->isPro );?> Settings</h2>

            <div class="widget-liquid-left">

                <form action="options.php" method="post">           
<?php

                #----------------------------------------------------------------------------
                # Start of authorization process
                #----------------------------------------------------------------------------
                // Check if we have api key and secret stored in db
                // if we don't then display form to collect them from user
                $code = get_option( 'cws_fgp_code' );

                if( !isset( $code['api_key'] ) && !isset( $code['api_secret'] ) ) {
                    // settings_fields( $option_group )
                    // Output nonce, action, and option_page fields for a settings page. Please note that this function must be called inside of the form tag for the options page.
                    // $option_group - A settings group name. This should match the group name used in register_setting(). 
                    settings_fields( 'cws_fgp_code' );

                    // do_settings_sections( $page );
                    // Prints out all settings sections added to a particular settings page.
                    // The slug name of the page whose settings sections you want to output. This should match the page name used in add_settings_section().
                    do_settings_sections( 'cws_fgp' ); 
?>
                    <input name="Submit" type="submit" value="Save Changes" />  

                </form> 
            <?php
                // If we have the api key and secret
                // check we have a oauth_token & oauth_verifier
                // if not get one form flickr
                } elseif ( isset( $code['api_key'] ) && isset( $code['api_secret'] )  ) {

                    // IK added for oauth update
                    // this is returned after user allows access to flickr... 
                    if( isset( $_GET['oauth_token'] ) && isset( $_GET['oauth_verifier'] ) )
                    {
                        $code = get_option( 'cws_fgp_code' );
                        // save them to database
                        $flickr = new phpFlickr( $code['api_key'], $code['api_secret'], $this->isDev  );

                        $flickr->getAccessToken(); 
                        $OauthToken = $flickr->getOauthToken(); 
                        $OauthSecretToken = $flickr->getOauthSecretToken(); 

                        echo ""."\$OauthToken=".$OauthToken." \$OauthSecretToken".$OauthSecretToken."";

                        // store it in db
                        update_option( 'cws_fgp_oauthToken', $OauthToken );
                        update_option( 'cws_fgp_oauthSecretToken', $OauthSecretToken );

                        // Look up username of authentcated user and store in db               
                        $base_url   = $flickr->urls_getUserPhotos();
                        $user = $flickr->urls_lookupUser( $base_url );
                        update_option( 'cws_fgp_user', $user['id'] );

                        $url = admin_url( "options-general.php?page=".$_GET["page"] );                        
                        wp_redirect( "$url" );
                    } 
                    elseif ( !get_option( 'cws_fgp_oauthToken' ) || !get_option( 'cws_fgp_oauthSecretToken' ) ) 
                    {
                        echo "need to run authorize sequence...<br>";
                        echo "seems we have api_key and api_secret<br>";


                        if( isset( $_GET['oauth_token'] ) && isset( $_GET['oauth_verifier'] ) )
                        {
                            $code = get_option( 'cws_fgp_code' );
                            // save them to database
                            $flickr = new phpFlickr( $code['api_key'], $code['api_secret'], $this->isDev  );

                            $flickr->getAccessToken(); 
                            $OauthToken = $flickr->getOauthToken(); 
                            $OauthSecretToken = $flickr->getOauthSecretToken(); 

                            echo ""."\$OauthToken=".$OauthToken." \$OauthSecretToken".$OauthSecretToken."";

                            // store it in db
                            update_option( 'cws_fgp_oauthToken', $OauthToken );
                            update_option( 'cws_fgp_oauthSecretToken', $OauthSecretToken );

                            $url = admin_url( "options-general.php?page=".$_GET["page"] );                        
                            wp_redirect( "$url" );
                        }


                        settings_fields( 'cws_fgp_code' );

                        // do_settings_sections( $page );
                        // Prints out all settings sections added to a particular settings page.
                        // The slug name of the page whose settings sections you want to output. This should match the page name used in add_settings_section().
                        do_settings_sections( 'cws_fgp' ); 
?>
<input name="Submit" type="submit" value="Save Changes?" /> 

</form><?php 
                    }
                    ?>

                    <?php

                    // Do we have a cws_fgp_oauthToken OR cws_fgp_oauthSecretTokenstored?
                    if( !get_option( 'cws_fgp_oauthToken' ) || !get_option( 'cws_fgp_oauthSecretToken' ) ) {

                        $flickr = new phpFlickr( $code['api_key'], $code['api_secret'], $this->isDev  );

                        $protocol = 'http://';
                        if ( is_ssl() ) { $protocol = 'https://'; }

                        $callback = $protocol . $_SERVER['SERVER_NAME']  . dirname($_SERVER['PHP_SELF']) . "/admin.php?page=cws_fgp";

                        $flickr->getRequestToken( $callback );  

                    } 
                  ?>
 <form action="options.php" method="post">  
                  <?php  
                    //else
                    // If we are authenticated
                    if( get_option( 'cws_fgp_oauthToken' ) && get_option( 'cws_fgp_oauthSecretToken' ) )
                    {
                    /**
                     * User is authenticated so display plugin config settings
                     * 
                     */

                    // Get Access Token
                    //$token = $this->getAccessToken();
                    
                    settings_fields( 'cws_fgp_options' );
                    do_settings_sections( 'cws_fgp_defaults' );
                    ?>
                    <input name="Submit" type="submit" value="Save Changes" />  
                </form> 

                <!--  Reset form -->
                <form action="options.php" method="post">

            <?php   settings_fields( 'cws_fgp_reset' );
                    do_settings_sections( 'cws_fgp_reset' );  
            ?>
                    <input name="Submit" type="submit" value="Deauthorise" onclick="if(!this.form.reset.checked){alert('You must click the checkbox to confirm you want to deauthorize current Flickr account.');return false}" />

                </form>                             
            <?php                      
                }}
            ?>

            </div><!-- / left -->
                <?php $this->cws_fgp_meta_box_feedback(); ?>

            <?php
                if( !$this->isPro == 1 ) {
                    // Only call for the upgrade meta box if this is not a Pro install
                    $this->cws_fgp_meta_box_pro(); 
                }
            ?>

        </div>
        <?php
    }
    

    // Display a feedback links
    public function cws_fgp_meta_box_feedback() {
    ?>

        <div class="widget-liquid-right">
            <div id="widgets-right">    
                <div style="width:20%;" class="postbox-container side">
                    <div class="metabox-holder">
                        <div class="postbox" id="feedback">
                            <!-- <h3><span>Please rate the plugin!</span></h3> -->
                            <h3><span>Found a bug? Let me know!</span></h3>
                            <div class="inside">                            
                                <!-- <p>If you have found this useful please leave us a <a href="https://wordpress.org/support/plugin/flickr-viewer/reviews/">good rating</a></p>
                                <p>&raquo; Share it with your friends <a href="<?php echo "http://twitter.com/share?url=http://bit.ly/q4nqNA&text=Check out this awesome WordPress Plugin I'm using - Flickr Gallery for WordPress" ?>">Tweet It</a></p>-->
                                <p>If you have found a bug please email me <a href="mailto:info@cheshirewebsolutions.com?subject=Feedback%20Flickr%20Gallery%20Viewer">info@cheshirewebsolutions.com</a></p>                               
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>  
        
    <?php
        
    }


    // Display a Flickr Pro Promo Box
    function cws_fgp_meta_box_pro() {
    ?>
    <div class="widget-liquid-right">
        <div id="widgets-right">
            <!-- <div style="width:20%;" class="postbox-container side"> -->
            <div class="postbox-container side">
                <div class="metabox-holder">
                    <div class="postbox" id="donate">
                        <?php echo $this->cws_fgp_upgrade_content(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div><?php   
    }


    // seperate upgrade content from markup so can use content in more places, like shortcode snippets page and pro shortcodes in the frontend
    function cws_fgp_upgrade_content() {

        $strOutput = "<h3><span>Get Flickr Gallery Pro!</span></h3>
                    <div class=\"inside\">
                        <p>Grab yourself the <a href=\"http://www.cheshirewebsolutions.com/?utm_source=cws_fgp_config&utm_medium=text-link&utm_content=meta_box_pro&utm_campaign=cws_fgp_plugin\">Pro</a> version of the plugin.                        
                        <a href=\"http://www.cheshirewebsolutions.com/?utm_source=wp_fgp_viewer&utm_medium=wp_plugin&utm_content=meta_box_download_it_here&utm_campaign=plugin_upgrade\">Download it here</a> <span><strong>GET 20% OFF!</strong> – use discount code <strong>WPMEGA20</strong> on checkout</span></p>
                        <h3>Reasons to UPGRADE!</h3>
                        <ol>
                            <li>Show your <strong>Private images</strong>.</li>
                            <li>Enhanced lightbox, touch sensitive – works great on tables/mobile devices</li>
                            <li>It’s much faster! We cache the Flickr Feed</li>
                            <li>Included Advanced Layout Options</li>
                            <!-- <li>Included Polaroid Stack Grid Shortcode!</li>
                            <li>Included Photo Booth Strip Shortcode!</li> -->
                            <li>Customisable Lightbox Dimensions! have it as big as you like</li>
                            <li>Display images in a specific album! just supply the album id</li>
                            <li>Powered by link has been removed!</li>
                            <li>Included Priority Email Support!</li>
                            <li>Included Download Original Image link</li>
                            <li>More layouts coming soon</li>
                        </ol>

                    </div>";

        return $strOutput;
    }


	/**
	 * Register Settings, Settings Section and Settings Fileds.
     * 
     * @link    https://codex.wordpress.org/Function_Reference/register_setting
     * @link    https://codex.wordpress.org/Function_Reference/add_settings_section
     * @link    https://codex.wordpress.org/Function_Reference/add_settings_field
	 *
	 * @since    1.0.0
	 */    
    public function register_plugin_settings() {
        // register_setting( $option_group, $option_name, $sanitize_callback ).
        // $option_group - A settings group name. Must exist prior to the register_setting call. This must match the group name in settings_fields().
        // $option_name - The name of an option to sanitize and save.
        // $sanitize_callback - A callback function that sanitizes the option's value.
        register_setting( 'cws_fgp_code', 'cws_fgp_code', array( $this, 'validate_options' ) );
        register_setting( 'cws_fgp_options', 'cws_fgp_options', array( $this, 'validate_main_options' ) );

        register_setting( 'cws_fgp_reset', 'cws_fgp_reset', array( $this, 'validate_reset_options' ) );

        // add_settings_section( $id, $title, $callback, $page )
        // $id - String for use in the 'id' attribute of tags
        // $title - Title of the section
        // $callback - Function that fills the section with the desired content. The function should echo its output.
        // $page - The menu page on which to display this section. Should match $menu_slug in add_options_page();
        add_settings_section( 'cws_fgp_add_code', 'Authenticate with Flickr', array( $this, 'section_text' ), 'cws_fgp' );
        add_settings_section( 'cws_fgp_add_options', 'Default Settings', array( $this, 'section_main_text' ), 'cws_fgp_defaults' );

        add_settings_section( 'cws_fgp_add_reset', 'Deauthorise Plugin from your Flickr Account', array( $this, 'section_reset_text' ), 'cws_fgp_reset' );

        // add_settings_field( $id, $title, $callback, $page, $section, $args );
        // $id - String for use in the 'id' attribute of tags
        // $title - Title of the field
        // $callback - Function that fills the field with the desired inputs as part of the larger form. Passed a single argument, 
        // the $args array. Name and id of the input should match the $id given to this function. The function should echo its output.
        // $page - The menu page on which to display this field. Should match $menu_slug in add_options_page();
        // $section - (optional) The section of the settings page in which to show the box. A section added with add_settings_section() [optional]
        // $args - (optional) Additional arguments that are passed to the $callback function
        // add_settings_field( 'cws_myplugin_api_key', 'Enter API Key here', array( $this, 'setting_input' ), 'cws_fgp', 'cws_fgp_add_code' );
        add_settings_field( 'cws_fgp_api_key', 'Enter API Key here', array( $this, 'api_key' ), 'cws_fgp', 'cws_fgp_add_code' );
        add_settings_field( 'cws_fgp_api_secret', 'Enter API Secret here', array( $this, 'api_secret' ), 'cws_fgp', 'cws_fgp_add_code' );

        // Add default option field - Thumbnail Size 
        add_settings_field( 'cws_fgp_thumbnail_size', 'Thumbnail Size (px)', array( $this, 'options_thumbnail_size' ), 'cws_fgp_defaults', 'cws_fgp_add_options' );

        // Add default option field - Album Thumbnail Size 
        //add_settings_field( 'cws_fgp_album_thumbnail_size', 'Album Thumbnail Size (px)', array( $this, 'options_album_thumbnail_size' ), 'cws_fgp_defaults', 'cws_fgp_add_options' );

        if( $this->isPro ) 
        {
            // PRO ONLY
            // Add default option field - Lighbox Image Size 
            add_settings_field( 'cws_fgp_lightbox_image_size', 'Lightbox Image Size (px)', array( $this, 'options_lightbox_image_size' ), 'cws_fgp_defaults', 'cws_fgp_add_options' );        
        
            // Add default option checkbox - Show private albums checkbox
            add_settings_field( 'cws_fgp_show_privacy_filter', 'Privacy Filter', array( $this, 'options_show_privacy_filter' ), 'cws_fgp_defaults', 'cws_fgp_add_options' );        
            
            // Add default option checkbox - Enable Cache
            add_settings_field( 'cws_fgp_enable_cache', 'Enable Cache', array( $this, 'options_enable_cache' ), 'cws_fgp_defaults', 'cws_fgp_add_options' );
            
            // Add default option checkbox - Expose Original file
            add_settings_field( 'cws_fgp_enable_download', 'Download Original Image Link', array( $this, 'options_enable_download' ), 'cws_fgp_defaults', 'cws_fgp_add_options' );
        }

        // Add default option field - Number of image results
        add_settings_field( 'cws_fgp_num_image_results', 'Number of images per page', array( $this, 'options_num_image_results' ), 'cws_fgp_defaults', 'cws_fgp_add_options' );

        // Add default option field - Number of album results
        // add_settings_field( 'cws_fgp_num_album_results', 'Number of albums per page', array( $this, 'options_num_album_results' ), 'cws_fgp_defaults', 'cws_fgp_add_options' );

        // Add default option checkbox - Show album title
        //add_settings_field( 'cws_fgp_show_album_title', 'Show Album Title', array( $this, 'options_show_album_title' ), 'cws_fgp_defaults', 'cws_fgp_add_options' );   

        // Add default option checkbox - Show image title
        ///add_settings_field( 'cws_fgp_show_image_title', 'Show Image Title', array( $this, 'options_show_image_title' ), 'cws_fgp_defaults', 'cws_fgp_add_options' ); 

        // Add default option checkbox - Show album details
        // add_settings_field( 'cws_fgp_show_album_details', 'Show Album Details', array( $this, 'options_show_album_details' ), 'cws_fgp_defaults', 'cws_fgp_add_options' );   

        // Add default option checkbox - Show image details
        ///add_settings_field( 'cws_fgp_show_image_details', 'Show Image Details', array( $this, 'options_show_image_details' ), 'cws_fgp_defaults', 'cws_fgp_add_options' ); 
        
        // Add reset option
        add_settings_field( 'cws_fgp_reset', 'Click here to confirm you want to deauthorise plugin from your Flickr account', array( $this, 'options_reset' ), 'cws_fgp_reset', 'cws_fgp_add_reset' );   
    }
    
    
	/**
	 * Draw the Section Header for the admin area.
	 *
	 * @since    1.0.0
	 */
    function section_text() {
        echo "You need to authorize this plugin with Flickr to enable the display Images from Flickr.<br>";
        echo "Simply do the following steps<br>";
        echo "<ul>";
            echo "<li>Generate an API Key with Flickr: <a href=\"https://www.flickr.com/services/api/key.gne\" target=\"_blank\">Open</a></li>";
            echo "<li>Setup authentication information : <a href=\"https://www.flickr.com/services/api/registered_keys.gne\" target=\"_blank\">Open</a>";     
            echo "<ol>";
                echo "<li>Note down your API Key and Secret.</li>";
                echo "<li>Edit 'Edit Authentication Flow' and selct 'Web Application' from App Type</li>";
                echo "<li>Set the callback url to:<br /><strong>http://" . $_SERVER['SERVER_NAME']  . dirname($_SERVER['PHP_SELF']) . "/admin.php?page=cws_fgp</strong></li>";
            echo "</ol></li>";
        echo "Fill out the form below. You will be redirected to Flickr for authentication.";
        echo "</ul>";
	  }
    

    function section_main_text() {
        
    }

    //
    function section_reset_text() {
        
    }   

    function section() {

    } 
    
    
	/**
	 * Get the Access Token stored in db.
	 *
	 * @since    1.0.0
	 */    
    public function getAccessToken() {
        $token = get_option( 'cws_fgp_access_token' );

        return $token;
    }
    
  
    /**
     * Get the Reset option stored in db.
     *
     * @since    1.0.0
     */  
    public function deauthorizeFlickrAccount() {
        // get options from db

        if( get_option( 'cws_fgp_reset' ) ){
            return true;
        } 

        return false;
    }




    public function isAuthenticated(){
        
        // get options from db
        $code = get_option( 'cws_fgp_code' );
        $token = get_option( 'cws_fgp_access_token' );
        
        if ( !isset( $token['access_token'] ) ) {
            // get oauth2 code
            //$this->getOAuthToken();
        }
        else{
            // check if it needs refreshing
            $now = date("U");
            
            // get cws_gpp_token_expires
            $token_expires = get_option( 'cws_gpp_token_expires' );

            // check if $now is greater than cws_gpp_token_expires
            if ( $now > $token_expires ) {
                $this->refreshToken(); 
                return;
            }   
            
            return true;
        }

        return false;
    }





    /**
     * Get current url.
     *
     * @since    2.3.0
     */ 
    function getUrl() {
        $url  = ( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
        $url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
        $url .= $_SERVER["REQUEST_URI"];
        return $url;
    }

    
    /*
     *
     * Pagination Helper
     *
     * $num_pages, int
     * $current_page, int
     * $album_id
     *
     * return string
     */     
    public function get_pagination( $total_num_albums, $num_image_results, $cws_page, $album_id=NULL ) {
       
        if( $this->debug ) error_log( 'Inside: CWS_Flickr_Gallery_Pro_Admin::get_pagination()' ); 

        // Calculate how many pages we need, total number of albums / number of images to display per page as set in settings of shortcode
        if( $num_image_results > 0 ){
            $num_pages  = ceil( $total_num_albums / $num_image_results );

            // If ony need one page then do not display pagination
            if ( $num_pages <= 1 ){
                return;
            }


        if( ! isset( $cws_page ) || $cws_page < 1 ){ $cws_page = 1; } // TODO: Do we need this check?
        
        // Create page links
        $html[] = "<div class=\"cws-pagination\"><ul class=\"page-nav\">\n";
        
        $previous = $cws_page - 1;
        $next     = $cws_page + 1;
        
        // Previous link
        if( $previous > 0 ) {

            // if have album id, i.e. display this on the results page
            if( $album_id ) {
                $html[] = "<li><a href=\"?cws_page=$previous&cws_album_id=$album_id\" id='prev_page'>Previous</a></li>";
            } else {
                $html[] = "<li><a href=\"?cws_page=$previous\" id='prev_page'>Previous</a></li>";                
            }
        }
        
        for( $i=1; $i <= $num_pages; $i++ ) {
        
            $class = "";

            // Add class to current page
            if( $i == $cws_page) {
                $class = " class='selected'";
            }

            $html[] = "<li".$class.">";

            if( $album_id ) {
                $html[] = "<a href=\"?cws_page=$i&cws_album_id=$album_id\" id='page_".$i."'>".$i."</a></li>\n";
            } else {
                $html[] = "<a href=\"?cws_page=$i\" id='page_".$i."'>".$i."</a></li>\n";
            }
        }
        
        // Next link
        if( $next <= $num_pages ) {

            // if have album id
            if( $album_id ){
                $html[] = "<li><a href=\"?cws_page=$next&cws_album_id=$album_id\" id='next_page'>Next</a></li>";
            } else {
                $html[] = "<li><a href=\"?cws_page=$next\" id='next_page'>Next</a></li>";
            }
        }
        
        // Display Powered by link if not Pro
        if( !$this->isPro == 1 ){
            $html[] = "</ul><span>Powered by: <a href=\"http://www.cheshirewebsolutions.com/\">Flickr Gallery for WordPress Plugin</a></span></div>\n";
        } else {
            $html[] = "</ul></div>\n";
        }
        return implode( "\n", $html );
        }
        return;
    }


    /**
     * Display and fill the form field. Flickr API Key
     *
     * @since    1.0.0
     */
    function api_key() {
        // get option 'api_key' value from the database
        $options = get_option( 'cws_fgp_code' );
        $api_key = $options['api_key'];
        echo "<input id='api_key' name='cws_fgp_code[api_key]' type='text' value='$api_key' >";
    }
    

    /**
     * Display and fill the form field. Flickr API Key
     *
     * @since    1.0.0
     */
    function api_secret() {
        // get option 'api_secret' value from the database
        $options = get_option( 'cws_fgp_code' );
        $api_secret = $options['api_secret'];
        echo "<input id='api_secret' name='cws_fgp_code[api_secret]' type='text' value='$api_secret' >";
    }    
    
	/**
	 * Display and fill the form fields for storing defaults.
     *
     * Thumbnail Size in pixels
	 *
	 * @since    1.0.0
	 */    
    function options_thumbnail_size_OLD() {

        // get option 'thumb_size' value from the database
        $options = get_option( 'cws_fgp_options' );
        $thumb_size = $options['thumb_size'];

        echo "<input id='thumb_size' name='cws_fgp_options[thumb_size]' type='text' value='$thumb_size' >";
    }    
    
 
    /**
     * Display and fill the form fields for storing defaults.
     *
     * Album Thumbnail Size in pixels
     *
     * @since    1.0.0
     */    
    function options_album_thumbnail_size_OLD() {

        // get option 'album_thumb_size' value from the database
        $options = get_option( 'cws_fgp_options' );
        $album_thumb_size = $options['album_thumb_size'];

        echo "<input id='album_thumb_size' name='cws_fgp_options[album_thumb_size]' type='text' value='$album_thumb_size' >";
    }   


    /**
     * Enable Cache
     *
     * Pro Only
     *
     * @since    1.0.0
     */    
    function options_enable_cache() {

        // set some defaults...
        $checked = '';

        // get option 'enable_cache' value from the database
        $options = get_option( 'cws_fgp_options' );
        $enable_cache = $options['enable_cache'];

        if($options['enable_cache']) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='enable_cache' name='cws_fgp_options[enable_cache]' type='checkbox' /><small>Only check this once you are happy with other settings.</small>";
    }  


    /**
     * Enable Download
     *
     * Exposes download link next to each thumbnail to allow user to access original image file
     * Pro Only
     *
     * @since    1.0.0
     */    
    function options_enable_download() {

        // set some defaults...
        $checked = '';

        // get option 'enable_download' value from the database
        $options = get_option( 'cws_fgp_options' );
        $options['enable_download'] = isset($options['enable_download']) ? $options['enable_download'] : "";

        $enable_cache = $options['enable_download'];

        if($options['enable_download']) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='enable_download' name='cws_fgp_options[enable_download]' type='checkbox' /><small>Allow user to download original image file.</small>";
    }  


    /**
     * Display and fill the form fields for storing defaults.
     *
     * Number of images results to display per page
     *
     * @since    1.0.0
     */    
    function options_num_image_results() {

        // get option 'num_image_results' value from the database
        $options = get_option( 'cws_fgp_options' );
        $num_image_results = $options['num_image_results'];

        echo "<input id='num_image_results' name='cws_fgp_options[num_image_results]' type='text' value='$num_image_results' >";
    }       


    /**
     * Display and fill the form fields for storing defaults.
     *
     * Show Private Albums
     *
     * @since    1.0.0
     */
    function options_show_privacy_filter() {
        $options = get_option('cws_fgp_options'); 
        $options['privacy_filter'] = isset($options['privacy_filter']) ? $options['privacy_filter'] : "";

        $items = array( 
            '' => 'Any',
            '1' => '1 (public photos)', 
            '2' => '2 (private photos visible to friends)', 
            '3' => '3 (private photos visible to family)', 
            '4' => '4 (private photos visible to friends & family)', 
            '5' => '5 (completely private photos)', 
        );

        echo "<select id='cws_fgp_show_privacy_filter' name='cws_fgp_options[privacy_filter]'>";
        foreach($items as $key => $value) {
            $selected = ($options['privacy_filter']==$key) ? 'selected="selected"' : '';
            echo "<option value='$key' $selected>$value</option>";
        }
        echo "</select>";
    }    


    /**
     * Display and fill the form fields for storing defaults.
     *
     * Album Thumbnail Size, default options from Flickr
     *
     * @since    1.0.0
     */
    
    function options_lightbox_image_size() {
        $options = get_option('cws_fgp_options'); 
        $options['lightbox_image_size'] = isset($options['lightbox_image_size']) ? $options['lightbox_image_size'] : "";

        $sizes = array(
            "square" => "_s",
            "square_75" => "_s",
            "square_150" => "_q",
            "thumbnail" => "_t",
            "small" => "_m",
            "small_240" => "_m",
            "small_320" => "_n",
            "medium" => "",
            "medium_500" => "",
            "medium_640" => "_z",
            "medium_800" => "_c",
            "large" => "_b",
            "large_1024" => "_b",
            "large_1600" => "_h",
            /*"large_2048" => "_k",
            "original" => "_o",*/
        );

        echo "<select id='cws_fgp_lightbox_image_size' name='cws_fgp_options[lightbox_image_size]'>";
        foreach($sizes as $key => $value) {
            $selected = ($options['lightbox_image_size']==$key) ? 'selected="selected"' : '';
            echo "<option value='$key' $selected>$key</option>";
        }
        echo "</select>";
    }


    /**
     * Display and fill the form fields for storing defaults.
     *
     * Image Thumbnail Size, default options from Flickr
     *
     * @since    1.0.0
     */
    
    function options_thumbnail_size() {
        $options = get_option('cws_fgp_options'); 
        $options['thumb_size'] = isset($options['thumb_size']) ? $options['thumb_size'] : "";

        // check if already Pro
        if( !$this->isPro ) {
            $sizes = array(
                "square" => "_s",
                "square_75" => "_s",
                "square_150" => "_q",
                "thumbnail" => "_t",
                "small" => "_m",
                "small_240" => "_m",
                "small_320" => "_n",
                "medium" => "",
                "medium_500" => "",
                /*"medium_640" => "_z",
                "medium_800" => "_c",
                "large" => "_b",
                "large_1024" => "_b",
                "large_1600" => "_h",
                "large_2048" => "_k",
                "original" => "_o",*/
            );
        } else {
            $sizes = array(
                "square" => "_s",
                "square_75" => "_s",
                "square_150" => "_q",
                "thumbnail" => "_t",
                "small" => "_m",
                "small_240" => "_m",
                "small_320" => "_n",
                "medium" => "",
                "medium_500" => "",
                "medium_640" => "_z",
                "medium_800" => "_c",
                /*"large" => "_b",
                "large_1024" => "_b",
                "large_1600" => "_h",
                "large_2048" => "_k",
                "original" => "_o",*/
            );
        }

        echo "<select id='cws_fgp_thumbnail_size' name='cws_fgp_options[thumb_size]'>";
        foreach($sizes as $key => $value) {
            $selected = ($options['thumb_size']==$key) ? 'selected="selected"' : '';
            echo "<option value='$key' $selected>$key</option>";
        }
        echo "</select>";
    }


    /**
     * Display and fill the form fields for storing defaults.
     *
     * Show Album Title
     *
     * @since    1.0.0
     */
    /*
    function options_show_album_title() {

        // set some defaults...
        $checked = '';

        // get option 'show_album_title' value from the database
        $options = get_option( 'cws_fgp_options' );
        $show_album_title = $options['show_album_title'];

        if($options['show_album_title']) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='show_album_title' name='cws_fgp_options[show_album_title]' type='checkbox' />";
    } */ 


    /**
     * Display and fill the form fields for storing defaults.
     *
     * Show Image Title
     *
     * @since    1.0.0
     */    
    function options_show_image_title() {

        // set some defaults...
        $checked = '';

        // get option 'show_image_title' value from the database
        $options = get_option( 'cws_fgp_options' );
        $show_image_title = $options['show_image_title'];

        if($options['show_image_title']) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='show_image_title' name='cws_fgp_options[show_image_title]' type='checkbox' />";
    } 


    /**
     * Display and fill the form fields for storing defaults.
     *
     * Show Album Details
     *
     * @since    1.0.0
     */    
    /*
    function options_show_album_details() {

        // set some defaults...
        $checked = '';

        // get option 'show_album_details' value from the database
        $options = get_option( 'cws_fgp_options' );
        $show_album_details = $options['show_album_details'];

        if($options['show_album_details']) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='show_album_details' name='cws_fgp_options[show_album_details]' type='checkbox' />";
    } */
    

    /**
     * Display and fill the form fields for storing defaults.
     *
     * Show Image Details
     *
     * @since    1.0.0
     */    
    function options_show_image_details() {

        // set some defaults...
        $checked = '';

        // get option 'show_image_details' value from the database
        $options = get_option( 'cws_fgp_options' );
        $options['show_image_details'] = isset($options['show_image_details']) ? $options['show_image_details'] : "";

        $show_image_details = $options['show_image_details'];

        if($options['show_image_details']) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='show_image_details' name='cws_fgp_options[show_image_details]' type='checkbox' />";
    } 


    /**
     * Display and fill the form fields for storing defaults.
     *
     * Show Album Details
     *
     * @since    1.0.0
     */    
    function options_reset() {

        // set some defaults...
        $checked = '';

         // get option 'show_album_details' value from the database
        $options = get_option( 'cws_fgp_reset' );       
        
        if($options['cws_fgp_reset']) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='reset' name='cws_fgp_reset[reset]' type='checkbox' required />";
    } 


    /**
    * Validate user input (we want text only).
    *
    * @since    1.0.0
    */        
    function validate_options( $input ) {

        //$valid = true;

        // If the input is an empty string, add the error message and mark the validity as false                
        if ( '' == trim( $input['api_key'] ) || '' == trim( $input['api_secret'] ) ) {                                       
            // $this->add_error( 'invalid-address', 'You must provide a valid address.' );                 
            //add_settings_error( 'cws_fgp_code', 'invalid-email', 'You have entered an invalid e-mail address.' );

            $valid = false;              
        } else {
            $valid['api_key'] = esc_attr ( $input['api_key'] );
            $valid['api_secret'] = esc_attr ( $input['api_secret'] );
        }      

        return $valid;
    }
    

    /**
     * Validate user input.
     *
     * @since    1.0.0
     */         
    function validate_main_options( $input ) {

        $valid['thumb_size']            = esc_attr( $input['thumb_size'] );
        $valid['album_thumb_size']      = esc_attr( $input['album_thumb_size'] );
        $valid['num_image_results']     = esc_attr( $input['num_image_results'] );
        $valid['num_album_results']     = esc_attr( $input['num_album_results'] );
        $valid['lightbox_image_size']   = esc_attr( $input['lightbox_image_size'] );
        $valid['privacy_filter']        = esc_attr( $input['privacy_filter'] );

        // Correct validation of checkboxes
        //$valid['show_album_title'] = ( isset( $input['show_album_title'] ) && true == $input['show_album_title'] ? true : false );
        //$valid['show_album_details'] = ( isset( $input['show_album_details'] ) && true == $input['show_album_details'] ? true : false );

        $valid['show_image_title'] = ( isset( $input['show_image_title'] ) && true == $input['show_image_title'] ? true : false );
        $valid['enable_cache'] = ( isset( $input['enable_cache'] ) && true == $input['enable_cache'] ? true : false );
        $valid['show_image_details'] = ( isset( $input['show_image_details'] ) && true == $input['show_image_details'] ? true : false );
        $valid['enable_download'] = ( isset( $input['enable_download'] ) && true == $input['enable_download'] ? true : false );

        return $valid;
    } 


    /**
     * Validate user input.
     *
     * @since    1.0.0
     */         
    function validate_reset_options( $input ) {

        // Correct validation of checkboxes
        $valid['reset'] = ( isset( $input['reset'] ) && true == $input['reset'] ? true : false );

        return $valid;
    } 


    // Dispay upgrade notice
    function cws_fgp_admin_installed_notice( $userObj ) {

        // var_dump($userObj->ID);

            // check if already Pro
            if( !$this->isPro ) {

                // Check if user has dismissed notice previously
                if ( ! get_user_meta( $userObj->ID, 'cws_fgp_ignore_upgrade' ) ) {
                    global $pagenow;
                    // Only show upgrade notice if on this page
                    if ( $pagenow == 'options-general.php' || $pagenow == 'admin.php' ) {
                    ?>
                    <div id="message" class="updated cws-fgp-message">
                        <div class="squeezer">
                            <h4><?php _e( '<strong>Flickr Gallery Viewer has been installed &#8211; Get the Pro version</strong>', 'cws_fgp_' ); ?></h4>
                            <h4><?php _e( '<strong>GET 20% OFF! &#8211; use discount code WPMEGA20 on checkout</strong>', 'cws_fgp_' ); ?></h4>
                            <p class="submit">
                                <a href="http://www.cheshirewebsolutions.com/?utm_source=cws_fgp_config&utm_medium=button&utm_content=upgrade_notice_message&utm_campaign=cws_fgp_plugin" class="button-primary"><?php _e( 'Visit Site', 'cws_fgp_' ); ?></a>
                                <a href="<?php echo admin_url('admin.php?page=cws_fgp'); ?>" class="button-primary"><?php _e( 'Settings', 'cws_fgp_' ); ?></a>
                                <a href="?cws_fgp_ignore_upgrade=0" class="secondary-button">Hide Notice</a>
                            </p>
                        </div>
                    </div>
                    <?php
                    }                
                } // end check if already dismissed

            } // end isPro check

            // Set installed option
            update_option( 'cws_fgp_installed', 0);
    }


    // Dispay Migrate Flickr to Google Photos notice
    // function cws_fgp_admin_installed_notice( $userObj ) {
    function cws_fgp_admin_migrate_notice( $userObj ) {

        //var_dump($userObj->ID);

            // check if already Pro
            //if( !$this->isPro ) 
            {

                // Check if user has dismissed notice previously
                // if ( ! get_user_meta( $userObj->ID, 'cws_fgp_ignore_upgrade' ) ) {
                if ( ! get_user_meta( $userObj->ID, 'cws_fgp_ignore_migrate' ) ) {                    
                    global $pagenow;
                    // Only show upgrade notice if on this page
                    if ( $pagenow == 'options-general.php' || $pagenow == 'admin.php' ) {
                    ?>
                    <div id="message" class="updated cws-fgp-message">
                        <div class="squeezer">
                            <h4><?php _e( '<strong>Consider moving your photos from Flickr to Google Photos - <a target="_blank" href="https://lifehacker.com/how-to-move-your-photos-from-flickr-to-another-service-1830307573">read WHY!</a></strong>', 'cws_fgp_' ); ?></h4>
                            <p>Starting Tuesday, February 5, Flickr will start deleting photos (oldest first) for free users over the 1,000-photo limit. Deletions continue until your account reaches 1,000 photos.</p>
                            <p>We have got you covered with our <a target="_blank" href="https://wordpress.org/plugins/google-picasa-albums-viewer/">Google Photos Plugin</a> for WordPress</p>
                            <h4><?php _e( '<strong>GET 20% OFF! &#8211; use discount code WPMEGA20 on checkout</strong>', 'cws_fgp_' ); ?></h4>
                            <p class="submit">
                                <a target="_blank" href="https://www.cheshirewebsolutions.com/google-photos-for-wordpress-plugin/" class="button-primary"><?php _e( 'Get Google Photos Pro', 'cws_fgp_' ); ?></a>
                                <!-- <a href="?cws_fgp_ignore_migrate=0" class="secondary-button">Hide Notice</a> -->
                            </p>
                        </div>
                    </div>
                    <?php
                    }                
                } // end check if already dismissed

            } // end isPro check

            // Set installed option
            update_option( 'cws_fgp_installed', 0);
    }    

  
    // If installed display upgrade / migrate notice
    function cws_fgp_admin_notices_styles() {
       
        // Installed notices
        if ( get_option( 'cws_fgp_installed' ) == 1 ) {
            add_action( 'admin_notices', $this->cws_fgp_admin_installed_notice() );  
        }
    }
        
    // Allow user to dismiss upgrade notice :)
    function cws_fgp_ignore_upgrade( $userObj2 ) {   

        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset( $_GET['cws_fgp_ignore_upgrade'] ) && '0' == $_GET['cws_fgp_ignore_upgrade'] ) {
            // add_user_meta($current_user->ID, 'cws_fgp_ignore_upgrade', 'true', true);
            add_user_meta($userObj2->ID, 'cws_fgp_ignore_upgrade', 'true', true);

            // Redirect to plugin settings page
            wp_redirect( admin_url( 'admin.php?page=cws_fgp' ) );
        }
    }   

//

}


class CWS_FGP_WP_PM_User extends WP_User {

    function getID() {
        return $this->ID;
    }

}


class CWS_FGP_WP_PM {

  protected $user;

  function __construct ( CWS_FGP_WP_PM_User $user = NULL) {
    if ( ! is_null( $user ) && $user->exists() ) $this->user = $user;
  }

  function getUser() {
    return $this->user;
  }

}