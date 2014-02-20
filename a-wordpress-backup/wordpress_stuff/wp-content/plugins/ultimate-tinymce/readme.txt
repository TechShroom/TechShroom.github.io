=== Ultimate TinyMCE ===
Contributors: josh401, Marventus
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=A9E5VNRBMVBCS
Tags: editor, buttons, button, add, font, font style, font select, table, tables, visual editor, search, replace, colors, color, anchor, advance, advanced, links, link, popup, javascript, upgrade, update, admin, image, images, citations, preview, html, custom, custom css, css, borders, pages, posts, pretty, colorful, php, php widget, shortcode, shortcodes, style, styles, plugin, login, excerpt, id, post, page, youtube, syntax, highlight, highlighter, image maps, tinymce, Tinymce, ultimate, Ultimate Tinymce
Requires at least: 3.5.1
Tested up to: 3.8-RC2
Stable tag: 5.2
License: GPLv2

Description: Beef up the WordPress TinyMCE content editor with a plethora of advanced options.

== Description ==

Are you a visual person?  Do the letter combinations "HTML" and "CSS" send you running for the hills; but you still want to create beautiful blogs like the pros?  Then this is the plugin for you!

[youtube http://www.youtube.com/watch?v=01reHnBCAIA]

<strong>Ultimate TinyMCE</strong> will add over 40 new buttons to the default visual tinymce editor, giving you the power to visually create your pages and posts. No need for mucking about in HTML and CSS.

<strong>How will Ultimate TinyMCE Help You?</strong>
<ul>
<li>No need to learn the languages of HTML and CSS (although the basics can certainly help) when creating your pages/posts.</li>
<li>Easily manipulate your fonts, font sizes, colors, styles, and css from a graphical user interface.</li>
<li>Shortcodes Manager - Now all your shortcodes are available in a handy dropdown box.</li>
<li>Create tables through a grahical interface (much like microsoft excel) to display your data.</li>
<li>Insert YouTube videos by simply copying and pasting the share url.</li>
<li>Graphical Image Mapping to make your images more exciting.</li>
<li>Use shortcodes to insert column breaks.  This is a VERY cool feature.  You can break any content area into up to six separate columns.</li>
<li>Ultimate Tinymce will add more than 50 new buttons and features to your visual editor.</li>
</ul>


== Installation ==

1. Upload the plugin to your 'wp-content/plugins' directory, or download and install automatically through your admin panel.
     
2. Activate the plugin through the 'Plugins' menu in WordPress.

3.  You will need to manually activate your buttons the first time you install.  You can do this by going to the admin panel, settings page, Ultimate TinyMCE.  Here, you can set your preferences.


== Frequently Asked Questions ==

= Support Forum =

Please use my <a href="http://forum.joshlobe.com/member.php?action=register&referrer=1">SUPPORT FORUM</a> for expedited help.

== Screenshots ==

Rather than "bloat" the plugin with screenshots. Please check out tons of screenshots and descriptions on the <a href="http://utmce.joshlobe.com/button-definitions/">Ultimate Tinymce Official Website</a>.

== Upgrade Notice ==
* Upgrade notice.

== Features ==

* Simply too many features to list!!  Please visit the <a href="http://utmce.joshlobe.com">Ultimate Tinymce</a> website for a complete list.

== Changelog ==

= 5.2 =
* 12-22-2013

= Tweaks =
* Checked all code to ensure WordPress 3.8 compliance.
* Adjusted miscellaneous links and nuances.
* Fixed overlapping input field on signup form.

= 5.1 =
* 11-01-2013

= IMPORTANT =
* Secured a risk-factor in the plugin.  THANK YOU to @dd32 from WordPress.org for bringing this to my attention.  
* Fixed a possible security loophole whereby the plugin could be deactvated by a remote CURL command.

= Tweaks =

= 5.0 =
* 09-13-2013

= Tweaks =
* Updated external links.
* Updated deprecated function.
* Some code re-write for WP 3.7.

= 4.9.1 =
* 08-22-2013

= Maintenance Release =
* Fixed sprite images loading inside content editor.

= 4.9 =
* 08-21-2013

= New Features =
* Performed a complete overhaul on all plugin button icons used in the editor. They are now grayscale by default, and colorized on hover (just like default WP button icons).
* Resized all editor button icons for a more consistent look and feel.
* Added an option to select where in the admin menu tree the Ultimate Tinymce settings page should appear (ie. Settings, Tools, Appearance, or Main level).

= Tweaks =
* Optimized how editor button icons are loaded.  Created one sprite image, and now call all button icons from the sprite. This reduced the number of http requests (for icon images) when loading the editor from 22, to just 1.
* Removed some extranneous CSS code.  
* Added links to the new <a href="http://docs.joshlobe.com/">Ultimate Tinymce WIKI</a>.

= 4.8.1 =
* 07-18-2013

= Bug Fixes =
* Fixed Polish Z character not working.
* Fixed YoutubeIframe button not opening.
* Fixed download issue.

= 4.8 =
* 07-18-2013

= Tweaks =
* Modified CSS stylesheets to be more spec-oriented.
* Modified styling used in the plugin settings page.

= Bug Fixes =
* Fixed bug in "codemagic" addon with text wrapping.
* Fixed some styling issues.
* Fixed "Load Defaults" not working for some options.

= 4.7 =
* 05-23-2013

= Bug Fixes =
* Fixed menu page "knocking out" other pages.
* Fixed two erroneous commas in "mce_langs.php" file which was causing errors in IE.

= 4.6 =
* 05-08-2013

= New Features =
* Removed all metaboxes and re-configured using a tabbed section.  This should make "option navigation" a bit easier.
* Moved Ultimate Tinymce Settings Page to a top level page.  It is no longer found under the "Settings" tab.
* Added an admin tour using wp-pointers.

= Removed Features =
* Removed support the developer.

= Updates =
* Added option for using Ultimate Tinymce excerpts in Pages.

= Updated Addons =
* Updated the CodeMagic addon.  This fixes much of the white-space issues when moving content between the editor and the CodeMagic window.

= Bug Fixes=
* Updated all addon language strings into the common Ultimate Tinymce language localization.  This means all the addon text strings can now be translated using the Ultimate Tinymce plugin .po and .mo language files.  Also, it prevents the similar looking "table.table_desc" being displayed instead of the actual text string.  My apologies to Translators... this has added approximately 800 new text strings to the plugin.  But, now it is done correctly ;)
* Fixed YouTube bug where video options were not working properly (such as related videos, or play in hd).
* Fixed save buttons not displaying properly in some browsers (removed center html tags; replaced with margins).

= Tweaks =
* Consolidated "Buttons 1" and "Buttons 2" into a single "Buttons tab.  This will cause all options that were set for "Buttons 2" to be lost.  These will need to be re-configured.  I apologize for the invonvenience... but it had to be done to put the "tabs" together.

= 4.5 =
* 04-20-2013

= New Features =
* A new panel for quickly configuring plugin settings and options.

= Removed Items =
* Removed options for setting other plugins buttons.  A new interface will be added soon.

= Tweaks =
* Consolidated, re-organized, and optimized code.  Added comments and improved 'readability'.
* A new option for supporting the developer.
* Updated language strings.
* Updated plugin uninstallation script to completely remove database options.

= 4.4 =
* 04-09-2013
* Fixed mis-informed option.