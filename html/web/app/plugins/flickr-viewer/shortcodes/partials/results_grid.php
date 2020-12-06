<?php
    // Init Masonry
    wp_enqueue_script( 'cws_fgp_imagesLoaded', plugin_dir_url( __FILE__ )  . '../../public/js/imagesloaded.pkgd.min.js', array( 'jquery' ), false, true ); 
    wp_enqueue_script( 'cws_fgp_init_masonry', plugin_dir_url( __FILE__ )  . '../../public/js/init_masonry.js', array( 'masonry' ), false , true );            
    
    // If Pro use Photoswipe for improved responsiveness and better ux
    if( $plugin->get_isPro() == 1 ){
        // include plugin_dir_path( __FILE__ )  . '../partials_pro/photoswipe.html';

    // fix WP 5 'update failed' bug
        $pathPhotoswipe = plugin_dir_path( __FILE__ ) . '../partials_pro/photoswipe.html';
        $strPhotoswipe = file_get_contents($pathPhotoswipe);
        //var_dump($strPhotoswipe);
        $strOutput .= $strPhotoswipe;
    //

        // Enque Pro FX CSS
        wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../partials_pro/css/style_fx.css' );

        // Start include PhotoSwipe files
        wp_enqueue_style( 'props-style1', plugin_dir_url( __FILE__ )  . '../partials_pro/props/css/photoswipe.css' );
        wp_enqueue_style( 'props-style2', plugin_dir_url( __FILE__ )  . '../partials_pro/props/css/default-skin/default-skin.css' );

        // Include Javascript
        wp_enqueue_script( 'cws_gpp_ps', plugin_dir_url( __FILE__ )  . '../partials_pro/props/js/photoswipe.min.js', array(), false, false ); 
        wp_enqueue_script( 'cws_gpp_psui', plugin_dir_url( __FILE__ )  . '../partials_pro/props/js/photoswipe-ui-default.min.js', array(), false, false ); 
        wp_enqueue_script( 'cws_gpp_init_ps', plugin_dir_url( __FILE__ )  . '../partials_pro/grid/js/init_ps.js', array( 'jquery' ), false, false );
        // end inclucde PhotoSwipe files
    } else {
        // Include Lightbox
        wp_enqueue_script( 'cws_fgp_lightbox', plugin_dir_url( __FILE__ )  . '../../public/js/lightbox/lightbox.js', array( 'jquery' ), false, true );                 
        wp_enqueue_script( 'cws_fgp_init_lightbox', plugin_dir_url( __FILE__ )  . '../../public/js/lightbox/init_lightbox.js', array( 'cws_fgp_lightbox' ), false , true );
    }

    $strOutput .=  "<div class='listviewxxx'>\n";
    // $strOutput .=  "<style>.grid-item.albums{ width: " . $thumb_size . "px !important; }</style>\n";

    // Work out intColumnWidth based on value chosen in $thumb_size...
    switch ($thumb_size) {

        case 'square':
        case 'square_75':        
            $intColumnWidth = 75 + 20;
            $intDetailWidth = 75;
            break;

        case 'square_150':        
            $intColumnWidth = 150 + 20;
            $intDetailWidth = 150;
            break;

        case 'thumbnail':        
            $intColumnWidth = 100 + 20;
            $intDetailWidth = 100;
            break;            

        case 'small':
        case 'small_240':
            $intColumnWidth = 240 + 20;
            $intDetailWidth = 240;
            break;

        case 'small_320':
            $intColumnWidth = 320 + 20;
            $intDetailWidth = 320;
            break;

        case 'medium':
        case 'medium_500':
            $intColumnWidth = 500 + 20;
            $intDetailWidth = 500;
            break;

        case 'medium_640':
            $intColumnWidth = 640 + 20;
            $intDetailWidth = 640;
            break;            
        
        case 'medium_800':
            $intColumnWidth = 800 + 20;
            $intDetailWidth = 800;
            break; 

        default:
            $intColumnWidth = 75 + 20;
            $intDetailWidth = 75;
            break;
    }
    $strOutput .=  "<style>.grid-item.albums{ width: " . $intColumnWidth . "px !important; }</style>\n";

    // $strOutput .=  "<style>.grid-item.images{ width: " . $intColumnWidth . "px !important; }</style>\n";
    $strOutput .=  "<style>.grid-item.images{ width: auto !important; }</style>\n";

    $strOutput .= "<div id=\"mygallery\" class=\"grid\">";

    // Init masonry 
    $strOutput .= "<div class='grid js-masonry' data-masonry-options='{ \"itemSelector\": \"figure\", \"columnWidth\": figure, \"isFitWidth\": true }'>\n";

    #----------------------------------------------------------------------------
    # Iterate over the array and extract the info we want
    #----------------------------------------------------------------------------

    $intCounter = 0;

    // loop through each photo
    foreach ( $photos['photos']['photo'] as $photo ) {

        $strOutput .= "<figure class=\"effect-$strFXStyle\" data-index=\"".$intCounter."\" itemprop=\"associatedMedia\" itemscope itemtype=\"http://schema.org/ImageObject\">\n";   
           
            //$strOutput .= "<div class='thumbnail grid-item images' data-index=\"".$intCounter."\" style='width:" . $intColumnWidth . "px;'>\n";
            $strOutput .= "<div class='thumbnail grid-item images' data-index=\"".$intCounter."\">\n";
             
                // print out a link to the photo page, attaching the id of the photo
                //$strOutput .= "<a itemprop=\"contentUrl\" data-size=\"" . $photo['width_l'] . "x" . $photo['height_l']  . "\" data-index=\"".$intCounter."\" class='result-image-link' href='" . $flickr->buildPhotoURL( $photo, $lightbox_image_size ) . "' data-lightbox='result-set' data-title='$photo[title]'><img data-index=\"".$intCounter."\" class='result-image' src='" . $flickr->buildPhotoURL( $photo, $thumb_size ) . "' alt='$photo[title]'/>\n";
                $strOutput .= "<a itemprop=\"contentUrl\" data-size=\"" . $photo['width_l'] . "x" . $photo['height_l']  . "\" data-index=\"".$intCounter."\" class='result-image-link' href='" . $tbPhpFlickr->buildPhotoURL( $photo, $lightbox_image_size ) . "' data-lightbox='result-set' data-title='$photo[title]'>\n";
                $strOutput .= "<img data-index=\"".$intCounter."\" class='result-image' src='" . $tbPhpFlickr->buildPhotoURL( $photo, $thumb_size ) . "' alt='$photo[title]' />\n";

                if( $show_title ) {  
                    $strOutput .= "<figcaption style=\"display:none;\">\n
                                    <p>" . $photo['title'] . "</p>\n
                                </figcaption>\n";

                    $strOutput .= "</a>";
                } 

                $strOutput .= "</a>";

                // Display link to original image file
                if( $plugin->get_isPro() == 1 && $enable_download == true && isset($photo['originalsecret']) && isset( $photo['originalformat'] ) ) {
                    $origUpload = "https://farm" . $photo['farm'] . ".static.flickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['originalsecret'] . "_o" . "." . $photo['originalformat'];
                    $strOutput .= "<div class='download details'><a target=\"_blank\" download=\"" . $photo['title'] . "\" href=\"" . $origUpload . "\"><span><i class=\"fa fa-download\" aria-hidden=\"true\"></i></span></a></div>";
                }

                // if NO fx value has been set in shortcode...
                // Pro only...
                if( $fx === NULL ) {
                    // Create this div only if title or details are to be shown
                    if( $show_title ) {                    
                        $strOutput .= "<div class='details' style='max-width:" . $intDetailWidth . "px'>\n";
                        $strOutput .= "<ul>\n";
                        $strOutput .= "<li class='title'><small>" . $photo['title'] . "</small></li>\n";
                        $strOutput .= "</ul>\n";
                        $strOutput .= "</div>\n"; // End .details                        
                    }
                }

            $strOutput .=  "</div>\n"; // End .thumbnail

        $strOutput .= "</figure>\n";

        $intCounter++;
    }

    $strOutput .= "</div>"; // End .grid
    $strOutput .= "</div>"; // End #mygallery  