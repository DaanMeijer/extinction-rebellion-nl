=== Htaccess Editor - Safely Edit Htaccess File ===
Tags: htaccess, htaccess editor, htaccess file, htaccess backup, fix htaccess, modify htaccess, file editor, htaccess backup
Contributors: WebFactory, UnderConstructionPage, googlemapswidget, wpreset
Requires at least: 4.0
Requires PHP: 5.2
Tested up to: 5.4
Stable tag: 1.65
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A safe & simple htaccess file editor. Test htaccess syntax before saving & automatically create htaccess file backups.

== Description ==

<a href="https://wphtaccess.com/?utm_source=wordpressorg&utm_medium=content&utm_campaign=wp-htaccess-editor&utm_term=readme-top">WP Htaccess Editor</a> provides a **simple, safe & fast way** to edit & test the site's .htaccess file from WP admin. Before saving htaccess can be tested for syntax errors. It also automatically creates a backup every time you make a change to the htaccess file. Backups can be restored directly from the plugin, or via FTP if the errors in .htaccess file prevent WP from running normally. For all questions, including support please use the official <a href="https://wordpress.org/support/plugin/wp-htaccess-editor">forum</a>.

Access WP Htaccess Editor via WP Admin - Settings menu.

#### Testing Htaccess Syntax

Use the "test before saving" button to test htaccess file syntax before saving. Please note that this test does not check the logic of your htaccess file, ie if the redirects work as intended. It only checks for syntax errors.

#### Automatic Backups

Htaccess Editor makes automatic backups of htaccess file every time you make a change to it. Backups are located in `/wp-content/htaccess-editor-backups/` and timestamped so you can easily find the latest htaccess backup and restore it.

#### WordPress Network (WPMU) Support

WP Htaccess Editor is fully compatible and tested with WP Network (WPMU). It shows up under the Settings menu in network admin. It's not available in individual sites as there is only one .htaccess file per network.


The plugin was originally developed by <a href="https://profiles.wordpress.org/lukenzi">Lukenzi</a> in March of 2011.

== Installation ==

Follow the usual routine;

1. Open WordPress admin, go to Plugins, click Add New
2. Enter "htaccess editor" in search and hit Enter
3. Plugin will show up as the first on the list, click "Install Now"
4. Activate & open plugin's settings page located under the Settings menu

Or if needed, upload manually;

1. Download the latest stable version from from <a href="https://downloads.wordpress.org/plugin/wp-htaccess-editor.latest-stable.zip">downloads.wordpress.org/plugin/wp-htaccess-editor.latest-stable.zip</a>
2. Unzip it and upload to _/wp-content/plugins/_
3. Open WordPress admin - Plugins and click "Activate" next to "WP Htaccess Editor"
4. Open plugin's admin page located under the Settings menu


== Screenshots ==

1. WP Htaccess Editor admin page
2. Actions have to be double-confirmed to prevent accidents


== Changelog ==
= v1.65 =
* 2019/08/16
* fixed a few bugs
* new feature: test htaccess file for syntax errors before saving
* 50,000 installations hit on 2019-08-10

= v1.60 =
* 2019/03/12
* fixed a few bugs
* new: editor size is persistent; saved in localStorage
* menu item moved from Tools to Settings
* full WordPress Network (WPMU) compatibility

= v1.55 =
* 2019/01/15
* added code editor resize feature
* fixed a few bugs
* 40k installations hit on 2019-01-09 with 172,000 downloads

= v1.50 =
* 2018/12/21
* WebFactory took over development
* complete plugin rewrite
* 30,000 installations; 162,200 downloads

= v1.3.0 =
* Added Spanish translation (Thanks to Andrew Kurtis from WebHostingHub.com)
* Updated design
* Updated info links

= v1.2.0 =
* Improved code
* Improved design
* Improved security
* Removed debug panel
* Updated translations
* Updated links
* Updated screenshots
* Adding plugin logo

= v1.1.1 =
* Fixed CHMOD

= v1.1.0 =
* Adding Czech and English language
* Adding debug panel
* Added information about the author and translators
* Fixed vulnerability
* Fixed bug loading translations files
* Optimized for minimum memory requirements
* Small code modifications

= v1.0.1 =
* Adding button for create .htaccess file if not exists
* Fixed bug in the permissions to view the plugin
* Optimized for smaller memory requirements

= v1.0.0 =
* 2011/03/24
* First stable version
* Adding to WordPress repository

== Frequently Asked Questions ==

= I've killed my site! Help!? =

Nothing is lost or deleted. You can easily get your site back.
You're probably getting an error 500 or a white screen (of death). First connect to your site via FTP and locate the .htaccess file. Delete it, or rename it. Try the site again - it should open. If it did locate the backup of the old, working .htaccess file in `/wp-content/htaccess-editor-backups/` copy the file to your site's root folder and you're back in business.

= I get an error saying the .htaccess file can't be edited or created =

Sorry, we can't change the file access privileges set by your server. You'll have to edit the file via FTP.

Head over to our <a href="https://wordpress.org/support/plugin/wp-htaccess-editor">support forums</a>. We'll gladly assist you.

= How do I get support? =

Head over to our <a href="https://wordpress.org/support/plugin/wp-htaccess-editor">support forums</a>. We'll gladly help you.

= Do you support WP-CLI? =

Not yet, but we plan to.
