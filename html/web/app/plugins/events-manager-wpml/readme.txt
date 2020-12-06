=== Events Manager and WPML Compatibility ===
Contributors: netweblogic
Donate link: http://wp-events-plugin.com
Tags: events, multilingual, languages, translation, wpml, event, event registration, event calendar, events calendar, event management, events-manager
Requires at least: 3.3
Tested up to: 5.4
Stable tag: 2.0.1
License: GPLv2
Requires PHP: 5.2

Integrates the Events Manager and WPML plugins together to provide a smoother multilingual experience (Requires Events Manager and WPML)

== Description ==

This plugin helps make [Events Manager](http://wordpress.org/extend/plugins/events-manager/) and [WPML](http://wpml.org) work better together by allowing translation of all event-related features:

** Version 2.0 is a major revamp that fixes *all known issues*, nuances and limitiation. 2.0 enables *full compatiblity* with [Events Manager](http://wordpress.org/extend/plugins/events-manager/) and [Events Manager Pro Add-ons](https://eventsmanagerpro.com) **

* Detects translated pages of specific EM pages (assigned in Events > Settings > Pages) and displays relevant language content
* Searching locations and events within the context of the current language being viewed.
* Recurring events can be created in multiple languages, recurrence translations are correctly created.
* Event translations will share relevant information across all translations, including
 * Event Times
 * Location Information
  * If translations for the location exist, translated events will show/link to location of the same language, if not the original location translation.
 * Bookings and Booking Forms
 * If you delete an event that is the originally translated event, booking and other meta info is transferred to default language or next available language translation.
* Location address information can be translated, whilst sharing coordinate, country/zip information accross translations.
* Event-related text can be translated for each language including:
    * Custom texts, emails templates and formats on the settings page.
    * Booking and attendee custom forms (available in Pro)
    * Custom event and gateway emails (available in Pro)
* MultiSite cross-site support, including showing events from other blogs in your network when EM Global Tables Mode is enabled.
* Displaying untranslated items in lists or hiding them, according to WPML settings.

= Special Installation Steps =
Please ensure that WPML 4.2 and EM 5.6.7 or higher are installed BEFORE updating or activating this plugin.

When setting up EM and WPML, you should create translated versions of the event, location, category, tag, etc. pages assigned in Events > Settings > Pages of your admin area. Duplicating them using WPML is enough.

Given the flexibiltiy of both plugins, there is an huge number of possible setting/language combinations to test, and despite our rigorous testing it is impossible to test every setting combination and scenario. To the best of our knowledge, we have achieved 100% compatibility, but should you come across on your setup and we'll do our best to fix them as time permits.
 
== Installation ==

This plugin requires WPML and Events Manager to be installed BEFORE installing this plugin.

Events Manager WPML works like any standard Wordpress plugin. [See the Codex for installation instructions](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

== Changelog ==
= 2.0.1 =
* Added admin notice and prevent plugin from loading when installing/updating without updating older versions of Events Manager < 5.9.7, due to fatal errors produced.

= 2.0 =
* fixed translated event ical link issues
* fixed PHP 7.2+ compat warnings when enabling recurring events via EM_WPML_FORCE_RECURRENCES constant
* fixed location shortcodes using eventless and eventful attributes not working properly, now translated locations with/out an event (translated or not) will show up correctly in results
* fixed duplicate events/locations via WPML not working properly
* added EM_WPML::get_translations integration with new EM 5.9.6.1 function,
* moved calendar day links rewriting from EM_WPML to EM_WPML_Permalinks,
* fixed is_original() not taking into account recurring events post type
* added initial support for 'language' argument (currently uses value to search WPML DB)
* added support for the EM_ML_Search::$active conditional flag
* added wpml_setting filter for initialization
* added syncing for Events Manager 5.9.6.2 language/parent support in location/event tables
* added EM_WPML::get_wpml_element_meta() allowing EM_WPML::get_translations() to support taxonomies as well
* added em_ml_set_language_by_post_ids and em_ml_attach_translations for Events Manager 5.9.6.2 language/parent support in location/event tables
* added recurring event support as of Events Manager 5.9.6.2
* added support for translatable tickets in the WPML Translation Editor
* updated xml file to reflect new custom fields and recurring post type
* fixed location saving in TE for new EM 5.9.6.2 integration of saving addresses
* added translation syncing to and from WPML upon activation and in admin tools (including per-blog)
* fixed bugs with creation of orphaned/phantom event/locations when duplicating via WPML or when updating original events/locations with duplicate translations
* fixed is_original and get_original checks producing incorrect results during the duplication process via WPML, causing inconsistent record creation
* removed EM_WPML_Search in lieu of EM natively handling multilingual searches
* added switch_language detection via respective functions in EM and vice versa
* modified all filter functions in EM_WPML to account for EM native translation detection
* changed em_wpml translation cache to use the EM_ML cache properties

= 1.2 =
* added forced language redirect support for pro settings pages to prevent page reference errors
* fixed translations not getting published when using the translation editor
* additional indirect fixes within the core plugins:
 * added translatable email reminders in EM Pro 2.6.1
 * fixed translated custom emails in EM Pro 2.6
 * fixed translated custom booking form fields not getting translated in booking admin table columns in EM Pro 2.6
 * added information notice when viewing booking, showing the language booking was made in EM 5.9.2
 * added language column to booking admin tables, showing the language booking was made in EM 5.9.2 

= 1.1 =
* fixed validation issues on first submission of a translation due to recent WPML changes
* added fix for translation editor validation issues (kudos David)
* removed unnecessary taxonomy filters thanks to recent fixes in EM and how data is written to $wp_query globals
* fixed calendar day display issues in recent WPML versions
* fixed category page display issues (mainly fixed in Events Manager 5.8)
* fixed PHP warning on trash pages when viewing all lanaguages
* fixed duplicating events via WPML not copying location information first time around
* special thanks David Garcia Watkins and the rest of the WPML dev team for their assistance with many of these bugs!

= 1.0.1 =
* fixed PHP error causing parse errors and blank screens in some setups

= 1.0 =
* this is a complete rewrite, from the ground up, vastly improving overall stability and fixing many bugs that arose over time due to WPML/EM updates
* changed architecture so it hooks into EM's multilingual actions and filters made available in EM_ML and EM_ML.. objects
* changed and removed dependency on em_wpml index table, translations are now resolved on the fly using WPML's records and functions
* fixed RSS and iCal feed links translate and show correct languages
* fixed event category and tag page display issues related to formatting and language selectors
* fixed event duplication via EM not including translations
* fixed WPML duplication of languages not saving event/location properly
* fixed various PHP warnings
* fixed translated permalink and language selector issues on event pages showing events for a current calendar day
* fixed settings pages 'forgetting' certain EM-related page choices where formatting is used when saving/viewing in a different language to the main one
* fixed location validation issues when saving events and their translations
* fixed broken bookings between translations, where bookings are tied to event translations rather then the original event language
* fixed location sharing/translation issues between translations
* fixed various placeholders and formats not translating properly
* added event/location attribute sharing from original event/location as well as making translations of attributes possible
* added translateable booking ticket name and descriptions
* see Events Manager 5.6 and Events Manager Pro 2.4 changelogs for more information on MultiLingual supported features which are automatically compatible with this plugin 

= 0.3 =
* fixed version update checks and table installations on MultiSite causing event submission issues
* fixed attribute translations not being editable

= 0.2 =
* fixed PHP warnings due to non-static function declarations
* fixed unexpected behaviour when checking translated EM assigned pages

= 0.1 =
* first release