<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    CWS_Flickr_Gallery_Pro
 * @subpackage CWS_Flickr_Gallery_Pro/includes
 * @author     Ian Kennerley <info@cheshirewebsolutions.com>
 */
class CWS_Flickr_Gallery_Pro_Activator {

	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		$cws_fgp_options = array(
					'num_image_results' => 4,
					'thumb_size' => 'square_150',
					'privacy_filter' => "1",
					'show_album_title' => 1,
					'show_album_details' => 0,
					'show_image_title' => 1,
					'results_page' => '',
					'theme' => '',
					'lightbox_image_size' => 'medium_800',
					'enable_cache' => false,
					'row_height' => '450',
					'enable_download' => false,					
				);
        						
		// update_option( 'cws_fgp_options', $cws_fgp_options );    
		
		// Check to see if we already have some options
		$existing_options = get_option( 'cws_fgp_options' );

		if( is_array( $existing_options ) ) {
			$result = array_merge( 	$cws_fgp_options, $existing_options );
			update_option( 'cws_fgp_options', $result ); 
		} else {
			update_option( 'cws_fgp_options', $cws_fgp_options );
		}

		// delete dismiss upgrade notice
		$current_user = cws_fgp_getCurrentUser();
		delete_user_meta( $current_user->ID, 'cws_fgp_ignore_upgrade' );
	}
}