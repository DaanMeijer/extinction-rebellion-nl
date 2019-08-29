<?PHP
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
    if (is_page('rebel-without-borders')) {
    ?>
        <meta name="og:image" content="https://extinctionrebellion.nl/app/uploads/2019/08/homepage-video-overlay.jpg" />
    <?php }
}
add_action( 'wp_head', 'add_og_image' );
