<?php // MyPlugin - Admin Menu

// disable direct file access
if (!defined('ABSPATH')) {

    exit;
}

// add top-level administrative menu
function myplugin_add_toplevel_menu()
{

    /*

    add_menu_page(
    string   $page_title,
    string   $menu_title,
    string   $capability,
    string   $menu_slug,
    callable $function = '',
    string   $icon_url = '',
    int      $position = null
    )

     */

    // add_menu_page(
    //     esc_html__('General Settings', 'myplugin'),
    //     esc_html__('WhyDonate', 'myplugin'),
    //     'manage_options',
    //     'wdplugin-feature1',
    //     'wdplugin_settings_subpage_feature1',
    //     'dashicons-screenoptions',
    //     100
    // );

    // add_submenu_page(
    //     'wdplugin-feature1',
    //     __('Plugin Feature 1', 'myplugin'),
    //     __('Feature 1', 'myplugin'),
    //     'manage_options',
    //     'wdplugin-feature1',
    //     'wdplugin_settings_subpage_feature1'
    // );

    // add_submenu_page(
    //     'wdplugin-feature1',
    //     __('Plugin Feature 2', 'myplugin'),
    //     __('Feature 2', 'myplugin'),
    //     'manage_options',
    //     'wdplugin-feature2',
    //     'wdplugin_settings_subpage_feature2'
    // );

    // **** Previous submenus for main menu start **** //

    // add_submenu_page(
    //     'myplugin',
    //     __('Add New Fundraiser', 'myplugin'),
    //     __('Feature 3', 'myplugin'),
    //     'manage_options',
    //     'new-fundraiser',
    //     'wdplugin_settings_new_fundraiser'
    // );

    // add_submenu_page(
    //     'myplugin',
    //     __('Plugin Feature 1', 'myplugin'),
    //     __('Feature 1', 'myplugin'),
    //     'manage_options',
    //     'myplugin-feature-1',
    //     'wdplugin_settings_subpage_feature1'
    // );

    // add_submenu_page(
    //     'myplugin',
    //     __('Plugin Feature 2', 'myplugin'),
    //     __('Feature 2', 'myplugin'),
    //     'manage_options',
    //     'myplugin-feature-2',
    //     'wdplugin_settings_subpage_feature2'
    // );

    // **** Previous submenus for main menu end **** //

    // add_menu_page(
    //     esc_html__('MyPlugin Settings', 'myplugin'),
    //     esc_html__('WhyDonate', 'myplugin'),
    //     'manage_options',
    //     'myplugin-settings',
    //     'wdplugin_settings',
    //     'dashicons-screenoptions',
    //     100
    // );

    // add_submenu_page(
    //     'myplugin',
    //     __('Whydonate Settings', 'myplugin'),
    //     __('Settings', 'myplugin'),
    //     'manage_options',
    //     'myplugin-settings',
    //     'wdplugin_settings'
    // );

    add_menu_page(
        esc_html__('Whydonate Plugin', 'myplugin'),
        esc_html__('Whydonate', 'myplugin'),
        'manage_options',
        'myplugin',
        'wdplugin_settings',
        plugin_dir_url(__FILE__) . 'setup/images/whydonate-logo.png',
        100
    );

    add_submenu_page(
        'myplugin',
        __('Account setup', 'myplugin'),
        __('Account setup', 'whydonate-v2'),
        'manage_options',
        'myplugin',
        'wdplugin_settings'
    );

    global $wpdb;
    $table_name = $wpdb->prefix . "wdplugin_api_key";

    $result = $wpdb->get_results("SELECT id from $table_name WHERE `id` IS NOT NULL");

    if (count($result) > 0) {
        global $wpdb;
        // $table_name = "wp_wdplugin_api_key";
        // $table_name = $wpdb->prefix . "wdplugin_api_key";
        $retrieve_data = $wpdb->get_results("SELECT * FROM $table_name WHERE id = 1");

        if (!empty($retrieve_data)) {
            $apiKey = '';
            foreach ($retrieve_data as $retrieved_data) {
                $apiKey = $retrieved_data->apiKey;
            }

            if (!empty($apiKey)) {

                // add_submenu_page(
                //     'myplugin',
                //     __('Add Widget', 'myplugin'),
                //     __('Add Widget', 'whydonate-v2'),
                //     'manage_options',
                //     'whydonate-config-widget',
                //     'wdplugin_config_widget'
                // );

                // add_submenu_page(
                //     'myplugin',
                //     __('Add New Style', 'myplugin'),
                //     __('New Style', 'myplugin'),
                //     'manage_options',
                //     'whydonate-add-style',
                //     'wdplugin_add_plugin_style'
                // );

                add_submenu_page(
                    'myplugin',
                    __('Fundraisers', 'myplugin'),
                    __('Fundraisers', 'whydonate-v2'),
                    'manage_options',
                    'whydonate-fundraiser-list',
                    'wdplugin_fundraiser_list'
                );

                add_submenu_page(
                    'myplugin',
                    __('Widgets', 'myplugin'),
                    __('Widgets', 'whydonate-v2'),
                    'manage_options',
                    'whydonate-widget-list',
                    'wdplugin_widget_list'
                );

                add_submenu_page(
                    'myplugin',
                    __('Styling', 'myplugin'),
                    __('Styling', 'whydonate-v2'),
                    'manage_options',
                    'whydonate-plugin-style-list',
                    'wdplugin_plugin_style_list'
                );

                add_submenu_page(
                    'myplugin',
                    __('Donations', 'myplugin'),
                    __('Donations', 'whydonate-v2'),
                    'manage_options',
                    'wdplugin-donation',
                    'wdplugin_donation_submenu_callback'
                );
            }
        }
    }
}

add_action('admin_menu', 'myplugin_add_toplevel_menu');
// add_action('admin_init', 'register_mysettings');
