=== Flickr Viewer ===

Contributors: nakunakifi
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZAZHU9ERY8W34
Tags: photostream, flickr, gallery, album, photos, flickr favourites, flickr photostream, image, image gallery,
Requires at least: 4.0.1
Tested up to: 5.0.3
Stable tag: 1.1.8

Awesome simple gallery plugin to display your Flickr Photostream, Favourites, Galleries and Albums on your website. 

== Description ==

Display your Flickr Photostream, Favourites, Galleries and Albums on your website. 
Comes with lightbox and pagination. More features coming soon!

* Use Shortcodes to display your Flickr photostream, favourites, galleries and albums. 
* The images are displayed using a lightbox. 
* Admin settings to control size of thumbnail images and visibility settings etc
* Just authenticate with your Flickr Account and you are ready to go!
* Visit <a href="http://wordpress-plugin-for-flickr.cheshirewebsolutions.com/?utm_source=cws_fgp_config&utm_medium=text-link&utm_content=readme&utm_campaign=cws_fgp_plugin" rel="friend">Flickr Viewer Pro Demo Site</a>



If you have suggestions for a new add-on, feel free to email me at info@cheshirewebsolutions.com.
Want regular updates? 
follow me on Twitter!
https://twitter.com/CheshireWebSol



== Prerequisites ==

1. PHP v5.5

== Installation ==

1. Unzip into your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Make your settings, Admin->Settings->Flickr Viewer
4. Use the 'Photostream' shortcode [cws_fgp_photostream] on a page of your choice.
5. Use the 'Favourites' shortcode [cws_fgp_favourites] on a page of your choice.


== Frequently Asked Questions ==

= Can it display private images? =

The Pro version can display Private images with it's Privacy filter options.


== Screenshots ==

1. This is the Justified Image Gallery.
2. This is the Favourites Grid with image details enabled. 
3. This is the grid view of you Photostream.
4. This is an example of the lightbox displaying photo you clicked on.
5. This is an example of Album grid view.
6. Another example grid view of you Photostream without details.


== Shortcodes ==

* [cws_fgp_photostream] Will display your Photostream.

* [cws_fgp_favourites] Will display your Favourites.



== Shortcode Usage ==

* Display Photostream Example
[cws_fgp_photostream theme=grid thumb_size=square_150 show_title=0]

* Display Favourites Example 
[cws_fgp_favourites theme=grid thumb_size=square_150 show_title=1]

* Display Gallery Example 
[cws_fgp_galleries theme=grid gallery_id=72157678104065683 show_title=1 thumb_size=square_150]


There are 2 main shortcodes:

* [cws_fgp_photostream]
* [cws_fgp_favourites]


== Credits ==

* Flickr Viewer - Ian Kennerley, http://nakunakifi.com 
* Lightbox2 (http://lokeshdhakar.com/projects/lightbox2)
* Masonry (http://desandro.github.io/masonry/)
* phpFlickr (Dan Coulter (dan@dancoulter.com))
* Justified Image Grid - Miro Mannino
* Photoswipe - Dmitry Semenov

== Changelog ==

= 1.1.8 =
* Fix mixed content notice for Google Fonts
* Fix 'update failed' notices for WP 5

= 1.1.7 =
* Add is_ssl to img src in buildPhotoURL()
* Add Notice, Migrate to Google Photos in response to Flickr's new guidelines.

= 1.1.6 =
* Bug Fix, in certain views high res images were not being used in lightbox, this has been fixed.

= 1.1.5 =
* Bug Fix, shortcode photoset cache dir 

= 1.1.4 =
* Updated to use OAuth authentication method

= 1.1.3 =
* Fixed missing template files
* Changed Constructors with the same name as their class is deprecated in PHP7 in phpFlickr.php (pager)

= 1.1.2 =
* Fixed pagination bugs in album shortcode

= 1.1.1 =
* Fixed bug in album shortcode
* Added options num_album_results and num_image_results to [cws_fgp_albums] to limit number of albums returned and number of images.

= 1.1.0 =
* Added Gallery shortcode.
* Added Albums shortcode

= 1.0.0 =
* Complete Re-write of plugin.

= 0.1 =
* Beta Release