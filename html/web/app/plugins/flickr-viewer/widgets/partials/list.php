<?php
     // get the page link from id
    $results_page = esc_url( get_permalink( $results_page_id ) );

        // $response = $plugin_admin->getAlbumList( $AccessToken, $thumb_size, $show_title, $cws_page, $num_results, $visibility );
        $response = $plugin_admin->getAlbumList( $AccessToken, $thumb_size, $show_title, $cws_page, $num_results);

        #----------------------------------------------------------------------------
        # Convert the XML response into SimpleXML Object
        #----------------------------------------------------------------------------
        if (!function_exists('produce_XML_object_tree')) {

            function produce_XML_object_tree( $response ) {
                libxml_use_internal_errors( true );
                try {
                    // Create simplexml object from feed
                    $xml = new SimpleXMLElement( $response );
                }  catch ( Exception $e ) {
                    // Something went wrong.
                    $error_message = 'SimpleXMLElement threw an exception.';
                    foreach( libxml_get_errors() as $error_line ) {
                        $error_message .= "\t" . $error_line->message;
                    }
                    trigger_error( $error_message );
                    return false;
                }
                return $xml;
            } // end function produce
        }

        // Create SimpleXML Object
        $xml = produce_XML_object_tree ( $response );



    $strOutput = "";
    $strOutput .=  "<div class='listview'>\n";

    if( $xml === false ) {
        echo 'Sorry there has been a problem with your feed.';
    } else {
 
        $xml->registerXPathNamespace('gphoto', 'http://schemas.google.com/photos/2007');
        $xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/');

        // register namespace to get access to total number of albums found
        $xml->registerXPathNamespace('opensearch', 'http://a9.com/-/spec/opensearch/1.1/');
        $totalResults = $xml->xpath( "opensearch:totalResults" );
        $total_num_albums = $totalResults[0];

        // I used this to echo xml to screen, view html and copy and paste feed into .xml then open up in safari to view 
        // echo $xml->asXML();die();

        $arrAlbumID = array(); // array to hold album ids
        // loop over the albums
        foreach( $xml->entry as $feed ) {

            // get the data we need
            $title = $feed->title;

            // do not display if album name has been hidden
            if(!in_array( $title, $hide_albums )){

                $gphoto = $feed->children('http://schemas.google.com/photos/2007');
                $num_photos = $gphoto->numphotos; 
                $published = $feed->published; 
                $published = trim( $published );
                $published = substr( $published, 0, 10 );      

                $group = $feed->xpath('./media:group/media:thumbnail');
                $a = $group[0]->attributes(); // we need thumbnail path
                $id = $feed->xpath('./gphoto:id'); // and album id for our thumbnail

                $strOutput .=  "<div class='thumbnail' style='height: " . $thumb_size . "px;  margin:20px;'>\n";
                    $strOutput .=  "<div class='thumbimage' style='width: " . $thumb_size . "px; '>\n";
                        
                    if ( get_option( 'permalink_structure' ) ) { 
                        $strOutput .=  "<a href='" . $results_page . "?cws_album=$id[0]&cws_album_title=$title'><img alt='$title' src='$a[0]' title='$title' /></a>\n";
                    } else {
                        $strOutput .=  "<a href='" . $results_page . "&cws_album=$id[0]&cws_album_title=$title'><img alt='$title' src='$a[0]' title='$title' /></a>\n";
                    }
                        
                    $strOutput .=  "</div>\n"; // End .thumbimage

                $strOutput .=  "<div class='details'>\n";

                    $strOutput .=  "<ul>\n";

                    if ( $show_title ) {
                        $strOutput .=  "<li class='title'><a href='" .$results_page . "?cws_album=$id[0]'>$title</a></li>\n";
                    }
                    if ( $show_details ) {
                        $strOutput .= "<li class='details-meta'>Published: $published";
                        $strOutput .= "<li class='details-meta'>Images: $num_photos";
                    }

                    $strOutput .=  "</ul>\n";
                $strOutput .=  "</div>\n"; // End .details
            $strOutput .=  "</div>\n"; // End .thumbnail

                $arrAlbumID[] = (string)$id[0]; // cast simplexml object to string
            } // end do not display
        }

    }   // end else 
    
    $strOutput .=  "</div>\n"; // End .listview