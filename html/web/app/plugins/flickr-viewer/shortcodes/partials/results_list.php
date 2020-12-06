<?php
    $strOutput = "";
    $strOutput .=  "<div class='listview'>\n";

    $cws_album_title = get_query_var('cws_album_title');
    if ( $show_title ) {
        $strOutput .= "<div id='album_title'><h2>$cws_album_title</h2></div>\n";       
    }
    
    if( $xml === false ) {
        echo 'Sorry there has been a problem with your feed.';
    } else {
        // Define NamesSpaces
        $xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/'); // define namespace media

        // Get total number of albums found
        $num_photos = $xml->xpath( "gphoto:numphotos" );
        $num_photos = $num_photos[0];

        // Loop over the images
        foreach( $xml->entry as $feed ) {
            // Get the thumbnail tag           
            $group = $feed->xpath( './media:group/media:thumbnail' );
            $description = $feed->xpath( './media:group/media:description' );

            // Get the description
            if( str_word_count( $description[0] ) > 0 ) { $description = $description[0]; }

            // Extract the thumb link
            $a = $group[0]->attributes(); 

            // Convert "content" attributes into array            
            $b = $feed->content->attributes();
        
            $strOutput .=  "<div class='thumbnail' style='height: " . $thumb_size . "px;  margin:20px;'>\n";
                $strOutput .=  "<div class='thumbimage' style='width: " . $thumb_size . "px;'>\n";

                    $strOutput .=  "<a class='example-image-link' href='" . $b['src'] . "' data-lightbox='example-set' data-title='" . $feed->title . "'><img alt='" . $feed->title . "' src='" . $a['url'] . "' title='" . $feed->title . "' /></a>\n";
                $strOutput .=  "</div>\n"; // End .thumbimage

                // Display link to original image file
                if( $plugin->get_isPro() == 1 && $enable_download == true ){
                    $origUpload = str_replace( "/s$imgmax/", "/d/", $b['src'] );
                    $strOutput .= "<div class='download details' style='float:none;'><a target=\"_blank\" download=\"" . $feed->title . "\" href=\"" . $origUpload . "\"><span>Download Image</span></a><br></div>";
                }

                $strOutput .=  "<div class='details'>\n";

                    $strOutput .=  "<ul>\n";

                    if ( $show_title ) {
                        $strOutput .=  "<li class='title'>$feed->title</li>\n";
                    }

            if ( $show_details ) {
                // limit number of word to prevent layout issues
                // TODO: make an option
                //$strTruncatedText = wp_trim_words( $description, 40 );
                $strTruncatedText = wp_trim_words( $description[0], 40 );

                    if ( $description != "" ) { 
                        $strOutput .= "<li class='detail-meta'>$strTruncatedText</li>\n";
                    }

                //$strOutput .=  $output;
            }

                    $strOutput .=  "</ul>\n";
                $strOutput .=  "</div>\n"; // End .details
            $strOutput .=  "</div>\n"; // End .thumbnail
        } // end foreach $feed
    }

    $strOutput .=  "</div>\n"; // End .listview