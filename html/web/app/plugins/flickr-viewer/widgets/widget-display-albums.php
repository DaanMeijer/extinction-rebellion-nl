<?php
/**
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

/*************************************************
*
*	Set up 'Widget' to display albums
*	Drag and drop widget to a widgetized area 
*	of the theme
*
**************************************************/
class Widget_DisplayAlbums extends WP_Widget {
     
     
	function Widget_DisplayAlbums() {		
        parent::__construct( false, $name = 'Flickr Viewer: Photostream Grid' );
	}


    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
	function widget( $args, $instance ) { 
	 	
	 	global $key;
        extract( $args ); // TODO: check I need this!

        $show_albums = NULL; // init var to hold album names to display from widget form

        // Grab the title from widget form
        $wtitle = apply_filters('widget_title', $instance['title']);

		if ( !isset ( $wtitle) ) { $wtitle = "Small Slideshow"; }

		echo $args['before_widget'];
		echo $args['before_title'] . "<span>$wtitle</span>" . $args['after_title'];			
		
        $plugin = new CWS_Flickr_Gallery_Pro();
    
        // should this be in CWS_Flickr_Gallery_Pro_Public? with it being in a frontend shortcode
        $plugin_admin = new CWS_Flickr_Gallery_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro()  );


    $plugin = new CWS_Flickr_Gallery_Pro();
    $plugin_admin = new CWS_Flickr_Gallery_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro() );

    $code = get_option( 'cws_fgp_code' );
    $ftoken = get_option( 'cws_fgp_token' );

    // set this to false in production!
    $flickr = new phpFlickr( $code['api_key'], $code['api_secret'], true );

    $tbPhpFlickr = new tbPhpFlickr( $myCache = new CacheManager(), false  );
  
    $options['enable_cache'] = isset($options['enable_cache']) ? $options['enable_cache'] : false;

    // set File system cache, if option is set in settings page and is Pro
    if( $options['enable_cache'] == true && $plugin->isPro == 1 ) {
        $myCache->enableCache( "fs", dirname(__FILE__) . "/../cache" );
    }

        $oauth_token = get_option( 'cws_fgp_oauthToken' );
        $oauth_secret = get_option( 'cws_fgp_oauthSecretToken' );

        $flickr->setOauthToken($oauth_token, $oauth_secret);

        // If authenticated get list of albums
        // if( $plugin_admin->isAuthenticated() == true  ) {
        //if( $tbPhpFlickr->auth_checkToken( $code['api_key'] ) == true ) 
        {

            // Grab options stored in db
            $options = get_option( 'cws_fgp_options' );
            /*
            echo '<pre>';
            print_r($options);
            echo '</pre>';
            */

            // set some defaults...
            $options['num_results'] = isset($options['num_results']) ? $options['num_results'] : "";
            $options['show_title'] = isset($options['show_title']) ? $options['show_title'] : false;

            // TODO *** http://wordpress.stackexchange.com/questions/99603/what-does-extract-shortcode-atts-array-do ***
            // Pull some general plugin options to help make call to getAlbumList()
            $thumb_size = $options['thumb_size'];
            $show_title = $options['show_title'];
            $num_results = $options['num_results'];
            //$visibility = $options['private_albums'];

    		$wtitle = apply_filters( 'widget_title', $instance['title'] );
    		$num_results = apply_filters( 'widget_title', $instance['num_results'] );
    		$show_title = apply_filters( 'widget_title', $instance['show_title'] );
            $show_description = apply_filters( 'widget_title', $instance['show_description'] );
    		$show_albums = apply_filters( 'widget_title', $instance['show_albums'] );	
            $hide_albums = apply_filters( 'widget_title', $instance['hide_albums'] );   		
    
            // Map albums names to hide to array and trim white space
            $hide_albums = array_map( 'trim', explode( ',', $hide_albums ) );

            // TODO: what happens to this if I add an album to hide from widget form?
            // var_dump($hide_albums);
            if( isset( $hide_albums ) ) {
                $hide_albums[] = 'Auto Backup';
            }
            else {
                $hide_albums = 'Auto Backup';
            }
            // $hide_albums = 'Auto Backup';

            // var_dump($hide_albums);

            $show_title     = ( isset( $show_title) && true == $show_title ? true : false );                               // flag to display title
            $show_description     = ( isset( $show_description) && true == $show_description ? true : false );             // flag to display description
       
            $thumb_size     = (int) esc_attr( $instance['thumb_size'] );

            // $link_target    = esc_attr( $instance['link_target'] );
            $results_page    = esc_attr( $instance['link_target'] );
            // $results_page_id   = $link_target;
            $results_page_id   = $results_page;

            // http://code.tutsplus.com/articles/data-sanitization-and-validation-with-wordpress--wp-25536
            
            // Create array of album names to display
            if( strlen( $show_albums ) > 0 ) {  $show_albums = explode(',', $show_albums); }
 

            // TODO: cast other vars to int if required
            // $thumb_size = (int) $thumb_size;

            if ( $show_title === 'false' ) $show_title = false; // just to be sure...
            if ( $show_title === 'true' ) $show_title = true; // just to be sure...
            if ( $show_title === '0' ) $show_title = false; // just to be sure...
            if ( $show_title === '1' ) $show_title = true; // just to be sure...
            $show_title = ( bool ) $show_title;  

            if ( $show_description === 'false' ) $show_description = false; // just to be sure...
            if ( $show_description === 'true' ) $show_description = true; // just to be sure...
            if ( $show_description === '0' ) $show_description = false; // just to be sure...
            if ( $show_description === '1' ) $show_description = true; // just to be sure...
            $show_description = ( bool ) $show_description;          

            $show_details = $show_description;

            // if ( $show_details === 'false' ) $show_details = false; // just to be sure...
            // if ( $show_details === 'true' ) $show_details = true; // just to be sure...
            // if ( $show_details === '0' ) $show_details = false; // just to be sure...
            // if ( $show_details === '1' ) $show_details = true; // just to be sure...
            // $show_details = ( bool ) $show_details;  

            // Grab page from url
            $cws_page = get_query_var('cws_page');

            // Grab the access token
            $AccessToken = get_option( 'cws_fgp_access_token' );

            // To remove pagination from carousel page
            //if( $theme == 'carousel') { $num_results = 0; }

            // No pages requied for widget so set to 1
            $cws_page = 1;

            // Get Albums
            // page, per page, user id
            $photosets = $tbPhpFlickr->photosets_getList( 0,0, get_option( 'cws_fgp_user' ) );

            //echo "<pre>" . print_r($photosets, true) . "</pre>";

            // Decide which layout to use to display the albums
            // $theme = 'list';
            switch( $theme ) {
                #----------------------------------------------------------------------------
                # Grid Layout
                #----------------------------------------------------------------------------
                /*case "grid":
                    // Include Masonry
                    wp_enqueue_script( 'cws_fgp_masonry', plugin_dir_url( __FILE__ )  . '../public/js/masonry.pkgd.min.js', array( 'jquery' ), false, true ); 
                    wp_enqueue_script( 'cws_fgp_imagesLoaded', plugin_dir_url( __FILE__ )  . '../public/js/imagesloaded.pkgd.min.js', array( 'jquery' ), false, true ); 
                        
                    // Initialize Masonry
                    wp_enqueue_script( 'cws_fgp_init_masonry', plugin_dir_url( __FILE__ )  . '../public/js/init_masonry.js', array( 'cws_fgp_masonry' ), false , true );            
                    include 'partials/grid.php';
                    break;*/

                #----------------------------------------------------------------------------
                # List Layout
                #----------------------------------------------------------------------------
                /*case "list":
                    include 'partials/list.php';            
                    break;*/

                #----------------------------------------------------------------------------
                # Carousel Layout
                #----------------------------------------------------------------------------
                /*case "carousel":
                    // Include Slick
                    wp_enqueue_script( 'cws_fgp_slick', plugin_dir_url( __FILE__ )  . '../public/js/slick/slick.min.js', array( 'jquery' ), false, true ); 
                       
                    // Initialize Slick
                    wp_enqueue_script( 'cws_fgp_init_slick', plugin_dir_url( __FILE__ )  . '../public/js/slick/init_slick.js', array( 'cws_fgp_slick' ), false , true );
                    
                    include 'partials/carousel.php';
                    break;*/

                #----------------------------------------------------------------------------
                # Default Layout - Grid
                #----------------------------------------------------------------------------
                default:
                    include 'partials/grid.php';
                    //die();
            }
/*
            #----------------------------------------------------------------------------
            # Show output for pagination
            #----------------------------------------------------------------------------
            $num_pages  = ceil( $total_num_albums / $num_results );

            // If someone has done some url hacking reset page to something sensible
            if ( isset($cws_page) && $cws_page > $num_pages ) { $cws_page = $num_pages; }

            // total results, num to show per page, current page
            $strOutput .= $plugin_admin->get_pagination( $total_num_albums, $num_results, $cws_page );
*/
            
            echo $strOutput;

        } // end if authenticated check 

    		echo $args['after_widget'];
    }			
    	

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */	
	function update ( $new_instance, $old_instance ) {

		$instance = $old_instance;
		
		$instance['title']          = strip_tags( $new_instance['title'] );

        $num_results = strip_tags( $new_instance['num_results'] ); // strip tags
        $num_results = wp_kses($num_results, $allowed_html, $allowed_protocols); // sanitize
        $num_results = (int) $num_results; // cast to int
        $instance['num_results'] = $num_results;
//die($new_instance['show_albums']);
		$instance['show_title']     = strip_tags( $new_instance['show_title'] );			
        $instance['show_description'] = strip_tags( $new_instance['show_description'] );            

		$instance['show_albums']    = strip_tags( $new_instance['show_albums'] );
        $instance['hide_albums']    = strip_tags( $new_instance['hide_albums'] );
        $instance['link_target']    = strip_tags( $new_instance['link_target'] );

        $instance['thumb_size']    = strip_tags( $new_instance['thumb_size'] );

		return $instance;	     	
	}
 


    function defaults() {
        $instance['title'] = '';
        $instance['num_results'] = '';
        $instance['show_title'] = '';
        $instance['show_description'] = '';
        $instance['show_albums'] = '';
        $instance['hide_albums'] = '';
        $instance['thumb_size'] = '';
        $instance['show_pagination'] = '';
        $instance['link_target'] = '';

        return $instance;
    }


    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */    	
	function form( $instance ) {
	
        $defaults = $this->defaults();
        $instance = wp_parse_args( (array) $instance, $defaults );

		$title          = esc_attr( $instance['title'] );
		$num_results    = esc_attr( $instance['num_results'] );
		$show_title     = esc_attr( $instance['show_title'] );
        $show_description = esc_attr( $instance['show_description'] );
		$show_albums    = esc_attr( $instance['show_albums'] );	
        $hide_albums    = esc_attr( $instance['hide_albums'] ); 
        $thumb_size     = esc_attr( $instance['thumb_size'] );
		
		 ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cws_fgp' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p style="display:none;">
				<label for="<?php echo $this->get_field_id( 'show_title' ); ?>"><?php _e( 'Show album titles:', 'cws_fgp' ); ?></label> 
				<input id="<?php echo $this->get_field_id( 'show_title' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_title' ); ?>"  value="1" <?php if ( $show_title == 1) { echo 'checked'; } ?> />						
			</p>
            <p style="display:none;">
                <label for="<?php echo $this->get_field_id( 'show_description' ); ?>"><?php _e( 'Show album description:', 'cws_fgp' ); ?></label> 
                <input id="<?php echo $this->get_field_id( 'show_description' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_description' ); ?>"  value="1" <?php if ( $show_description == 1) { echo 'checked'; } ?> />                        
            </p>
			<p style="display:none;">
				<label for="<?php echo $this->get_field_id( 'show_albums' ); ?>"><?php _e( 'Show albums:', 'cws_fgp' ); ?></label> 
				<input id="<?php echo $this->get_field_id( 'show_albums' ); ?>" type="text" name="<?php echo $this->get_field_name( 'show_albums' ); ?>"  value="<?php echo $show_albums; ?>" />						
			</p>
            <p style="display:none;">
                <label for="<?php echo $this->get_field_id( 'hide_albums' ); ?>"><?php _e( 'Hide albums:', 'cws_fgp' ); ?></label> 
                <input id="<?php echo $this->get_field_id( 'hide_albums' ); ?>" type="text" name="<?php echo $this->get_field_name( 'hide_albums' ); ?>"  value="<?php echo $hide_albums; ?>" />                        
            </p>            
            <p style="display:none;">
                <label for="<?php echo $this->get_field_id( 'num_results' ); ?>"><?php _e( 'Max Number albums:', 'cws_fgp' ); ?></label> 
                <input id="<?php echo $this->get_field_id( 'num_results' ); ?>" size="2" type="text" name="<?php echo $this->get_field_name( 'num_results' ); ?>"  value="<?php echo $num_results; ?>" />                        
            </p> 
            <p>
                <label for="<?php echo $this->get_field_id( 'thumb_size' ); ?>"><?php _e( 'Thumbnail Size:', 'cws_fgp' ); ?></label> 
                <input id="<?php echo $this->get_field_id( 'thumb_size' ); ?>" size="3" type="text" name="<?php echo $this->get_field_name( 'thumb_size' ); ?>"  value="<?php echo $thumb_size; ?>" />                        
            </p>                        	
            <p style="display:none;">
                <label for="<?php echo $this->get_field_id('link_target'); ?>"><?php _e('CTA Link Target:'); ?></label>
                <?php wp_dropdown_pages(array('id' => $this->get_field_id('link_target'),'name' => $this->get_field_name('link_target'),'selected' => $instance['link_target'])); ?>
            </p>
<?php
	}	
    	
}