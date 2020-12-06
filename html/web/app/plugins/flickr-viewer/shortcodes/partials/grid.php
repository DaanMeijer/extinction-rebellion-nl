<?php
    $strOutput .=  "<div class='listviewxxx'>\n";
    $strOutput .=  "<style>.grid-item.albums{ width: " . $thumb_size . "px !important; }</style>\n";

    // Work out intColumnWidth based on value chosen in $thumb_size...
    switch ($thumb_size) {

        case 'square':
        case 'square_75':        
            $intColumnWidth = 75 + 20;
            break;

        case 'square_150':        
            $intColumnWidth = 150 + 20;
            break;

        case 'thumbnail':        
            $intColumnWidth = 100 + 20;
            break;            

        case 'small':
        case 'small_240':
            $intColumnWidth = 240 + 20;
            break;

        case 'small_320':
            $intColumnWidth = 320 + 20;
            break;

        case 'medium':
        case 'medium_500':
            $intColumnWidth = 500 + 20;
            break;

        case 'medium_640':
            $intColumnWidth = 640 + 20;
            break;            
        
        case 'medium_800':
            $intColumnWidth = 800 + 20;
            break; 

        default:
            $intColumnWidth = 75 + 20;
            break;
    }

    // Init masonry 
    $strOutput .= "<div class='grid js-masonry' data-masonry-options='{ \"itemSelector\": \".grid-item\", \"columnWidth\": ".$intColumnWidth.", \"isOriginLeft\": true, \"isFitWidth\": true }'>\n";


    #----------------------------------------------------------------------------
    # Iterate over the array and extract the info we want
    #----------------------------------------------------------------------------

    // loop through each photo
    foreach ( $photos['photos']['photo'] as $photo ) {
       
        $strOutput .= "<div class='thumbnail grid-item albums' style=\"" . $thumb_size . "\">\n";
            $strOutput .=  "<div class='thumbimage'>\n";

        // print out a link to the photo page, attaching the id of the photo
        $strOutput .= "<a class='result-image-link' href=\"" . $flickr->buildPhotoURL( $photo, $lightbox_image_size ) . "\" data-lightbox='result-set' data-title=\"View $photo[title]\" title=\"View $photo[title]\">";
             
        // This next line uses buildPhotoURL to construct the location of our image, and we want the 'Square' size
        // It also gives the image an alt attribute of the photo's title
        // $strOutput .= "<img src=\"" . $flickr->buildPhotoURL( $photo, $size ) .  "\" width=\"" . $thumb_size . "\" height=\"" . $thumb_size . "\" alt=\"$photo[title]\" />";
        $strOutput .= "<img src=\"" . $flickr->buildPhotoURL( $photo, $thumb_size ) . " alt=\"$photo[title]\" />";
         

        // close link 
        $strOutput .= "</a>";

            $strOutput .=  "</div>\n"; // End .thumbimage
        $strOutput .=  "</div>\n"; // End .thumbnail
    }

$strOutput .= "</div>";