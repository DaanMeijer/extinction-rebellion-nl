<?php // MyPlugin - Settings Page

// disable direct file access
if (!defined('ABSPATH')) {

    exit;
}

function wdplugin_settings_new_fundraiser()
{
    // Double check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">

            <?php

            // output security fields
            settings_fields('wdplugin_new_fundraiser');

            // output setting sections
            do_settings_sections('wdplugin_new_fundraiser');

            // submit button
            submit_button();

            ?>

        </form>
    </div>
<?php
}

function wdplugin_settings_subpage_feature1()
{
    // Double check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">

            <?php

            // output security fields
            settings_fields('wdplugin_feature1_settings');

            // output setting sections
            do_settings_sections('wdplugin_feature1_settings');

            // submit button
            submit_button();

            ?>

        </form>
    </div>
<?php
}

function wdplugin_settings_subpage_feature2()
{
    // Double check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

?>

    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">

            <?php

            // output security fields
            settings_fields('wdplugin_feature2_settings');

            // output setting sections
            do_settings_sections('wdplugin_feature2_settings');

            // submit button
            submit_button();

            ?>

        </form>
    </div>
<?php
}

// display the plugin settings page
function myplugin_display_settings_page()
{

    // check if user is allowed access
    if (!current_user_can('manage_options')) {
        return;
    }

?>

    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">

            <?php

            // output security fields
            settings_fields('myplugin_options');

            // output setting sections
            do_settings_sections('myplugin');

            // submit button
            submit_button();

            ?>

        </form>
    </div>

<?php

}

function wdplugin_settings()
{

    // check if user is allowed access
    if (!current_user_can('manage_options')) {
        return;
    }

    include_once 'setup/wdplugin-settings-display-page.php';
    //include_once 'setup/wdplugin-add-plugin-styles.php';
}

function wdplugin_config_widget()
{
    // check if user is allowed access
    if (!current_user_can('manage_options')) {
        return;
    }

    include_once 'setup/wdplugin-config-setup.php';
}

function wdplugin_add_fundraiser()
{
    // check if user is allowed access
    if (!current_user_can('manage_options')) {
        return;
    }

    include_once 'setup/wdplugin-add-fundraiser.php';
}

function wdplugin_fundraiser_list()
{
    // check if user is allowed access
    if (!current_user_can('manage_options')) {
        return;
    }

    include_once 'setup/wdplugin-fundraiser-list.php';
}

function wdplugin_widget_list()
{
    // check if user is allowed access
    if (!current_user_can('manage_options')) {
        return;
    }

    include_once 'setup/wdplugin-widget-list.php';
}

function wdplugin_plugin_style_list()
{
    // check if user is allowed access
    if (!current_user_can('manage_options')) {
        return;
    }

    include_once 'setup/wdplugin-plugin-style-list.php';
}

function wdplugin_add_plugin_style()
{
    // check if user is allowed access
    if (!current_user_can('manage_options')) {
        return;
    }

    include_once 'setup/wdplugin-add-plugin-styles.php';
}

function wdplugin_donation_submenu_callback()
{

    if (!current_user_can('manage_options')) {
        return;
    }

    include_once 'setup/wdplugin-donation.php';

    // if (get_locale() == 'en_US') {
    //     echo '<script type="text/javascript">
    //     window.location.replace("https://www.whydonate.eu/dashboard/")</script>';
    // } else {
    //     echo '<script type="text/javascript">
    //     window.location.replace("https://www.whydonate.nl/dashboard/")</script>';
    // }

    // if (get_locale() == 'en_US') {
    //     // echo '<script type="text/javascript">
    //     // $.ajax({
    //     //     url: "url",
    //     //     type: "POST",
    //     //     async: false,
    //     //     success: function() {
    //     //         window.open("https://www.whydonate.eu/dashboard/", "_blank");              
    //     //     }
    //     // });';
    //     // window.open("https://www.whydonate.eu/dashboard/", "_blank").focus();';

    //     echo '<script type="text/javascript">
    //     window.open("https://www.whydonate.eu/dashboard/", "_blank").focus();';
    // } else {
    //     echo '<script type="text/javascript">
    //     window.open("https://www.whydonate.nl/dashboard/", "_blank").focus();';
    // }
}

add_filter('style-list', 'wdplugin_add_plugin_style');
