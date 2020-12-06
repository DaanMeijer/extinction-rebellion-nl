<?php
/**
 *  List table for scheduled import.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    Import_Meetup_Events
 * @subpackage Import_Meetup_Events/includes
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class respoinsible for generate list table for scheduled import.
 */
class Import_Meetup_Events_List_Table extends WP_List_Table {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		global $status, $page;
	        // Set parent defaults.
	        parent::__construct( array(
	            'singular'  => 'xt_scheduled_import',     // singular name of the listed records.
	            'plural'    => 'ime_scheduled_import',    // plural name of the listed records.
	            'ajax'      => false,        // does this table support ajax?
	        ) );
	}

	/**
	 * Setup output for default column.
	 *
	 * @since    1.0.0
	 * @param array  $item Items.
	 * @param string $column_name  Column name.
	 * @return string
	 */
	function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Setup output for title column.
	 *
	 * @since    1.0.0
	 * @param array $item Items.
	 * @return array
	 */
	function column_title( $item ) {

		$ime_url_delete_args = array(
			'page'   => wp_unslash( $_REQUEST['page'] ),
			'ime_action' => 'ime_simport_delete',
			'import_id'  => absint( $item['ID'] ),
		);
		// Build row actions.
		$actions = array(
		    'delete' => sprintf( '<a href="%1$s" onclick="return confirm(\'Warning!! Are you sure to Delete this scheduled import? Scheduled import will be permanatly deleted.\')">%2$s</a>',esc_url( wp_nonce_url( add_query_arg( $ime_url_delete_args ), 'ime_delete_import_nonce' ) ), esc_html__( 'Delete', 'import-meetup-events' ) ),
		);

		// Return the title contents.
		return sprintf('<strong>%1$s</strong><span>%4$s</span> <span style="color:silver">(id:%2$s)</span>%3$s',
		    $item['title'],
		    $item['ID'],
		    $this->row_actions( $actions ),
		    __('Origin', 'import-meetup-events') . ': <b>' . ucfirst( $item["import_origin"] ) . '</b>'
		);
	}

	/**
	 * Setup output for Action column.
	 *
	 * @since    1.0.0
	 * @param array $item Items.
	 * @return array
	 */
	function column_action( $item ) {

		$xtmi_run_import_args = array(
			'page'   => wp_unslash( $_REQUEST['page'] ),
			'ime_action' => 'ime_run_import',
			'import_id'  => $item['ID'],
		);

		// Return the title contents.
		return sprintf( '<a class="button-primary" href="%1$s">%2$s</a><br/>%3$s',
			esc_url( wp_nonce_url( add_query_arg( $xtmi_run_import_args ), 'ime_run_import_nonce' ) ),
			esc_html__( 'Import Now', 'import-meetup-events' ),
			$item['last_import']
		);
	}

	function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("video")
            /*$2%s*/ $item['ID']             //The value of the checkbox should be the record's id
        );
    }

	/**
	 * Get column title.
	 *
	 * @since    1.0.0
	 */
	function get_columns() {
		$columns = array(
		 'cb'    => '<input type="checkbox" />',
		 'title'     => __( 'Scheduled import', 'import-meetup-events' ),
		 'import_status'   => __( 'Import Event Status', 'import-meetup-events' ),
		 'import_category'   => __( 'Import Category', 'import-meetup-events' ),
		 'import_frequency'   => __( 'Import Frequency', 'import-meetup-events' ),
		 'action'   => __( 'Action', 'import-meetup-events' ),
		);
		return $columns;
	}

	public function get_bulk_actions() {

        return array(
            'delete' => __( 'Delete', 'import-meetup-events' ),
        );

    }

	/**
	 * Prepare Meetup url data.
	 *
	 * @since    1.0.0
	 */
	function prepare_items( $origin = '' ) {
		$per_page = 10;
		$columns = $this->get_columns();
		$hidden = array( 'ID' );
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();
		
		if( $origin != '' ){
			$data = $this->get_scheduled_import_data( $origin );	
		}else{
			$data = $this->get_scheduled_import_data();
		}
		
		if ( ! empty( $data ) ) {
			$total_items = ( $data['total_records'] )? (int) $data['total_records'] : 0;
			// Set data to items.
			$this->items = ( $data['import_data'] )? $data['import_data'] : array();

			$this->set_pagination_args( array(
			    'total_items' => $total_items,  // WE have to calculate the total number of items.
			    'per_page'    => $per_page, // WE have to determine how many items to show on a page.
			    'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
			) );
		}
	}

	/**
	 * Get Meetup url data.
	 *
	 * @since    1.0.0
	 */
	function get_scheduled_import_data( $origin = '' ) {
		global $ime_events;

		$scheduled_import_data = array( 'total_records' => 0, 'import_data' => array() );
		$per_page = 10;
		$current_page = $this->get_pagenum();

		$query_args = array(
			'post_type' => 'ime_scheduled_import',
			'posts_per_page' => $per_page,
			'paged' => $current_page,
		);

		if( $origin != '' ){
			$query_args['meta_key'] = 'import_origin';
			$query_args['meta_value'] = esc_attr( $origin );
		}
		$importdata_query = new WP_Query( $query_args );
		$scheduled_import_data['total_records'] = ( $importdata_query->found_posts ) ? (int) $importdata_query->found_posts : 0;
		// The Loop.
		if ( $importdata_query->have_posts() ) {
			while ( $importdata_query->have_posts() ) {
				$importdata_query->the_post();

				$import_id = get_the_ID();
				$import_data = get_post_meta( $import_id, 'import_eventdata', true );
				$import_origin = get_post_meta( $import_id, 'import_origin', true );
				$import_plugin = isset( $import_data['import_into'] ) ? $import_data['import_into'] : '';
				$import_status = isset( $import_data['event_status'] ) ? $import_data['event_status'] : '';
				
				$term_names = array();
				$import_terms = isset( $import_data['event_cats'] ) ? $import_data['event_cats'] : array(); 
				
				if ( $import_terms && ! empty( $import_terms ) ) {
					foreach ( $import_terms as $term ) {
						$get_term = '';
						if( $import_plugin == 'tec' ){

							$get_term = get_term( $term, $ime_events->tec->get_taxonomy() );	

						}elseif( $import_plugin == 'em' ){

							$get_term = get_term( $term, $ime_events->em->get_taxonomy() );	

						}elseif( $import_plugin == 'eventon' ){

							$get_term = get_term( $term, $ime_events->eventon->get_taxonomy() );

						}elseif( $import_plugin == 'event_organizer' ){

							$get_term = get_term( $term, $ime_events->event_organizer->get_taxonomy() );

						}elseif( $import_plugin == 'aioec' ){

							$get_term = get_term( $term, $ime_events->aioec->get_taxonomy() );	

						}elseif( $import_plugin == 'my_calendar' ){

							$get_term = get_term( $term, $ime_events->my_calendar->get_taxonomy() );
								
						}elseif( $import_plugin == 'ime' ){

							$get_term = get_term( $term, $ime_events->ime->get_taxonomy() );	
						}else{
							$get_term = get_term( $term, $ime_events->tec->get_taxonomy() );
						}

						if( !is_wp_error( $get_term ) && !empty( $get_term ) ){
							$term_names[] = $get_term->name;
						}
					}
				}	

				$last_import_history_date = '';
				$history_args = array(
					'post_type'   => 'ime_import_history',
					'post_status' => 'publish',
					'posts_per_page' => 1,
					'meta_key'   => 'schedule_import_id',
					'meta_value' => $import_id,

				);

				$history = new WP_Query( $history_args );
				if ( $history->have_posts() ) {
					while ( $history->have_posts() ) {
						$history->the_post();
						$last_import_history_date = sprintf( __( 'Last Import: %s ago', 'import-meetup-events' ), human_time_diff( get_the_date( 'U' ), current_time( 'timestamp' ) ) );
					}
				}
				wp_reset_postdata();

				$scheduled_import_data['import_data'][] = array(
					'ID' => $import_id,
					'title' => get_the_title(),
					'import_status'   => ucfirst( $import_status ),
					'import_category' => implode( ', ', $term_names ),
					'import_frequency'=> isset( $import_data['import_frequency'] ) ? ucfirst( $import_data['import_frequency'] ) : '',
					'import_origin'   => $import_origin,
					'last_import'     => $last_import_history_date,
				);
			}
		}
		// Restore original Post Data.
		wp_reset_postdata();
		return $scheduled_import_data;
	}
}

/**
 * Class respoinsible for generate list table for scheduled import.
 */
class Import_Meetup_Events_History_List_Table extends WP_List_Table {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		global $status, $page;
	        // Set parent defaults.
	        parent::__construct( array(
	            'singular'  => 'import_history',     // singular name of the listed records.
	            'plural'    => 'ime_import_histories',   // plural name of the listed records.
	            'ajax'      => false,        // does this table support ajax?
	        ) );
	}

	/**
	 * Setup output for default column.
	 *
	 * @since    1.0.0
	 * @param array  $item Items.
	 * @param string $column_name  Column name.
	 * @return string
	 */
	function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Setup output for title column.
	 *
	 * @since    1.0.0
	 * @param array $item Items.
	 * @return array
	 */
	function column_title( $item ) {

		$ime_url_delete_args = array(
			'page'   => wp_unslash( $_REQUEST['page'] ),
			'tab'   => wp_unslash( $_REQUEST['tab'] ),
			'ime_action' => 'ime_history_delete',
			'history_id'  => absint( $item['ID'] ),
		);
		// Build row actions.
		$actions = array(
		    'delete' => sprintf( '<a href="%1$s" onclick="return confirm(\'Warning!! Are you sure to Delete this import history? Import history will be permanatly deleted.\')">%2$s</a>',esc_url( wp_nonce_url( add_query_arg( $ime_url_delete_args ), 'ime_delete_history_nonce' ) ), esc_html__( 'Delete', 'import-meetup-events' ) ),
		);

		// Return the title contents.
		return sprintf('<strong>%1$s</strong><span>%3$s</span> %2$s',
		    $item['title'],
		    $this->row_actions( $actions ),
		    __('Origin', 'import-meetup-events') . ': <b>' . ucfirst( get_post_meta( $item['ID'], 'import_origin', true ) ) . '</b>'
		);
	}

	/**
	 * Setup output for Action column.
	 *
	 * @since    1.0.0
	 * @param array $item Items.
	 * @return array
	 */
	function column_stats( $item ) {

		$created = get_post_meta( $item['ID'], 'created', true );
		$updated = get_post_meta( $item['ID'], 'updated', true );
		$skipped = get_post_meta( $item['ID'], 'skipped', true );

		$success_message = '<span style="color: silver"><strong>';
		if( $created > 0 ){
			$success_message .= sprintf( __( '%d Created', 'import-meetup-events' ), $created )."<br>";
		}
		if( $updated > 0 ){
			$success_message .= sprintf( __( '%d Updated', 'import-meetup-events' ), $updated )."<br>";
		}
		if( $skipped > 0 ){
			$success_message .= sprintf( __( '%d Skipped', 'import-meetup-events' ), $skipped ) ."<br>";
		}
		$success_message .= "</strong></span>";

		// Return the title contents.
		return $success_message;
	}

	function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("video")
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }

	/**
	 * Get column title.
	 *
	 * @since    1.0.0
	 */
	function get_columns() {
		$columns = array(
		 'cb'    => '<input type="checkbox" />',
		 'title'     => __( 'Import', 'import-meetup-events' ),
		 'import_category' => __( 'Import Category', 'import-meetup-events' ),
		 'import_date'  => __( 'Import Date', 'import-meetup-events' ),
		 'stats' => __( 'Import Stats', 'import-meetup-events' ),
		);
		return $columns;
	}

	public function get_bulk_actions() {

        return array(
            'delete' => __( 'Delete', 'import-meetup-events' ),
        );

    }

	/**
	 * Prepare Meetup url data.
	 *
	 * @since    1.0.0
	 */
	function prepare_items( $origin = '' ) {
		$per_page = 10;
		$columns = $this->get_columns();
		$hidden = array( 'ID' );
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();
		
		if( $origin != '' ){
			$data = $this->get_import_history_data( $origin );	
		}else{
			$data = $this->get_import_history_data();
		}
		
		if ( ! empty( $data ) ) {
			$total_items = ( $data['total_records'] )? (int) $data['total_records'] : 0;
			// Set data to items.
			$this->items = ( $data['import_data'] )? $data['import_data'] : array();

			$this->set_pagination_args( array(
			    'total_items' => $total_items,  // WE have to calculate the total number of items.
			    'per_page'    => $per_page, // WE have to determine how many items to show on a page.
			    'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
			) );
		}
	}

	/**
	 * Get Meetup url data.
	 *
	 * @since    1.0.0
	 */
	function get_import_history_data( $origin = '' ) {
		global $ime_events;

		$scheduled_import_data = array( 'total_records' => 0, 'import_data' => array() );
		$per_page = 10;
		$current_page = $this->get_pagenum();

		$query_args = array(
			'post_type' => 'ime_import_history',
			'posts_per_page' => $per_page,
			'paged' => $current_page,
		);

		if( $origin != '' ){
			$query_args['meta_key'] = 'import_origin';
			$query_args['meta_value'] = esc_attr( $origin );
		}

		$importdata_query = new WP_Query( $query_args );
		$scheduled_import_data['total_records'] = ( $importdata_query->found_posts ) ? (int) $importdata_query->found_posts : 0;
		// The Loop.
		if ( $importdata_query->have_posts() ) {
			while ( $importdata_query->have_posts() ) {
				$importdata_query->the_post();

				$import_id = get_the_ID();
				$import_data = get_post_meta( $import_id, 'import_data', true );
				$import_origin = get_post_meta( $import_id, 'import_origin', true );
				$import_plugin = isset( $import_data['import_into'] ) ? $import_data['import_into'] : '';
				
				$term_names = array();
				$import_terms = isset( $import_data['event_cats'] ) ? $import_data['event_cats'] : array(); 
				
				if ( $import_terms && ! empty( $import_terms ) ) {
					foreach ( $import_terms as $term ) {
						$get_term = '';
						if( $import_plugin == 'tec' ){

							$get_term = get_term( $term, $ime_events->tec->get_taxonomy() );	

						}elseif( $import_plugin == 'em' ){

							$get_term = get_term( $term, $ime_events->em->get_taxonomy() );	

						}elseif( $import_plugin == 'eventon' ){

							$get_term = get_term( $term, $ime_events->eventon->get_taxonomy() );

						}elseif( $import_plugin == 'event_organizer' ){

							$get_term = get_term( $term, $ime_events->event_organizer->get_taxonomy() );

						}elseif( $import_plugin == 'aioec' ){

							$get_term = get_term( $term, $ime_events->aioec->get_taxonomy() );	

						}elseif( $import_plugin == 'my_calendar' ){

							$get_term = get_term( $term, $ime_events->my_calendar->get_taxonomy() );
								
						}elseif( $import_plugin == 'ime' ){

							$get_term = get_term( $term, $ime_events->ime->get_taxonomy() );	
						}else{
							
							$get_term = get_term( $term, $ime_events->tec->get_taxonomy() );

						}
						
						if( !is_wp_error( $get_term ) && !empty( $get_term ) ){
							$term_names[] = $get_term->name;
						}
					}
				}

				$scheduled_import_data['import_data'][] = array(
					'ID' => $import_id,
					'title' => get_the_title(),
					'import_category' => implode( ', ', $term_names ),
					'import_date' => get_the_date("F j Y, h:i A"),
				);
			}
		}
		// Restore original Post Data.
		wp_reset_postdata();
		return $scheduled_import_data;
	}
}