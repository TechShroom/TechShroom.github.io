=== Custom Post Type Privacy ===
Contributors: kimedia, peteholiday
Tags: posts, pages,, custom post types, bbpress, users, security, restrict, restriction, groups, private
Requires at least: 3.0
Tested up to: 3.3.4
Stable Tag 0.3

Custom Post Type Privacy allows WordPress authors to grant access to users and groups of users across all posts, pages and custom post types.

== Description ==

This is a simple plugin to allow for restricting access to content.

Users may be members of multiple groups. Multiple groups and multiple individual users may be allowed to view each
post. Overlaps are ignored -- if the user is a member of any group that is allowed to view the post, that user will
be able to view it.


== Installation ==

Installation is quick and simple:

1. Upload 'custom-post-type-privacy' folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress

To upgrade the plugin manually, follow these steps:

1. Disable the plugin
1. Upload the new version to /wp-content/plugins/
1. Reactivate the plugin

== change log ==

0.2 fixed issue with saving posts (wpdb wasn't global)
0.3 new children should now inherit parent's privacy groups and users
