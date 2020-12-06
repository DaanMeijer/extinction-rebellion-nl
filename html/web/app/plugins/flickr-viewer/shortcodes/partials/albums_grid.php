<?php
    // has the user been clicking pagination
    $cws_page = isset( $_GET['cws_page'] ) ? $_GET['cws_page'] : 1;


#----------------------------------------------------------------------------
# Display album covers with link to album images
#----------------------------------------------------------------------------
if( !isset( $cws_album_id ) ) {

    $arrArgs = array();
    $arrArgs['per_page'] = $num_image_results;
    $arrArgs['page'] = $cws_page;
    $arrArgs['extras'] = 'original_format,url_l,o_dims';    
    if( isset( $privacy_filter) && !empty( $privacy_filter ) ) { $arrArgs['privacy_filter'] = $privacy_filter; }

    if( $plugin->get_isPro() == 1 && $enable_download == true ) {
        $extras = 'original_format';
    } else {
        $extras = '';        
    }

    // Get the users photosets
// photosets_getList ($user_id = NULL, $page = NULL, $per_page = NULL, $primary_photo_extras = NULL)     
    //$photosets = $flickr->photosets_getList( $token['user']['nsid'] );
    // FIX FOR LUHMAN ERROR
      //$photosets = $flickr->photosets_getList( $user['id'], $cws_page, $num_album_results );
    $photosets = $tbPhpFlickr->photosets_getList( $cws_page, $num_album_results, $user['id'] );

    // check the response
    if( $cws_debug == "1" ) {   
        echo '<pre>';
        print_r($photosets);
        print_r($tbPhpFlickr);
        echo "user_id: " . $user['id'];
        echo "<br>cws_page:  $cws_page";
        echo "<br>num_album_results: $num_album_results<br> ";
        echo '</pre>';
    }

    // has the user been clicking pagination
    // $cws_page = isset( $_GET['cws_page'] ) ? $_GET['cws_page'] : 1;

    // if Pro we need to include the extra args to get access to download original large format file
    if( $plugin->get_isPro() == 1 ) {
        $extras = 'original_format,url_l,o_dims';
    } else {
        $extras ='';        
    }
/*
    // loop over the albums
    foreach ( $photosets['photoset'] as $album ) {
        $photos = $flickr->photosets_getPhotos( $album['id'], $extras, $privacy_filter, $num_image_results, $cws_page );
    }
*/
    
    // Work out intColumnWidth based on value chosen in $thumb_size...
    switch ( $thumb_size ) {

        case 'square':
        case 'square_75':
            $thumb_size = 75;      
            $intColumnWidth = 75 + 20;
            break;

        case 'square_150':
            $thumb_size = 150;      
            $intColumnWidth = 150 + 20;
            break;

        case 'thumbnail':
            $thumb_size = 100;      
            $intColumnWidth = 100 + 20;
            break;            

        case 'small':
        case 'small_240':
            $thumb_size = 240;
            $intColumnWidth = 240 + 20;
            break;

        case 'small_320':
            $thumb_size = 320;                      
            $intColumnWidth = 320 + 20;
            break;

        case 'medium':
        case 'medium_500':
            $thumb_size = 500;                      
            $intColumnWidth = 500 + 20;
            break;

        case 'medium_640':
            $thumb_size = 640;                      
            $intColumnWidth = 640 + 20;
            break;            
        
        case 'medium_800':
            $thumb_size = 800;                      
            $intColumnWidth = 800 + 20;
            break; 

        default:
            $thumb_size = 75;                      
            $intColumnWidth = 75 + 20;
            break;
    }

	$intCounter = 0;

    $strOutput .=  "<div class='listviewxxx'>\n";
    //$strOutput .=  "<style>.grid-item.albums{ width: " . $thumb_size . "px !important; }</style>\n";
    //$strOutput .=  "<style>.grid-item.images{ width: " . $intColumnWidth . "px !important; }</style>\n";

    $strOutput .= "<div id=\"mygallery\" class=\"grid\">";

    // Init masonry 
    $strOutput .= "<div class='grid js-masonry' data-masonry-options='{ \"itemSelector\": \"figure\", \"columnWidth\": figure, \"isFitWidth\": true }'>\n";

    // loop through each photo
    foreach ( $photosets['photoset'] as $album ) { 
        // $photos = $flickr->photosets_getPhotos($album['id'], $extras, $privacy_filter, $num_image_results, $cws_page);

// FIX FOR LUHMAN ERROR
//$photos = $flickr->photosets_getPhotos($album['id'], $extras, $privacy_filter, $num_image_results, 1);
$photos = $tbPhpFlickr->photosets_getPhotos($album['id'], $extras, $privacy_filter, $num_image_results, 1);

/*
echo '<pre>';
//print_r($photos);
print_r($photos['photoset']['photo']);
// $photos['photoset']['photo'][0]
echo '</pre>';
// echo 'thumb_size: ' .$thumb_size;
*/
        $strOutput .= "<figure class=\"effect-$strFXStyle\" data-index=\"".$intCounter."\" itemprop=\"associatedMedia\" itemscope itemtype=\"http://schema.org/ImageObject\">\n";   
           
            // $strOutput .= "<div class='thumbnail grid-item images' data-index=\"".$intCounter."\" style='width:" . $thumb_size . "px;'>\n";
            $strOutput .= "<div class='thumbnail grid-item images' data-index=\"".$intCounter."\" style=' width: 200px; height: 200px;background-position: center center; background-size: cover; background-image: url(" . $tbPhpFlickr->buildPhotoURL( $photos['photoset']['photo'][0], $thumb_size ) . ");'>\n";

                // print out a link to the photo page, attaching the id of the photo
                $strOutput .= "<a style='display:block; width:100%; height:100%;' href='?cws_album_id=" . $album['id'] . "' data-lightbox='result-set' data-title='" . $photos['photoset']['title']  . "'>";

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
                        $strOutput .= "<div class='details albums'>\n";
                        $strOutput .= "<ul>\n";
                    }
                    if ( $show_title ) {
                        $strOutput .= "<li class='title'>" . $photos['photoset']['title'] . "</li>\n";
                        $strOutput .= "<li><small>" . $photos['photoset']['total'] . " photos</small>";

                    }

                    if ( $show_details ) {
                        $output = "<li><small>" . $photos['photoset']['total'] . " photos</small>";
                        $strOutput .= $output;
                    }

                    $strOutput .= "</ul>\n";

                    // Close this div only if title or details are to be shown
                    // if( $show_title || $show_details ) {
                    if( $show_title  ) {
                        $strOutput .= "</div>\n"; // End .details
                    }

                }

				$strOutput .= "</a>\n";

            $strOutput .=  "</div>\n"; // End .thumbnail

        $strOutput .= "</figure>\n";
        $intCounter++;
    }

    $strOutput .= "</div>"; // End .grid
    $strOutput .= "</div>"; // End #mygallery  

    $strOutput .= "</div>"; // End listview? 

    $pages = $photosets['pages']; // total number of pages
    $total_num_albums = $photosets['total']; // total number of photos



    #----------------------------------------------------------------------------
    # Show output for pagination
    #----------------------------------------------------------------------------
    if( $num_image_results > 0 ) {


//echo $photosets['total'];


        // $num_pages  = ceil( $total_num_albums / $num_image_results );
        // $num_pages  = ceil( $photos['photoset']['total'] / $num_image_results );

$num_pages  = ceil( $photosets['total'] / $num_image_results );
        

        // Display debug values...
        if( $cws_debug == "1" ){ 
            echo "<hr>total_num_albums = $total_num_albums<br>";
            echo "num_image_results = $num_image_results<br>";
            echo "num_album_results = $num_album_results<br>";
            echo "lightbox_image_size = $lightbox_image_size<br>";
            echo "num_pages = $num_pages<br>";
            // echo "size = $size<br>";
            echo "thumb_size = $thumb_size<br>";
            echo "row_height = $row_height<br>";            
            echo "enable_cache = " . $options['enable_cache'] ;                       
            echo "<hr>";

    echo "<hr>";
echo "photosets total: " . $photosets['total'] . "<br>";
echo "num_image_results: $num_image_results<br>";
echo "cws_page: $cws_page<br>";
echo "id: $id<br>";
    echo "<hr>";

        }

    }
    // total results, num to show per page, current page
    // $strOutput .= $plugin_admin->get_pagination( $total_num_albums, $num_image_results, $cws_page );
    // $strOutput .= $plugin_admin->get_pagination( $photos['photoset']['total'], $num_image_results, $cws_page, $id );

    $strOutput .= "<br style='clear:both;'>";

    $strOutput .= $plugin_admin->get_pagination( $photosets['total'], $num_album_results, $cws_page, $id );
} else {
#----------------------------------------------------------------------------
# Display images in selected gallery (because we have $cws_album_id set in $_GET )
#----------------------------------------------------------------------------
    /*
    if( $plugin->get_isPro() == 1 ) {
        $extras = 'original_format,url_l,o_dims';
    } else {
        $extras ='';        
    }
    */

    $extras = 'original_format,url_l,o_dims';

    $id = $cws_album_id;

    // Get the images from album specified by id
    // $photos = $flickr->photosets_getPhotos( $id, $extras, $privacy_filter, $num_image_results, $cws_page );
    $photos = $tbPhpFlickr->photosets_getPhotos( $id, $extras, $privacy_filter, $num_image_results, $cws_page );
    

    if( $cws_debug == "1" ) { 
        echo 'Options set in shortcode';
        echo "<pre>" . print_r( $atts, true ) . "</pre>";
        echo 'Response from Flickr';        
        echo "<pre>" . print_r( $photos, true ) . "</pre>";
    }

    // include( plugin_dir_path( __FILE__ ) . '../partials_pro/grid/results_grid_ps.php');  // why is this in Pro folder!!!???
    include( plugin_dir_path( __FILE__ ) . '../partials/album_results_grid.php');


    #----------------------------------------------------------------------------
    # Show output for pagination
    #----------------------------------------------------------------------------
    if( $num_image_results > 0 ) {


//echo $photosets['total'];


        // $num_pages  = ceil( $total_num_albums / $num_image_results );
        // $num_pages  = ceil( $photos['photoset']['total'] / $num_image_results );

$num_pages  = ceil( $photos['photoset']['total'] / $num_image_results );
        

        // Display debug values...
        if( $cws_debug == "1" ){ 
            echo "<hr>total_num_albums = $total_num_albums<br>";
            echo "num_image_results = $num_image_results<br>";
            echo "lightbox_image_size = $lightbox_image_size<br>";
            echo "num_pages = $num_pages<br>";
            // echo "size = $size<br>";
            echo "thumb_size = $thumb_size<br>";
            echo "row_height = $row_height<br>";            
            echo "enable_cache = " . $options['enable_cache'] ;                       
            echo "<hr>";
        
echo "<hr>";
echo "photos['photoset']['total']: " . $photos['photoset']['total'] . "<br>";
echo "num_image_results: $num_image_results<br>";
echo "cws_page: $cws_page<br>";
echo "id: $id<br>";
echo "<hr>";

        }

    }
    // total results, num to show per page, current page
    // $strOutput .= $plugin_admin->get_pagination( $total_num_albums, $num_image_results, $cws_page );
    // $strOutput .= $plugin_admin->get_pagination( $photos['photoset']['total'], $num_image_results, $cws_page, $id );
    $strOutput .= $plugin_admin->get_pagination( $photos['photoset']['total'], $num_image_results, $cws_page, $id );
}