<?PHP
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once(ABSPATH . 'wp-admin/includes/image.php' );

add_theme_support("post-thumbnails");
add_theme_support("custom-logo");
add_theme_support( 'title-tag' );

add_filter('upload_mimes', function($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

add_filter('acf/settings/save_json', function( $path ) {
    // update path
    $path = get_stylesheet_directory() . '/acf-json';

    // return
    return $path;
});

add_filter('acf/settings/load_json', function( $paths ) {
    // remove original path (optional)
    unset($paths[0]);

    // append path
    $paths[] = get_stylesheet_directory() . '/acf-json';

    // return
    return $paths;
});


add_action('wp_enqueue_scripts', function(){
    wp_enqueue_script('xrnl', get_theme_file_uri("dist/js/app.js"), ['jquery']);
});


/**
 * Custom load javascript for local group pages.
 */
function xrnl_lg_scripts() {

  if (is_singular('xrnl_local_group')) {
    wp_deregister_script('xrnl');
    wp_enqueue_script('popper-js', get_theme_file_uri('node_modules/popper.js/dist/umd/popper.js'));
    wp_enqueue_script('bootstrap-js', get_theme_file_uri('node_modules/bootstrap/dist/js/bootstrap.min.js'), array('jquery', 'popper-js'));
    wp_enqueue_script('xrnl-local-group-tabs', get_theme_file_uri('assets/js/xrnl-local-group-tabs.js'), array('bootstrap-js'));
  }

}
add_action( 'wp_enqueue_scripts', 'xrnl_lg_scripts' );


add_action('init', function() {
    register_nav_menu('primary', 'Main menu');
    register_nav_menu('primary-mobile', 'Main menu mobile');
    register_nav_menu('footer-1', 'Footer menu 1');
    register_nav_menu('footer-2', 'Footer menu 2');
    register_nav_menu('language', 'Language selector');
});

function exclude_category( $query ) {
	if ( $query->is_home() && $query->is_main_query() ) {
		$query->set( 'cat', '-29' );
	}
}
add_action( 'pre_get_posts', 'exclude_category' );

require_once(__DIR__. '/class-wp-bootstrap-navwalker.php');

add_action('init', function(){
    $labels = array(
        'name'                  => _x( 'Volunteer Vacancies', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Volunteer Vacancy', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Vacancies', 'text_domain' ),
        'name_admin_bar'        => __( 'Volunteer Vacancies', 'text_domain' ),
        'archives'              => __( 'Item Archives', 'text_domain' ),
        'attributes'            => __( 'Item Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
        'all_items'             => __( 'All Items', 'text_domain' ),
        'add_new_item'          => __( 'Add New Item', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Item', 'text_domain' ),
        'edit_item'             => __( 'Edit Item', 'text_domain' ),
        'update_item'           => __( 'Update Item', 'text_domain' ),
        'view_item'             => __( 'View Item', 'text_domain' ),
        'view_items'            => __( 'View Items', 'text_domain' ),
        'search_items'          => __( 'Search Item', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
    );
    $rewrite = array(
        'slug'                  => 'volunteer',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => __( 'Volunteer Vacancy', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type( 'volunteer_vacancy', $args );

    add_filter('user_can_richedit', function( $default ){
      if( get_post_type() === 'volunteer_vacancy') return false;
      return $default;
    });

});

add_action('init', function(){
    $labels = array(
        'name'                  => _x( 'Community Groups', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Community Group', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Community', 'text_domain' ),
        'name_admin_bar'        => __( 'Community Groups', 'text_domain' ),
        'archives'              => __( 'Item Archives', 'text_domain' ),
        'attributes'            => __( 'Item Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
        'all_items'             => __( 'All groups', 'text_domain' ),
        'add_new_item'          => __( 'New Community Group', 'text_domain' ),
        'add_new'               => __( 'Add new group', 'text_domain' ),
        'new_item'              => __( 'New Item', 'text_domain' ),
        'edit_item'             => __( 'Edit Group', 'text_domain' ),
        'update_item'           => __( 'Update Item', 'text_domain' ),
        'view_item'             => __( 'View Item', 'text_domain' ),
        'view_items'            => __( 'View Items', 'text_domain' ),
        'search_items'          => __( 'Search Item', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
    );
    $rewrite = array(
        'slug'                  => 'community',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => __( 'Community Group', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 7,
        'menu_icon'             => 'dashicons-heart',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type( 'community_group', $args );

});

add_action('init', function(){
    $labels = array(
        'name'                  => _x( 'Local Groups', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Local Group', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Local Groups', 'text_domain' ),
        'name_admin_bar'        => __( 'Local Groups', 'text_domain' ),
        'archives'              => __( 'Item Archives', 'text_domain' ),
        'attributes'            => __( 'Item Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
        'all_items'             => __( 'All local groups', 'text_domain' ),
        'add_new_item'          => __( 'New Local Group', 'text_domain' ),
        'add_new'               => __( 'Add new group', 'text_domain' ),
        'new_item'              => __( 'New Item', 'text_domain' ),
        'edit_item'             => __( 'Edit Group', 'text_domain' ),
        'update_item'           => __( 'Update Item', 'text_domain' ),
        'view_item'             => __( 'View Item', 'text_domain' ),
        'view_items'            => __( 'View Items', 'text_domain' ),
        'search_items'          => __( 'Search Item', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
    );
    $rewrite = array(
        'slug'                  => 'groep',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => __( 'Local Group', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array('title','author','revisions'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-location',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type( 'xrnl_local_group', $args );

});

// Register Sidebars
function blog_sidebar() {
	register_sidebar( array(
		'id'            => 'blog_sidebar',
		'class'         => 'blog_sidebar',
        'name'          => __( 'Blog Sidebar', 'theme-xrnl' ),
        'description'   => __( 'Appears on blog posts in the sidebar.', 'theme-xrnl' ),
	) );
}
add_action( 'widgets_init', 'blog_sidebar' );


// Yoast SEO seems to not render og:image, so we are going to do this
// hack
function add_og_image() {
    if (is_page('spring-rebellion-2020') || is_page('lente-rebellie-2020')) {
    ?>
        <meta name="og:image" content="https://extinctionrebellion.nl/app/uploads/2020/02/seo-image.png" />
    <?php }
}
add_action( 'wp_head', 'add_og_image' );

// Make sure events details page has `/events/:slug` url instead of `/meetup-event/:slug`
add_filter( 'register_post_type_args', function($args, $post_type){
    if ( $post_type == 'meetup_events') {
        $args['rewrite']['slug'] = 'events';
    }
    return $args;
}, 10, 2 );

// Get Distinct Event cities
function event_cities() {
    $args = array(
        'posts_per_page' => 1e9,
        'post_type' => 'meetup_events',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'event_start_date', // Check the start date field
                'value' => date("Y-m-d"), // Set today's date (note the similar format)
                'compare' => '>=', // Return the ones greater than today's date
                'type' => 'DATE' // Let WordPress know we're working with date
            )
        )
    );

    $events = new WP_Query( $args );
    $cities = array();

    while ( $events->have_posts() ) {
        $events->the_post();
        $city = get_post_meta( get_the_ID(), 'venue_city', true );
        $venue = get_post_meta( get_the_ID(), 'venue_address', true );
        if ($venue == 'Online'){
            $city = 'Online';
        } elseif ($city == ''){
            continue;
        }
        if (array_key_exists($city, $cities)) {
            $cities[$city]++;
        } elseif ($city != '') {
            $cities[$city] = 1;
        }
    }
    ksort($cities);

    return $cities;
}

// Get Distinct Event categories
function event_categories() {
    $args = array(
        'posts_per_page' => 1e9,
        'post_type' => 'meetup_events',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'event_start_date', // Check the start date field
                'value' => date("Y-m-d"), // Set today's date (note the similar format)
                'compare' => '>=', // Return the ones greater than today's date
                'type' => 'DATE' // Let WordPress know we're working with date
            )
        )
    );

    $events = new WP_Query( $args );

    $categories = array();
    while ( $events->have_posts() ) {
        $events->the_post();

        $term_obj_list = get_the_terms(get_the_ID(), 'meetup_category');
        if($term_obj_list) {
            $post_categories = wp_list_pluck($term_obj_list, 'name');
        } else {
            continue;
        }


        foreach($post_categories as $post_category) {
            if(array_key_exists($post_category, $categories)) {
                $categories[$post_category]++;
            } else {
                $categories[$post_category] = 1;
            }
        }
    }
    ksort($categories);

    return $categories;
}

// Get Distinct Vacancy working and local groups
function vacancy_groups( $vacancies ) {
    $working_groups = array();
    $local_groups = array();

    while ( $vacancies->have_posts() ) {
        $vacancies->the_post();
        $role = json_decode(get_the_content());
        $working_groups[] = $role->workingGroup;
        $local_groups[] = $role->localGroup;
    }

    function unique_sorted( $array ) {
        $array = array_unique($array);
        sort($array);
        return $array;
    }

    return array(
        'local_groups' => unique_sorted($local_groups),
        'working_groups' => unique_sorted($working_groups)
    );
}

function xrnl_query_vars( $qvars ) {
    $qvars[] = 'city';
    $qvars[] = 'category';
    $qvars[] = 'working_group';
    $qvars[] = 'local_group';
    return $qvars;
}
add_filter( 'query_vars', 'xrnl_query_vars' );

function excerpt($limit) {
    $excerpt = explode(' ', get_the_excerpt(), $limit);
    if (count($excerpt)>=$limit) {
        array_pop($excerpt);
        $excerpt = implode(" ",$excerpt).'...';
    } else {
        $excerpt = implode(" ",$excerpt);
    }
    $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
    return $excerpt;
}

/**
 * Grab events from database
 *
 * @param array $data None, city, category or both
 * @return array Of events that satisfy the request
 */
function events_query( $data ) {
  //if($_SERVER['REMOTE_ADDR'] != '127.0.0.1'){ return "IP " . $_SERVER['REMOTE_ADDR'] . " detected. Only localhost is allowed " . $_SERVER['SERVER_ADDR']; }

  global $wpdb;

  $params = [];
  $suffix = "";
  if($data['city'] != NULL)  {
    if ($data['city'] == 'Online') {
      $suffix = $suffix . " AND pm_address.meta_value = %s";
      //$query = $query . " AND pm_address.meta_value = %s";
      array_push($params, $data['city']);
    } else {
      $suffix = $suffix . " AND pm_city.meta_value = %s";
      //$query = $query . " AND pm_city.meta_value = %s";
      array_push($params, $data['city']);
    }
  }

  if($data['category'] != NULL) {
    $suffix = $suffix . " AND t.name = %s";
    array_push($params, $data['category']);
  }

  if($data['since_date'] != NULL) {
    $suffix = $suffix . " AND pm_start_ts.meta_value >= %s";
    array_push($params, $data['since_date']);
  }

  if($data['until_date'] != NULL) {
    $suffix = $suffix . " AND pm_start_ts.meta_value <= %s";
    array_push($params, $data['until_date']);
  }

  $meta_query = "SELECT * FROM {$wpdb->postmeta} pm WHERE pm.post_id IN
                  (SELECT p.ID
                  FROM {$wpdb->posts} p
                  LEFT JOIN {$wpdb->term_relationships} tr
                  ON tr.object_id = p.ID

                  LEFT JOIN {$wpdb->term_taxonomy} tt
                  ON tt.term_taxonomy_id = tr.term_taxonomy_id
                  AND tt.taxonomy = 'meetup_category'

                  LEFT JOIN {$wpdb->terms} t
                  ON t.term_id = tt.term_id

                  LEFT JOIN {$wpdb->postmeta} pm_city
                  ON p.ID = pm_city.post_id

                  LEFT JOIN {$wpdb->postmeta} pm_address
                  ON p.ID = pm_address.post_id

                  LEFT JOIN {$wpdb->postmeta} pm_start_ts
                  ON p.ID = pm_start_ts.post_id

                  WHERE p.post_type = 'meetup_events'
                  AND pm_city.meta_key = 'venue_city'
                  AND pm_address.meta_key = 'venue_address'
                  AND pm_start_ts.meta_key = 'start_ts' {$suffix}
                  AND (p.post_status = 'publish' OR p.post_status = 'draft'))";

  $query = "SELECT p.ID as id, p.post_title as title, p.post_content as content, p.post_status as status, t.name as category
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->term_relationships} tr
            ON tr.object_id = p.ID

            LEFT JOIN {$wpdb->term_taxonomy} tt
            ON tt.term_taxonomy_id = tr.term_taxonomy_id
            AND tt.taxonomy = 'meetup_category'

            LEFT JOIN {$wpdb->terms} t
            ON t.term_id = tt.term_id

            LEFT JOIN {$wpdb->postmeta} pm_city
            ON p.ID = pm_city.post_id

            LEFT JOIN {$wpdb->postmeta} pm_address
            ON p.ID = pm_address.post_id

            LEFT JOIN {$wpdb->postmeta} pm_start_ts
            ON p.ID = pm_start_ts.post_id

            WHERE p.post_type = 'meetup_events'
            AND pm_city.meta_key = 'venue_city'
            AND pm_address.meta_key = 'venue_address'
            AND pm_start_ts.meta_key = 'start_ts' {$suffix}
            AND (p.post_status = 'publish' OR p.post_status = 'draft')";

  $prepared_sql = $wpdb->prepare($query, $params);
  $prepared_meta = $wpdb->prepare($meta_query, $params);

  $events = $wpdb->get_results($prepared_sql, OBJECT);
  $metas = $wpdb->get_results($prepared_meta, OBJECT);

  $metas_by_id = array();
  foreach($metas as $meta) {
    if($metas_by_id[$meta->post_id]) {
      $metas_by_id[$meta->post_id][$meta->meta_key] = $meta->meta_value;
    } else {
      $metas_by_id[$meta->post_id] = array($meta->meta_key => $meta->meta_value);
    }
  }

  foreach($events as $event) {
    $single_metas = $metas_by_id[$event->id];
    $event->meta = $single_metas;
    // foreach($single_metas as $meta_key => $meta_value) {
    //   $event->{$meta_key} = $meta_value;
    // }
  }

  return $events;
}


function get_event($data) {
  global $wpdb;

  $meta_query = "SELECT * FROM {$wpdb->postmeta} pm WHERE pm.post_id = %s";

  $query = "SELECT p.ID as id, p.post_title as title, p.post_content as content, p.post_status as status, t.name as category
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->term_relationships} tr
            ON tr.object_id = p.ID

            LEFT JOIN {$wpdb->term_taxonomy} tt
            ON tt.term_taxonomy_id = tr.term_taxonomy_id
            AND tt.taxonomy = 'meetup_category'

            LEFT JOIN {$wpdb->terms} t
            ON t.term_id = tt.term_id

            WHERE p.ID = %s";

  $prepared_sql = $wpdb->prepare($query, $data['id']);
  $prepared_meta = $wpdb->prepare($meta_query, $data['id']);

  $event = $wpdb->get_results($prepared_sql, OBJECT);
  $metas = $wpdb->get_results($prepared_meta, OBJECT);

  foreach($metas as $meta) {
    if($event['meta'] != NULL) {
      $event['meta'][$meta->meta_key] = $meta->meta_value;
    } else {
      $event['meta'] = array($meta->meta_key => $meta->meta_value);
    }
  }


  // foreach($single_metas as $meta_key => $meta_value) {
  //   $event->{$meta_key} = $meta_value;
  // }

  return $event;
}

/**
 * Insert event into  database
 * For now, all data must be suppplied as form-data
*/
function insert_event($data) {
  $start_date = new DateTime($data['start_date']);
  $end_date = new DateTime($data['end_date']);

  $post = array(
    'post_title' => $data['title'],
    'post_content' => $data['content'],
    //'post_author' => 59,
    //'post_date_gmt' => '2020-08-19 00:00:00',
    'post_status' => 'draft',
    'post_type' => 'meetup_events',
    'meta_input' => array(
      'venue_name' => $data['venue_name'],
      'venue_city' => $data['venue_city'],
      'venue_address' => $data['venue_address'],
      'venue_state' => $data['venue_state'],
      'venue_country' => $data['venue_country'],
      'venue_zipcode' => $data['venue_zipcode'],
      'venue_lat' => $data['venue_lat'],
      'venue_lon' => $data['venue_lon'],
      'venue_url' => $data['venue_url'],
      'organizer_name' => $data['organizer_name'],
      'organizer_email' => $data['organizer_email'],
      'organizer_phone' => $data['organizer_phone'],
      'organizer_url' => $data['organizer_url'],
      'event_start_date' => $start_date->format('Y-m-d'),
      'event_start_hour' => $start_date->format('h'),
      'event_start_minute' => $start_date->format('i'),
      'event_start_meridian' => $start_date->format('a'),
      'event_end_date' =>  $end_date->format('Y-m-d'),
      'event_end_hour' => $end_date->format('h'),
      'event_end_minute' => $end_date->format('i'),
      'event_end_meridian' => $end_date->format('a'),
      'start_ts' => date_timestamp_get($start_date),
      'end_ts' => date_timestamp_get($end_date),
      '_thumbnail_id' => $data['thumbnail_id'],
      'facebook_id' => $data['facebook_id'],
      'name_hash' => $data['name_hash'],
      'description_hash' => $data['description_hash'],
      'start_time_hash' => $data['start_time_hash'],
      'end_time_hash' => $data['end_time_hash'],
      'place_hash' => $data['place_hash'],
      'owner_hash' => $data['owner_hash'],
      'cover_hash' => $data['cover_hash'],
      '_cover_bytes_hash' => $data['_cover_bytes_hash']
    ),
  );

  if($data['picture_url'] != NULL) {
    $attach_id = uploadPicture($data['picture_url'], $data['title']);
    $post['meta_input']['_thumbnail_id'] = $attach_id;
  }

  $err = wp_insert_post($post, true);

  if(!is_wp_error($err)) {
    wp_set_object_terms($err, $data['category'], 'meetup_category');
  }
  return $err;
}

function uploadPicture($url, $title) {
  $extension = '.png';
  $mimeType = 'image/png';

  if(strripos($url, '.jpg') > 0) {
    $extension = '.jpg';
    $mimeType = 'image/jpeg';
  }


  $filename = hash('md5', $title) . '_cover' . rand() . $extension;
  $uploaddir = wp_upload_dir();
  $uploadfile = $uploaddir['path'] . '/' . $filename;
  $contents= file_get_contents($url);
  $savefile = fopen($uploadfile, 'w');
  fwrite($savefile, $contents);
  fclose($savefile);

  $attachment = array(
      'post_mime_type' =>$mimeType,
      'post_title' => $title,
      'post_content' => '',
      'post_status' => 'inherit'
  );

  $attach_id = wp_insert_attachment( $attachment, $uploadfile );

  $imagenew = get_post( $attach_id );

  $fullsizepath = get_attached_file( $imagenew->ID );

  //print $fullsizepath;
   $attach_data = wp_generate_attachment_metadata( $attach_id, $fullsizepath );
   wp_update_attachment_metadata( $attach_id, $attach_data );

   return $attach_id;
}

function updatePicture($url, $filename) {
  $uploaddir = wp_upload_dir();
  $uploadfile = $filename;
  $contents = file_get_contents($url);
  $savefile = fopen($uploadfile, 'w');
  fwrite($savefile, $contents);
  fclose($savefile);
}

/**
 * Update existing event in the  database
 * For now, all data must be suppplied as form-data
*/
function update_event($data) {
  //$data = $request->get_json_params();
  // print var_dump($data);

  $start_date = new DateTime($data['start_date']);
  $end_date = new DateTime($data['end_date']);

  $post = array(
    'ID' => $data['id'],
    'meta_input' => array()
  );

  if($data['picture_url'] != NULL){
    $original_post = get_post($data['id']);
    $thumbnail_id = get_post_meta($data['id'], '_thumbnail_id', true);

    if($thumbnail_id) {
      $filename = get_attached_file( $thumbnail_id );
      //echo "Thumbnail already exists, updating " . $filename
      updatePicture($data['picture_url'], $filename);
    } else {
      $attach_id = uploadPicture($data['picture_url'], $original_post->post_title);
      $post['meta_input']['_thumbnail_id'] = $attach_id;
    }
  }

  if($data['title'] != NULL) {
    $post['post_title'] = $data['title'];
  }
  if($data['content'] != NULL) {
    $post['post_content'] = $data['content'];
  }
  if($data['post_status'] != NULL) {
    $post['post_status'] = $data['post_status'];
  }
  if($data['venue_name'] != NULL) {
    $post['meta_input']['venue_name'] = $data['venue_name'];
  }
  if($data['venue_city'] != NULL) {
    $post['meta_input']['venue_city'] = $data['venue_city'];
  }
  if($data['venue_address'] != NULL) {
    $post['meta_input']['venue_address'] = $data['venue_address'];
  }
  if($data['venue_state'] != NULL) {
    $post['meta_input']['venue_state'] = $data['venue_state'];
  }
  if($data['venue_country'] != NULL) {
    $post['meta_input']['venue_country'] = $data['venue_country'];
  }
  if($data['venue_zipcode'] != NULL) {
    $post['meta_input']['venue_zipcode'] = $data['venue_zipcode'];
  }
  if($data['venue_lat'] != NULL) {
    $post['meta_input']['venue_lat'] = $data['venue_lat'];
  }
  if($data['venue_lon'] != NULL) {
    $post['meta_input']['venue_lon'] = $data['venue_lon'];
  }
  if($data['venue_url'] != NULL) {
    $post['meta_input']['venue_url'] = $data['venue_url'];
  }
  if($data['organizer_name'] != NULL) {
    $post['meta_input']['organizer_name'] = $data['organizer_name'];
  }
  if($data['organizer_email'] != NULL) {
    $post['meta_input']['organizer_email'] = $data['organizer_email'];
  }
  if($data['organizer_phone'] != NULL) {
    $post['meta_input']['organizer_phone'] = $data['organizer_phone'];
  }
  if($data['organizer_url'] != NULL) {
    $post['meta_input']['organizer_url'] = $data['organizer_url'];
  }
  if($data['start_date'] != NULL) {
    $post['meta_input']['event_start_date'] = $start_date->format('Y-m-d');
    $post['meta_input']['event_start_hour'] = $start_date->format('h');
    $post['meta_input']['event_start_minute'] = $start_date->format('i');
    $post['meta_input']['event_start_meridian'] = $start_date->format('a');
    $post['meta_input']['start_ts'] = date_timestamp_get($start_date);
  }
  if($data['end_date'] != NULL) {
    $post['meta_input']['event_end_date'] = $end_date->format('Y-m-d');
    $post['meta_input']['event_end_hour'] = $end_date->format('h');
    $post['meta_input']['event_end_minute'] = $end_date->format('i');
    $post['meta_input']['event_end_meridian'] = $end_date->format('a');
    $post['meta_input']['end_ts'] = date_timestamp_get($end_date);
  }
  if($data['thumbnail_id'] != NULL) {
    $post['meta_input']['_thumbnail_id'] = $data['thumbnail_id'];
  }
  if($data['facebook_id'] != NULL) {
    $post['meta_input']['facebook_id'] = $data['facebook_id'];
  }

  if($data['name_hash'] != NULL) {
    $post['meta_input']['name_hash'] = $data['name_hash'];
  }
  if($data['description_hash'] != NULL) {
    $post['meta_input']['description_hash'] = $data['description_hash'];
  }
  if($data['start_time_hash'] != NULL) {
    $post['meta_input']['start_time_hash'] = $data['start_time_hash'];
  }
  if($data['end_time_hash'] != NULL) {
    $post['meta_input']['end_time_hash'] = $data['end_time_hash'];
  }
  if($data['place_hash'] != NULL) {
    $post['meta_input']['place_hash'] = $data['place_hash'];
  }
  if($data['owner_hash'] != NULL) {
    $post['meta_input']['owner_hash'] = $data['owner_hash'];
  }
  if($data['cover_hash'] != NULL) {
    $post['meta_input']['cover_hash'] = $data['cover_hash'];
  }
  if($data['_cover_bytes_hash'] != NULL) {
    $post['meta_input']['_cover_bytes_hash'] = $data['_cover_bytes_hash'];
  }

  $err = wp_update_post($post, true);

  if(!is_wp_error($err) && $data['category'] != NULL) {
    wp_set_object_terms($err, $data['category'], 'meetup_category');
  }
  return $err;
}

add_filter( 'rest_authentication_errors', function( $result ) {
  // If a previous authentication check was applied,
  // pass that result along without modification.
  if ( true === $result || is_wp_error( $result ) ) {
      return $result;
  }

  if(substr($_SERVER['REQUEST_URI'], 0, 19) == '/wp-json/events_api' ) {
    if($_SERVER['REMOTE_ADDR'] != ALLOW_EVENT_REQUEST_FROM_IP) {
      return new WP_Error(
          'forbidden',
          __( 'You are not allowed to access this resource' ),
          array( 'status' => 401 )
      );
    } else {
      return true;
    }
  }

  // No authentication has been performed yet.
  // Return an error if user is not logged in.
  if ( ! is_user_logged_in() ) {
      return new WP_Error(
          'rest_not_logged_in',
          __( 'You are not currently logged in.' ),
          array( 'status' => 401 )
      );
  }

  // Our custom authentication check should have no effect
  // on logged-in requests
  return $result;
});

add_action( 'rest_api_init', function () {
  register_rest_route( 'events_api/v1', '/events/', array(
    'methods' => 'GET',
    'callback' => 'events_query',
    'permission_callback' => function ($result) {
      //return current_user_can( 'read_private_posts' );
      //return is_user_logged_in();
      //return $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'];
      return $result;
    }
  ) );
  register_rest_route( 'events_api/v1', '/events/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'get_event',
    // 'permission_callback' => function () {
    //   //return current_user_can( 'read_private_posts' );
    //   //return is_user_logged_in();
    //   return $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'];
    // }
  ) );
  register_rest_route( 'events_api/v1', '/events/', array(
    'methods' => 'POST',
    'callback' => 'insert_event',
    // 'permission_callback' => function () {
    //   //return current_user_can( 'read_private_posts' );
    //   //return is_user_logged_in();
    //   return $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'];
    // }
  ) );
  register_rest_route( 'events_api/v1', '/events/(?P<id>\d+)', array(
    'methods' => 'PUT',
    'callback' => 'update_event',
    // 'permission_callback' => function () {
    //   //return current_user_can( 'read_private_posts' );
    //   //return is_user_logged_in();
    //   return $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'];
    // }
  ) );
} );

/*
 * Options page for custom fields that reusable across pages.
 * 'Admin Settings' is only accessible by admins,
 * 'Editorial content' is accessible by editors also.
 * see more at: https://www.advancedcustomfields.com/resources/options-page/
 */

if( function_exists('acf_add_options_page') ) {

  $xrnl_settings = acf_add_options_page(array(
  'page_title' => 'XRNL General Settings',
  'menu_title' => 'XRNL Settings',
  'menu_slug' => 'xrnl-general-settings',
  'capability' => 'edit_others_pages',
  'icon_url' => 'dashicons-pets',
  'position' => '9',
  'redirect' => true
  ));

  acf_add_options_sub_page(array(
  'page_title' => 'Editorial Content',
  'menu_title' => 'Editorial',
  'parent_slug' => $xrnl_settings['menu_slug'],
  'capability' => 'edit_others_pages',
  'update_button' => 'Save content',
  'updated_message' => 'Content saved'
  ));

  acf_add_options_sub_page(array(
  'page_title' => 'Admin Settings',
  'menu_title' => 'Admin',
  'parent_slug' => $xrnl_settings['menu_slug'],
  'capability' => 'manage_options'
  ));

}