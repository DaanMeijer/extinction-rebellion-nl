<?php
/*
Plugin Name: Events Manager and WPML Compatibility
Version: 2.0.1
Plugin URI: http://wp-events-plugin.com
Description: Integrates the Events Manager and WPML plugins together to provide a smoother multilingual experience (EM and WPML also needed)
Author: Marcus Sykes
Author URI: http://wp-events-plugin.com
*/

/*
Copyright (c) 2019, Marcus Sykes

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/*
EXTRA INSTALLATION STEPS

To close some gaps, extra steps are needed
- Pages
	- You should translate pages that EM overrides into different languages, meaning the pages you choose from our Events > Settings > Pages tab in the various panels, such as:
		- events page
		- locations page
		- categories page
		- edit events page
		- edit locations page
		- edit bookings page
		- my bookings page
 */

define('EM_WPML_VERSION','2.0');

class EM_WPML{
    public static function init(){
	    if( !class_exists('SitePress') || !defined('EM_VERSION') ) return; //only continue of both EM and WPML are activated
	    if( !apply_filters('wpml_setting', false, 'setup_complete') ) return; //only continue if WPML is configured
	    // ensure we're on the latest compatible versions of EM/WPML
	    if( EM_VERSION < 5.962 ){
	    	add_action('admin_notices', function(){
	    		echo '<div class="notice notice-error"><p>'. sprintf(__('This version of the %s plugin requires at least %s to work properly, please update as soon as possible.', 'events-manager-wpml'), '<code>Events Manager and WPML</code>', '<code>Events Manager 5.9.7</code>') .'</p></div>';
		    });
	    	return;
	    }
	    // check installation
	    if( version_compare(EM_WPML_VERSION, get_option('em_wpml_version')) ){
	        em_wpml_activate();
	    }
	    register_activation_hook( __FILE__, 'em_wpml_activate' );
	    register_deactivation_hook( __FILE__, 'em_wpml_deactivate' );
	    add_action('events_manager_updated', 'EM_WPML::events_manager_updated');
	    add_action('em_ml_init', 'EM_WPML::em_ml_init');
		
		// EM_ML filters - register translateable languages and currently displayed language
		add_filter('em_ml_langs','EM_WPML::em_ml_langs');
		add_filter('em_ml_wplang','EM_WPML::em_ml_wplang');
		add_filter('em_ml_current_language','EM_WPML::em_ml_current_language');
		
		// original event/location filters
		add_filter('em_ml_get_original','EM_WPML::get_original',10,2);
		add_filter('em_ml_is_original','EM_WPML::is_original',10,2);

		// other functions
	    add_filter('em_ml_get_translations', 'EM_WPML::get_translations', 10, 2);
		add_filter('em_ml_get_translated_post_id','EM_WPML::get_translated_post_id',10,3);
		add_filter('em_ml_get_the_language','EM_WPML::get_the_language',10,2);
		add_filter('em_ml_get_translation_id','EM_WPML::get_translation_id',10,3);
		add_filter('em_ml_set_language_by_post_ids', 'EM_WPML::set_language_by_post_ids', 10, 6);
	    add_filter('em_ml_attach_translations', 'EM_WPML::attach_translations', 10, 5);
	
	    // wpml hooks
		add_action('wpml_switch_language', 'EM_WPML::wpml_switch_language', 10, 1);
	
	    // continue initialization
	    if( is_admin() ){
		    include_once('em-wpml-admin.php');
	    }
	    include('em-wpml-permalinks.php');
	    include('em-wpml-io.php');
	
	    // add js vars that override EM's
	    add_filter('em_wp_localize_script', 'EM_WPML::em_wp_localize_script', 100);
    }
	
	/**
	 * Runs once EM_ML is initialized. Decides whether to show or hide untranslated events and locations in EM searches based on the Custom Types settings in WPML.
	 */
    public static function em_ml_init(){
	    global $sitepress;
	    $custom_post_settings = $sitepress->get_setting('custom_posts_sync_option');
	    EM_ML_Search::$show_untranslated['location'] = !empty($custom_post_settings[EM_POST_TYPE_LOCATION]) && $custom_post_settings[EM_POST_TYPE_LOCATION] == 2;
	    EM_ML_Search::$show_untranslated['event'] = !empty($custom_post_settings[EM_POST_TYPE_EVENT]) && $custom_post_settings[EM_POST_TYPE_EVENT] == 2;
	    EM_ML_Search::$show_untranslated['event-recurring'] = !empty($custom_post_settings['event-recurring']) && $custom_post_settings['event-recurring'] == 2;
    }
    
    /**
     * Localizes the script variables
     * @param array $em_localized_js
     * @return array
     */
    public static function em_wp_localize_script($em_localized_js){
        global $sitepress;
        $em_localized_js['ajaxurl'] = add_query_arg(array('lang'=>$sitepress->get_current_language()), $em_localized_js['ajaxurl']);
        $em_localized_js['locationajaxurl'] = add_query_arg(array('lang'=>$sitepress->get_current_language()), $em_localized_js['locationajaxurl']);;
		if( get_option('dbem_rsvp_enabled') ){
		    $em_localized_js['bookingajaxurl'] = add_query_arg(array('lang'=>$sitepress->get_current_language()), $em_localized_js['bookingajaxurl']);
		}
        return $em_localized_js;
    }
	
	/**
	 * Replicates a language switch within EM_ML when WPML has switched languages, the equivalent of doing EM_ML::switch_to_language()
	 * @param $lang
	 */
    public static function wpml_switch_language( $lang ){
    	global $sitepress;
        EM_ML::$current_language = $sitepress->get_locale_from_language_code($lang);
    }
    
    /**
     * Takes a post id, checks if the current language isn't the default language and returns a translated post id if it exists, used to switch our overriding pages or post types
     * If $blog_id is supplied and the site is in EM MS Global mode, the parent function calling the filter should have already switched blogs to $blog_id, otherwise the current blog ID is assumed.
     * @param int|null $translated_post_id
     * @param int $post_id
     * @param string $post_type
     * @return int
     */
    public static function get_translated_post_id( $translated_post_id, $post_id, $post_type ){
    	if( $translated_post_id !== null ) return $translated_post_id; // EM already knows
        if( function_exists('wpml_object_id_filter') ) return wpml_object_id_filter($post_id, $post_type); //3.2 compatible
        return icl_object_id($post_id, $post_type); // <3.2 compatible
    }
	
	/**
	 * Filters the language and provides the language used in $object in the WPLANG compatible format
	 * @param string $lang language code passed by filter (default_locale)
	 * @param EM_Event|EM_Location $object
	 * @return string language code
	 */
	public static function get_the_language( $lang, $object ){
	    global $sitepress;
	    if( $lang !== null ) return $lang; // EM already knows
	    if( !empty($object) ){
	        $sitepress_lang = $sitepress->get_language_for_element($object->post_id, 'post_'.$object->post_type);
	        if( !empty($sitepress_lang) ){
	        	$lang = $sitepress->get_locale_from_language_code($sitepress_lang);
	        }
	    }
	    return $lang;
	}
	
    /**
     * Gets the post id of the default translation for this event, location or post. It'll return the same post_id if no translation is available.
     * If $language is false, we return a different translation in precedence of the original id, default language id, or next available.
     * @param int|null $post_id
     * @param EM_Event|EM_Location|WP_Post $object
     * @param string $lang
     * @return int
     * 
     */
    public static function get_translation_id( $post_id, $object, $lang ){
        global $sitepress;
        if( $post_id !== null ) return $post_id; // EM already knows
        if( !empty($object->post_id) && !empty($object->post_type) ){
            //clean $type to include post_ prefix for WPML
            $wpml_post_type = em_is_event($object) || em_is_location($object) ? 'post_'.$object->post_type:$object->post_type;
    	    //get WPML code from locale we have been provided and then find the relevant translation
    	    $lang_code = $lang  ? $sitepress->get_language_code_from_locale($lang) : false;
    	    //get translations for this object and loop through them
    	    $default_language = $sitepress->get_default_language();
    	    $trid = $sitepress->get_element_trid($object->post_id, $wpml_post_type);
    	    $translations = $sitepress->get_element_translations($trid, $wpml_post_type);
    	    //return translation
    	    if( !empty($lang_code) && !empty($translations[$lang_code]->element_id) ){
    	        //if we were supplied a language to search for, check if it's available, if not continue searching
    	        return $translations[$lang_code]->element_id;
    	    }elseif( empty($lang) ){
    	        //if no language was supplied we just get the next available translation
    	        //try and find the original language if there is one that's not this object we're requesting
        	    foreach( $translations as $translation ){
    				if( $translation->element_id != $object->post_id && $translation->original ){
    					return $translation->element_id;
    				}
        	    }
        	    //otherwise, give preference to default language, then any other language
    	        if( !empty($translations[$default_language]) && $translations[$default_language]->element_id != $object->post_id ){
        	        //default language 
        	        return $translations[$default_language]->element_id;
        	    }else{
        	        //find the next translation we can find
            	    foreach( $translations as $translation ){
        				if( $translation->element_id != $object->post_id ){
        					//if not, use the first available translation we find
        					return $translation->element_id;
        				}
            	    }
        	    }
    	    }
        }
        return $post_id;
    }
	
	/**
	 * @param $EM_Object
	 * @return stdClass
	 */
    public static function get_wpml_element_meta( $EM_Object ){
    	$element = new stdClass();
	    if( !empty($EM_Object->post_type) ){
		    $element->element_type = 'post_'. $EM_Object->post_type;
		    $element->element_id = $EM_Object->post_id;
	    }elseif( !empty($EM_Object->taxonomy) && !empty($EM_Object->term_id) ){
		    $element->element_type = 'tax_'. $EM_Object->taxonomy;
		    $element->element_id = $EM_Object->term_id;
	    }
	    return $element;
    }
	
	/**
	 * @param array $translations
	 * @param EM_Event|EM_Location|EM_Taxonomy_Term $EM_Object
	 * @return array
	 * @see EM_ML::get_translations()
	 */
    public static function get_translations( $translations, $EM_Object ){
    	global $sitepress;
	    if( !empty($translations) ) return $translations; // EM already knows
	    $translations = array(); //reset the default
    	$element = EM_WPML::get_wpml_element_meta( $EM_Object );
    	$object_class = get_class($EM_Object);
    	$trid = $sitepress->get_element_trid( $element->element_id, $element->element_type );
    	$wpml_translations = $sitepress->get_element_translations( $trid, $element->element_type );
    	foreach( $wpml_translations as $wpml_translation ){
    		//generate the object
		    $EM_Object = new $object_class( $wpml_translation->element_id, 'post_id' ); //Taxomony_Term or EM_Object (we overload if a taxonomy)
		    //determine the language and add to array
		    $object_language = $sitepress->get_locale( $wpml_translation->language_code );
			//add to array
		    $translations[$object_language] = $EM_Object;
	    }
    	return $translations;
    }
	
	/**
	 * @param bool $result                      Value passed by filter, will always be true.
	 * @param EM_Event|EM_Location $object      Must be EM_Event or EM_Location
	 * @return boolean
	 * @see EM_ML::is_original()
	 */
	public static function is_original( $result, $object = null ){
		global $pagenow;
		if( $result !== null ) return $result; // EM already knows
		//if we're in admin, we need to check if we're adding an new WPML translation
    	if( is_admin() ){
    	    //check we are adding a new translation belonging to a trid set
    	    if( $pagenow == 'post-new.php' && !empty($_REQUEST['trid']) ) return false;
            //if this is a translation being edited (not first time), WPML submits this variable
            if( $pagenow == 'post.php' && !empty($_REQUEST['icl_translation_of']) ) return false;
            //if this is a translation submitted, we have a trid and post ID we can use to check out
            if( $pagenow == 'post.php' && !empty($_REQUEST['icl_trid']) && $object->post_id == $_REQUEST['post_ID'] ){
            	$original_post_id = SitePress::get_original_element_id_by_trid($_REQUEST['icl_trid']);
				return $original_post_id == $object->post_id || $original_post_id === false;
            }
		    if( !empty($_REQUEST['action'])  && $_REQUEST['action'] == 'make_duplicates' && !empty($_REQUEST['post_id']) && !empty($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'make_duplicates') ){
			    // we know the originating post ID, also if we are dealing with another object of same post type during duplication, we must assume it's a translation (this allows for correct checks of a location within an event)
			    if( $object->post_id == $_REQUEST['post_id'] || ( $_REQUEST['post_id'] != $object->post_id && $object->post_type == get_post_type( absint($_REQUEST['post_id']) ) ) ){
			    	//we need to double-check the originating post id, because it's possible to duplicate a translation of an original
			    	$master_post_id = absint($_REQUEST['post_id']);
			    	$original_post_id = SitePress::get_original_element_id($master_post_id, 'post_'.$object->post_type); // get the real orginal
				    EM_ML::$originals_cache[$object->blog_id][$object->post_id] = $original_post_id; //set cache for later
				    return $object->post_id == $_REQUEST['post_id'];
			    }
		    }
		}
		//if we got this far, check that $object has a post_id as EM_Event and EM_Location would have, and get the original translation via WPML
		if( !empty($object->post_id) ){
		    if( !empty(EM_ML::$originals_cache[$object->blog_id][$object->post_id]) ){
		        $original_post_id = EM_ML::$originals_cache[$object->blog_id][$object->post_id];
		    }else{
		        $original_post_id = SitePress::get_original_element_id($object->post_id, 'post_'.$object->post_type);
		    }
		    //save original ID if not a false value
		    if( $original_post_id !== false ) EM_ML::$originals_cache[$object->blog_id][$object->post_id] = $original_post_id;
		    //return whether this is the original post or not
		    return $original_post_id == $object->post_id || $original_post_id === false;
		}
		return $result;
	}
	
	/**
	 * Returns the original EM_Location object from the provided EM_Location object
	 * @param EM_Location|EM_Event|null $original
	 * @param EM_Location|EM_Event $EM_Object
	 * @return EM_Location|EM_Event
	 */
	public static function get_original( $original, $EM_Object ){
		if( $original !== null ) return $original; // EM already knows
        // when duplicating, WPML might not be able to get the original ID just yet... we short circuit things here
        if( !empty($_REQUEST['action'])  && $_REQUEST['action'] == 'make_duplicates' && !empty($_REQUEST['post_id']) && !empty($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'make_duplicates') ){
            // we know the original post ID, also if we are dealing with another object of same post type during duplication, we must assume it's a translation (this allows for correct checks of a location within an event)
            if( $EM_Object->post_id == $_REQUEST['post_id'] || ( $_REQUEST['post_id'] != $EM_Object->post_id && $EM_Object->post_type == get_post_type($_REQUEST['post_id']) ) ){
	            $original_post_id = absint($_REQUEST['post_id']);
            }
        }
        //find the original post id via WPML if we didn't already
        if( empty($original_post_id) ){
	        $original_post_id = SitePress::get_original_element_id($EM_Object->post_id, 'post_'.$EM_Object->post_type);
	        //check a few admin specific stuff if a standard check didn't work, in case we're in the admin area translating via WPML
	        if( empty($original_post_id) && is_admin() ){
		        if( !empty($_REQUEST['trid']) || !empty($_REQUEST['icl_trid']) ){
			        $trid = !empty($_REQUEST['trid']) ? $_REQUEST['trid'] : $_REQUEST['icl_trid'];
			        //we are adding a new translation belonging to a trid set
			        $original_post_id = SitePress::get_original_element_id_by_trid($trid);
		        }elseif( !empty($_REQUEST['icl_translation_of']) ){
			        //a new translation has just been submitted
			        $translation_of = $_REQUEST['icl_translation_of']; //could be a translation from another translation, e.g. try adding a translation from a second language
			        $original_post_id = SitePress::get_original_element_id($translation_of, 'post_'.$EM_Object->post_type);
		        }
	        }
        }
        //save to the cache (whether already saved or not)
        EM_ML::$originals_cache[$EM_Object->blog_id][$EM_Object->post_id] = $original_post_id;
        //if the post_ids don't match then the original translation is different to the one passed on, so switch the $object to that translation
        if( $original_post_id != $EM_Object->post_id ){
            //get the EM_Event or EM_Location object
            if( em_is_event($EM_Object) ){
	            $original = em_get_event($original_post_id, 'post_id');
            }elseif( em_is_location($EM_Object) ){
	            $original = em_get_location($original_post_id, 'post_id');
            }
        }else{
        	$original = $EM_Object;
        }
	    return $original;
	}
	
	/*
	 * EM_ML hooks - register available languages and current language displayed
	 */
	
	/**
	 * Provides an array of languages that are translateable by WPML
	 * @return array In the form of locale => language name e.g. array('fr_FR'=>'French');
	 */
	public static function em_ml_langs(){
		global $sitepress;
		$sitepress_langs = $sitepress->get_active_languages();
		$langs = array();
		foreach($sitepress_langs as $lang){
			$langs[$lang['default_locale']] = $lang['display_name'];
		}
		return $langs;
	}
	
	/**
	 * Returns the default language locale
	 * @return string
	 */
	public static function em_ml_wplang(){
		global $sitepress;
		$sitepress_langs = $sitepress->get_active_languages();
		$sitepress_lang = $sitepress->get_default_language();
		if( !empty($sitepress_langs[$sitepress_lang]) ){
		    return $sitepress_langs[$sitepress_lang]['default_locale'];
		}else{
		    return get_locale();
		}
	}
	
	/**
	 * Returns the current language locale
	 * @return string
	 */
	public static function em_ml_current_language(){
	    global $sitepress;
		$sitepress_lang = $sitepress->get_current_language();
		return $sitepress->get_locale_from_language_code($sitepress_lang);
	}
	
	/**
	 * @param boolean $result
	 * @param string $locale
	 * @param array $post_ids
	 * @param string $post_type
	 * @param int $blog_id
	 * @param boolean $update
	 * @return boolean
	 */
	public static function set_language_by_post_ids( $result, $locale, $post_ids, $post_type, $blog_id = null, $update = false ){
		global $sitepress;
		if( $result ){
			if( EM_MS_GLOBAL && !empty($blog_id) ) switch_to_blog($blog_id);
			$lang = $sitepress->get_language_code_from_locale( $locale );
			$trid = false;
			foreach( $post_ids as $post_id ){
				if( $update ) $trid = $sitepress->get_element_trid( $post_id, 'post_'.$trid );
				do_action( 'wpml_set_element_language_details', array(
					'element_id' => absint($post_id),
					'language_code' => $lang,
					'element_type' => 'post_'.$post_type,
					'check_duplicates' => false,
					'trid' => $trid,
				));
			}
			if( EM_MS_GLOBAL && !empty($blog_id) ) restore_current_blog();
			return $result;
		}
		return $result;
	}
	
	public static function attach_translations( $result, $locale, $post_ids_map, $post_type, $blog_id ){
		global $wpdb, $sitepress;
		if( $result ){
			$lang = $sitepress->get_language_code_from_locale($locale);
			//sanitize
			$post_ids = array();
			foreach( $post_ids_map as $original_post_id => $post_id ){
				$post_ids[$original_post_id] = absint($post_id);
			}
			//run queries
			$original_trids_sql = $wpdb->prepare("SELECT element_id, trid, language_code FROM {$wpdb->prefix}icl_translations WHERE element_id IN (". implode(',', array_keys($post_ids)) .") AND element_type=%s", 'post_'.$post_type);
			$original_trids = $wpdb->get_results($original_trids_sql, OBJECT_K);
			// delete previous translations for this post id and add them again, easier in two queries than retroactively updating
			$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}icl_translations WHERE element_id IN (". implode(',', $post_ids) .") AND element_type=%s", 'post_'.$post_type) );
			//go through each recurrence in current language
			$inserts = array();
			foreach( $post_ids as $original_post_id => $post_id ){
				if( !empty($original_trids[$original_post_id] )){
					$trid = $original_trids[$original_post_id]->trid;
					$original_lang = $original_trids[$original_post_id]->language_code;
					// prepare for bulk add
					$translation_id = $wpdb->get_var($wpdb->prepare("SELECT translation_id FROM {$wpdb->prefix}icl_translations WHERE (element_id=%s OR trid=%s) AND language_code=%s AND element_type=%s", $post_id, $trid, $lang, 'post_'.$post_type));
					if( !$translation_id ){
						//save a value into WPML table
						$inserts[] = $wpdb->prepare("(%d, %d, %s, %s, %s)", array($post_id, $trid, 'post_'.$post_type, $lang, $original_lang));
					}else{
						//if there's a ongoing job, this will attach to that job
						$wpdb->update( $wpdb->prefix.'icl_translations', array('element_id' => $post_id, 'trid' => $trid, 'source_language_code'=>$original_lang), array('translation_id'=>$translation_id));
					}
				}
				EM_ML::$originals_cache[$blog_id][$post_id] = $original_post_id;
			}
			if( count($inserts) > 0 ){
				//$wpdb->insert($wpdb->prefix.'icl_translations', array('element_type'=>"post_".EM_POST_TYPE_EVENT, 'trid'=>$post_id, 'element_id'=>$post_id, 'language_code'=>$lang));
				$query = "INSERT INTO ".$wpdb->prefix."icl_translations (element_id, trid, element_type, language_code, source_language_code) VALUES ".implode(',', $inserts);
				$result = $wpdb->query( $query ) !== false;
			}
		}
		return $result;
	}
	
	public static function events_manager_updated(){
		//sync translations for the first time since we added event_language and location_language fields
		if( get_option('dbem_version') && get_option('dbem_version') < 5.9616  ){
			if( !class_exists('EM_WPML_Admin') ) include_once('em-wpml-admin.php');
			EM_WPML_Admin::sync_translations();
		}
	}
	
}
add_action('wpml_after_init', 'EM_WPML::init'); //should be before init priority 10 which is when EM_ML loads

function em_wpml_activate() {
	if( method_exists('EM_ML', 'toggle_languages_index') ) EM_ML::toggle_languages_index('add');
	if( !class_exists('EM_WPML_Admin') ) include_once('em-wpml-admin.php');
	EM_WPML_Admin::update();
	EM_WPML_Admin::sync_translations();
	update_option('em_wpml_version', EM_WPML_VERSION);
}

function em_wpml_deactivate(){
	if( method_exists('EM_ML', 'toggle_languages_index') ) EM_ML::toggle_languages_index('remove');
}
register_activation_hook( __FILE__,'em_wpml_activate');