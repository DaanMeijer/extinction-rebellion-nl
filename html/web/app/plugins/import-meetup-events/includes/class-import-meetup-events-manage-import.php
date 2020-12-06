<?php
/**
 * Class for manane Imports submissions.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    Import_Meetup_Events
 * @subpackage Import_Meetup_Events/includes
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Import_Meetup_Events_Manage_Import {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'setup_success_messages' ) );
		add_action( 'admin_init', array( $this, 'handle_import_form_submit' ) , 99);
		add_action( 'admin_init', array( $this, 'handle_import_settings_submit' ), 99 );
		add_action( 'admin_init', array( $this, 'handle_listtable_oprations' ), 99 );
	}

	/**
	 * Process insert group form for TEC.
	 *
	 * @since    1.0.0
	 */
	public function handle_import_form_submit() {
		global $ime_errors; 
		$event_data = array();

		if ( isset( $_POST['ime_action'] ) && $_POST['ime_action'] == 'ime_import_submit' &&  check_admin_referer( 'ime_import_form_nonce_action', 'ime_import_form_nonce' ) ) {
			
			$event_data['import_into'] = isset( $_POST['event_plugin'] ) ? sanitize_text_field( $_POST['event_plugin']) : '';
			if( $event_data['import_into'] == '' ){
				$ime_errors[] = esc_html__( 'Please provide Import into plugin for Event import.', 'import-meetup-events' );
				return;
			}
			
			$event_data['import_type'] = 'onetime';
			$event_data['import_frequency'] = 'daily';
			$event_data['event_status'] = isset( $_POST['event_status'] ) ? sanitize_text_field( $_POST['event_status']) : 'pending';
			$event_data['event_cats'] = isset( $_POST['event_cats'] ) ? $_POST['event_cats'] : array();
			$event_data['event_author']     = !empty( $_POST['event_author'] ) ? $_POST['event_author'] : get_current_user_id();
			$event_origin = isset( $_POST['import_origin'] ) ? sanitize_text_field( $_POST['import_origin'] ):'meetup';

			$this->handle_meetup_import_form_submit( $event_data );
		}
	}

	/**
	 * Process insert group form for TEC.
	 *
	 * @since    1.0.0
	 */
	public function handle_import_settings_submit() {
		global $ime_errors, $ime_success_msg;
		if ( isset( $_POST['ime_action'] ) && $_POST['ime_action'] == 'ime_save_settings' &&  check_admin_referer( 'ime_setting_form_nonce_action', 'ime_setting_form_nonce' ) ) {
			
			$ime_options = isset( $_POST['meetup'] ) ? $_POST['meetup'] : array();
			$is_update = update_option( IME_OPTIONS, $ime_options );
			if( $is_update ){
				$ime_success_msg[] = __( 'Import settings has been saved successfully.', 'import-meetup-events' );
			}else{
				$ime_errors[] = __( 'Something went wrong! please try again.', 'import-meetup-events' );
			}
		}
	}

	/**
	 * Delete scheduled import from list table.
	 *
	 * @since    1.0.0
	 */
	public function handle_listtable_oprations() {

		global $ime_success_msg;
		if ( isset( $_GET['ime_action'] ) && $_GET['ime_action'] == 'ime_simport_delete' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'ime_delete_import_nonce') ) {
			$import_id = $_GET['import_id'];
			$page = isset($_GET['page'] ) ? $_GET['page'] : 'meetup_import';
			$tab = isset($_GET['tab'] ) ? $_GET['tab'] : 'scheduled';
			$wp_redirect = admin_url( 'admin.php?page='.$page );
			if ( $import_id > 0 ) {
				$post_type = get_post_type( $import_id );
				if ( $post_type == 'ime_scheduled_import' ) {
					wp_delete_post( $import_id, true );
					$query_args = array( 'ime_msg' => 'import_del', 'tab' => $tab );
        			wp_redirect(  add_query_arg( $query_args, $wp_redirect ) );
					exit;
				}
			}
		}

		if ( isset( $_GET['ime_action'] ) && $_GET['ime_action'] == 'ime_history_delete' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'ime_delete_history_nonce' ) ) {
			$history_id = (int)$_GET['history_id'];
			$page = isset($_GET['page'] ) ? $_GET['page'] : 'meetup_import';
			$tab = isset($_GET['tab'] ) ? $_GET['tab'] : 'history';
			$wp_redirect = admin_url( 'admin.php?page='.$page );
			if ( $history_id > 0 ) {
				wp_delete_post( $history_id, true );
				$query_args = array( 'ime_msg' => 'history_del', 'tab' => $tab );
        		wp_redirect(  add_query_arg( $query_args, $wp_redirect ) );
				exit;
			}
		}

		if ( isset( $_GET['ime_action'] ) && $_GET['ime_action'] == 'ime_run_import' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'ime_run_import_nonce') ) {
			$import_id = (int)$_GET['import_id'];
			$page = isset($_GET['page'] ) ? $_GET['page'] : 'meetup_import';
			$tab = isset($_GET['tab'] ) ? $_GET['tab'] : 'scheduled';
			$wp_redirect = admin_url( 'admin.php?page='.$page );
			if ( $import_id > 0 ) {
				do_action( 'ime_run_scheduled_import', $import_id );
				$query_args = array( 'ime_msg' => 'import_success', 'tab' => $tab );
        		wp_redirect(  add_query_arg( $query_args, $wp_redirect ) );
				exit;
			}
		}

		$is_bulk_delete = ( ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' ) || ( isset( $_GET['action2'] ) && $_GET['action2'] == 'delete' ) );

		if ( $is_bulk_delete && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'bulk-ime_scheduled_import') ) {
			$tab = isset($_GET['tab'] ) ? $_GET['tab'] : 'scheduled';
			$wp_redirect = get_site_url() . urldecode( $_REQUEST['_wp_http_referer'] );
        	$delete_ids = $_REQUEST['xt_scheduled_import'];
        	if( !empty( $delete_ids ) ){
        		foreach ($delete_ids as $delete_id ) {
        			wp_delete_post( $delete_id, true );
        		}            		
        	}
        	$query_args = array( 'ime_msg' => 'import_dels', 'tab' => $tab );
        	wp_redirect(  add_query_arg( $query_args, $wp_redirect ) );
			exit;
		}

		if ( $is_bulk_delete && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'bulk-ime_import_histories') ) {
			$tab = isset($_GET['tab'] ) ? $_GET['tab'] : 'history';
			$wp_redirect = get_site_url() . urldecode( $_REQUEST['_wp_http_referer'] );
        	$delete_ids = $_REQUEST['import_history'];
        	if( !empty( $delete_ids ) ){
        		foreach ($delete_ids as $delete_id ) {
        			wp_delete_post( $delete_id, true );
        		}            		
        	}	
        	$query_args = array( 'ime_msg' => 'history_dels', 'tab' => $tab );
        	wp_redirect(  add_query_arg( $query_args, $wp_redirect ) );
			exit;
		}
	}

	/**
	 * Handle meetup import form submit.
	 *
	 * @since    1.0.0
	 */
	public function handle_meetup_import_form_submit( $event_data ){
		global $ime_errors, $ime_success_msg, $ime_events;

		/*$meetup_options = ime_get_import_options('meetup');
		if( !isset( $meetup_options['meetup_api_key'] ) || $meetup_options['meetup_api_key'] == ''0 ){
			$ime_errors[] = __( 'Please insert "Meetup API key" in settings.', 'import-meetup-events');
			return;
		}*/
				
		$event_data['import_origin'] = 'meetup';
		$event_data['meetup_url'] = isset( $_POST['meetup_url'] ) ? $_POST['meetup_url'] : '';
		
		if ( filter_var( $event_data['meetup_url'], FILTER_VALIDATE_URL) === false ){
			$ime_errors[] = esc_html__( 'Please provide valid Meetup group URL.', 'import-meetup-events' );
			return;
		}
		$event_data['meetup_url'] = esc_url( $event_data['meetup_url'] );

		$import_events = $ime_events->meetup->import_events( $event_data );
		if( $import_events && !empty( $import_events ) ){
			$ime_events->common->display_import_success_message( $import_events, $event_data );
		}
	}
	
	/**
	 * Setup Success message.
	 *
	 * @since    1.0.0
	 */
	public function setup_success_messages(){
		global $ime_success_msg;
		if( isset( $_GET['ime_msg'] ) && $_GET['ime_msg'] != '' ){
			switch ( $_GET['ime_msg'] ) {
				case 'import_del':
					$ime_success_msg[] = esc_html__( 'Scheduled import deleted successfully.', 'import-meetup-events' );
					break;

				case 'import_dels':
					$ime_success_msg[] = esc_html__( 'Scheduled imports are deleted successfully.', 'import-meetup-events' );
					break;

				case 'import_success':
					$ime_success_msg[] = esc_html__( 'Scheduled import has been run successfully.', 'import-meetup-events' );
					break;

				case 'history_del':
					$ime_success_msg[] = esc_html__( 'Import history deleted successfully.', 'import-meetup-events' );
					break;

				case 'history_dels':
					$ime_success_msg[] = esc_html__( 'Import histories are deleted successfully.', 'import-meetup-events' );
					break;					
								
				default:
					$ime_success_msg[] = esc_html__( 'Scheduled imports are deleted successfully.', 'import-meetup-events' );
					break;
			}
		}
	}
}
