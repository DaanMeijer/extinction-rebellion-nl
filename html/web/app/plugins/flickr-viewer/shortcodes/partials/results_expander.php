<?php 
    $cws_album_title = get_query_var('cws_album_title');

    $strOutput .= "<div class='container'>\n";
    $strOutput .= "<div class='main'>\n";
    
    if ( $show_title ) {
        $strOutput .= "<div id='album_title'><h2>$cws_album_title</h2></div>\n";       
    }
        $strOutput .= "<ul id='og-grid' class='og-grid'>\n";

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
        
            $strOutput .= "<li>\n";
                $strOutput .= "<a href='#' data-largesrc='" . $b['src'] . "' data-title='' data-description='$description[0]'><img src='" . $a['url'] . "' alt='image_from_picasa' /></a>\n";
            $strOutput .= "</li>\n";

        } // foreach $feed        
    }

        $strOutput .= "</ul>\n";
    $strOutput .= "</div>\n"; // End .container
    $strOutput .= "</div>\n"; // End .main