<?php
/*
  Plugin Name: Open External Links in a New Window
  Plugin URI: https://wordpress.org/extend/plugins/open-external-links-in-a-new-window/
  Description: Opens all external links in a new window. XHTML Strict compliant and search engine optimized (SEO).
  Version: 1.4
  Author: WebFactory Ltd
  Author URI: https://www.webfactoryltd.com/
  Text Domain: open-external-links-in-a-new-window
  License: GPL2

  Copyright 2015 - 2019  WebFactory Ltd (email: support@webfactoryltd.com)

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
  wp_die('Do not open this file directly.');
}

load_plugin_textdomain('open-external-links-in-a-new-window');
add_action('wp_head', 'external_links_in_new_windows_client');

// Loads the code for the website
function external_links_in_new_windows_client()
{
  echo "\n\n<!-- ".__("This site uses the Open External Links in a New Window plugin v1.4 by WebFactory Ltd. ","open-external-links-in-a-new-window"). __("Download it for free at", "open-external-links-in-a-new-window")." https://wordpress.org/extend/plugins/open-external-links-in-a-new-window/ -->\n";

  $blogdomain = parse_url(get_option('home'));  
  echo "<script type=\"text/javascript\">//<![CDATA[";
  echo "
  function external_links_in_new_windows_loop() {
    if (!document.links) {
      document.links = document.getElementsByTagName('a');
    }
    var change_link = false;
    var force = '".trim(get_option("external_links_in_new_windows_force", ''))."';
    var ignore = '".trim(get_option("external_links_in_new_windows_ignore", ''))."';

    for (var t=0; t<document.links.length; t++) {
      var all_links = document.links[t];
      change_link = false;
      
      if(document.links[t].hasAttribute('onClick') == false) {
        // forced if the address starts with http (or also https), but does not link to the current domain
        if(all_links.href.search(/^http/) != -1 && all_links.href.search('".$blogdomain['host']."') == -1 && all_links.href.search(/^#/) == -1) {
          // console.log('Changed ' + all_links.href);
          change_link = true;
        }
          
        if(force != '' && all_links.href.search(force) != -1) {
          // forced
          // console.log('force ' + all_links.href);
          change_link = true;
        }
        
        if(ignore != '' && all_links.href.search(ignore) != -1) {
          // console.log('ignore ' + all_links.href);
          // ignored
          change_link = false;
        }

        if(change_link == true) {
          // console.log('Changed ' + all_links.href);
          document.links[t].setAttribute('onClick', 'javascript:window.open(\\''+all_links.href+'\\'); return false;');
          document.links[t].removeAttribute('target');
        }
      }
    }
  }
  
  // Load
  function external_links_in_new_windows_load(func)
  {  
    var oldonload = window.onload;
    if (typeof window.onload != 'function'){
      window.onload = func;
    } else {
      window.onload = function(){
        oldonload();
        func();
      }
    }
  }

  external_links_in_new_windows_load(external_links_in_new_windows_loop);
  ";

  echo "//]]></script>\n\n";
}

// Administration interface

/* What to do when the plugin is activated? */
register_activation_hook(__FILE__, 'external_links_in_new_windows_activate');

/* What to do when the plugin is deactivated? */
register_deactivation_hook( __FILE__, 'external_links_in_new_windows_deactivate' );

function external_links_in_new_windows_activate() {
  update_option("external_links_in_new_windows_force", '');
  update_option("external_links_in_new_windows_ignore", '');
}

function external_links_in_new_windows_deactivate() {
  delete_option('external_links_in_new_windows_force');
  delete_option('external_links_in_new_windows_ignore');
}

add_action('admin_menu', 'external_links_in_new_windows_admin_menu');
function external_links_in_new_windows_admin_menu() {
  add_options_page(__('Open external links in a new window', "open-external-links-in-a-new-window"), __('External Links',"open-external-links-in-a-new-window"), 'manage_options', 'external_links_in_new_windows', 'external_links_in_new_windows_admin_options_page');
}

function external_links_in_new_windows_admin_options_page() {
?>
  <div class="wrap">
  <h2><?php _e("Open External Links in a New Window", "open-external-links-in-a-new-window"); ?></h2>
  <p>
  <form method="post" action="options.php">
  <?php wp_nonce_field('update-options'); ?>
  
  <?php _e("By default, this plugins makes all external links (i.e. links that point outside the current host name) open in a new window/tab.","open-external-links-in-a-new-window"); ?><br />
  
  <?php _e("You can change this behavior by providing regular expressions that either forces or ignores whether the link should open in a new window.","open-external-links-in-a-new-window"); ?><br />
  <br />

  
  <?php _e("Force links to open in a new window if they match this JS regular expression:","open-external-links-in-a-new-window"); ?><br />
  <input class="regular-text code" name="external_links_in_new_windows_force" type="text" id="external_links_in_new_windows_force" value="<?php echo esc_attr(get_option('external_links_in_new_windows_force', '')); ?>" /><br /><br />

  <?php _e("Ignore links, and do not open them in a new window, if they match this JS regular expression: (even if they start with http:// or https://)","open-external-links-in-a-new-window"); ?><br />
  <input class="regular-text code" name="external_links_in_new_windows_ignore" type="text" id="external_links_in_new_windows_ignore" value="<?php echo esc_attr(get_option('external_links_in_new_windows_ignore', '')); ?>" /><br /><br />

<p>Like the plugin? <a href="https://wordpress.org/support/plugin/open-external-links-in-a-new-window/reviews/#new-post" target="_blank">Please rate it ★★★★★.</a> Thank you!</p>
</p>

  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="page_options" value="external_links_in_new_windows_force,external_links_in_new_windows_ignore" />
  <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
  
    
  <?php _e("The matches are prioritized in the following order:","open-external-links-in-a-new-window"); ?>
  <ol>
    <li><?php _e("Ignored, matching URLs","open-external-links-in-a-new-window"); ?></li>
    <li><?php _e("Forced, matching URLs","open-external-links-in-a-new-window"); ?></li>
    <li><?php _e("http:// or https://","open-external-links-in-a-new-window"); ?></li>
  </ol>
  

  </form>
  </p>
  </div>
<?php
}

function external_links_in_new_windows_plugin_action_links( $links, $file ) {
  if ( $file == plugin_basename( dirname(__FILE__).'/open-external-links-in-a-new-window.php' ) ) {
    $settings = '<a href="options-general.php?page=external_links_in_new_windows">'.__('Settings', "open-external-links-in-a-new-window").'</a>';
    array_unshift($links, $settings);
  }
  
  return $links;
}

add_filter( 'plugin_action_links', 'external_links_in_new_windows_plugin_action_links', 10, 2 );
