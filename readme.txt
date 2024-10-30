=== Current Comments ===
Contributors: allendav
Tags: comments, backbone 
Requires at least: 3.6
Tested up to: 3.6
Stable tag: 0.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Live comments widget for WordPress, powered by Backbone.js

== Description ==

Example plugin for using Backbone.js with WordPress; displays a live comment stream in a widget.

== Installation ==

1. Upload the plugin
2. Activate the plugin
3. Add the plugin widget to your sidebar or other widget area

== Frequently Asked Questions ==

= A comment was made on a post, but it didn't appear in the widget.  Why not?  =

Comments appear once approved - be sure the comment has been approved.

= If I unapprove a comment, will it be removed from the widget? =

Yes!

= Why is a minimum of WordPress 3.6 required?  =

This plugin uses Backbone.js 1.0.0, which was added in WordPress 3.6.

== Screenshots ==

1. The current comment widget - it's alive!

== Changelog ==

= 0.4.0 =
* Limited the number of comments returned to 10
* Updated to support localization

= 0.3.0 =
* Updated to use Backbone 1.0.0 in WordPress 3.6 (collection add merge)
* Changed get_comments query to use DESC so that most recent 10 comments are sent
* Changed ajax update to only fetch any comments added since last query
* Removed highlighting of new comments and replaced it with automatically updating timestamps using moment.js
* Simplified subview logic to simply prepend new comments at the top of the comments view

= 0.2 =
* Initial release

