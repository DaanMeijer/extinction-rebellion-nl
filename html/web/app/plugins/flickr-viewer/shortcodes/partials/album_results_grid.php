<?php
    // Init Masonry
    wp_enqueue_script( 'cws_fgp_imagesLoaded', plugin_dir_url( __FILE__ )  . '../../public/js/imagesloaded.pkgd.min.js', array( 'jquery' ), false, true ); 
    wp_enqueue_script( 'cws_fgp_init_masonry', plugin_dir_url( __FILE__ )  . '../../public/js/init_masonry.js', array( 'masonry' ), false , true );            
    
    // If Pro use Photoswipe for improved responsiveness and better ux
    if( $plugin->get_isPro() == 1 ){
        // include 'partials_pro/photoswipe.html';
        include plugin_dir_path( __FILE__ )  . '../partials_pro/photoswipe.html';

        // Enque Pro FX CSS
        // wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../css/style_fx.css' );
wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../partials_pro/css/style_fx.css' );        

        // Start include PhotoSwipe files
        // wp_enqueue_style( 'props-style1', plugin_dir_url( __FILE__ )  . '../props/css/photoswipe.css' );
wp_enqueue_style( 'props-style1', plugin_dir_url( __FILE__ )  . '../partials_pro/props/css/photoswipe.css' );

        // wp_enqueue_style( 'props-style2', plugin_dir_url( __FILE__ )  . '../props/css/default-skin/default-skin.css' );
wp_enqueue_style( 'props-style2', plugin_dir_url( __FILE__ )  . '../partials_pro/props/css/default-skin/default-skin.css' );


        // Include Javascript
        // wp_enqueue_script( 'cws_gpp_ps', plugin_dir_url( __FILE__ )  . '../props/js/photoswipe.min.js', array(), false, false ); 
wp_enqueue_script( 'cws_gpp_ps', plugin_dir_url( __FILE__ )  . '../partials_pro/props/js/photoswipe.min.js', array(), false, false ); 

        // wp_enqueue_script( 'cws_gpp_psui', plugin_dir_url( __FILE__ )  . '../props/js/photoswipe-ui-default.min.js', array(), false, false ); 
wp_enqueue_script( 'cws_gpp_psui', plugin_dir_url( __FILE__ )  . '../partials_pro/props/js/photoswipe-ui-default.min.js', array(), false, false ); 

        // wp_enqueue_script( 'cws_gpp_init_ps', plugin_dir_url( __FILE__ )  . '../grid/js/init_ps.js', array( 'jquery' ), false, false );
wp_enqueue_script( 'cws_gpp_init_ps', plugin_dir_url( __FILE__ )  . '../partials_pro/grid/js/init_ps.js', array( 'jquery' ), false, false );


        // end include PhotoSwipe files
    } else {
        // Include Lightbox
        wp_enqueue_script( 'cws_fgp_lightbox', plugin_dir_url( __FILE__ )  . '../../public/js/lightbox/lightbox.js', array( 'jquery' ), false, true );                 
        wp_enqueue_script( 'cws_fgp_init_lightbox', plugin_dir_url( __FILE__ )  . '../../public/js/lightbox/init_lightbox.js', array( 'cws_fgp_lightbox' ), false , true );
    }

    // Work out intColumnWidth based on value chosen in $thumb_size...
    switch ($thumb_size) {

// commented out all the $thumbsize below to make thumbnails cropped square...

        case 'square':
        case 'square_75':
            //$thumb_size = 75;      
            $intColumnWidth = 75 + 20;
            break;

        case 'square_150':
            //$thumb_size = 150;      
            $intColumnWidth = 150 + 20;
            break;

        case 'thumbnail':
            //$thumb_size = 100;      
            $intColumnWidth = 100 + 20;
            break;            

        case 'small':
        case 'small_240':
            //$thumb_size = 240;
            $intColumnWidth = 240 + 20;
            break;

        case 'small_320':
            //$thumb_size = 320;                      
            $intColumnWidth = 320 + 20;
            break;

        case 'medium':
        case 'medium_500':
            //$thumb_size = 500;                      
            $intColumnWidth = 500 + 20;
            break;

        case 'medium_640':
            //$thumb_size = 640;                      
            $intColumnWidth = 640 + 20;
            break;            
        
        case 'medium_800':
            //$thumb_size = 800;                      
            $intColumnWidth = 800 + 20;
            break; 

        default:
            //$thumb_size = 75;                      
            $intColumnWidth = 75 + 20;
            break;
    }

    $strOutput .=  "<div class='listviewxxx'>\n";

    $strOutput .=  "<style>.grid-item.albums{ width: " . $thumb_size . "px !important; }</style>\n";
    $strOutput .=  "<style>.grid-item.images{ width: " . $intColumnWidth . "px !important; }</style>\n";

    $strOutput .= "<div id=\"mygallery\" class=\"grid\">";

    // Init masonry 
    $strOutput .= "<div class='grid js-masonry' data-masonry-options='{ \"itemSelector\": \"figure\", \"columnWidth\": figure, \"isFitWidth\": true }'>\n";

    #----------------------------------------------------------------------------
    # Iterate over the array and extract the info we want
    #----------------------------------------------------------------------------

    $intCounter = 0;

    // loop through each photo
    foreach ( $photos['photoset']['photo'] as $photo ) { 

        $strOutput .= "<figure class=\"effect-$strFXStyle\" data-index=\"".$intCounter."\" itemprop=\"associatedMedia\" itemscope itemtype=\"http://schema.org/ImageObject\">\n";   
           
            // $strOutput .= "<div class='thumbnail grid-item images' data-index=\"".$intCounter."\" style='width:" . $thumb_size . "px;'>\n";
            $strOutput .= "<div class='thumbnail grid-item images' data-index=\"" . $intCounter . "\">\n";
              
                // print out a link to the photo page, attaching the id of the photo
                $strOutput .= "<a itemprop=\"contentUrl\" data-size=\"" . $photo['width_l'] . "x" . $photo['height_l']  . "\" data-index=\"".$intCounter."\" class='result-image-link' href='" . $tbPhpFlickr->buildPhotoURL( $photo, $lightbox_image_size ) . "' data-lightbox='result-set' data-title='$photo[title]'>";
                $strOutput .= "<img width=\"" . $photo['width_l'] . "\" data-index=\"".$intCounter."\" class='result-image' src='" . $tbPhpFlickr->buildPhotoURL( $photo, $thumb_size ) . "' alt='$photo[title]'/>";
                
                if( $show_title ) {  
                    $strOutput .= "<figcaption style=\"display:none;\">\n
                                    <p>" . $photo['title'] . "</p>\n
                                </figcaption>\n";
                }

                $strOutput .= "</a>\n";

                // Display link to original image file
                if( $plugin->get_isPro() == 1 && $enable_download == true && isset($photo['originalsecret']) && isset( $photo['originalformat'] ) ) {
                    $origUpload = "https://farm" . $photo['farm'] . ".static.flickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['originalsecret'] . "_o" . "." . $photo['originalformat'];
                    $strOutput .= "<div class='download details'><a target=\"_blank\" download=\"" . $photo['title'] . "\" href=\"" . $origUpload . "\"><span><i class=\"fa fa-download\" aria-hidden=\"true\"></i></span></a></div>";
                }

                // if NO fx value has been set in shortcode...
                // Pro only...
                if( $fx === NULL ) {
                    // Create this div only if title or details are to be shown
                    //if( $show_title  || $show_details ) {
                    if( $show_title ) {                    
                        $strOutput .= "<div class='details'>\n";
                        $strOutput .= "<ul>\n";
                    }
                    if ( $show_title ) {
                        $strOutput .= "<li class='title'><small>" . $photo['title'] . "</small></li>\n";
                    }

                    /*if ( $show_details && $description != ""  ) {
                        $output = "<li><small>$description[0]</small>";
                        $strOutput .= $output;
                    }*/

                    $strOutput .= "</ul>\n";

                    // Close this div only if title or details are to be shown
                    // if( $show_title || $show_details ) {
                    if( $show_title  ) {
                        $strOutput .= "</div>\n"; // End .details
                    }

                }

            $strOutput .=  "</div>\n"; // End .thumbnail

        $strOutput .= "</figure>\n";
        $intCounter++;
    }

    $strOutput .= "</div>"; // End .grid
    $strOutput .= "</div>"; // End #mygallery  