<?PHP
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

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
        'slug'                  => 'local',
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
        'menu_icon'             => 'dashicons-groups',
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

add_filter( 'rest_authentication_errors', function( $result ) {
    // If a previous authentication check was applied,
    // pass that result along without modification.
    if ( true === $result || is_wp_error( $result ) ) {
        return $result;
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

/* options page for custom fields that reusable across pages and only accessible by admins.
 * see more at: https://www.advancedcustomfields.com/resources/options-page/
 *
 * */


if( function_exists('acf_add_options_page') ) {
	acf_add_options_page();
}
