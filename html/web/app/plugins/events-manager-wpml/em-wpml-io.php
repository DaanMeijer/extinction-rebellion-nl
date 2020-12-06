<?php
/**
 * Model-related functions. Each ML plugin will do its own thing so must be accounted for accordingly. Below is a description of things that should happen so everything 'works'.
 * 
 * When an event or location is saved, we need to perform certain options depending whether saved on the front-end editor, 
 * or if saved/translated in the backend, since events share information across translations.
 * 
 * Event translations should assign one event to be the 'original' event, meaning bookings and event times will be managed by the 'orignal' event.
 * Since WPML can change default languages and you can potentially create events in non-default languages first, the first language will be the 'orignal' event.
 * If an event is deleted and is the original event, but there are still other translations, the original event is reassigned to the default language translation, or whichever other event is found first
 */
class EM_WPML_IO {
	
	private static $translation_editor = false;
    
    public static function init(){
        //Saving/Editing
        add_filter('em_location_save','EM_WPML_IO::location_save',10,2);
        add_filter('em_event_duplicate','EM_WPML_IO::event_duplicate',10,2);
        add_filter('em_event_save_meta', 'EM_WPML_IO::event_save_meta', 10, 2);
	    add_filter('em_event_save_events', 'EM_WPML_IO::save_events', 100, 4);
	    add_filter('em_ml_ticket_delete', 'EM_WPML_IO::ticket_delete', 10, 2);
	    //Recurring events
	    add_filter('delete_events','EM_WPML_IO::delete_events', 10,3);
	    //EM duplication
	    add_filter('em_event_duplicate','EM_WPML_IO::event_duplicate',10,2);
	    add_filter('em_event_duplicate_url','EM_WPML_IO::event_duplicate_url',100,2);
	    //WPML duplication
	    add_action( 'icl_make_duplicate', 'EM_WPML_IO::icl_make_duplicate', 10, 4);
	    add_action( 'wpml_before_make_duplicate', 'EM_WPML_IO::wpml_before_make_duplicate', 10, 4 );
	    //WPML deletion
	    add_action('em_ml_transfer_original_event', 'EM_WPML_IO::transfer_original',10,2);
	    add_action('em_ml_transfer_original_location', 'EM_WPML_IO::transfer_original',10,2);
	    
	    //WPML translation editor
	    add_filter('wpml_custom_field_values_for_post_signature', 'EM_WPML_IO::add_data_custom_field_to_md5', 10, 2);
	    add_action('wp_ajax_wpml_save_job_ajax', 'EM_WPML_IO::wp_ajax_wpml_save_job_ajax', 1);
	    add_action('admin_init', 'EM_WPML_IO::regularize_translation_editor');
	    
	    //force body translation of EM posts since it still uses the classic Translation Editor
	    /* BUG - does not always work... could reproduce at first, then not anymore when resetting WPML entirely. This seems to fail when the post has a custom package */
	    add_filter( 'wpml_pb_should_body_be_translated', function( $translate, WP_Post $post ){
	        if( em_is_location( $post ) ) return true;
	        return $translate;
	    }, PHP_INT_MAX, 2 );
	
    }
    
    /**
     * Writes a record into the WPML translation tables if non-existent when a location has been added via an event
     * @param boolean $result
     * @param EM_Location $EM_Location
     * @return boolean
    */
    public static function location_save($result, $EM_Location){
    	global $wpdb, $sitepress;
    	$trid = $sitepress->get_element_trid($EM_Location->post_id, 'post_'.EM_POST_TYPE_LOCATION);
		if( empty($trid) ){
    		//save a value into WPML table
    		$wpdb->insert($wpdb->prefix.'icl_translations', array('element_type'=>"post_".EM_POST_TYPE_LOCATION, 'trid'=>$EM_Location->post_id, 'element_id'=>$EM_Location->post_id, 'language_code'=>ICL_LANGUAGE_CODE));
    	}
    	return $result;
    }
    
    /**
     * If we delete an original CPT, WPML doesn't assign a new 'original' CPT. Therefore we just assign the one we've chosen for transfer via EM_ML_IO functions and make that the original.
     * 
     * @param EM_Object $object
     * @param EM_Object $original_object
     */
    public static function transfer_original($object, $original_object){
        global $wpdb;
        if( !empty($object->post_id) ){
            $sql = $wpdb->prepare("UPDATE {$wpdb->prefix}icl_translations SET source_language_code = NULL WHERE element_id = %d", $object->post_id); 
            $wpdb->query($sql);
        }
    }
    
    /**
     * When an event is duplicated, we need to get the original event's translations and copy them as duplicates of the current event.
     * An assumption is made here, which is that the event that was duplicated already is the original, since in ML mode we should be only duplicating the original language.
     * 
     * @param boolean $result
     * @param EM_Event $EM_Event
     * @return boolean
     */
    public static function event_duplicate($result, $EM_Event){
    	global $wpdb, $sitepress, $EM_WPML_DUPLICATING;
    	if( $result && empty($EM_WPML_DUPLICATING) ){
    	    $wpml_post_type = 'post_'.$EM_Event->post_type;
    	    //get the translation info of the duplicated event, for use later on
    	    $event = $result; /* @var $event EM_Event */
    	    $duplicated_trid = $sitepress->get_element_trid($event->post_id, $wpml_post_type);
    	    //first we must change it to be the translation of an original event if this isn't
    	    $trid = $sitepress->get_element_trid($EM_Event->post_id, $wpml_post_type);
    	    $translations = $sitepress->get_element_translations($trid, $wpml_post_type);
    	    foreach( $translations as $lang_code => $translation ){
    	        //check that we're not in the original language, as that has been duplicated already
    	        if( $translations[$lang_code]->element_id != $EM_Event->post_id ){
    	            //get the translation of original event that was duplicated if exists and duplicate it
    	            $event = em_get_event($translations[$lang_code]->element_id, 'post_id');
    	            $EM_WPML_DUPLICATING = true;
    	            $event = $event->duplicate();
    	            $EM_WPML_DUPLICATING = false;
    	            //once saved, we modify the WPML DB slightly so it matches the translation info of the newly translated event
    	            //$original_properties = $wpdb->get_row('SELECT language_code, source_language_code')
    	            $array_set = array('language_code'=>$lang_code, 'trid'=>$duplicated_trid);
    	            $array_set_format = array('%s','%d');
    	            if( !empty($translation->source_language_code) ){
    	                $array_set['source_language_code'] = $translation->source_language_code;
    	                $array_set_format[] = '%s';
    	            }
    	            $wpdb->update( $wpdb->prefix.'icl_translations', $array_set, array('element_type'=>$wpml_post_type, 'element_id'=>$event->post_id), $array_set_format, array('%s','%d'));
    	        }
    	    }
    	}
    	return $result;
    }
    
    /**
     * Modifies the duplication URL so that it contains the lang query parameter of the original event that is being duplicated.
     * 
     * @param string $url
     * @param EM_Event $EM_Event
     * @return string
     */
    public static function event_duplicate_url( $url, $EM_Event ){
        global $sitepress;
        if( !EM_ML::is_original($EM_Event) ){
            $EM_Event = EM_ML::get_original_event($EM_Event);
	        $sitepress_lang = $sitepress->get_language_for_element($EM_Event->post_id, 'post_'.$EM_Event->post_type);
    	    $url = add_query_arg(array('lang'=>$sitepress_lang), $url);
    	    //gets escaped later
        }
        return $url;
    }
	
	/**
	 * Runds before WPML duplicates a post and prevents EM from validating events and locations where meta hasn't been saved yet, and setting post to draft because of this.
	 */
	public static function wpml_before_make_duplicate(){
    	// these are copied directly from respective init() functions to prevent early validation before we save meta
	    remove_filter('wp_insert_post_data',array('EM_Event_Post_Admin','wp_insert_post_data'),100);
	    remove_action('save_post',array('EM_Event_Post_Admin','save_post'),1);
	    remove_action('save_post',array('EM_Event_Recurring_Post_Admin','save_post'),10000);
	    remove_filter('wp_insert_post_data',array('EM_Location_Post_Admin','wp_insert_post_data'),100);
	    remove_action('save_post',array('EM_Location_Post_Admin','save_post'),1);
    }
    
    /**
     * When an event is duplicated via WPML, we need to save the event meta via the EM_Event and EM_Location objects.
     * This way, it grabs the original translation meta and saves it into the duplicate via other hooks in EM_WPML_IO.
     * 
     * @param int $master_post_id
     * @param string $lang
     * @param array $post_array
     * @param int $id
     */
    public static function icl_make_duplicate( $master_post_id, $lang, $post_array, $id ){
    	global $wpdb, $sitepress;
	    // WPML just duplicated an event or location into another language or is syncing a duplicate, if we reset the event_id/location_id and save the post, it'll save a new event/location
	    // BUG - in our xml file, we set _event_id to ignore, but if we duplicate an event and then save the original, _event_id of the duplicate is overwritten. This fixes the problem on our side of things.
	    if( em_is_event($id) ){
			$event = em_get_event($master_post_id, 'post_id');
		    if( !EM_ML::is_original($event) ) $event = EM_ML::get_original_event($event); // in case we are duplicating via classic editor of a translation
            $EM_Event = new EM_Event( $id, 'post_id' );
            $EM_Event->event_language = $sitepress->get_locale_from_language_code($lang); //predefine the language since we're in the default current language context
		    $EM_Event->event_parent = $event->event_id;
		    $EM_Event->event_translation = 1;
            if( $event->event_id == $EM_Event->event_id ){
                //double-check the event_id value here due to the bug
                $EM_Event->event_id = null; // set to null only if translation matches the master id
                if( EM_MS_GLOBAL ){
	                $event_id = $wpdb->get_var( $wpdb->prepare('SELECT event_id FROM '.EM_EVENTS_TABLE.' WHERE post_id=%d AND blog_id=%d', $id, get_current_blog_id() ));
                }else{
	                $event_id = $wpdb->get_var( $wpdb->prepare('SELECT event_id FROM '.EM_EVENTS_TABLE.' WHERE post_id=%d'), $id);
                }
                if( $event_id && $event_id != $EM_Event->event_id ){
	                $EM_Event->event_id = $event_id;
                }
            }
            $EM_Event->save_meta();
        }elseif( em_is_location($id) ){
            $location = em_get_location($master_post_id, 'post_id');
            if( !EM_ML::is_original($location) ) $location = EM_ML::get_original_location($location); // in case we are duplicating via classic editor of a translation
            $EM_Location = new EM_Location($id, 'post_id'); //get from DB not cache function, since save_post may have loaded a location already
            $EM_Location->location_language = $sitepress->get_locale_from_language_code($lang); //predefine the language since we're in the default current language context
		    $EM_Location->location_parent = $location->location_id;
		    $EM_Location->location_translation = 1;
            if( $location->location_id == $EM_Location->location_id ){
                //double-check the event_id value here due to the bug
                $EM_Location->location_id = null; // set to null only if translation matches the master id
                if( EM_MS_GLOBAL ){
	                $location_id = $wpdb->get_var( $wpdb->prepare('SELECT location_id FROM '.EM_LOCATIONS_TABLE.' WHERE post_id=%d AND blog_id=%d', $id, get_current_blog_id() ));
                }else{
	                $location_id = $wpdb->get_var( $wpdb->prepare('SELECT location_id FROM '.EM_LOCATIONS_TABLE.' WHERE post_id=%d'), $id);
                }
                if( $location_id && $location_id != $EM_Location->location_id ){
	                $EM_Location->location_id = $location_id;
                }
            }
            $EM_Location->save_meta();
        }
    }
	
	/**
	 * @param EM_Event $EM_Event
	 * @param string $language WPLANG language, not WPML locale
	 * @return array
	 */
	public static function get_event_ticket_translations( $EM_Event, $language = null ){
		global $sitepress;
		//now we need to go through each language and
		$translations = array();
		$EM_Event = EM_ML::get_original_event($EM_Event); //we go through the original event
		foreach( $EM_Event->get_tickets() as $EM_Ticket ){ /* @var EM_Ticket $EM_Ticket */
			if( !empty($EM_Ticket->ticket_meta['langs']) ){
				foreach( $EM_Ticket->ticket_meta['langs'] as $locale => $ticket_strings ){
					if( $language && $locale !== $language ) continue;
					$lang = $sitepress->get_language_code_from_locale($locale);
					if( !empty($ticket_strings['ticket_name']) ){
						$t = 'ticket_name_'.$EM_Ticket->ticket_id;
						if( empty($translations[$t][$lang]) ) $translations[$t] = array();
						$translations[$t][$lang]['value'] = $EM_Ticket->ticket_meta['langs'][$locale]['ticket_name'];
						$translations[$t][$lang]['status'] = 1;
					}
					if( !empty($ticket_strings['ticket_description']) ){
						$t = 'ticket_description_'.$EM_Ticket->ticket_id;
						if( empty($translations[$t][$lang]) ) $translations[$t] = array();
						$translations[$t][$lang]['value'] = $EM_Ticket->ticket_meta['langs'][$locale]['ticket_description'];
						$translations[$t][$lang]['status'] = 1;
					}
				}
			}
		}
		return $translations;
	}
	
	/**
	 * Generates a tickets string package array for WPML based on an event object.
	 * @param $EM_Event
	 * @param int
	 * @param int
	 * @return array
	 */
	public static function get_event_tickets_package( $EM_Event, $post_id = null, $post_type = null ){
		global $sitepress;
		$EM_Event = EM_ML::get_original_event($EM_Event);
		if( !$post_id ) $post_id = $EM_Event->post_id;
		if( !$post_type ) $post_type = $EM_Event->post_type;
		$package = array(
			'title' => 'Events Manager Tickets',
			'name' => 'tickets-'. $post_id, //we don't use blog_id for extra uniqueness, because WPML only translates blogs locally, not on other parts of the network
			'post_id' => $post_id,
			'trid' => $sitepress->get_element_trid( $post_id, 'post_'.$post_type ),
			'kind' => esc_html__('Events', 'events-manager'),
			'kind_slug' => EM_POST_TYPE_EVENT,
		);
		return $package;
	}
	
	/**
	 * @param boolean $result
	 * @param EM_Event $EM_Event
	 * @return boolean
	 */
	public static function event_save_meta( $result, $EM_Event ){
		global $sitepress;
		if( !$result ) return $result;
		//handle bookings and tickets package for the event
		if( $EM_Event->event_rsvp ){
			$package = static::get_event_tickets_package( $EM_Event );
			//now we either register strings for original translations, or save translations
			if( EM_ML::is_original($EM_Event) ){
				//get all tickets of an event and allow translating of the ticket name/description for each one
				foreach( $EM_Event->get_tickets() as $EM_Ticket ){ /* @var EM_Ticket $EM_Ticket */
					$ticket_label_prefix = '#'. $EM_Ticket->ticket_id. ' ';
					// BUG - This won't work for any translation jobs currently in progress, you will only see the ticket strings for translations when opening a TE with a new Job ID.
					do_action( 'wpml_register_string', $EM_Ticket->ticket_name, 'ticket_name_'.$EM_Ticket->ticket_id, $package, $ticket_label_prefix.esc_html__('Name', 'events-manager'), 'LINE' );
					do_action( 'wpml_register_string', $EM_Ticket->ticket_description, 'ticket_description_'.$EM_Ticket->ticket_id, $package, $ticket_label_prefix.esc_html__('Description', 'events-manager'), 'AREA' );
				}
				$translations = self::get_event_ticket_translations( $EM_Event );
			}elseif( !self::$translation_editor ){
				//if we did not go through the translation editor (i.e. via the old-style editor), save this translation to WPML. TE stuff saves to EM in wpml_pro_translation_completed()
				$translations = self::get_event_ticket_translations( $EM_Event, $EM_Event->event_language );
			}
			if( !empty($translations) ){
				// BUG - This line seems to have no effect. Without the hacky fixes in admin_init and save_events, translations won't show up.
				// to reproduce, open up or create an existing event without translations, update it so packages are registered, translate the event with TE disabled, go back and enable TE then load it up, tickets (might) appear and translations do not.
				do_action('wpml_set_translated_strings', $translations, $package);
			}
		}
		return $result;
	}
	
	/**
	 * For WPML, we need to go through every single event created and add the ticket translations to the TE
	 *
	 * @param boolean $result
	 * @param EM_Event $EM_Event
	 * @param array $event_ids
	 * @param array $post_ids
	 * @return boolean
	 * @throws Exception DateTime Exception
	 */
	public static function save_events($result, $EM_Event, $event_ids, $post_ids){
		if( $result ){
			$te = self::$translation_editor;
			self::$translation_editor = false;
			$original = EM_ML::is_original($EM_Event);
			foreach( $event_ids as $event_id ){
				$event = em_get_event( $event_id );
				static::event_save_meta( true, $event );
				// TODO workaround for updated recurrences not updating other non-ticket translatable fields
				/*
				BUG when updating recurrences, if things like body or any other text changes, the original post of a recurrence is correctly updated but we need some way to trigger a refresh of currently ongoing jobs.
				
				Reproduce:
				1. Create a recurring event, leave body/description empty
				2. View recurrences and open one for translation so a Job ID is created, note that there's no body field
				3. Go back to the recurring event, modify the title slightly and add some content to body/description
				4. Reload TE, the title for translation remains the same, body is missing. Original language recurrence is updated correctly.
				- Newly created translations of a recurrence after editing will reflect the updated body (showing a body field) and title
				 */
				if( $original ){
					/* Hacky - see function PHPDocs */
					// We run this function here rather than in event_save_meta because EM_ML::is_original($event) will not always return true since newly recreated recurrences are still not fully registered by WPML.
					static::wpml_job_refresh_missing_ticket_package_strings($event);
				}
			}
			self::$translation_editor = $te;
		}
		return $result;
	}
	
	/**
	 * Deletes translation info from WPML Tables when recurrences are deleted.
	 *
	 * @param boolean $result
	 * @param EM_Location $EM_Event
	 * @param array $events
	 * @return boolean
	 *
	 * @todo use a WPML-specific function and pass on post IDs that way, given there may be other traces of meta in other tables
	 */
	public static function delete_events($result, $EM_Event, $events){
		global $wpdb;
		if($result){
			$original = EM_ML::is_original($EM_Event);
			$package_kind = esc_html__('Events', 'events-manager');
			$post_ids = array();
			foreach($events as $event){
				$post_ids[] = $event->post_id;
				if( $original ){
					do_action( 'wpml_delete_package_action', 'tickets-'. $event->post_id, $package_kind );
				}
			}
			if( count($post_ids) > 0 ){
				$wpdb->query("DELETE FROM ".$wpdb->prefix."icl_translations WHERE element_id IN (".implode(',',$post_ids).")");
			}
		}
		return $result;
	}
	
	/**
	 * Unregisters strings of any ticket that gets deleted.
	 * @param boolean $result
	 * @param EM_Ticket $EM_Ticket
	 * @return boolean
	 */
	public static function ticket_delete($result, $EM_Ticket ) {
		if( $result ){
			$EM_Event = em_get_event( $EM_Ticket->event_id );
			$EM_Event = EM_ML::get_original_event($EM_Event);
			$context = EM_POST_TYPE_EVENT.'-tickets-' . $EM_Event->post_id;
			/*
			BUG - icl_unregister string causes PHP errors because no strings get deleted from wp_icl_translate. Reproduce by commenting the lines below and then:
			1. create a recurring event with one or more ticket
			1.a. translate the recurring event (not sure if this step is required)
			2. view the recurrences and open one in the Translation Editor so a JOB ID is assigned then close
			3. add a ticket to the RECURRING event and save again, so that tickets are recreated in all recurrences and icl_unregister_string is called whilst deleting previous tickets
			4. open the same TE for the event RECURRENCE you previously created a TE for
			PHP Warnings will appear at top of page and tickets without labels will appear in TE
			*/
			//START Bug "Fix"
			global $wpdb;
			$package = new WPML_Package( static::get_event_tickets_package($EM_Ticket->get_event()) );
			$package_id = $package->get_package_id();
			foreach( array('ticket_name','ticket_description') as $prop ){
				// Hacky - bulk deleted tickets don't have the ticket name available, so we need to find the string_id without that value so icl_get_string_id is a no go
				//$string_id = icl_get_string_id( $EM_Ticket->$prop, $context, $prop.'_'.$EM_Ticket->ticket_id);
				$string_id = $wpdb->get_var( $wpdb->prepare('SELECT id FROM '.$wpdb->prefix.'icl_strings WHERE context=%s AND name=%s', $context, $prop.'_'.$EM_Ticket->ticket_id) );
				$field_name = 'package-string-'.$package_id.'-'.$string_id;
				$wpdb->delete( $wpdb->prefix.'icl_translate', array('field_type'=>$field_name));
			}
			//END Bug "Fix"
			icl_unregister_string($context, 'ticket_name_'.$EM_Ticket->ticket_id);
			icl_unregister_string($context, 'ticket_description_'.$EM_Ticket->ticket_id);
			
		}
		return $result;
	}
	
	// START WPML Translation Editor Hooks
	
	/**
	 * WPML translation editor, we swap the 'save_post' action until custom fields have been copied.
	 */
	public static function wp_ajax_wpml_save_job_ajax() {
		//prevent event and location save_post filters from firing, we'll do it manually
		remove_action('save_post',array('EM_Event_Post_Admin','save_post'),1);
		remove_filter('wp_insert_post_data',array('EM_Event_Post_Admin','wp_insert_post_data'),100);
		remove_action('save_post',array('EM_Event_Recurring_Post_Admin','save_post'),10000);
		remove_filter('wp_insert_post_data',array('EM_Location_Post_Admin','wp_insert_post_data'),100);
		remove_action('save_post',array('EM_Location_Post_Admin','save_post'),1);
		add_action('wpml_pro_translation_completed','EM_WPML_IO::wpml_pro_translation_completed', 10, 2);
	}
	
	/**
	 * Handles translation editor saves
	 * @param integer $post_id
	 * @param array $fields
	 */
	public static function wpml_pro_translation_completed( $post_id, $fields ){ //we don't need the extra args passed by filter
		global $sitepress, $wpdb;
		$post_type = get_post_type($post_id);
		self::$translation_editor = true;
		if( em_is_event($post_type) ){
			//merge any data into event
			$EM_Event = em_get_event($post_id, 'post_id');
			$event = EM_ML::get_original_event($EM_Event);
			$EM_Event->event_language = $sitepress->get_locale_from_language_code($_POST['lang']); //we might not know this yet if a new event
			// Hacky - the part below is the only way around avoiding using wpml_translate_string that I can see as reliable. See line 422 for more info on this.
				//sort out all ticket strings provided via $fields in this translation so we have them by ticket ID and then field type for further down
				$tickets_package = self::get_event_tickets_package( $event );
				$package = new WPML_Package($tickets_package);
				$package_id = $package->get_package_id();
				$string_ids = $ticket_translations = array();
				foreach( $fields as $key => $field ){
					if( preg_match("/package-string-".$package_id."-([0-9]+)$/", $key, $match) ){
						$string_ids[absint($match[1])] = $field['data'];
					}
				}
				if( !empty($string_ids) ){
					$strings_data = $wpdb->get_results('SELECT name, id FROM '.$wpdb->prefix.'icl_strings WHERE id IN ('. implode(',', array_keys($string_ids)) .')');
					foreach( $strings_data as $string_data ){
						if( preg_match('/^(ticket_description|ticket_name)_([0-9]+)$/', $string_data->name, $match) ){
							$ticket_translations[$match[2]][$match[1]] = $string_ids[$string_data->id];
						}
					}
				}
			//get all tickets of a translated event via the TE and save to EM tables
			//do_action( 'wpml_switch_language', $_POST['lang'] );
			foreach( $EM_Event->get_tickets() as $EM_Ticket ){ /* @var EM_Ticket $EM_Ticket */
				/*
				 BUG - You need to re-save a completed translation to get these translations reflected via wpml_translate_string, even if previously translated - caching won't help as wp_cache_flush() on this line has no effect.
				 
				 I think the bug lies in the global $i18n variable, where the custom gettext domain for this package doesn't get added correctly when new data is added to the translation.
				 
				 - Reproducing:
				 
				 1. add a ticket, go to TE and complete translation.
				 2. All tickets even previously translated will NOT provide translated strings via filters or icl_translate() or wpml_translate_string below
				 3. Go back to original event, then click to translate TE again, just click 'save' and below filters/functions will provide translated string.
				 
				 - Additionally, any string that is translated and then translation is MODIFIED and SAVED, is only reflected in filters/functions below by re-saving a second time as per steps above, first time round the original value is provided.
				 */
				/* BUG FIX ATTEMPT - Tried to use icl_translate directly instead of wpml_translate_string, no difference.
				$context = EM_POST_TYPE_EVENT.'-tickets-' . $event->post_id;
				$has_translation = false;
				$ticket_name = icl_translate($context, 'ticket_name_'.$EM_Ticket->ticket_id, $EM_Ticket->ticket_name, false, $has_translation, $_POST['lang']);
				$ticket_description = icl_translate($context, 'ticket_description'.$EM_Ticket->ticket_id, $EM_Ticket->ticket_description, false, $has_translation, $_POST['lang']);
				*/
				//$ticket_name = apply_filters( 'wpml_translate_string', $EM_Ticket->ticket_name, 'ticket_name_'.$EM_Ticket->ticket_id, $tickets_package );
				$ticket_name = !empty($ticket_translations[$EM_Ticket->ticket_id]['ticket_name']) ? $ticket_translations[$EM_Ticket->ticket_id]['ticket_name'] : $EM_Ticket->ticket_name;
				if( $ticket_name !== $EM_Ticket->ticket_name ){ //either original string, or translated string with no change
					$EM_Ticket->ticket_meta['langs'][$EM_Event->event_language]['ticket_name'] = $ticket_name;
				}
				//$ticket_description = apply_filters( 'wpml_translate_string', $EM_Ticket->ticket_description, 'ticket_description_'.$EM_Ticket->ticket_id, $tickets_package );
				$ticket_description = !empty($ticket_translations[$EM_Ticket->ticket_id]['ticket_description']) ? $ticket_translations[$EM_Ticket->ticket_id]['ticket_description'] : $EM_Ticket->ticket_description;
				if( $ticket_description !== $EM_Ticket->ticket_description ){
					$EM_Ticket->ticket_meta['langs'][$EM_Event->event_language]['ticket_description'] = $ticket_description;
				}
			}
			//do_action( 'wpml_switch_language', ICL_LANGUAGE_CODE );
			//save event meta, which will in turn save the shared tickets object to its original data point
			$EM_Event->save_meta();
		}elseif( em_is_location($post_type) ){
			// save the meta to location tables
			$EM_Location = new EM_Location($post_id, 'post_id'); // will get a refreshed version of the location with up-to-date postmeta
			$EM_Location->save_meta();
		}
		self::$translation_editor = false;
	}
	
	/**
	 * Adds ticket strings to array which is used to generate an MD5 signature in WPML which tells whether translations need updating.
	 * @param array $custom_fields_values
	 * @param int $post_id
	 * @return array
	 */
	public static function add_data_custom_field_to_md5( array $custom_fields_values, $post_id ) {
		$post_type = get_post_type( $post_id );
		if( em_is_event($post_type) ){
			$EM_Event = em_get_event( $post_id, 'post_id' );
			foreach( $EM_Event->get_tickets() as $EM_Ticket ){
				$custom_fields_values[] = $EM_Ticket->ticket_name;
				$custom_fields_values[] = $EM_Ticket->ticket_description;
			}
		}
		return $custom_fields_values;
	}
	
	/**
	 * Hacky - Fired on admin_init to fix specific issues that must be done whilst loading the TE
	 * 1. Fixes issues with the translation editor, where ticket strings aren't registered for translation
	 * 2. Triggers a saving of event ticket translations when creating a new translation job, just in case users are going in from a previously translated event (more specifically, translated tickets) before v1.2.2
	 * @hooked admin_init
	 */
	public static function regularize_translation_editor(){
		global $pagenow;
		if( $pagenow == 'admin.php' && !empty($_GET['page']) && $_GET['page'] == 'wpml-translation-management/menu/translations-queue.php' ){
			/*
				Hacky #1
				This will fix issues with events created BEFORE EM-WPML 2.0 without an active Job ID. Without registering packages via self::event_save_meta(), the filter in Hacky #3 never reloads the page due to the bug associated in that part.
				Without this fix, users would need to close the TE first time around, and reload it so that job_id is in the URL and then Hacky #3
			
				Reproduce:
				1. Comment out this if statement.
				2. Create or find an event with bookings and one ticket created without this plugin active (so no ticket packages are registered).
				3. Reactivate the plugin (if necessary) and open a TE translation in any language not being translated so the url will have trid in query params.
				4. Ticket isn't available for translation.
				5. Cancel translation, go back to event in admin and load TE translation again, so now job_id is in the url.
				6. Hacky #2 is triggered and tickets appear.
			 */
			if( !empty($_GET['trid'])){
				$post_id = SitePress::get_original_element_id_by_trid($_GET['trid']);
				if( em_is_event($post_id) ){
					$EM_Event = em_get_event($post_id, 'post_id');
					static::event_save_meta( true, $EM_Event ); //saves tickets packages for events that were created before EM WPML 2.0, this will then trigger the bug where body text isn't translated and therefore letting our filter further down do its thing
				}
			}
			/*
				Hacky #2
				Adds ticket string packages and also updates the translations of those strings to any job which for any reason doesn't have up-to-date ticket registered string packages and/or translations. This includes:
			
				* jobs that already existed prior to EM-WPML 2.0 (meaning Hacky #1 won't help) - no tickets will appear otherwise
			        Reproduce :
					1. Uncomment if statement below
					2. Click to translate and open a TE of an event currently in progress
					2.a. if you don't have a on-going job, uncomment the if statement above, load the TE, close it and reload it with a job_id present.
					3. Tickets will not show up.
					4. Uncomment the if statement(s), reload, tickets show up.
			    * recurring events that had tickets etc. added/removed whilst an active job for a recurrence exists
			        Reproduce :
			        1. Create or open recurring event.
					2. Open up a recurrence of this recurring event in another tab
					3. Open the TE for any language to translate the recurrence.
					4. Go to recurring event and add a new ticket
					5. Refresh recurrence TE page and ticket will show up
					6. Translate the recurring event new ticket.
					7. comment out the if statement below
					8. reload TE of recurrence, translation is not there
			 */
			if( !empty($_GET['job_id']) ){
				$job_id = $_GET['job_id'];
				$job = new WPML_Post_Translation_Job( $job_id );
				$post_type = $job->get_post_type();
				if( em_is_event($post_type) ){
					$post_id = $job->get_original_element_id();
					//save the original to register package and strings
					$EM_Event = em_get_event($post_id, 'post_id');
					// this line is only relevant for events that had a Job ID BEFORE EM-WPML 2.0 because ticket packages were not registered. Saving the event and reloading the TE would fix this too.
					static::event_save_meta( true, $EM_Event ); //in theory, should be enough, but this will only register the ticket package, actual strings in package don't get added via WPML's methods.
					static::wpml_job_refresh_missing_ticket_package_strings( $EM_Event ); //specifically fixes jobs in-progress where package may be registered but strings for package are not
					// Hacky - since a translated event won't 100% exist yet (until translation is complete), therefore static::event_save_meta() on the translated EM_Event object won't work.
					static::wpml_job_fix_missing_translations_from_ticket_meta( $EM_Event, $job );
				}
			}
			/* 2
				Hacky - fixes problems where body doesn't get added to the TE, but only works for first time someone initializes the TE to translate a specific post, or if the original is updated.
				
				Reproduce:
				1. Comment out this filter below.
				2. Create an event or open up a current event, maybe update the event.
				3. Visit the TE and no body is ready to be translated, even though wpml_pb_should_body_be_translated is hooked in correctly via our static::init() function
			 */
			add_filter('wpml_job_assigned_to_after_assignment', function( $result, $job_id ){
				$job = new WPML_Post_Translation_Job( $job_id );
				$post_type = $job->get_post_type();
				if( em_is_event($post_type) ){
					global $wpdb;
					//$wpdb->query( $wpdb->prepare('UPDATE '.$wpdb->prefix."icl_translate SET field_translate = 1 AND field_finished = IF(field_data_translated=field_data, 0, 1) WHERE field_type='body' AND job_id=".absint($job_id)));
					$wpdb->update($wpdb->prefix.'icl_translate', array('field_translate'=>1), array('job_id'=> absint($job_id), 'field_type' => 'body'));
					if( $wpdb->rows_affected > 0 ){
						//reload page so this change is reflected
						$url = add_query_arg( array('trid'=>null, 'job_id'=>$job_id, 'update_needed'=>null, 'source_language_code' => null) );
						if( wp_redirect( esc_url_raw($url) ) ) exit();
					}
				}
				return $result;
			}, 10, 2);
		}
	}
	
	/**
	 * Hacky - any jobs that are in-process never get updated with translations if the recurring event has new tickets that are then translated. new jobs that aren't created yet (even though translations exist) for an event recurrence already do have translations.
	 * Not Crucial - it's possible that this is intended WPML behaviour. Translations are indeed populated, but not marked completed. In theory, I think it should be, because the translation is already there, this fixes that issue.
	 *
	 * Reproduce:
	 * 1. comment any calls to this function in this file
	 * 1.a. ensure calls to static::wpml_job_refresh_missing_ticket_package_strings() are uncommented
	 * 2. open up a recurrence in TE, so that a Job ID is assigned (reload so the job_id is in the URL, if not already)
	 * 3. in another window edit the recurring event by adding a new ticket and save recurring event
	 * 4. translate this ticket in the recurring event
	 * 5. reload the recurrence from step 2 and the ticket is there for translation, but not translated.
	 *
	 * Extra remarks:
	 * - This was my first attempt to fix the issue corrected by self::wpml_job_refresh_missing_ticket_package_strings() and there is some overlap, but this will fix the problem with new Jobs, vs. current jobs in-progress.
	 * - The below updates all string translations of tickets based off Job ID, at the same time, it marks the ticket translations as complete if translations exist
	 * - frankly, we could go further here for recurrences and mark things like body and title complete, as they are pre-translated already, but this is not so crucial.
	 *
	 * @param EM_Event $EM_Event
	 * @param WPML_Post_Translation_Job $job
	 */
	public static function wpml_job_fix_missing_translations_from_ticket_meta( EM_Event $EM_Event, WPML_Post_Translation_Job $job ){
		global $sitepress, $wpdb;
		//if( !$EM_Event->is_recurrence() ) return;
		$job_id = $job->get_id();
		$context = EM_POST_TYPE_EVENT.'-tickets-' . $EM_Event->post_id;
		$lang = $job->get_language_code();
		$locale = $sitepress->get_locale_from_language_code( $lang );
		$package = new WPML_Package(static::get_event_tickets_package($EM_Event));
		$package_id = $package->get_package_id();
		foreach( $EM_Event->get_tickets() as $EM_Ticket ){ /* @var EM_Ticket $EM_Ticket */
			if( !empty($EM_Ticket->ticket_meta['langs'][$locale]) ){
				foreach( $EM_Ticket->ticket_meta['langs'][$locale] as $k => $translation ){ //in theory it'll translate ticket_name and ticket_description... maybe future-proof for new fields?
					$string_id = icl_get_string_id( $EM_Ticket->$k, $context, $k.'_'.$EM_Ticket->ticket_id);
					$field_name = 'package-string-'.$package_id.'-'.$string_id;
					$wpdb->update($wpdb->prefix.'icl_string_translations', array('value'=> $translation), array('string_id'=>$string_id, 'language'=>$lang), array('value'=>'%s'), array('string_id'=>'%d', 'language'=>'%s'));
					$wpdb->update($wpdb->prefix.'icl_translate', array('field_data_translated'=> base64_encode($translation), 'field_finished'=>1), array('job_id'=>$job_id, 'field_type'=>$field_name), array('field_data_translated'=>'%s', 'field_finished'=>'%d'), array('job_id'=>'%d', 'language'=>'%s'));
				}
			}
		}
	}
	
	/**
	 * Hacky - This fixes the problem where wpml_register_string in self::event_save_meta() doesn't seem to add new strings to an active job. This adds MISSING strings and updates them with translations accordingly.
	 *
	 * Reproduce:
	 * 1. comment out any calls to this function on this file
	 * 1.a. maybe also comment out all calls to static::wpml_job_fix_missing_translations_from_ticket_meta()
	 * 2. open up a recurrence in TE, so that a Job ID is assigned (reload so the job_id is in the URL, if not already)
	 * 3. in another window edit the recurring event by adding a new ticket and save recurring event
	 * 4. reload the recurrence from step 2 and the ticket is there, but not translated.
	 *
	 * Remarks:
	 * - This function checks if an event already has a Translation Jobs currently in progress (in the TE), if there are any, it checks the current strings to make sure all tickets have been correctly added to the TE.
	 * - Whilst ticket_delete should handle this in theory, I saw some situations where tickets deleted didn't delete the TE packages, so it's quite easy to re-check that here on the }elseif{ statement.
	 *
	 * @param EM_Event $EM_Event
	 */
	public static function wpml_job_refresh_missing_ticket_package_strings(EM_Event $EM_Event ){
		global $wpdb, $sitepress;
		//we refresh this either on TE instantiation, or maybe we can find any open jobs for these posts and refresh during save_events
		// we can either get the Job ID via wp_icl_translations.trid > translation_id -> wp_icl_translation_status.translation_id > rid -> wp_icl_translate_job.rid > job_id (latest increment)
		// or check field_type based on package id, given each event has a package and only one query necessary
		$job_ids = $wpdb->get_col("SELECT job_id FROM {$wpdb->prefix}icl_translate WHERE field_type='original_id' AND field_data=".absint($EM_Event->post_id).' ORDER BY job_id DESC');
		$processed_languages = array();
		foreach( $job_ids as $job_id ){
			//we have an active job, so we check the relevant tickets for this event and add them for translation;
			$job = new WPML_Post_Translation_Job($job_id);
			$lang = $job->get_language_code();
			$locale = $sitepress->get_locale_from_language_code($lang);
			if( in_array($lang, $processed_languages) ) continue; // we only translate the most recent job of each language
			$processed_languages[] = $lang;
			//get all string ids in this event tickets package
			$context = EM_POST_TYPE_EVENT.'-tickets-' . $EM_Event->post_id;
			$sql = $wpdb->prepare("SELECT id, name FROM {$wpdb->prefix}icl_strings WHERE context=%s AND name LIKE %s", $context, 'ticket_%');
			$string_ids = $wpdb->get_results($sql, OBJECT_K);
			//get all strings in job belonging to this package
			$package = new WPML_Package( static::get_event_tickets_package($EM_Event) );
			$package_id = $package->get_package_id();
			$package_search = "package-string-$package_id-";
			$sql = $wpdb->prepare("SELECT REPLACE(field_type, '%s', '') as string_id FROM {$wpdb->prefix}icl_translate WHERE job_id=%d AND field_type LIKE %s", $package_search, $job_id, $package_search."%");
			$job_string_ids = $wpdb->get_col($sql);
			foreach( $job_string_ids as $k => $v ) $job_string_ids[$k] = str_replace("package-string-$package_id-", '', $v); //clean field types
			//now determine which strring IDs are not in the Job so we can add them.
			//$absent_string_ids = array_diff(array_keys($string_ids), $job_string_ids);
			$absent_string_ids = array_merge(array_diff(array_keys($string_ids), $job_string_ids), array_diff($job_string_ids, array_keys($string_ids)));
			//prep the loop
			$wp_icl_translate = array('job_id' => $job_id,'content_id' => '0','field_wrap_tag' => '','field_format' => 'base64','field_translate' => '1','field_finished' => '0');
			$EM_Tickets = $EM_Event->get_tickets();
			//loop and add new strings to the job
			foreach( $absent_string_ids as $absent_string_id ){
				$field_name = $string_ids[$absent_string_id]->name;
				if( preg_match('/^(ticket_name|ticket_description)_([0-9]+)$/', $field_name, $ticket_match) && !empty($EM_Tickets->tickets[$ticket_match[2]]) ){
					$EM_Ticket = $EM_Tickets->tickets[$ticket_match[2]];
					$prop = $ticket_match[1];
					$data = $wp_icl_translate;
					$data['field_type'] = "package-string-$package_id-".$absent_string_id;
					$data['field_data'] = base64_encode($EM_Ticket->$prop);
					if( !empty($EM_Ticket->ticket_meta['langs'][$locale][$prop]) ){
						$data['field_data_translated'] = base64_encode($EM_Ticket->ticket_meta['langs'][$locale][$prop]);
						$data['field_finished'] = 1;
					}
					$wpdb->insert( $wpdb->prefix.'icl_translate', $data);
				}elseif( !empty($ticket_match) ){
					//this might be unnessessary, as it's handled by em_ml_ticket_delete
					$EM_Ticket = new EM_Ticket();
					$EM_Ticket->ticket_id = $ticket_match[2];
					$EM_Ticket->event_id = $EM_Event->event_id;
					static::ticket_delete( true, $EM_Ticket );
				}
			}
		}
	}
	// END WPML Translation Editor Hooks
}
EM_WPML_IO::init();