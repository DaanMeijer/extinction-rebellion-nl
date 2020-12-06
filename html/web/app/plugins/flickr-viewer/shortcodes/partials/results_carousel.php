<?php

    wp_register_style( 'cws_fgp_cws_fgp_slick_carousel_css', plugins_url( '../../public/css/slick/slick.css',__FILE__ ) , '', 1 );
    wp_register_style( 'cws_fgp_cws_fgp_slick_carousel_css_theme', plugins_url( '../../public/css/slick/slick-theme.css',__FILE__ ) , '', 1 );

    wp_register_style( 'cws_fgp_cws_fgp_slick_lb_css', plugins_url( '../../public/css/slick-lightbox/slick-lightbox.css',__FILE__ ) , '', 1 );

    if ( function_exists( 'wp_enqueue_style' ) ) {
        wp_enqueue_style( 'cws_fgp_cws_fgp_slick_carousel_css' );
        wp_enqueue_style( 'cws_fgp_cws_fgp_slick_carousel_css_theme' );
        wp_enqueue_style( 'cws_fgp_cws_fgp_slick_lb_css' );
    } 

    // Some naughty inline styles
    $strOutput .=  "<style>.grid-item { width: " . $thumb_size . "px;  padding:1px;}</style>\n";

    $strOutput .=  "<div class='carousel'>\n";

    $cws_album_title = get_query_var('cws_album_title');

    $strOutput .= "<div id='album_title'><h2>$cws_album_title</h2></div>\n";                        
    $strOutput .= "<div class=\"multiple-items-sc\">\n";

    if( $xml === false ) {
        echo 'Sorry there has been a problem with your feed.';
    } else {
        // Define NamesSpaces
        $xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/');
        $xml->registerXPathNamespace('gphoto', 'http://schemas.google.com/photos/2007');
        // echo $xml->asXML();die();
        
        // Get total number of albums found
        $num_photos = $xml->xpath( "gphoto:numphotos" );
        $num_photos = $num_photos[0];
        //echo "num_photos: $num_photos<br>";

        // Loop over the images
        foreach( $xml->entry as $feed ) {
            // Get the thumbnail tag
            $group = $feed->xpath( './media:group/media:thumbnail' );
            $description = $feed->xpath( './media:group/media:description' );

            // Extract the thumb link
            $a = $group[0]->attributes(); 

            // Convert "content" attributes into array
            $b = $feed->content->attributes(); 

            // Album id for our thumbnail
            $id = $feed->xpath('./gphoto:id'); 
            // var_dump($b['src']);
            //print_r($feed->asXML());
        
            $strOutput .= "<div class=\"item\">\n";
                //$strOutput .= "<a href='" . $b['src'] . "' data-largesrc='" . $b['src'] . "' data-title='$title' data-description='$text'><img src='" . $a['url'] . "' alt='$title' /></a>\n";
                $strOutput .= "<a href='" . $b['src'] . "' data-largesrc='" . $b['src'] . "' data-title='' data-description=''><img src='" . $a['url'] . "' alt='' /></a>\n";
            $strOutput .= "</div>\n"; // End .item
        } // foreach $feed        
    }

    $strOutput .= "</div>\n"; // End .multiple-items-sc
    $strOutput .= "</div>\n"; // End .carousel