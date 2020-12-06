<?php
/*
  Plugin Name: WP Htaccess Editor
  Plugin URI: https://wphtaccess.com/
  Description: Safe and easy way to edit the .htaccess file directly from WP admin without using FTP.
  Version: 1.65
  Author: WebFactory Ltd
  Author URI: https://www.webfactoryltd.com/
  Text Domain: wp-htaccess-editor
  Network: true

  Copyright 2011 - 2018  Lukenzi  (email: lukenzi@gmail.com)
  Copyright 2018 - 2019  WebFactory Ltd (email: support@webfactoryltd.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// include only file
if (!defined('ABSPATH')) {
  wp_die(__('Do not open this file directly.', 'wp-htaccess-editor'));
}


class WP_Htaccess_Editor
{
  protected static $instance = null;
  public $version = 0;
  public $plugin_url = '';
  public $plugin_basename = '';
  protected $options = array();
  protected $wp_filesystem = null;
  public $backup_folder = 'htaccess-editor-backups';


  /**
   * Creates a new WP_Htaccess_Editor object and implements singleton
   *
   * @return WP_Htaccess_Editor
   */
  static function get_instance()
  {
    if (false == is_a(self::$instance, 'WP_Htaccess_Editor')) {
      self::$instance = new WP_Htaccess_Editor();
    }

    return self::$instance;
  } // get_instance


  /**
   * Initialize properties, hook to filters and actions
   *
   * @return null
   */
  private function __construct()
  {
    $this->version         = $this->get_plugin_version();
    $this->plugin_url      = plugin_dir_url(__FILE__);
    $this->plugin_basename = plugin_basename(__FILE__);

    $this->load_options();
    $this->setup_wp_filesystem();

    add_action('admin_menu', array($this, 'admin_menu'));
    add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    add_action('wp_ajax_wp_htaccess_editor_dismiss_notice', array($this, 'ajax_dismiss_notice'));
    add_action('wp_ajax_wp_htaccess_editor_do_action', array($this, 'ajax_do_action'));

    add_filter('plugin_action_links_' . $this->plugin_basename, array($this, 'plugin_action_links'));
    add_filter('plugin_row_meta', array($this, 'plugin_meta_links'), 10, 2);
    add_filter('admin_footer_text', array($this, 'admin_footer_text'));
    add_filter('install_plugins_table_api_args_featured', array($this, 'featured_plugins_tab'));
  } // __construct


  /**
   * Get plugin version from file header.
   *
   * @return string
   */
  function get_plugin_version()
  {
    $plugin_data = get_file_data(__FILE__, array('version' => 'Version'), 'plugin');

    return $plugin_data['version'];
  } // get_plugin_version


  /**
   * Add "Edit .htaccess file" action link to plugins table, left side
   *
   * @param array  $links  Initial list of links.
   *
   * @return array
   */
  function plugin_action_links($links)
  {
    // whole plugin is for admins only
    if (false === current_user_can('administrator')) {
      return $links;
    }

    $settings_link = '<a href="' . admin_url('options-general.php?page=wp-htaccess-editor') . '" title="' . __('Edit .htaccess file', 'wp-htaccess-editor') . '">' . __('Edit .htaccess file', 'wp-htaccess-editor') . '</a>';

    array_unshift($links, $settings_link);

    return $links;
  } // plugin_action_links


  /**
   * Add links to plugin's description in plugins table
   *
   * @param array  $links  Initial list of links.
   * @param string $file   Basename of current plugin.
   *
   * @return array
   */
  function plugin_meta_links($links, $file)
  {
    if ($file !== $this->plugin_basename) {
      return $links;
    }

    $home_link = '<a target="_blank" href="' . $this->generate_web_link('plugins-table-right') . '" title="' . __('Plugin Homepage', 'wp-htaccess-editor') . '">' . __('Plugin Homepage', 'wp-htaccess-editor') . '</a>';
    $support_link = '<a target="_blank" href="https://wordpress.org/support/plugin/wp-htaccess-editor" title="' . __('Get help', 'wp-htaccess-editor') . '">' . __('Support', 'wp-htaccess-editor') . '</a>';
    $rate_link = '<a target="_blank" href="https://wordpress.org/support/plugin/wp-htaccess-editor/reviews/#new-post" title="' . __('Rate the plugin', 'wp-htaccess-editor') . '">' . __('Rate the plugin ★★★★★', 'wp-htaccess-editor') . '</a>';

    $links[] = $home_link;
    $links[] = $support_link;
    $links[] = $rate_link;

    return $links;
  } // plugin_meta_links


  /**
   * Test if we're on plugin's admin page
   *
   * @return bool
   */
  function is_plugin_page()
  {
    $current_screen = get_current_screen();

    if ($current_screen->id === 'settings_page_wp-htaccess-editor') {
      return true;
    } else {
      return false;
    }
  } // is_plugin_page


  /**
   * Add powered by text in admin footer
   *
   * @param string  $text_org  Default footer text.
   *
   * @return string
   */
  function admin_footer_text($text_org)
  {
    if (false === $this->is_plugin_page()) {
      return $text_org;
    }

    $text = '<i><a target="_blank" href="' . $this->generate_web_link('admin_footer') . '">WP Htaccess Editor</a> v' . $this->version . ' by <a href="https://www.webfactoryltd.com/" title="' . __('Visit our site to get more great plugins', 'wp-htaccess-editor') . '" target="_blank">WebFactory Ltd</a>.';
    $text .= ' Please <a target="_blank" href="https://wordpress.org/support/plugin/wp-htaccess-editor/reviews/#new-post" title="' . __('Rate the plugin', 'wp-htaccess-editor') . '">' . __('Rate the plugin ★★★★★', 'wp-htaccess-editor') . '</a>.</i> ';

    return $text;
  } // admin_footer_text


  /**
   * Loads plugin's translated strings
   *
   * @return null
   */
  function load_textdomain()
  {
    load_plugin_textdomain('wp-htaccess-editor');
  } // load_textdomain


  /**
   * Initialize the WP file system.
   *
   * @return object
   */
  private function setup_wp_filesystem()
  {
    global $wp_filesystem;

    if (empty($wp_filesystem)) {
      require_once ABSPATH . '/wp-admin/includes/file.php';
      WP_Filesystem();
    }

    $this->wp_filesystem = $wp_filesystem;
    return $this->wp_filesystem;
  } // setup_wp_filesystem


  /**
   * Get full backup folder path
   *
   * @return string
   */
  function get_backup_folder()
  {
    $folder = trailingslashit(WP_CONTENT_DIR) . trailingslashit($this->backup_folder);

    return $folder;
  } // get_backup_folder


  /**
   * Get .htaccess file path.
   *
   * @param bool  $folder_only  Optional. Return folder name only without filename.
   *
   * @return string
   */
  function get_htaccess_path($folder_only = false)
  {
    if ($folder_only) {
      return get_home_path();
    } else {
      return get_home_path() . '.htaccess';
    }
  } // get_htaccess_path


  /**
   * Get .htaccess file content.
   *
   * @return string
   */
  function get_htaccess_content()
  {
    $content = $this->wp_filesystem->get_contents($this->get_htaccess_path());

    return $content;
  } // get_htaccess_path


  /**
   * Check if .htaccess is writable.
   *
   * @return bool
   */
  function is_htaccess_writable()
  {
    $htaccess_path = $this->get_htaccess_path();

    return $this->wp_filesystem->is_writable($htaccess_path);
  } // is_htaccess_writable


  /**
   * Check if .htaccess exists and is redable.
   *
   * @return bool
   */
  function is_htaccess_readable()
  {
    $htaccess_path = $this->get_htaccess_path();

    return $this->wp_filesystem->is_readable($htaccess_path);
  } // is_htaccess_writable


  /**
   * Load and prepare the options array. If needed create a new DB options entry.
   *
   * @return array
   */
  private function load_options()
  {
    $options = get_option('wp-htaccess-editor', array());
    $change = false;

    if (empty($options['meta'])) {
      $options['meta'] = array('first_version' => $this->version, 'first_install' => current_time('timestamp', true), 'edits_count' => 0);
      $change = true;
    }
    if (empty($options['dismissed_notices'])) {
      $options['dismissed_notices'] = array();
      $change = true;
    }
    if (empty($options['options'])) {
      $options['options'] = array('last_backup' => false);
      $change = true;
    }
    if ($change) {
      update_option('wp-htaccess-editor', $options, true);
    }

    $this->options = $options;
    return $options;
  } // load_options


  /**
   * Get meta part of plugin options.
   *
   * @return array
   */
  function get_meta()
  {
    return $this->options['meta'];
  } // get_meta


  /**
   * Get all dismissed notices, or check for one specific notice.
   *
   * @param string  $notice_name  Optional. Check if specified notice is dismissed.
   *
   * @return bool|array
   */
  function get_dismissed_notices($notice_name = '')
  {
    $notices = $this->options['dismissed_notices'];

    if (empty($notice_name)) {
      return $notices;
    } else {
      if (empty($notices[$notice_name])) {
        return false;
      } else {
        return true;
      }
    }
  } // get_dismissed_notices


  /**
   * Get all options or a single option key.
   *
   * @param string  $option_key  Optional.
   *
   * @return array
   */
  function get_options($option_key = '')
  {
    if (empty($option_key)) {
      return $this->options['options'];
    } else {
      if (isset($this->options['options'][$option_key])) {
        return $this->options['options'][$option_key];
      } else {
        return null;
      }
    }
  } // get_options


  /**
   * Update plugin options array
   *
   * @param string  $key   Option key.
   * @param string  $data  Data to save.
   *
   * @return bool
   */
  private function update_options($key, $data)
  {
    if (false === in_array($key, array('options', 'meta', 'dismissed_notices'))) {
      trigger_error('Unknown option key used in update_options($key, $data) function.', E_USER_ERROR);
      return false;
    }

    $this->options[$key] = $data;
    $tmp = update_option('wp-htaccess-editor', $this->options);

    return $tmp;
  } // set_options


  /**
   * Add plugin menu entry under Settings menu
   *
   * @return null
   */
  function admin_menu()
  {
    add_options_page(__('WP Htaccess Editor', 'wp-htaccess-editor'), __('WP Htaccess Editor', 'wp-htaccess-editor'), 'administrator', 'wp-htaccess-editor', array($this, 'plugin_page'));
  } // admin_menu


  /**
   * Helper function for generating UTM tagged links
   *
   * @param string  $placement  Optional. UTM content param.
   * @param string  $page       Optional. Page to link to.
   * @param array   $params     Optional. Extra URL params.
   * @param string  $anchor     Optional. URL anchor part.
   *
   * @return string
   */
  function generate_web_link($placement = '', $page = '/', $params = array(), $anchor = '')
  {
    $base_url = 'https://wphtaccess.com';

    if ('/' != $page) {
      $page = '/' . trim($page, '/') . '/';
    }
    if ($page == '//') {
      $page = '/';
    }

    $parts = array_merge(array('utm_source' => 'wp-htaccess-free', 'utm_medium' => 'plugin', 'utm_content' => $placement, 'utm_campaign' => 'wp-htaccess-free-v' . $this->version), $params);

    if (!empty($anchor)) {
      $anchor = '#' . trim($anchor, '#');
    }

    $out = $base_url . $page . '?' . http_build_query($parts, '', '&amp;') . $anchor;

    return $out;
  } // generate_web_link


  /**
   * Dismiss notice via AJAX call
   *
   * @return null
   */
  function ajax_dismiss_notice()
  {
    check_ajax_referer('wp-htaccess-editor_dismiss_notice');

    // complete plugin is for admins only
    if (false === current_user_can('administrator')) {
      wp_send_json_error(__('You are not allowed to perform this action.', 'wp-htaccess-editor'));
    }

    if (empty($_GET['notice_name'])) {
      wp_send_json_error(__('Notice name is undefined.', 'wp-htaccess-editor'));
    } else {
      $notice_name = substr(sanitize_key($_GET['notice_name']), 0, 64);
    }

    if (!$this->dismiss_notice($notice_name)) {
      wp_send_json_error(__('Notice is already dismissed.', 'wp-htaccess-editor'));
    } else {
      wp_send_json_success();
    }
  } // ajax_dismiss_notice


  /**
   * Dismiss notice by adding it to dismissed_notices options array
   *
   * @param string  $notice_name  Notice to dismiss.
   *
   * @return bool
   */
  function dismiss_notice($notice_name)
  {
    if ($this->get_dismissed_notices($notice_name)) {
      return false;
    } else {
      $notices = $this->get_dismissed_notices();
      $notices[$notice_name] = true;
      $this->update_options('dismissed_notices', $notices);
      return true;
    }
  } // dismiss_notice


  /**
   * Returns all WP pointers
   *
   * @return array
   */
  function get_pointers()
  {
    $pointers = array();
    $meta = $this->get_meta();

    // TODO: reformat & prepare for translation
    $pointers['welcome'] = array('target' => '#menu-settings', 'edge' => 'left', 'align' => 'right', 'content' => 'Thank you for using the <b style="font-weight: 700;">WP Htaccess Editor</b> plugin.<br>Open <a href="' . admin_url('options-general.php?page=wp-htaccess-editor') . '">Settings - WP Htaccess Editor</a> to access the editor and start editing the <i>.htaccess</i> file.');

    if (true === version_compare($meta['first_version'], '1.60', '<')) {
      // TODO: reformat & prepare for translation
      $pointers['menu-relocation'] = array('target' => '#menu-settings', 'edge' => 'left', 'align' => 'right', 'content' => 'We\'ve moved the <b style="font-weight: 700;">WP Htaccess Editor</b> plugin from Tools to the Settings menu. Sorry for the inconvenience. <br>To edit htaccess open <a href="' . admin_url('options-general.php?page=wp-htaccess-editor') . '">Settings - WP Htaccess Editor</a>.');
    }

    return $pointers;
  } // get_pointers


  /**
   * Enqueue CSS and JS files
   *
   * @param string  $hook  Page hook name.
   *
   * @return null
   */
  function admin_enqueue_scripts($hook)
  {
    // welcome pointer is shown on all pages except WPHE, only to admins, until dismissed
    $pointers = $this->get_pointers();
    $dismissed_notices = $this->get_dismissed_notices();

    foreach ($dismissed_notices as $notice_name => $tmp) {
      if ($tmp) {
        unset($pointers[$notice_name]);
      }
    } // foreach

    if (current_user_can('administrator') && !empty($pointers) && 'settings_page_wp-htaccess-editor' != $hook) {
      $pointers['_nonce_dismiss_pointer'] = wp_create_nonce('wp-htaccess-editor_dismiss_notice');

      wp_enqueue_style('wp-pointer');

      wp_enqueue_script('wp-htaccess-editor-pointers', $this->plugin_url . 'js/wp-htaccess-editor-pointers.js', array('jquery'), $this->version, true);
      wp_enqueue_script('wp-pointer');
      wp_localize_script('wp-pointer', 'wp_htaccess_editor_pointers', $pointers);
    }

    // exit early if not on WPHE page
    if ('settings_page_wp-htaccess-editor' != $hook) {
      return;
    }

    $editor_settings['codeEditor'] = wp_enqueue_code_editor(array($this->get_htaccess_path()));
    $editor_settings['codeEditor']['codemirror']['mode'] = 'nginx';

    $js_localize = array(
      'undocumented_error' => __('An undocumented error has occurred. Please refresh the page and try again.', 'wp-htaccess-editor'),
      'documented_error' => __('An error has occurred.', 'wp-htaccess-editor'),
      'plugin_name' => __('WP Htaccess Editor', 'wp-htaccess-editor'),
      'home_url' => get_home_url(),
      'settings_url' => admin_url('options-general.php?page=wp-htaccess-editor'),
      'loading_icon_url' => $this->plugin_url . 'img/loading-icon.png',
      'cancel_button' => __('Cancel', 'wp-htaccess-editor'),
      'ok_button' => __('OK', 'wp-htaccess-editor'),
      'saving' => __('Saving in progress. Please wait.', 'wp-htaccess-editor'),
      'restoring' => __('Restoring in progress. Please wait.', 'wp-htaccess-editor'),
      'save_success' => __('Changes have been saved.', 'wp-htaccess-editor'),
      'restore_message' => __('This will restore the last saved backup of .htaccess. There is NO UNDO.', 'wp-htaccess-editor'),
      'restore_title' => __('Restore last backup?', 'wp-htaccess-editor'),
      'restore_button' => __('Restore Last Saved Backup of .htaccess', 'wp-htaccess-editor'),
      'restore_success' => __('Backup has been successfully restored. Click OK to reload the page.', 'wp-htaccess-editor'),
      'test_success' => __('Test Completed Successfully', 'wp-htaccess-editor'),
      'test_failed' => __('Test Failed', 'wp-htaccess-editor'),
      'testing' => __('Testing .htaccess syntax. Please wait.', 'wp-htaccess-editor'),
      'site_error' => __('There is an error in the .htaccess file and your site is probably no longer accessible.<br><br>DO NOT panic or reload this page. Close this message. First, try the "Restore Last Backup" button. If it doesn\'t work read instruction on this very page on how to restore the site.', 'wp-htaccess-editor'),
      'nonce_dismiss_notice' => wp_create_nonce('wp-htaccess-editor_dismiss_notice'),
      'nonce_do_action' => wp_create_nonce('wp-htaccess-editor_do_action'),
      'cm_settings' => $editor_settings
    );

    wp_enqueue_style('wp-codemirror');
    wp_enqueue_style('wp-htaccess-editor', $this->plugin_url . 'css/wp-htaccess-editor.css', array(), $this->version);
    wp_enqueue_style('wp-htaccess-editor-sweetalert2', $this->plugin_url . 'css/sweetalert2.min.css', array(), $this->version);

    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('wp-htaccess-editor-sweetalert2', $this->plugin_url . 'js/sweetalert2.min.js', array('jquery'), $this->version, true);
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_script('wp-htaccess-editor', $this->plugin_url . 'js/wp-htaccess-editor.js', array('jquery'), $this->version, true);
    wp_enqueue_script('wp-htaccess-cm-resize', $this->plugin_url . 'js/cm-resize.min.js', array('jquery'), $this->version, true);
    wp_localize_script('wp-htaccess-editor', 'wp_htaccess_editor', $js_localize);

    // fix for aggressive plugins that include their CSS files on all pages
    wp_dequeue_style('uiStyleSheet');
    wp_dequeue_style('wpcufpnAdmin');
    wp_dequeue_style('unifStyleSheet');
    wp_dequeue_style('wpcufpn_codemirror');
    wp_dequeue_style('wpcufpn_codemirrorTheme');
    wp_dequeue_style('collapse-admin-css');
    wp_dequeue_style('jquery-ui-css');
    wp_dequeue_style('tribe-common-admin');
    wp_dequeue_style('file-manager__jquery-ui-css');
    wp_dequeue_style('file-manager__jquery-ui-css-theme');
    wp_dequeue_style('wpmegmaps-jqueryui');
    wp_dequeue_style('wp-botwatch-css');
  } // admin_enqueue_scripts


  /**
   * Run an action via AJAX call.
   *
   * @return null
   */
  function ajax_do_action()
  {
    check_ajax_referer('wp-htaccess-editor_do_action');

    if (false === current_user_can('administrator')) {
      wp_send_json_error(__('You are not allowed to perform this action.', 'wp-htaccess-editor'));
    }

    if (empty($_POST['subaction'])) {
      wp_send_json_error(__('Subaction name is undefined.', 'wp-htaccess-editor'));
    } else {
      $subaction = substr(sanitize_key($_POST['subaction']), 0, 64);
    }

    if ($subaction == 'save_htaccess') {
      if (false == $this->create_htaccess_backup(true, true)) {
        wp_send_json_error(__('Unable to create .htaccess backup in /wp-content/. Please check file permissions.', 'wp-htaccess-editor'));
      }

      // since we're working with code it's hard/impossible to sanitize input
      // WP core doesn't sanitize either for plugin/theme editor so it should be OK
      // nonce is in place and user permissions are double checked
      $new_content = wp_unslash(trim($_POST['new_content']));
      if (false == $this->is_htaccess_writable() || false == $this->write_htaccess($new_content)) {
        wp_send_json_error(__('Could not write .htaccess file. Please check file permissions.', 'wp-htaccess-editor'));
      }

      wp_send_json_success();
    } elseif ($subaction == 'test_htaccess') {
      $new_content = wp_unslash(trim($_POST['new_content']));
      $uploads_directory = wp_upload_dir();
      $test_id = rand(1000, 9999);
      $htaccess_test_folder = $uploads_directory['basedir'] . '/htaccess-test-' . $test_id . '/';
      $htaccess_test_url = $uploads_directory['baseurl'] . '/htaccess-test-' . $test_id . '/';

      // Create test directory and files
      if (!$this->wp_filesystem->is_dir($htaccess_test_folder)) {
        if (true !== $this->wp_filesystem->mkdir($htaccess_test_folder, 0777)) {
          wp_send_json_error(__('Failed to create test directory. Please check that your uploads folder is writable.', 'wp-htaccess-editor'));
        }
      }

      if (true !== $this->wp_filesystem->put_contents($htaccess_test_folder . 'index.html', 'htaccess-test-' . $test_id)) {
        wp_send_json_error(__('Failed to create test files. Please check that your uploads folder is writable.', 'wp-htaccess-editor'));
      }

      if (true !== $this->wp_filesystem->put_contents($htaccess_test_folder . '.htaccess', $new_content)) {
        wp_send_json_error(__('Failed to create test directory and files. Please check that your uploads folder is writeable.', 'wp-htaccess-editor'));
      }

      // Retrieve test file over http
      $response = wp_remote_get($htaccess_test_url . 'index.html', array('sslverify' => false, 'redirection' => 0));
      $response_code = wp_remote_retrieve_response_code($response);

      // Remove Test Directory
      $this->wp_filesystem->delete($htaccess_test_folder . '.htaccess');
      $this->wp_filesystem->delete($htaccess_test_folder . 'index.html');
      $this->wp_filesystem->rmdir($htaccess_test_folder);

      // Check if test file content is what we expect
      if ((in_array($response_code, range(200, 299)) && !is_wp_error($response) && wp_remote_retrieve_body($response) == 'htaccess-test-' . $test_id) || (in_array($response_code, range(300, 399)) && !is_wp_error($response))) {
        wp_send_json_success(__('This test only makes sure there are no syntax errors that could result in 500 errors for your entire site. It does not check the logic of the <i>.htaccess</i> file, ie if redirects work as intended.', 'wp-htaccess-editor'));
      } else {
        wp_send_json_error(__('There are syntax errors in your unsaved <i>.htaccess</i> content. Saving it will cause your entire site, including the admin, to become inaccessible. Fix the errors before saving.', 'wp-htaccess-editor'));
      }
    } elseif ($subaction == 'restore_htaccess_from_db') {
      $res = $this->restore_db_backup();
      if (!is_wp_error($res)) {
        wp_send_json_success();
      } else {
        wp_send_json_error($res->get_error_message());
      }
    } else {
      wp_send_json_error(__('Unknown subaction.', 'wp-htaccess-editor'));
    }
  } // ajax_do_action


  /**
   * Write new content to .htaccess
   *
   * @param string  $new_content  New content for .htaccess file.
   *
   * @return bool
   */
  function write_htaccess($new_content)
  {
    $htaccess_path = $this->get_htaccess_path();
    $result = $this->wp_filesystem->put_contents($htaccess_path, $new_content);
    @clearstatcache();

    if (true === $result) {
      $meta = $this->get_meta();
      $meta['edits_count']++;
      $this->update_options('meta', $meta);
      return true;
    } else {
      return false;
    }
  } // write_htaccess


  /**
   * Backup .htaccess file.
   *
   * @param bool  $db_save  Create backup in DB.
   * @param bool  $file_save  Create backup in FS.
   *
   * @return bool
   */
  function create_htaccess_backup($db_save = true, $file_save = true)
  {
    $success = false;
    $orig_path = $this->get_htaccess_path();

    if ($this->is_htaccess_readable()) {
      $htaccess_content_orig = $this->wp_filesystem->get_contents($orig_path);
    } else {
      // .htaccess doesn't exists, nothing to backup
      return true;
    }

    if ($db_save) {
      $tmp = $this->get_options();
      $tmp['last_backup'] = $htaccess_content_orig;
      $this->update_options('options', $tmp);
      $success = true;
    }

    if ($file_save) {
      if (!$this->create_secure_backup_folder()) {
        return false;
      }

      $backup_path = trailingslashit(WP_CONTENT_DIR) . $this->backup_folder . '/htaccess-' . date('Y-m-d-H-i-s', current_time('timestamp')) . '.backup';
      $success = $this->wp_filesystem->put_contents($backup_path, $htaccess_content_orig);
    }

    if ($success) {
      return true;
    } else {
      return false;
    }
  } // create_htaccess_backup


  /**
   * Create .htaccess backup folder and secure it.
   *
   * @return bool
   */
  function create_secure_backup_folder()
  {
    @clearstatcache();
    $secure_path = $this->get_backup_folder() . '.htaccess';
    $secure_text = trim('
# WP Htaccess Editor - secure backups
<files *.*>
  order allow,deny
  deny from all
</files>');

    if (false == $this->wp_filesystem->is_dir($this->get_backup_folder())) {
      $new_folder = $this->wp_filesystem->mkdir($this->get_backup_folder());
      if (!$new_folder) {
        return false;
      }
    }

    if ($this->wp_filesystem->exists($secure_path)) {
      return true;
    } else {
      $write = $this->wp_filesystem->put_contents($secure_path, $secure_text);
      if ($write) {
        return true;
      } else {
        return false;
      }
    }
  } // create_secure_backup_folder


  /**
   * Restore last .htaccess backup from DB.
   *
   * @return bool
   */
  function restore_db_backup()
  {
    $htaccess_path = $this->get_htaccess_path();

    $backup_contents = $this->get_options('last_backup');

    if (false === $backup_contents) {
      return new WP_Error(1, __('There is no available backup to restore.', 'wp-htaccess-editor'));
    }

    if (!empty($backup_contents)) {
      @clearstatcache();
      $write = $this->wp_filesystem->put_contents($htaccess_path, $backup_contents);
      @clearstatcache();
      if ($write) {
        return true;
      } else {
        return new WP_Error(1, __('Unable to write .htaccess file.', 'wp-htaccess-editor'));
      }
    } else {
      return new WP_Error(1, __('Backup data is empty.', 'wp-htaccess-editor'));
    }
  } // restore_db_backup


  /**
   * Outputs complete plugin's admin page
   *
   * @return null
   */
  function plugin_page()
  {
    $notice_shown = false;
    $meta = $this->get_meta();
    $htaccess_content = $this->get_htaccess_content();

    // double check for admin priv
    if (!current_user_can('administrator')) {
      wp_die(__('Sorry, you are not allowed to access this page.', 'wp-htaccess-editor'));
    }

    settings_errors();
    echo '<div class="wrap">';
    echo '<h1><img src="' . $this->plugin_url . 'img/wp-htaccess-editor-logo.png" alt="' . __('WP Htaccess Editor', 'wp-htaccess-editor') . '" title="' . __('WP Htaccess Editor', 'wp-htaccess-editor') . '"></h1>';
    echo '<form id="wp-htaccess-editor-form" action="' . admin_url('options-general.php?page=wp-htaccess-editor') . '" method="post" autocomplete="off">';

    // TODO: properly mark for translation
    if (false === $this->is_htaccess_readable()) {
      echo '<div class="card notice-wrapper notice-error">';
      echo '<h2>' . __('.htaccess file does not exist!', 'wp-htaccess-editor') . '</h2>';
      echo '<p>We couldn\'t locate your .htaccess file on the default location <code>' . $this->get_htaccess_path() . '</code>. We\'ll attempt to create the file when you make the first save with the editor.<br>We recommend opening <a href="' . admin_url('options-permalink.php') . '">Settings - Permalinks</a> and setting a URL structure other than plain which will create the default WP .htaccess file.</p>';
      echo '</div>';
      $notice_shown = true;
    }

    // TODO: properly mark for translation
    if (false === $this->is_htaccess_writable() && false === $notice_shown) {
      echo '<div class="card notice-wrapper notice-error">';
      echo '<h2>' . __('.htaccess file is not writable!', 'wp-htaccess-editor') . '</h2>';
      echo '<p>Your .htaccess file located in <code>' . $this->get_htaccess_path() . '</code> can\'t be edited by WordPress or this plugin. You can only edit it via FTP.<br>If you want to edit it via WordPress check the file permissions and set them to 644.</p>';
      echo '</div>';
      $notice_shown = true;
    }

    // TODO: properly mark for translation
    echo '<div class="card" id="card-description">';
    echo '<a class="toggle-card" href="#" title="' . __('Collapse / expand box', 'wp-htaccess-editor') . '"><span class="dashicons dashicons-arrow-up-alt2"></span></a>';
    echo '<h2>' . __('Please read carefully before proceeding', 'wp-htaccess-editor') . '</h2>';
    echo '<p>There is nothing wrong with editing the .htaccess file. However, <b>in case you make a mistake while editing it, there is a possibility you\'ll need FTP access to restore your site to a working state</b>. That\'s why this plugin makes automatic backups, and we have described in detail how to recover from such incidents in the paragraphs below.<br>';

    echo 'For more details about .htaccess syntax and examples, please visit the <a href="http://httpd.apache.org/docs/current/howto/htaccess.html" target="_blank">official Apache Tutorial</a>.</p>';

    echo '<b>How to restore the site in case of error 500 or white screen caused by .htaccess</b>';
    echo '<p>Do not panic. No data is lost, and your site will be up again in minutes. FTP to your site or open the server\'s control panel such as cPanel to locate the .htaccess file in <code>' . $this->get_htaccess_path() . '</code>. Once you find the file there are several options to restore the site;<ol><li>Edit the file and fix the error(s) you made, or</li><li>Delete the file. Obviously, any custom rules in it will be gone, and in order for permalinks to work again you have to visit <a href="' . admin_url('options-permalink.php') . '">WP Admin - Options - Permalinks</a> and click "Save Changes". This will rebuild the default .htaccess file, or</li><li>Third (and preferred) way of fixing is to restore the file from the backup which you\'ll find in the <code>' . $this->get_backup_folder()  . '</code> folder. The folder will probably contain multiple backup files. Locate the latest one by looking at the timestamp in the filename. Once located copy the file to <code>' . $this->get_htaccess_path(true) . '</code> and rename it to .htaccess.</li></ol>';

    echo '<b>How to restore .htaccess in case of a non-white-screen error</b>';
    echo '<p>Click the "Restore Last Saved Backup" button below the editor and .htaccess will be restored to the version before the last save. Please note that this method only works if the error in the file is logical, not syntactical. For instance, if you banned the wrong IP you can undo. But if you misspelled "RewriteCond" you have to use the method above as the only way to recover is via FTP or cPanel.</p>';

    echo '<b>Support</b>';
    echo '<p>For additional support and questions, please visit the <a href="https://wordpress.org/support/plugin/wp-htaccess-editor" target="_blank">official support forum</a> or <a href="mailto:support@webfactoryltd.com?subject=WP Htaccess Editor problems">email us</a> if the issue can\'t be discussed publically.</p>';
    echo '</div>';


    // ask for rating after first save
    if (true == $this->get_dismissed_notices('rate')) {
      // notice dismissed, never show again
    } else {
      if ($meta['edits_count'] > 0 && false === $notice_shown) {
        $notice_shown = true;
        $show_rate_notice = '';
      } else {
        $show_rate_notice = 'display: none;';
      }

      echo '<div id="wphe-rating-notice" class="card notice-wrapper" style="' . $show_rate_notice . '">';
      echo '<h2>' . __('Please help us keep the plugin free &amp; up-to-date', 'wp-htaccess-editor') . '</h2>';
      echo '<p>' . __('If you use &amp; enjoy WP Htaccess Editor, <b>please rate it on WordPress.org</b>. It only takes a second and helps us keep the plugin free and maintained. Thank you!', 'wp-htaccess-editor') . '</p>';
      echo '<p><a class="button-primary button" title="' . __('Rate WP Htaccess Editor', 'wp-htaccess-editor') . '" target="_blank" href="https://wordpress.org/support/plugin/wp-htaccess-editor/reviews/#new-post">' . __('Help keep the plugin free - rate it!', 'wp-htaccess-editor') . '</a>  <a href="#" class="wphe-dismiss-notice dismiss-notice-rate" data-notice="rate">' . __('I\'ve already rated it', 'wp-htaccess-editor') . '</a></p>';
      echo '</div>';
    }

    echo '<div id="htaccess-editor-wrap">';
    if (false == $this->get_dismissed_notices('editor-warning')) {
      echo '<div id="enable-editor-notice" class="notice-wrapper"><h3><strong>Please be careful when editing the .htaccess file!</strong><br>This plugin makes automatic backups every time you make a change. Detailed instructions on how to restore backups are available in the box above.</h3><br><a href="#" data-notice="editor-warning" class="wphe-dismiss-notice button button-secondary">I understand. Enable the editor.</a></div>';
    }
    echo '<textarea cols="70" rows="20" name="newcontent" id="newcontent" aria-describedby="editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4">' . esc_textarea($htaccess_content) . '</textarea>';
    echo '</div>';

    echo '<p id="wphe-buttons" style="' . ($this->get_dismissed_notices('editor-warning') ? '' : 'display: none;') . '">';
    echo '<a id="wphe_save_htaccess" href="#" class="button button-primary"> Save Changes</a>';
    echo '<a id="wphe_test_htaccess" href="#" class="button button-secondary">Test Before Saving</a>';
    echo '<a id="wphe_restore_htaccess" href="#" class="button button-secondary">Restore Last Backup</a>';
    echo '</p>';

    echo '</form>';
    echo '</div>'; // wrap
  } // plugin_page


  /**
   * Adds plugins to recommended list
   *
   * @param object  $res  List of plugins for recommended list
   * 
   * @return object
   */
  function plugins_api_result($res)
  {
    remove_filter('plugins_api_result', array($this, 'plugins_api_result'), 10);

    $res = $this->add_plugin_favs('wp-reset', $res);
    $res = $this->add_plugin_favs('eps-301-redirects', $res);
    $res = $this->add_plugin_favs('simple-author-box', $res);

    return $res;
  } // plugins_api_result


  /**
   * Helper function for adding plugins to recommended list
   *
   * @param $args
   * 
   * @return $args
   */
  function featured_plugins_tab($args)
  {
    add_filter('plugins_api_result', array($this, 'plugins_api_result'), 10);

    return $args;
  } // featured_plugins_tab


  /**
   * Fetches plugin info from cache or wp.org
   *
   * @param string  $plugin_slug  Plugin slug
   * @param object  $res  List of plugins
   * 
   * @return object $res List of plugins
   */
  function add_plugin_favs($plugin_slug, $res)
  {
    if (!empty($res->plugins) && is_array($res->plugins)) {
      foreach ($res->plugins as $plugin) {
        if (is_object($plugin) && !empty($plugin->slug) && $plugin->slug == $plugin_slug) {
          return $res;
        }
      } // foreach
    }

    $plugin_info = get_transient('wf-plugin-info-' . $plugin_slug);
    if ($plugin_info && is_object($plugin_info)) {
      array_unshift($res->plugins, $plugin_info);
    } else {
      $plugin_info = plugins_api('plugin_information', array(
        'slug'   => $plugin_slug,
        'is_ssl' => is_ssl(),
        'fields' => array(
          'banners'           => true,
          'reviews'           => true,
          'downloaded'        => true,
          'active_installs'   => true,
          'icons'             => true,
          'short_description' => true,
        )
      ));
      if (!is_wp_error($plugin_info)) {
        array_unshift($res->plugins, $plugin_info);
        set_transient('wf-plugin-info-' . $plugin_slug, $plugin_info, DAY_IN_SECONDS * 7);
      }
    }

    return $res;
  } // add_plugin_favs


  /**
   * Clean up on uninstall; no action on deactive at the moment.
   *
   * @return null
   */
  static function uninstall()
  {
    delete_option('wp-htaccess-editor');
  } // uninstall


  /**
   * Disabled; we use singleton pattern so magic functions need to be disabled.
   *
   * @return null
   */
  private function __clone()
  { }


  /**
   * Disabled; we use singleton pattern so magic functions need to be disabled.
   *
   * @return null
   */
  private function __sleep()
  { }


  /**
   * Disabled; we use singleton pattern so magic functions need to be disabled.
   *
   * @return null
   */
  private function __wakeup()
  { }
} // WP_Htaccess_Editor class


// Create plugin instance and hook things up
// Only in admin; there's no frontend functionality
if (is_admin()) {
  global $wp_htaccess_editor;
  $wp_htaccess_editor = WP_Htaccess_Editor::get_instance();
  add_action('plugins_loaded', array($wp_htaccess_editor, 'load_textdomain'));
  register_uninstall_hook(__FILE__, array('WP_Htaccess_Editor', 'uninstall'));
}
