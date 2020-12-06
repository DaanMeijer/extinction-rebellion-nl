<?php
$strOutput = "";

$w = $thumb_size * 1.75;

#----------------------------------------------------------------------------
# Iterate over the $vals
#----------------------------------------------------------------------------
$strOutput .=  "<div class='listview'>\n";

// TODO: some settings
foreach ( $vals as $val ) {
  
    switch ( $val["tag"] ) {

        case "MEDIA:THUMBNAIL":
                $thumbnail = trim( $val["attributes"]["URL"] . "\n" );
                break;

        case "MEDIA:DESCRIPTION":
                $desc = trim( $val["value"] . "\n" );
                break;

        case "MEDIA:TITLE":
                $title = trim( $val["value"] );
                break;

        case "LINK":
                if ( $val["attributes"]["REL"] == "alternate" ) {
                    $href = trim( $val["attributes"]["HREF"] );
                }
                break;

        case "GPHOTO:NUMPHOTOS":
                $num_photos = trim( $val["value"] );
                break;

        case "GPHOTO:LOCATION":
                $location = trim( $val["value"] );
                break;

        case "PUBLISHED":
                $published = trim( $val["value"] );
                $published = substr( $published, 0, 10 );   
                break;

        case "GPHOTO:ID":
                $gphotoid = trim( $val["value"] );
                break;

        case "OPENSEARCH:TOTALRESULTS":
                $total_num_albums = trim( $val["value"] );
                break;
    }

    #----------------------------------------------------------------------------
    # Once we have all the info we need, prepare the output...
    #----------------------------------------------------------------------------

    // get the page link from id
    $results_page = esc_url( get_permalink( $results_page_id ) );

// album names have been specified...
if( !empty( $show_albums ) ){

    if ( isset( $thumbnail ) && isset( $title ) && isset( $href ) && isset( $num_photos ) && isset( $published ) && in_array( $title, $show_albums )) {

        $strOutput .=  "<div class='thumbnail' style='height: " . $thumb_size . "px;  margin:20px;'>\n";
        $strOutput .=  "<div class='thumbimage' style='width: " . $thumb_size . "px; '>\n";
        $strOutput .=  "<a href='" . $results_page . "?cws_album=$gphotoid'><img alt='$title' src='$thumbnail' title='$title' /></a>\n";
        $strOutput .=  "</div>\n"; // End .thumbimage

        $strOutput .=  "<div class='details'>\n";

        $strOutput .=  "<ul>\n";

        if ( $show_title ) {
            $strOutput .=  "<li class='title'><a href='" .$results_page . "?cws_album=$gphotoid'>$title</a></li>\n";
        }

        if ( $show_details ) {

            // limit number of word to prevent layout issues
            // TODO: make an option
            $strTruncatedText = wp_trim_words( $desc, 40 );

            $strOutput .= "<li class='details-meta'>Published: $published, $num_photos images.";

            if ($desc != "") { 
                $strOutput .= " $strTruncatedText";
            }
            if ($location != "") {
                $strOutput .= " $location</li>\n";
            }
        }

        $strOutput .=  "</ul>\n";
        $strOutput .=  "</div>\n"; // End .details
        $strOutput .=  "</div>\n"; // End .thumbnail

        #----------------------------------
        # Reset the variables
        #----------------------------------
        unset( $thumbnail, $title, $href, $description, $output );
    }
}

// if we have no specific album names to show
// i.e. $show_albums === NULL
if( empty( $show_albums ) ){
    // have no albums names specified... therfore show me all albums...
    if ( isset( $thumbnail ) && isset( $title ) && isset( $href ) && isset( $num_photos ) && isset( $published )  && !in_array( $title, $hide_albums )) {

        $strOutput .=  "<div class='thumbnail' style='height: " . $thumb_size . "px;  margin:20px;'>\n";
        $strOutput .=  "<div class='thumbimage' style='width: " . $thumb_size . "px; '>\n";
        $strOutput .=  "<a href='" . $results_page . "?cws_album=$gphotoid'><img alt='$title' src='$thumbnail' title='$title' /></a>\n";
        $strOutput .=  "</div>\n"; // End .thumbimage

        $strOutput .=  "<div class='details'>\n";

        $strOutput .=  "<ul>\n";

        if ( $show_title ) {
            $strOutput .=  "<li class='title'><a href='" .$results_page . "?cws_album=$gphotoid'>$title</a></li>\n";
        }

        if ( $show_details ) {

            // limit number of word to prevent layout issues
            // TODO: make an option
            $strTruncatedText = wp_trim_words( $desc, 40 );

            $strOutput .= "<li class='details-meta'>Published: $published, $num_photos images.";

            if ($desc != "") { 
                $strOutput .= " $strTruncatedText";
            }
            if ($location != "") {
                $strOutput .= " $location</li>\n";
            }
        }
        
        $strOutput .=  "</ul>\n";
        $strOutput .=  "</div>\n"; // End .details
        $strOutput .=  "</div>\n"; // End .thumbnail

        #----------------------------------
        # Reset the variables
        #----------------------------------
        unset( $thumbnail, $title, $href, $description, $output );
    }

}
}
$strOutput .=  "</div>\n"; // End .listview