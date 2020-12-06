<?php
	
class Actionnetwork_Sync extends ActionNetwork {
	
	public $updated = 0;
	public $inserted = 0;
	public $deleted = 0;
	private $processStartTime = 0;
	private $nestingLevel = 0;
	private $endpoints = array( 'petitions', 'events', 'fundraising_pages', 'advocacy_campaigns', 'forms' );
	
	function __construct() {
		$api_key = get_option( 'actionnetwork_api_key' );
		parent::__construct( $api_key );
		$this->processStartTime = time();
	}
	
	function init() {
		global $wpdb;

		// error_log( "Actionnetwork_Sync::init called", 0 );

		// mark all existing API-synced actions for deletion
		// (any that are still synced will be un-marked)
		$wpdb->query("UPDATE {$wpdb->prefix}actionnetwork SET enabled=-1 WHERE an_id != ''");
		
		// load actions from Action Network into the queue
		foreach ($this->endpoints as $endpoint) {
			$this->traverseFullCollection( $endpoint, 'addToQueue' );
		}

	}
	
	function addToQueue( $resource, $endpoint, $index, $total ) {
		global $wpdb;
		$wpdb->insert(
			$wpdb->prefix.'actionnetwork_queue',
			array (
				'resource' => serialize($resource),
				'endpoint' => $endpoint,
				'processed' => 0,
			)
		);

		// error_log( "Actionnetwork_Sync::addToQueue called; endpoint: $endpoint, index: $index, total: $total", 0 );
	}
	
	function getQueueStatus() {
		global $wpdb;
		$total = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."actionnetwork_queue");
		$processed = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."actionnetwork_queue WHERE processed = 1");
		if ($total == 0) {
			$status = 'empty';
		} elseif ($total && ($total == $processed)) {
			$status = 'complete';
		} else {
			$status = 'processing';
		}
		
		update_option( 'actionnetwork_queue_status', $status );
		
		return array(
			'status' => $status,
			'total' => $total,
			'updated' => $this->updated,
			'inserted' => $this->inserted,
			'processed' => $processed,
		);
	}
	
	function processQueue() {
		
		// check queue status
		$status = $this->getQueueStatus();

		// error_log( "Actionnetwork_Sync::processQueue called. Status:\n\n".print_r($status,1)."\n\n", 0 );

		if ($status['status'] == 'empty') { return; }
		if ($status['status'] == 'complete') {
			$this->cleanUp();
			wp_die();
			return;
		} 
		
		// check memory usage
		$start_new_process = false;
		$memory_limit = $this->get_memory_limit() * 0.9;
		$current_memory = memory_get_usage( true );
		if ( $current_memory >= $memory_limit ) { $start_new_process = true; }
		
		// check nesting level
		if ($this->nestingLevel > 100) {
			$start_new_process = true;
		}
		
		// check process time
		$time_elapsed = time() - $this->processStartTime;
		if ( $time_elapsed > 20 ) { $start_new_process = true; }
		
		// if over 90% of memory or 20 seconds, use ajax to start a new process
		// and pass updated and inserted variables
		if ($start_new_process) {
			
			$ajax_url = admin_url( 'admin-ajax.php' );

			// since we're making this call from the server, we can't use a nonce
			$timeint = time() / mt_rand( 1, 10 ) * mt_rand( 1, 10 );
			$timestr = (string) $timeint;
			$token = md5( $timestr );
			update_option( 'actionnetwork_ajax_token', $token );

			$body = array(
				'action' => 'actionnetwork_process_queue',
				'queue_action' => 'continue',
				'updated' => $this->updated,
				'inserted' => $this->inserted,
				'token' => $token,
			);
			$args = array( 'body' => $body );
			
			// error_log( "Actionnetwork_Sync::processQueue trying to start new process, making ajax call to $ajax_url with following args:\n\n" . print_r( $args, 1) . "\n\nActionnetwork_Sync's current state:\n\n" . print_r( $this, 1), 0 );
			
			wp_remote_post( $ajax_url, $args );
			wp_die();
			return;
		}
		
		// process the next resource
		$this->processResource();
		
		// call processQueue to check queue and process status before processing the next resource
		$this->nestingLevel++;
		$this->processQueue();
	}
	
	protected function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}
		if ( ! $memory_limit || -1 === $memory_limit ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32000M';
		}
		return intval( $memory_limit ) * 1024 * 1024;
	}
	
	function processResource() {
		global $wpdb;
		
		// get a resource out of the database
		$result = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."actionnetwork_queue WHERE processed = 0 LIMIT 0,1", ARRAY_A );
		$resource = unserialize($result['resource']);
		$resource_id = $result['resource_id'];
		$endpoint = $result['endpoint'];
		
		$data = array();
		
		// load an_id, created_date, modified_date, name, title, start_date into $data
		$data['an_id'] = $this->getResourceId($resource);
		$data['created_date'] = isset($resource->created_date) ? strtotime($resource->created_date) : null;
		$data['modified_date'] = isset($resource->modified_date) ? strtotime($resource->modified_date) : null;
		$data['start_date'] = isset($resource->start_date) ? strtotime($resource->start_date) : null;
		$data['browser_url'] = isset($resource->browser_url) ? $resource->browser_url : '';
		$data['title'] = isset($resource->title) ? $resource->title : '';
		$data['name'] = isset($resource->name) ? $resource->name : '';
		$data['description'] = isset($resource->description) ? $resource->description : '';
		$data['location'] = isset($resource->location) ? serialize($resource->location) : '';
	
		// set $data['enabled'] to 0 if:
		// * action_network:hidden is true
		// * status is "cancelled"
		// * event has a start_date that is past
		$data['enabled'] = 1;
		if (isset($resource->{'action_network:hidden'}) && ($resource->{'action_network:hidden'} == true)) {
			$data['enabled'] = 0;
		}
		if (isset($resource->status) && ($resource->status == 'cancelled')) {
			$data['enabled'] = 0;
		}
		if ($data['start_date'] && ($data['start_date'] < (int) current_time('timestamp'))) {
			$data['enabled'] = 0;
		}
	
		// use endpoint (minus pluralizing s) to set $data['type']
		$data['type'] = substr($endpoint,0,strlen($endpoint) - 1);

		// check if action exists in database
		// if it does, we don't need to get embed codes, because those never change
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}actionnetwork WHERE an_id='{$data['an_id']}'";
		$count = $wpdb->get_var( $sql );
		if ($count) {
			// if modified_date is more recent than latest api sync, update
			$last_updated = get_option('actionnetwork_cache_timestamp', 0);
			if ($last_updated < $data['modified_date']) {
				$wpdb->update(
					$wpdb->prefix.'actionnetwork',
					$data,
					array( 'an_id' => $data['an_id'] )
				);
				$this->updated++;
			
			// otherwise just reset the 'enabled' field (to prevent deletion, and hide events whose start date has passed)
			} else {
				$wpdb->update(
					$wpdb->prefix.'actionnetwork',
					array( 'enabled' => $data['enabled'] ),
					array( 'an_id' => $data['an_id'] )
				);
			}

		} else {
			// if action *doesn't* exist in the database, get embed codes, insert
			$embed_codes = $this->getEmbedCodes($resource, true);
			$data = array_merge($data, $this->cleanEmbedCodes($embed_codes));
			$wpdb->insert(
				$wpdb->prefix.'actionnetwork',
				$data
			);
			$this->inserted++;
		}

		// mark resource as processed
		$wpdb->update(
			$wpdb->prefix.'actionnetwork_queue',
			array( 'processed' => 1 ),
			array( 'resource_id' => $resource_id )
		);
	}
	
	function cleanEmbedCodes($embed_codes_raw) {
		$embed_fields = array(
			'embed_standard_layout_only_styles',
			'embed_full_layout_only_styles',
			'embed_standard_no_styles',
			'embed_full_no_styles',
			'embed_standard_default_styles',
			'embed_full_default_styles',
		);
		foreach ($embed_fields as $embed_field) {
			$embed_codes[$embed_field] = isset($embed_codes_raw[$embed_field]) ? $embed_codes_raw[$embed_field] : '';
		}
		return $embed_codes;
	}
	
	function cleanUp() {
		global $wpdb;
		
		// clear the process queue
		$wpdb->query("DELETE FROM {$wpdb->prefix}actionnetwork_queue WHERE processed = 1");
		
		// remove all API-synced action that are still marked for deletion
		$this->deleted = $wpdb->query("DELETE FROM {$wpdb->prefix}actionnetwork WHERE an_id != '' AND enabled=-1");
		
		// update queue status and cache timestamps options
		update_option( 'actionnetwork_queue_status', 'empty' );
		update_option( 'actionnetwork_cache_timestamp', (int) current_time('timestamp') );
		
		// set an admin notice
		$notices = get_option('actionnetwork_deferred_admin_notices', array());
		$notices['api_sync_completed'] = __(
			/* translators: all %d refer to number of actions inserted, updated, deleted, etc. */
			sprintf( 'Action Network API Sync Completed. %d actions inserted. %d actions updated. %s actions deleted.', $this->inserted, $this->updated, $this->deleted ),
			'actionnetwork'
		);
		update_option('actionnetwork_deferred_admin_notices', $notices);
	}
	
}
