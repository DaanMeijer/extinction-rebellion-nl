<?php
class EM_WPML_Permalnks {

    public static function init(){
		remove_filter('rewrite_rules_array',array('EM_Permalinks','rewrite_rules_array'));
		add_filter('rewrite_rules_array',array('EM_WPML_Permalnks','rewrite_rules_array'));
	    add_action ( 'parse_query', 'EM_WPML_Permalnks::em_ical_item', 9 );
	    //rewrite language switcher links for our taxonomies if overriden with formats, because EM forces use of a page template by modifying the WP_Query object
	    //this confuses WPML since it checks whether WP_Query is structured to show a taxonomy page
	    add_filter('icl_ls_languages','EM_WPML_Permalnks::icl_ls_languages');
    }
    
    /**
     * This function replaces EM's EM_Permalinks::rewrite_rules_array() function/filter so that we switch languages to the default language when rewriting permalinks. 
     * Otherwise some of our permalink paths will be based off the translated pages and not the main EM pages.  
     * @param array $rules
     * @return array
     */
    public static function rewrite_rules_array( $rules ){
    	global $sitepress;
    	//check and switch blog to original language if necessary
    	$current_lang = $sitepress->get_current_language();
    	$default_lang = $sitepress->get_default_language();
    	if( $current_lang != $default_lang ) $sitepress->switch_lang($default_lang);
    	//run the EM permalinks within the original language context
    	$em_rules = EM_Permalinks::rewrite_rules_array(array());
    	$em_rules = self::rewrite_rules_array_langs($em_rules);
    	//switch blog back to current languate
    	if( $current_lang != $default_lang ) $sitepress->switch_lang($current_lang);
		return $em_rules + $rules;
    }
    
    /**
     * Adds extra permalink structures to the rewrites array to account for different variations of tralsnated pages.
     * Specifically, this deals with the calendar day pages showing a list of events on a specific date, which has a dynamic date endpoint in the URL.
     * @param array $em_rules
     * @return array
     */
    public static function rewrite_rules_array_langs($em_rules){
        global $sitepress;
		$events_page = get_post( get_option('dbem_events_page') );
		//Detect if there's an event page
		if( is_object($events_page) ){
			//get event page, current language, translations and real wpml home url of this site for use later on
			$trid = $sitepress->get_element_trid($events_page->ID);
		    $translations = $sitepress->get_element_translations($trid);
		    $current_lang = $sitepress->get_current_language();
		    $wpml_url_converter = new WPML_URL_Converter_Url_Helper();
		    $home_url = $wpml_url_converter->get_abs_home();
		    //get settings for current URL structure
		    $wpml_settings = $sitepress->get_settings();
		    $language_negotiation_type = !empty($wpml_settings['language_negotiation_type']) ? $wpml_settings['language_negotiation_type'] : 0;
		    //go through each translation and generate a permalink rule for the calendar day page
		    foreach( $translations as $lang => $translation ){
		        if( $lang != $current_lang && $translation->post_status == 'publish'){
		        	//get translated urls for processing permalink matching translation of events page
				    $home_url_translated = $sitepress->convert_url($home_url, $lang); //translated base URL
				    $event_page_translated = get_permalink($translation->element_id); //translated events page used as base for rewrite rule
				    //if we are using parameters for the language we need to strip the parameter from the urls here for correct insertion into rewrite rules
				    if( $language_negotiation_type == '3' ){
				    	$home_url_translated_parts = explode('?', $home_url_translated);
				    	$home_url_translated = $home_url_translated_parts[0];
				    	$event_page_translated_parts = explode('?', $event_page_translated);
				    	$event_page_translated = $event_page_translated_parts[0];
				    }
				    //remove the base URL from the events slug
		        	$events_slug = urldecode( preg_replace('/\/$/', '', str_replace(trailingslashit($home_url_translated), '', $event_page_translated)) );
				    //remove the language query parameter from the start of the link if we have directory-based permalinks e.g. /fr/events/etc/ => /events/etc/
				    if( $language_negotiation_type == '2' ) $events_slug = preg_replace('/^'.$lang.'\//', '', $events_slug);
				    //add the rewrite preg structure to end of events slug
				    $events_preg = trailingslashit($events_slug).'(\d{4}-\d{2}-\d{2})$';
				    //NUANCE - we can only add the rewrite rule if the events page slug of translations isn't the same as the original page, otherwise see get_page_by_path_filter workaround by WPML team
				    if( empty($em_rules[$events_preg]) ){
				    	$em_rules[$events_preg] = 'index.php?page_id='.$translation->element_id.'&calendar_day=$matches[1]'; //event calendar date search
		        	}
		        }
		    }
		}
		//echo "<pre>"; print_r(trailingslashit(home_url())); echo "</pre>"; die();
        return $em_rules;
    }
	
	/**
	 * Hooks into icl_ls_languages and fixes links for when viewing an events list page specific to a calendar day.
	 * @param array $langs
	 * @return array
	 */
	public static function icl_ls_languages($langs){
		global $wp_rewrite;
		//modify the URL if we're dealing with calendar day URLs
		if ( !empty($_REQUEST['calendar_day']) && preg_match('/\d{4}-\d{2}-\d{2}/', $_REQUEST['calendar_day']) ) {
			$query_args = EM_Calendar::get_query_args( array_intersect_key(EM_Calendar::get_default_search($_GET), EM_Events::get_post_search($_GET, true) ));
			if( $wp_rewrite->using_permalinks() ){
				//if using rewrites, add as a slug
				foreach( $langs as $lang => $lang_array ){
					$lang_url_parts = explode('?', $lang_array['url']);
					$lang_url_parts[0] = trailingslashit($lang_url_parts[0]). $_REQUEST['calendar_day'].'/';
					$langs[$lang]['url'] = esc_url_raw(add_query_arg($query_args, implode('?', $lang_url_parts)));
				}
			}else{
				$query_args['calendar_day'] = $_REQUEST['calendar_day'];
				foreach( $langs as $lang => $lang_array ){
					$langs[$lang]['url'] = esc_url_raw(add_query_arg($query_args, $lang_array['url']));
				}
			}
		}
		return $langs;
	}
	
	/**
	 * Jumps in before em_ical_item() earlier in the parse_query hook and fixes issues with multilingual ical links
	 */
	public static function em_ical_item(){
    	global $wp_query;
	    if( !empty($wp_query) && $wp_query->get('ical') && defined( 'ICL_SITEPRESS_VERSION' ) && $wp_query->get('_wpml_backup') ) {
		    $wp_query->set(EM_POST_TYPE_EVENT, $wp_query->get('_wpml_backup')[EM_POST_TYPE_EVENT]);
	    }
    }
}
EM_WPML_Permalnks::init();