<?php
    $strOutput = "";
    $strOutput .=  "<div class='listview'>\n";

    wp_register_style( 'cws_fgp_cws_fgp_slick_carousel_css', plugins_url( '../../public/css/slick/slick.css',__FILE__ ) , '', 1 );
    wp_register_style( 'cws_fgp_cws_fgp_slick_carousel_css_theme', plugins_url( '../../public/css/slick/slick-theme.css',__FILE__ ) , '', 1 );

    wp_register_style( 'cws_fgp_cws_fgp_slick_lb_css', plugins_url( '../../public/css/slick-lightbox/slick-lightbox.css',__FILE__ ) , '', 1 );

    if ( function_exists( 'wp_enqueue_style' ) ) {
        wp_enqueue_style( 'cws_fgp_cws_fgp_slick_carousel_css' );
        wp_enqueue_style( 'cws_fgp_cws_fgp_slick_carousel_css_theme' );
        wp_enqueue_style( 'cws_fgp_cws_fgp_slick_lb_css' );
    } 

    if( $xml === false ) {
        echo 'Sorry there has been a problem with your feed.';
    } else {

        $xml->registerXPathNamespace('gphoto', 'http://schemas.google.com/photos/2007');
        $xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/');
        
        $strOutput .=  "<div class='carousel'>\n";
            $strOutput .=  "<div class=\"multiple-items-sc\">\n";

            foreach( $xml->entry as $feed) {

                // get the data we need
                $title = $feed->title;

                if( !in_array( $title, $hide_albums ) ){

                    $gphoto = $feed->children('http://schemas.google.com/photos/2007');
                    $num_photos = $gphoto->numphotos; 
                    $published = $feed->published; 
                    $published = trim( $published );
                    $published = substr( $published, 0, 10 );      

                    $group = $feed->xpath('./media:group/media:thumbnail');
                    $a = $group[0]->attributes(); // we need thumbnail path
                    $id = $feed->xpath('./gphoto:id'); // and album id for our thumbnail

                    $strOutput .=  "<div class=\"item\">\n";

                    // check if permalinks are enabled
                    if ( get_option( 'permalink_structure' ) ) { 
                        // $strOutput .=  "<a href='" .$results_page . "?cws_album=$id[0]&cws_album_title=$title' data-largesrc='$href' data-title='$title' data-description='$text'><img src='$a[0]' alt='$title' /></a>\n";
                        $strOutput .=  "<a href='" .$results_page . "?cws_album=$id[0]&cws_album_title=$title' data-largesrc='' data-title='$title' data-description=''><img src='$a[0]' alt='$title' /></a>\n";                        
                    } else {
                        $strOutput .=  "<a href='" .$results_page . "&cws_album=$id[0]&cws_album_title=$title' data-largesrc='' data-title='$title' data-description=''><img src='$a[0]' alt='$title' /></a>\n";
                    }
                        
                    if ( $show_title ) { $strOutput .=  "<div id='album_title'><h2>$title</h2></div>\n"; }

                    $strOutput .=  "</div>\n"; // End .item
                }
            }
        } // end else
            $strOutput .=  "</div>\n"; // End .multiple-items-sc
        $strOutput .=  "</div>\n"; // End .carousel