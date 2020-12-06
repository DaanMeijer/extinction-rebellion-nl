=== Admin CSS MU ===
Contributors: arunbasillal
Donate link: http://millionclues.com/donate/
Tags: admin css, mu plugin, custom admin css, admin, admin interface, multisite, must use
Requires at least: 3.0
Tested up to: 5.4.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add custom CSS to style the WordPress Admin. Works with Multisites. 

== Description ==

A simple plugin that lets you add your custom CSS to style the WordPress Admin. Works with WordPress single install and WordPress multisites. 

Version 2.0 is updated wtih an Admin interface and can be found in Appearance > Admin CSS MU

Admin CSS MU uses CSSTidy to clean and optionally minify CSS. CSS is only minified while using it. The editor always shows un-minified CSS for easy editing. 

Note: If you want to easily add custom CSS on your Login and Front-end as well (along with WordPress Admin), use my [Custom Login Admin Front-end CSS](https://wordpress.org/plugins/custom-login-admin-front-end-css-with-multisite-support/) plugin instead. 

== Installation ==

To use as a normal plugin in WordPress single install and Multisite.

1. Install the plugin through the WordPress admin interface, or upload the folder /admin-css-mu/ to /wp-content/plugins/ using ftp.
2. Activate the plugin via WordPress admin interface. If it is a Multisite, Network Activate it.
3. Go to WordPress Admin > Appearance > Admin CSS MU and add the custom CSS you want. 
4. For Multisites, the settings page will be in the Appearance > Admin CSS MU of the main site. Only network admins can add/edit CSS.

== Frequently Asked Questions ==

= Can I try the CSS sample from the screenshot? =

Sure! To change the background colour of the dashboard add this and save.

`#wpwrap {
	background-color: #5f5f5f !important;
}
body, h1, p {
	color: #f1f1f1 !important;
}`

= I need more features. Can I hire you? =

Yes. Please [get in touch via my contact form](http://millionclues.com/contact/) with a brief description of your requirement and budget for the project. I will be in touch shortly.

= I found this plugin very useful, how can I show my appreciation? =

I am glad to hear that! You can either [make a donation](http://millionclues.com/donate/) or leave a [rating](https://wordpress.org/support/plugin/admin-css-mu/reviews/?rate=5#new-post) to motivate me to keep working on the plugin. 

== Screenshots ==

1. Admin Interface in Appearance > Admin CSS MU

== Changelog ==

= 2.5 =
* Date: 18.January.2018
* Added support for PHP version lesser than PHP 5.5
* Updated text domain. For plugins, text domain should be the slug which is admin-css-mu in this case
* Tested with WordPress 4.9.2

= 2.4 =
* Date: 29.August.2017
* Fixed: Compatibility issue with [Custom Login Admin Front-end CSS](https://wordpress.org/plugins/custom-login-admin-front-end-css-with-multisite-support/) plugin. 
* Updated readme.txt to work around [#2931](https://meta.trac.wordpress.org/ticket/2931)
* New: Added an option "Load Admin CSS" to enable and disable CSS easily. Useful during testing.
* Code improvements.

= 2.3 =
* Date: 18.August.2017
* Tested with WordPress 4.8.1. Result = pass.
* Updated CSSTidy classes to meet PHP 7.x standards.
* Added option to minify CSS. CSS is only minified during use and the CSS editor will show the un-minified and more readable version for easy editing. 

= 2.2 =
* Date: 14.December.2016
* Tested with WordPress 4.7.
* Uses wp_kses() instead of esc_html() during output to prevent stripping of useful html tags.
* Minor bug fixes.

= 2.1 =
* Date: 19.March.2016
* Updated multisite compatibility.
* Appearance > Admin CSS MU is now visible only on the main website. 

= 2.0 =
* Date: 15.March.2016
* Added an admin interface in Appearance > Admin CSS MU.
* Tested with WordPress 4.5.

= 1.01 =
* Date: 11.April.2016
* Code updates.
* Tested with WordPress 4.4.2.
* Changed the location of custom stylesheet into /wp-content/uploads/admin-css-mu/ while preserving the original folder for backward compatiblity with version 1.0

= 1.0 =
* Date: 25.June.2012
* First release of the plugin.

== Upgrade Notice ==

= 2.5 =
* Date: 18.January.2018
* Added support for PHP version lesser than PHP 5.5
* Updated text domain. For plugins, text domain should be the slug which is admin-css-mu in this case
* Tested with WordPress 4.9.2

= 2.4 =
* Date: 29.August.2017
* Fixed: Compatibility issue with [Custom Login Admin Front-end CSS](https://wordpress.org/plugins/custom-login-admin-front-end-css-with-multisite-support/) plugin. 
* Updated readme.txt to work around [#2931](https://meta.trac.wordpress.org/ticket/2931)
* New: Added an option "Load Admin CSS" to enable and disable CSS easily. Useful during testing.
* Code improvements.

= 2.3 =
* Tested with WordPress 4.8.1. Result = pass.
* Updated CSSTidy classes to meet PHP 7.x standards.
* Added option to minify CSS. CSS is only minified during use and the CSS editor will show the un-minified and more readable version for easy editing. 

= 2.2 =
* Date: 14.December.2016
* Minor bug fixes and fully compatible with WordPress 4.7. 

= 2.1 =
* Date: 19.March.2016
* Upgrade for Multisite compatibility. 

= 2.0 =
* Date: 15.March.2016
* Upgrade for an admin interface in Appearance > Admin CSS MU. Backward compatible. 

= 1.01 =
* Date: 11.April.2016
* Upgrade for better code. Backward comaptible with 1.0

= 1.0 =
* Date: 25.June.2012
* First release of the plugin.