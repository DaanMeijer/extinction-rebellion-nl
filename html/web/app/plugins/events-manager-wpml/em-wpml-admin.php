<?php
class EM_WPML_Admin {
    
	public static function init(){
		global $pagenow;
		add_filter('em_ml_admin_original_event_link','EM_WPML_Admin::em_ml_admin_original_event_link',10,1);
		$settings_pages = EM_ML_Admin::settings_pages();
		if( $pagenow == 'edit.php' && !empty($_REQUEST['page']) && in_array($_REQUEST['page'], $settings_pages) ){
		    add_action('admin_init', 'EM_WPML_Admin::options_redirect');
		}
		// sync tools
		add_action('em_options_page_panel_admin_tools', 'EM_WPML_Admin::em_options_page_panel_admin_tools');
		add_action('admin_init', 'EM_WPML_Admin::admin_tools_tables_sync_action');
	}
	
	public static function options_redirect(){
	    global $sitepress;
	    //check if we've redirected already
	    if( !empty($_REQUEST['wpmlredirect']) ){
	        global $EM_Notices; /* @var $EM_Notices EM_Notices */
	        $EM_Notices->add_info(__('You have been redirected to the main language of your site for this settings page. All translatable settings can be translated here.', 'events-manager-wpml'));
	    }
	    //redirect users if this isn't the main language of the blog
	    if( EM_ML::$current_language == EM_ML::$wplang ) return;
	    $sitepress_langs = $sitepress->get_active_languages();
	    foreach( $sitepress_langs as $lang => $lang_info ){
	        if( $lang_info['default_locale'] == EM_ML::$wplang ){
                wp_redirect(admin_url('edit.php?post_type=event&page='.$_REQUEST['page'].'&wpmlredirect=1&lang='.$lang));
                exit();
	        }
	    }
	}
	
	public static function em_ml_admin_original_event_link( $link ){
	    global $EM_Event;
    	if( empty($EM_Event->event_id) && !empty($_REQUEST['trid']) ){
			$post_id = SitePress::get_original_element_id_by_trid($_REQUEST['trid']);
			$original_event_link = em_get_event($post_id,'post_id')->get_edit_url();
		}
		return $link;
	}
	
	public static function sync_translations(){
    	$sync_to_wpml = static::sync_to_wpml();
    	$sync_from_wpml = static::sync_from_wpml();
    	return $sync_to_wpml && $sync_from_wpml;
	}
	
	public static function sync_from_wpml(){
		global $wpdb, $sitepress;
		$results = array();
		// get all events for this blog and update them with the relevant language within the em_event and em_location tables
		$multisite_cond = $where_multisite_cond = '';
		if( EM_MS_GLOBAL ){
			$blog_id = absint(get_current_blog_id());
			if( is_main_site() ){
				$multisite_cond = "AND (blog_id IS NULL OR blog_id=0 OR blog_id=$blog_id)";
				$where_multisite_cond = "WHERE (blog_id IS NULL OR blog_id=0 OR blog_id=$blog_id)";
			}else{
				$multisite_cond = "AND blog_id=$blog_id";
				$where_multisite_cond = "WHERE blog_id=$blog_id";
			}
		}
		// sync languages
		// get all translated languages for the current blog in WPML
		$langs = $wpdb->get_col('SELECT language_code FROM '.$wpdb->prefix.'icl_translations GROUP BY language_code');
		foreach( $langs as $lang ){
			$language = $sitepress->get_locale_from_language_code($lang);
			// events - language sync
			$vars = array($language, $lang, 'post_'.EM_POST_TYPE_EVENT);
			$sql = 'UPDATE '.EM_EVENTS_TABLE." SET event_language=%s WHERE post_id IN (SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE language_code=%s AND (element_type=%s OR element_type='post_event-recurring')) $multisite_cond";
			$results[] = $wpdb->query( $wpdb->prepare($sql, $vars) ) !== false;
			// locations
			$vars = array($language, $lang, 'post_'.EM_POST_TYPE_LOCATION);
			$sql = 'UPDATE '.EM_LOCATIONS_TABLE." SET location_language=%s WHERE post_id IN (SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE language_code=%s AND element_type=%s) $multisite_cond";
			$results[] = $wpdb->query( $wpdb->prepare($sql, $vars) ) !== false;
		}
		// sync parents
		$sql = $wpdb->prepare("
				SELECT child.element_id AS child_id, parent.element_id AS parent_id, child.element_type AS type
				FROM {$wpdb->prefix}icl_translations child
				LEFT JOIN {$wpdb->prefix}icl_translations parent ON parent.trid=child.trid
			    WHERE
			        parent.source_language_code IS NULL
			        AND child.source_language_code IS NOT NULL
			        AND parent.element_type IN ('%s', '%s', 'post_event-recurring')
			        AND child.element_type IN ('%s', '%s', 'post_event-recurring')
			        AND child.element_id IS NOT NULL;
			", 'post_'. EM_POST_TYPE_EVENT, 'post_'.EM_POST_TYPE_LOCATION, 'post_'. EM_POST_TYPE_EVENT, 'post_'.EM_POST_TYPE_LOCATION );
		$parent_relations = $wpdb->get_results( $sql, OBJECT_K );
		$event_id_sql = 'SELECT post_id, event_id AS id FROM '.EM_EVENTS_TABLE.' WHERE post_id IN (%s,%s)'.$multisite_cond;
		$location_id_sql = 'SELECT post_id, location_id AS id FROM '.EM_LOCATIONS_TABLE.' WHERE post_id IN (%s,%s)'.$multisite_cond;
		$parent_inserts = array('events' => array(), 'locations' => array());
		foreach( $parent_relations as $child_post_id => $relation ){
			//convert the post_id relation into an event_id relation
			if( $relation->type == 'post_'.EM_POST_TYPE_LOCATION ){
				$ids = $wpdb->get_results( $wpdb->prepare($location_id_sql, array($relation->child_id, $relation->parent_id)), OBJECT_K);
				$type = 'locations';
			}else{
				$ids = $wpdb->get_results( $wpdb->prepare($event_id_sql, array($relation->child_id, $relation->parent_id)), OBJECT_K);
				$type = 'events';
			}
			$child_id = $ids[$relation->child_id]->id;
			$parent_id = $ids[$relation->parent_id]->id;
			$parent_inserts[$type][] = $wpdb->prepare('(%d, %d, %d, 1)', $child_id, $child_post_id, $parent_id);
		}
		if( !empty($parent_inserts['events']) ){
			$wpdb->query('INSERT INTO '.EM_EVENTS_TABLE.' (event_id, post_id, event_parent, event_translation) VALUES '. implode(',', $parent_inserts['events']) .' ON DUPLICATE KEY UPDATE event_parent=VALUES(event_parent), event_translation=1');
		}
		if( !empty($parent_inserts['locations']) ){
			$wpdb->query('INSERT INTO '.EM_LOCATIONS_TABLE.' (location_id, post_id, location_parent, location_translation) VALUES '. implode(',', $parent_inserts['locations']) .' ON DUPLICATE KEY UPDATE location_parent=VALUES(location_parent), location_translation=1');
		}
		// sync the em_event/location tables with post meta - in order to avoid potentially thousands or more individual update/insert statements, we'll just delete all language and parent postmeta values for events/locations and then recreate them again.
		$results[] = $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ('_event_language', '_event_parent', '_event_translation') AND post_id IN ( SELECT post_id FROM ".EM_EVENTS_TABLE." $where_multisite_cond )") !== false;
		$results[] = $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ('_location_language', '_location_parent', '_location_translation') AND post_id IN ( SELECT post_id FROM ".EM_LOCATIONS_TABLE." $where_multisite_cond )") !== false;
		$results[] = $wpdb->query("INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) (SELECT post_id, '_event_language', event_language FROM ".EM_EVENTS_TABLE." $where_multisite_cond)") !== false;
		$results[] = $wpdb->query("INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) (SELECT post_id, '_event_parent', event_parent FROM ".EM_EVENTS_TABLE." WHERE event_parent IS NOT NULL $multisite_cond)") !== false;
		$results[] = $wpdb->query("INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) (SELECT post_id, '_event_translation', 1 FROM ".EM_EVENTS_TABLE." WHERE event_translation=1 $multisite_cond)") !== false;
		$results[] = $wpdb->query("INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) (SELECT post_id, '_location_language', location_language FROM ".EM_LOCATIONS_TABLE." $where_multisite_cond)") !== false;
		$results[] = $wpdb->query("INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) (SELECT post_id, '_location_parent', location_parent FROM ".EM_LOCATIONS_TABLE." WHERE location_parent IS NOT NULL $multisite_cond)") !== false;
		$results[] = $wpdb->query("INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) (SELECT post_id, '_location_translation', 1 FROM ".EM_LOCATIONS_TABLE." WHERE location_translation=1 $multisite_cond)") !== false;
		return !in_array(false, $results);
	}
	
	public static function sync_to_wpml(){
    	global $wpdb, $sitepress;
		$results = $langs = array();
		$subquery_sql = 'SELECT element_id FROM '.$wpdb->prefix.'icl_translations WHERE element_id IS NOT NULL';
		$post_types = array(
			EM_POST_TYPE_EVENT => array(
				'table' => EM_EVENTS_TABLE,
				'type' => 'event',
				'extra_sql_where' => 'AND (recurrence=0 OR recurrence IS NULL)',
				'extra_child_sql_where' => 'AND (child.recurrence=0 OR child.recurrence IS NULL)',
			),
			EM_POST_TYPE_LOCATION => array(
				'table' => EM_LOCATIONS_TABLE,
				'type' => 'location',
			),
			'event-recurring' => array(
				'table' => EM_EVENTS_TABLE,
				'type' => 'event',
				'extra_sql_where' => 'AND recurrence=1',
				'extra_child_sql_where' => 'AND child.recurrence=1',
			)
		);
		$max_trid = $wpdb->get_var('SELECT MAX(trid) FROM '.$wpdb->prefix.'icl_translations') + 1; //in case we need it
		// MS Global Mode will need specific conditions for blogs
		$multisite_cond = $multisite_cond_translations = '';
		if( EM_MS_GLOBAL ){
			if( is_main_site() ){
				$multisite_cond = 'AND (blog_id IS NULL OR blog_id=0 OR blog_id='.get_current_blog_id().')';
				$multisite_cond_translations = 'AND (child.blog_id IS NULL OR child.blog_id=0 OR child.blog_id='.get_current_blog_id().')';
			}else{
				$multisite_cond = 'AND blog_id='.get_current_blog_id();
				$multisite_cond_translations = 'AND child.blog_id='.get_current_blog_id();
			}
		}
		// go through each post type and sync
		foreach( $post_types as $post_type => $pt_options ){
			$table = $pt_options['table'];
			$type = $pt_options['type'];
			$extra_where = !empty($pt_options['extra_sql_where']) ? $pt_options['extra_sql_where'] : '';
			$extra_child_where = !empty($pt_options['extra_child_sql_where']) ? $pt_options['extra_child_sql_where'] : '';
			// modify subquery for post type and account for recurring events
			$subquery = $wpdb->prepare($subquery_sql . " AND element_type=%s", 'post_'.$post_type);
			// parents go in first so we have trids for the children (we can't assume the trid is a post_id)
			$sql = "SELECT post_id, {$type}_language AS language FROM $table WHERE {$type}_translation=0 AND post_id NOT IN ($subquery) $extra_where $multisite_cond";
			$post_originals = $wpdb->get_results( $sql, OBJECT_K);
			$results[] = $post_originals !== false;
			if( !empty($post_originals) ){
				$duplicate_trids = $wpdb->get_col( 'SELECT DISTINCT trid FROM '.$wpdb->prefix.'icl_translations WHERE trid IN ('. implode(',',  array_keys($post_originals)) .')' );
				$inserts = $trids = array();
				foreach( $post_originals as $post_data ){
					if( empty($post_data->language) ) continue; // in theory, languages should be set by now
					// determine the trid to use for this new original
					if( in_array($post_data->post_id, $duplicate_trids) ){
						$trid = $max_trid;
						$max_trid++;
					}else{
						$trid = $post_data->post_id;
					}
					$trids[$post_data->post_id] = $trid;
					// get the language code
					if( !empty($langs[$post_data->language]) ){
						$lang = $langs[$post_data->language];
					}else{
						$langs[$post_data->language] = $lang = $sitepress->get_language_code_from_locale($post_data->language);
					}
					// prep the insert
					$inserts[] = $wpdb->prepare('(%s, %d, %d, %s)', 'post_'.$post_type, $post_data->post_id, $trid, $lang);
				}
				if( !empty($inserts) ){
					// insert and proceed with children
					$results[] = $wpdb->query('INSERT INTO '.$wpdb->prefix.'icl_translations (element_type, element_id, trid, language_code) VALUES '.implode(',', $inserts)) !== false;
				}
			}
			
			// proceed with the children that are a different language of parent and not translated yet
			$sql = "
				SELECT child.post_id, parent.post_id as parent_post_id, child.{$type}_language AS language, parent.{$type}_language as language_source
				FROM $table child
					LEFT JOIN $table parent ON child.{$type}_parent=parent.{$type}_id
				WHERE
					parent.{$type}_language != child.{$type}_language
					AND child.post_id NOT IN ($subquery)
					$extra_child_where
					$multisite_cond_translations
			";
			$post_translations = $wpdb->get_results( $sql );
			$results[] = $post_translations !== false;
			//get trids for all these parent post ids
			if( !empty($post_translations) ){
				$post_ids = array();
				foreach( $post_translations as $post_translation ){
					$post_ids[$post_translation->parent_post_id] = $post_translation->parent_post_id;
					$post_ids[$post_translation->post_id] = $post_translation->post_id;
				}
				$sql = $wpdb->prepare('SELECT element_id, trid FROM '.$wpdb->prefix.'icl_translations WHERE element_id IN ('. implode(',', $post_ids) .') AND element_type=%s', 'post_'.$post_type);
				$trids = $wpdb->get_results($sql, OBJECT_K);
				$results[] = $trids !== false;
				$inserts = array();
				foreach( $post_translations as $post_data ){
					// get the trid
					if( !empty($trids[$post_data->parent_post_id]) ){
						// trid added further up
						$trid = $trids[$post_data->parent_post_id]->trid;
					}else{
						// this must then be a translation of an original already added
						$trid = $sitepress->get_element_trid( $post_data->parent_post_id, 'post_'.$post_type );
						if( empty($trid) ){
							// unlikely, but just in case, use max trid (not bother with a check like for originals)
							$trid = $max_trid;
							$max_trid++;
						}
					}
					// get the language
					if( !empty($langs[$post_data->language]) ){
						$lang = $langs[$post_data->language];
					}else{
						$langs[$post_data->language] = $lang = $sitepress->get_language_code_from_locale($post_data->language);
					}
					// get source language
					if( !empty($langs[$post_data->language_source]) ){
						$lang_source = $langs[$post_data->language_source];
					}else{
						$langs[$post_data->language_source] = $lang_source = $sitepress->get_language_code_from_locale($post_data->language_source);
					}
					// prep child insert
					$inserts[] = $wpdb->prepare('(%s, %d, %d, %s, %s)', 'post_'.$post_type, $post_data->post_id, $trid, $lang, $lang_source);
				}
				// add inserts if any
				if( !empty($inserts) ){
					$results[] = $wpdb->query('INSERT INTO '.$wpdb->prefix.'icl_translations (element_type, element_id, trid, language_code, source_language_code) VALUES '.implode(',', $inserts)) !== false;
				}
			}
		}
		return !in_array(false, $results);
	}
	
	public static function admin_tools_tables_sync_action(){
    	global $EM_Notices;
		if( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'sync_translations' && check_admin_referer('sync_translations') && em_wp_is_super_admin() ){
			if( is_multisite() ){
				if( !empty($_REQUEST['sync_translations_blog']) && is_numeric($_REQUEST['sync_translations_blog']) ){
					$blog_id = $_REQUEST['sync_translations_blog'];
					switch_to_blog($blog_id);
					$blog_name = get_bloginfo('name');
					$result = static::sync_translations();
					restore_current_blog();
					if( !$result ){
						$result = __('The following blog language tables could not be successfully reset:', 'events-manager-wpml');
						$result .= ' <strong>'.$blog_name.'</strong>';
					}
				}elseif( !empty($_REQUEST['sync_translations_blog']) && ($_REQUEST['sync_translations_blog'] == 'all' || $_REQUEST['sync_translations_blog'] == 'all-resume') ){
					global $wpdb, $current_site;
					$blog_ids = $blog_ids_progress = get_site_option('dbem_sync_translations_multisite_progress', false);
					if( !is_array($blog_ids) || $_REQUEST['sync_translations_blog'] == 'all' ){
						$blog_ids = $blog_ids_progress = $wpdb->get_col('SELECT blog_id FROM '.$wpdb->blogs.' WHERE site_id='.$current_site->id);
						update_site_option('dbem_sync_translations_multisite_progress', $blog_ids_progress);
					}
					foreach($blog_ids as $k => $blog_id){
						$result = true;
						$plugin_basename = plugin_basename(dirname(__FILE__).'/events-manager-wpml.php');
						if( in_array( $plugin_basename, (array) get_blog_option($blog_id, 'active_plugins', array() ) ) || is_plugin_active_for_network($plugin_basename) ){
							switch_to_blog($blog_id);
							$blog_name = get_bloginfo('name');
							$blog_result = static::sync_translations();
							if( !$blog_result ){
								$fails[$blog_id] = $blog_name;
							}else{
								unset($blog_ids_progress[$k]);
								update_site_option('dbem_sync_translations_multisite_progress', $blog_ids_progress);
							}
						}
					}
					if( !empty($fails) ){
						$result = __('The following blog language tables could not be successfully reset:', 'events-manager-wpml');
						$result .= '<ul>';
						foreach( $fails as $fail ) $result .= '<li>'.$fail.'</li>';
						$result .= '</ul>';
					}else{
						delete_site_option('dbem_sync_translations_multisite_progress');
					}
					restore_current_blog();
				}else{
					$result = __('A valid blog ID must be provided, you can only reset one blog at a time.','events-manager');
				}
			}else{
				$result = static::sync_translations();
				if( !$result ){
					$result = __('An error occurred whilst trying to reset languages.', 'events-manager-wpml');
				}
			}
			if( $result !== true ){
				$EM_Notices->add_error($result, true);
			}else{
				if( is_multisite() ){
					if( $_REQUEST['sync_translations_blog'] == 'all' || $_REQUEST['sync_translations_blog'] == 'all-resume' ){
						$EM_Notices->add_confirm(__('Event and Location languages on all blogs have been synced.','events-manager'), true);
					}else{
						$EM_Notices->add_confirm(sprintf(__('Event and Location languages for blog %s have been synced.','events-manager'), '<code>'.$blog_name.'</code>'), true);
					}
				}else{
					$EM_Notices->add_confirm(__('Event and Location languages have been synced.','events-manager'), true);
				}
			}
			wp_safe_redirect(em_wp_get_referer());
			exit();
		}
	}
	
	public static function em_options_page_panel_admin_tools(){
    	?>
		<table class="form-table">
            <tr class="em-header"><td colspan="2">
                <h4><?php _e ( 'Sync Translations', 'events-manager'); ?></h4>
				<?php if( is_multisite() && get_site_option('dbem_sync_translations_multisite_progress', false) !== false ): ?>
				<p style="color:red;">
					<?php
					echo sprintf( esc_html__('Your last attempt to sync languages on all blogs did not complete successfully. You can attempt to reset only those blogs that weren\'t completed by %s from the dropdowns below', 'events-manager-wpml'), '<code>'.esc_html__('Resume Previous Attempt (All Blogs)', 'events-manager').'</code>' );
					?>
				</p>
				<?php endif; ?>
			</td></tr>
            <tr>
                <th style="text-align:right;">
                    <a href="#" class="button-secondary" id="em-sync-translations"><?php esc_html_e('Sync Translations','events-manager'); ?></a>
                </th>
                <td>
                    <?php if( is_multisite() ): ?>
                        <select name="sync_translations_blog" class="em-sync-translations">
                            <option value="0"><?php esc_html_e('Select a blog...', 'events-manager'); ?></option>
                            <option value="all"><?php esc_html_e('All Blogs', 'events-manager'); ?></option>
                            <?php if( is_multisite() && get_site_option('dbem_sync_translations_multisite_progress', false) !== false ): ?>
                            <option value="all-resume"><?php esc_html_e('Resume Previous Attempt (All Blogs)', 'events-manager'); ?></option>
                            <?php endif; ?>
                            <?php
                            foreach( get_sites() as $WP_Site){ /* @var WP_Site $WP_Site */
                                echo '<option value="'.esc_attr($WP_Site->blog_id).'">'. esc_html($WP_Site->blogname) .'</option>';
                            }
                            ?>
                        </select>
                    <?php endif; ?>
                    <p>
                        <em><?php esc_html_e('Use this tool if you have any events or locations that are out of sync with WPML translations. This tool will sync missing records between Events Manager and WPML.','events-manager-wpml'); ?></em>
                    </p>
                </td>
				<script type="text/javascript" charset="utf-8">
					jQuery(document).ready(function($){
						$('select[name="sync_translations_value"]').change( function( e ){
							if( $(this).val() === '' ){
								$('a#em-sync-translations').css({opacity:0.5, cursor:'default'});
							}else{
								$('a#em-sync-translations').css({opacity:1, cursor:'pointer'});
							}
						}).trigger('change');
						$('a#em-sync-translations').click(function(e,el){
							e.preventDefault();
							var thisform = $(this).closest('form');
							thisform.find('input, textarea, select').prop('disabled', true);
							thisform.find('input[name=_wpnonce]').val('<?php echo wp_create_nonce('sync_translations'); ?>').prop('disabled', false);
							thisform.append($('<input type="hidden" name="action" value="sync_translations" />'));
							if( thisform.find('select.em-sync-translations').length > 0 ){
								thisform.append($('<input type="hidden" name="sync_translations_blog" value="'+ thisform.find('select.em-sync-translations').val() +'" />'));
							}
							thisform.submit();
						});
					});
				</script>
            </td></tr>
		</table>
		<?php
	}
	
	public static function update(){
		//recurring events notice
		if( version_compare( '2.0', get_option('em_wpml_version')) === 1 ){
			delete_option('em_wpml_disable_recurrence_notice');
			// add new admin notice for recurrences
			$msg = '<p>'.__('Events Manager and its WPML compatibility plugin have received a substantial update with major improvements to the MultiLingual integration. This now includes recurring event support, which was previously disabled when both plugins were installed and can now be reactivated.', 'events-manager-wpml').'</p>';
			$msg .= '<p><a href="https://wp-events-plugin.com/plugin-integrations/wpml/" target="_blank">'.__('Please visit our website for an updated list of supported features.', 'events-manager-wpml').'</a></p>';
			$EM_Admin_Notice = new EM_Admin_Notice('em-wpml-recurrences-new', 'info', $msg);
			$EM_Admin_Notice->where = 'all';
			EM_Admin_Notices::add($EM_Admin_Notice);
		}
	}
}
add_action('em_ml_init', 'EM_WPML_Admin::init');