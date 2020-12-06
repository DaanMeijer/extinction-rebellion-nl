=== External Links in New Window / New Tab ===
Contributors: WebFactory, wpreset, googlemapswidget, underconstructionpage
Tags: links, external links, target blank, new window, new tab, target new, blank window, blank tab, tabs, SEO
Requires at least: 4.0
Requires PHP: 5.2
Tested up to: 5.4
Stable tag: 1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Open external links in a new window or new tab. SEO optimized!

== Description ==
Opens external links in a new tab or a or new window. You can set URLs that should either be forced to open in a new window or ignored.

The plugin produces XHTML Strict compliant code and is search engine optimized (SEO).
This is done using JavaScript's `window.open()`-function. It adds only a few lines of vanilla JavaScript to the page, and does not require any external libraries like jQuery.

Most other plugins perform a hack by altering the `target` parameter (i.e. `<a href="http://somewhere.example" target="_blank">`). That method is not XHTML Strict compliant.
This plugin handles the links client-side, which lets search engines follow the links properly. Also, if a browser does not support JavaScript, the plugin is simply inactive, and does not result in any errors.

**Credits**
Inspired by the [Zap_NewWindow](http://www.zappelfillip.de/2005-12-05/zap_newwindow/) plugin by [Tom K&ouml;hler](http://www.zappelfillip.de/ "His website is mostly in German").
The banner is a [photo](http://www.flickr.com/photos/monja/1367946568/in/photostream/) by [Monja Da Riva](http://www.monja.it/).

**Translations**
Danish by [Kristian Risager Larsen](https://kristianrisagerlarsen.dk)
Dutch by [Paul Staring](http://www.collectief-it.nl/)
Lithuanian by [Vincent G](http://Host1Free.com)
Other translations will be appreciated!

**Known bugs**
The plugin conflicts with other plugins that change the links' `onClick´ attribute.

**Original developer**
Kristian Risager Larsen - <a href="http://kezze.dk">kezze.dk</a>

== Installation ==
1. Copy the plugin to /wp-content/plugins/
1. Activate the plugin.
1. Eventually, change the settings in Settings->External Links.

== Changelog ==

= 1.4 =
* 2019-08-26
* WebFactory took over development
* minor fixes
* 40,000 installs; 178,750 downloads

= 1.3.3 =
Verified compatibility with Wordpress 5.0

= 1.3.2 =
Updated: Danish translation

= 1.3.1 =
Verified compatibility with Wordpress 4.0
Added: Plugin logo for Wordpress 4.0
Added: Dutch translation.

= 1.3 =
Added: Possibility to force and ignore user-defined strings in URLs. This feature has been requested.
Added: Lithuanian and Danish translation.

= 1.2 =
Added: Translation-ready.

= 1.1.1 =
Fixed: Deprecation warning (Thanks to [boo1865](http://wordpress.org/support/topic/plugin-open-external-links-in-a-new-window-doesnt-work?replies=2#post-2152292))

= 1.1.0 =
Changed: Better practice for opening links. The plugin now uses the onClick-attribute instead of writing JavaScript directly into the href-attribute. This enables users to right-click the link and open in a new window/tab, save the target etc.

= 1.0.1 =
Fixed: Removes target attribute from links instead of setting the attribute to null. (Thanks to [crashnet](http://wordpress.org/support/topic/plugin-open-external-links-in-a-new-window-target-attribute-left-empty?replies=2#post-1813522))

= 1.0 =
Fixed: Credits to Tom K&ouml;hler (Charset).
Fixed: Links.

= 0.9 =
Initial release.

== Upgrade Notice ==

= 1.3.1 =
Wordpress 4.0-compatibility, and Dutch translation.

= 1.3 =
In Settings->External links, you can now specify URL's that should be either forced to open in a new window, or ignored.

= 1.2 =
Added: Translation-ready.

= 1.1.1 =
Fixed: Deprecation warning.

= 1.1.0 =
Better practice for opening links. Please upgrade.

= 1.0.1 =
Minor bugfix.

= 1.0 =
Ready for production.

= 0.9 =
Initial release
