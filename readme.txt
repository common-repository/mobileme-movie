=== MobileMe Movie ===
Contributors: thiudis
Donate link: http://www.evolonix.com/donate/
Tags: MobileMe, movie, video
Requires at least: 2.0.2
Tested up to: 3.2.1
Stable tag: 1.1

This plugin allows you to insert a movie you have hosted on your MobileMe Gallery's website in your post or page.

== Description ==

This plugin allows you to insert a movie you have hosted on your MobileMe Gallery's website in your post or page. Use [mobileme url="http://gallery.me.com/appleid#albumid/moviename" type="m4v or mov" width="width" height="height"] to insert the movie. The easiest way to get the "url" is to copy it from your browser's address bar when viewing the movie on the Gallery's site.

The "type" attribute is optional if the movie is an .m4v file, which it uses by default, but required for .mov files. If it doesn't work without specifying the "type" attribute, then try adding it with the "mov" value. The "width" and "height" attributes are optional and default to width="640" and height="360". If one is specified, the other must be also, otherwise it uses the defaults.

== Installation ==

1. Upload `mobileme-movie` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `[mobileme url="http://gallery.me.com/appleid#albumid/moviename" type="m4v or mov" width="width" height="height"]` in your posts or pages wherever you want the movie to display.

== Frequently Asked Questions ==



== Screenshots ==



== Changelog ==

= 1.3 =
* Fixed a bug where the options page was being added to the admin bar for all users, regardless if they had access to it or not.

= 1.2 =
* Fixed a bug where an error may have occurred if the type, width, or height attributes were not specified.

= 1.1 =
* Changed the plugin to use the url="" attribute syntax instead of url: syntax.

== Upgrade Notice ==

= 1.2 =
Upgrade to this version if you are trying to use the shortcode without a type, width, or height specified. Previous versions may have resulted in an error if any of the mentioned attributes were not provided.

= 1.1 =
You should upgrade from 1.0 so that it is easier to understand what movie you are specifying. Before, where you would have specified [mobileme url:the_url] you now write it like [mobileme url="the_url"]. Much more cleaner and easier to read. You also had to specify &type=mov in the url before, where you now specify it in an attribute like type="mov".

== Arbitrary section ==

