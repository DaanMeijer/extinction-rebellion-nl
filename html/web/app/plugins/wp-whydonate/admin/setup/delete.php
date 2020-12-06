<?php

function remove_row()
{

    $id = $_POST['id'];

    echo 'called and try to delete called and try to delete called and try to delete called and try to delete called and try to delete' . $id;

    global $wpdb;
    $table_name = $wpdb->prefix . "wdplugin_style";
    $reponse = $wpdb->delete($table_name, array('id' => $id), array('%d'));

    wp_die();
}

add_action('wp_ajax_remove_row', 'remove_row');
