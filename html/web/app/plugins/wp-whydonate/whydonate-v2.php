<?php
/*
Plugin Name:  Whydonate
Plugin URI:   https://wordpress.org/plugins/wp-whydonate/
Description:  Donatie button voor je eigen website. Zamel geld in via iDeal, Creditcard, PayPal, VISA, Sofort en Bancontact. Binnen een paar minuten opgezet en veilig!
Author:       Whydonate
Author URI:   https://profiles.wordpress.org/nielscorver/
Version:      3.5
Text Domain:  myplugin
Domain Path:  /languages/
License:      GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
*/

// disable direct file access
if (!defined('ABSPATH')) {

    exit;
}

define('VERSION', '1.1');

function version_id()
{
    if (WP_DEBUG)
        return time();
    return VERSION;
}

global $wdplugin_db_version;
$wdplugin_db_version = '1.0';

// create db
function create_style_table()
{
    global $wpdb;
    global $wdplugin_db_version;

    // $table_name = 'wp_wdplugin_style';
    $table_name = $wpdb->prefix . "wdplugin_style";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE " . $table_name . " (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		styleName text DEFAULT '' NOT NULL,
		oneTimeCheck int DEFAULT 1 NOT NULL,
		monthlyCheck int DEFAULT 2 NOT NULL,
		yearlyCheck int DEFAULT 3 NOT NULL,
        firstAmountCheck int DEFAULT 1 NOT NULL,
		secondAmountCheck int DEFAULT 2 NOT NULL,
		thirdAmountCheck int DEFAULT 3 NOT NULL,
        forthAmountCheck int DEFAULT 4 NOT NULL,
        firstAmount int DEFAULT 25 NOT NULL,
        secondAmount int DEFAULT 50 NOT NULL,
        thirdAmount int DEFAULT 75 NOT NULL,
        forthAmount int DEFAULT 100 NOT NULL,
        otherChecked int DEFAULT 1 NOT NULL,
        showDonateButton int DEFAULT 1 NOT NULL,
        showProgressBar int DEFAULT 2 NOT NULL,
        showRaisedAmount int DEFAULT 3 NOT NULL,
        showEndDate int DEFAULT 4 NOT NULL,
        showDonationFormOnly int DEFAULT 0 NOT NULL,
        doNotShowBox int DEFAULT 0 NOT NULL,
        colorCode varchar(8) DEFAULT '#2828d6' NOT NULL,
        font text DEFAULT '' NOT NULL,
        buttonRadius int DEFAULT 30 NOT NULL,
        donationTitle varchar(50) DEFAULT 'My Safe Donation' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    add_option('wd_style_db_version', $wdplugin_db_version);
}

function create_api_table()
{
    global $wpdb;
    global $wdplugin_db_version;

    // $table_name = 'wp_wdplugin_api_key';
    $table_name = $wpdb->prefix . "wdplugin_api_key";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE " . $table_name . " (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		apiKey text DEFAULT '' NOT NULL,
        username text DEFAULT '' NOT NULL,
        email text DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    add_option('wd_apikey_db_version', $wdplugin_db_version);
}

function create_config_widget_table()
{
    global $wpdb;
    global $wdplugin_db_version;

    // $table_name = 'wp_wdplugin_config_widget';
    $table_name = $wpdb->prefix . "wdplugin_config_widget";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE " . $table_name . " (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
        styleId int DEFAULT 0 NOT NULL,
		pluginStyle text DEFAULT '' NOT NULL,
		fundraiserName text DEFAULT '' NOT NULL,
        slugName text DEFAULT '' NOT NULL,
        slugId int DEFAULT 0 NOT NULL,
        shortcode text DEFAULT '' NOT NULL,
        successUrl text DEFAULT '' NOT NULL,
        failureUrl text DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    add_option('wd_config_widget_db_version', $wdplugin_db_version);
}


// function plugin_update() {
//     global $plugin_version;

//     if ( get_site_option( 'plugin_version' ) != $plugin_version )
//         plugin_updates();

// }


// add_action( 'plugins_loaded', 'plugin_update' );


function upgrade_database_set_transition($upgrader_object, $options)
{
    // The path to our plugin's main file
    $our_plugin = plugin_basename(__FILE__);
    // If an update has taken place and the updated type is plugins and the plugins element exists
    if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset($options['plugins'])) {
        // Iterate through the plugins being updated and check if ours is there
        foreach ($options['plugins'] as $plugin) {
            if ($plugin == $our_plugin) {
                // Set a transient to record that our plugin has just been updated
                set_transient('wp_upe_updated', 1);
            }
        }
    }
}

add_action('upgrader_process_complete', 'upgrade_database_set_transition', 10, 2);

function wp_upe_display_update_notice()
{
    // Check the transient to see if we've just updated the plugin
    if (get_transient('wp_upe_updated')) {
        global $wpdb, $plugin_version;

        $table_name = $wpdb->prefix . 'wdplugin_config_widget';

        try {
            $wpdb->query(
                "ALTER TABLE $table_name
                 ADD COLUMN `successUrl` TEXT DEFAULT '' NOT NULL
                "
            );
        } catch (exception $e) {
            var_dump('successUrl column already existed');
        }


        try {
            $wpdb->query(
                "ALTER TABLE $table_name
                 ADD COLUMN `failureUrl` TEXT DEFAULT '' NOT NULL
                "
            );
        } catch (exception $e) {
            var_dump('failureUrl column already existed');
        }

        try {
            $wpdb->query(
                "ALTER TABLE $table_name
                DROP COLUMN `redirectUrl`
                "
            );
        } catch (exception $e) {
            var_dump('redirectUrl column already removed');
        }

        echo '<div class="notice notice-success">' . __('Thanks for updating. If you see any database error message please ignore those and refresh the page.', 'whydonate-v2') . '</div>';
        delete_transient('wp_upe_updated');
    }
}
add_action('admin_notices', 'wp_upe_display_update_notice');


function wpse_enqueue_datepicker()
{
    // Load the datepicker script (pre-registered in WordPress).
    // wp_enqueue_script( 'jquery-ui-datepicker' );

    // // You need styling for the datepicker. For simplicity I've linked to the jQuery UI CSS on a CDN.
    // wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
    // wp_enqueue_style( 'jquery-ui' );  

    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

    // wp_enqueue_script( 'jquery' );
    // wp_enqueue_script( 'jquery-ui-core' );
    // wp_enqueue_script('jquery-ui-datepicker');
    //wp_enqueue_script( 'jquery-datepicker', 'http://jquery-ui.googlecode.com/svn/trunk/ui/jquery.ui.datepicker.js', array('jquery', 'jquery-ui-core' ) );
}
add_action('init', 'wpse_enqueue_datepicker');


// function show_custom_message()
// {
//     if (show_custom_message == true) {
//         global $pagenow;
//         if ($pagenow == 'plugins.php') {
//             echo '<div class="notice notice-warning is-dismissible">
//                  <p>Hey, we are sorry your old shorcode will not work. You have to create new one instead!</p>
//              </div>';
//         }
//     }
// }

function my_plugin_remove_database_and_others()
{
    // global $wpdb;
    // // $table_name = 'wp_wdplugin_api_key';
    // $table_name = $wpdb->prefix . "wdplugin_api_key";
    // $sql = "DROP TABLE IF EXISTS " . $table_name . "";
    // $wpdb->query($sql);

    // // $table_name = 'wp_wdplugin_config_widget';
    // $table_name = $wpdb->prefix . "wdplugin_config_widget";
    // $sql = "DROP TABLE IF EXISTS " . $table_name . "";
    // $wpdb->query($sql);

    // // $table_name = 'wp_wdplugin_style';
    // $table_name = $wpdb->prefix . "wdplugin_style";
    // $sql = "DROP TABLE IF EXISTS " . $table_name . "";
    // $wpdb->query($sql);

    // delete_option("wd_apikey_db_version");


    // Delete installation entry from our database
    global $wpdb;
    $table_name = $wpdb->prefix . "wdplugin_api_key";
    $result = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    foreach ($result as $row) {
        $api_key = $row['apiKey'];
    }
    $site_url = get_site_url();
    $domain_part = explode("//", $site_url)[1];
    $domain = explode("/", $domain_part)[0];
    // Testing Api
    // $apiUrl = 'http://127.0.0.1:8000/api/v1/account/installations/?client=whydonate_staging';

    //    Replace apiUrl with this urls
    //    $stagingApi='https://whydonate-development.appspot.com/api/v1/account/installations/?client=whydonate_staging';
    $productionApi = 'https://whydonate-production-api.appspot.com/api/v1/account/installations/?client=whydonate_production';


    $myObj = array("url" => $domain);
    $myJSON = json_encode((object) $myObj);

    $curl = curl_init($productionApi);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'API-KEY: ' . $api_key
        )
    );
    curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);
    $result = curl_exec($curl);
    curl_close($curl);
    echo $result;
}

// function sentry_setup()
// {
//     require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
//     Sentry\init(['dsn' => 'https://7dd5aef857b04ae881fcb0121c119b89@sentry.io/1539971']);
//     //throw new Exception("My first Sentry error!");
// }

register_activation_hook(__FILE__, 'create_style_table');
register_activation_hook(__FILE__, 'create_api_table');
register_activation_hook(__FILE__, 'create_config_widget_table');
// register_activation_hook(__FILE__, 'show_custom_message');
// register_activation_hook(__FILE__, 'sentry_setup');
register_deactivation_hook(__FILE__, 'my_plugin_remove_database_and_others');
// register_uninstall_hook(__FILE__, 'my_plugin_remove_database');

// register_deactivation_hook(__FILE__, 'create_config_widget_table');

// load text domain
function myplugin_load_textdomain()
{

    load_plugin_textdomain('myplugin', false, plugin_dir_path(__FILE__) . 'languages/');
}
add_action('plugins_loaded', 'myplugin_load_textdomain');

// load default styles
function wdplugin_enqueue_scripts()
{
    // load active theme stylesheet in both cases
    wp_enqueue_style('myplugin', plugin_dir_url(__FILE__) . '/admin/css/wdplugin-style.css', false, version_id());

    // wp_enqueue_style('style', plugin_dir_url(__FILE__) . '/admin/css/materialize.min.css', false);

    wp_enqueue_style('material-icon', "https://fonts.googleapis.com/icon?family=Material+Icons", false);

    wp_enqueue_style('google-fonts', "https://fonts.googleapis.com/css?family=Abel|Abril+Fatface|Acme|Alegreya|Alegreya+Sans|Anton|Archivo|Archivo+Black|Archivo+Narrow|Arimo|Arvo|Asap|Asap+Condensed|Bitter|Bowlby+One+SC|Bree+Serif|Cabin|Cairo|Catamaran|Crete+Round|Crimson+Text|Cuprum|Dancing+Script|Dosis|Droid+Sans|Droid+Serif|EB+Garamond|Exo|Exo+2|Faustina|Fira+Sans|Fjalla+One|Francois+One|Gloria+Hallelujah|Hind|Inconsolata|Indie+Flower|Josefin+Sans|Julee|Karla|Lato|Libre+Baskerville|Libre+Franklin|Lobster|Lora|Mada|Manuale|Maven+Pro|Merriweather|Merriweather+Sans|Montserrat|Montserrat+Subrayada|Mukta+Vaani|Muli|Noto+Sans|Noto+Serif|Nunito|Open+Sans|Open+Sans+Condensed:300|Oswald|Oxygen|PT+Sans|PT+Sans+Caption|PT+Sans+Narrow|PT+Serif|Pacifico|Passion+One|Pathway+Gothic+One|Play|Playfair+Display|Poppins|Questrial|Quicksand|Raleway|Ranga|Roboto|Roboto+Condensed|Roboto+Mono|Roboto+Slab|Ropa+Sans|Rubik|Saira|Saira+Condensed|Saira+Extra+Condensed|Saira+Semi+Condensed|Sedgwick+Ave|Sedgwick+Ave+Display|Shadows+Into+Light|Signika|Slabo+27px|Source+Code+Pro|Source+Sans+Pro|Spectral|Titillium+Web|Ubuntu|Ubuntu+Condensed|Varela+Round|Vollkorn|Work+Sans|Yanone+Kaffeesatz|Zilla+Slab|Zilla+Slab+Highlight", false);

    wp_enqueue_script('script', plugin_dir_url(__FILE__) . '/admin/js/wdplugin.js', array('jquery'), version_id(), true);

    // wp_enqueue_script('script', plugin_dir_url(__FILE__) . '/admin/js/wdplugin.js', array('jquery'), true);

    // wp_enqueue_script('sentryjs', 'https://browser.sentry-cdn.com/5.12.1/bundle.min.js', false);

    $arr = array(
        'ajaxurl' => plugin_dir_url(__DIR__) . 'whydonate-v2/admin/setup/delete.php',
    );
    wp_localize_script('main-ajax', 'obj', $arr);
    wp_localize_script('jquery', 'ajaxurl', admin_url('admin-ajax.php'));

    // echo plugin_dir_url(DIR) . 'whydonate-v2/admin/setup/delete.php';

    // wp_enqueue_script('materialjs',  plugin_dir_url(__FILE__) . '/admin/js/materialize.min.js', array(), 1, true);
}

add_action('plugins_loaded', 'whydonate_load_text_domain');
function whydonate_load_text_domain()
{
    load_plugin_textdomain('whydonate-v2', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('admin_enqueue_scripts', 'wdplugin_enqueue_scripts', "", false, false);

add_filter('admin_footer_text', '__return_empty_string', 11);
add_filter('update_footer', '__return_empty_string', 11);

// include plugin dependencies: admin only
if (is_admin()) {

    require_once plugin_dir_path(__FILE__) . 'admin/admin-menu.php';
    require_once plugin_dir_path(__FILE__) . 'admin/settings-page.php';
    // require_once plugin_dir_path(__FILE__) . 'admin/settings-register.php';
    // require_once plugin_dir_path(__FILE__) . 'admin/settings-callbacks.php';
    // require_once plugin_dir_path(__FILE__) . 'admin/settings-validate.php';
    // require_once plugin_dir_path(__FILE__) . 'admin/settings-fields.php';

    // adding new fundraiser
    //require_once plugin_dir_path(__FILE__) . 'admin/new_fundraiser/new-fundraiser-register.php';
    // require_once plugin_dir_path(__FILE__) . 'admin/new_fundraiser/new-fundraiser-callbacks.php';
    // require_once plugin_dir_path(__FILE__) . 'admin/new_fundraiser/new-fundraiser-validate.php';

    //require_once plugin_dir_path(__FILE__) . 'admin/setup/wdplugin-add-plugin-styles.php';
}

// include plugin dependencies: admin and public
require_once plugin_dir_path(__FILE__) . 'includes/core-functions.php';
// include(plugin_dir_url(__FILE__) . 'includes/shortcode-app-view.php');
require_once plugin_dir_path(__FILE__) . 'includes/shortcode-app-view.php';


//include('./includes/shortcode-app-view.php');

// add_action('wp_ajax_nopriv_plugin_ajax_for_shortcode', 'plugin_ajax_for_shortcode');

// default plugin options
function myplugin_options_default()
{

    return array(
        'custom_url' => 'https://wordpress.org/',
        'custom_title' => esc_html__('Powered by WordPress', 'myplugin'),
        'custom_style' => 'disable',
        'custom_message' => '<p class="custom-message">' . esc_html__('My custom message', 'myplugin') . '</p>',
        'custom_footer' => esc_html__('Special message for users', 'myplugin'),
        'custom_toolbar' => false,
        'custom_scheme' => 'default',
    );
}

function my_plugin_action_links($links)
{
    $title = __('Account setup', 'whydonate-v2');
    $links = array_merge(array(
        '<a href="' . esc_url(admin_url('?page=myplugin')) . '">' . $title . '</a>'
    ), $links);
    return $links;
}
add_action('plugin_action_links_' . plugin_basename(__FILE__), 'my_plugin_action_links');



function general_admin_notice()
{
    global $pagenow;
    if ($pagenow == 'plugins.php') { ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('If you updated the Whydonate plugin, you need to remove the old shortcode and replace it with the new one in order to make it work.', 'whydonate-v2') ?></p>
        </div>
<?php
    }
}

add_action('admin_notices', 'general_admin_notice');

// function show_all_tables()
// {
//     global $wpdb;
//     foreach ($wpdb->get_results("SHOW TABLES", ARRAY_N) as $table) :
//         echo $table[0] . "<br/>";
//     endforeach;
// }
// add_action('wp_footer', 'show_all_tables');

// function tl_save_error()
// {
//     update_option('plugin_error',  ob_get_contents());
//     /* Then to display the error message: */
//     echo get_option('plugin_error');
//     /* Or you could do the following: */
//     file_put_contents('C:\Users\Shuvo\Desktop\errors', ob_get_contents());
// }
// add_action('activated_plugin', 'tl_save_error');
