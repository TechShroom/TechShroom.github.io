<?php
/*
Plugin Name: Use Shortcodes in Sidebar Widgets
Plugin URI: http://softcontent.eu/use-shortcodes-in-sidebar-widgets.html
Description: Lets you use Shortcodes in Sidebar Widgets
Version: 1.0
Author: Kai Fenner
Author URI: http://softcontent.eu/use-shortcodes-in-sidebar-widgets.html
License: GPL2
*/

/*  Copyright 2012  Kai Fenner (email : kai@appdamit.de)
	
	Code snippet from English Mike:
	http://englishmike.net/2008/07/07/wordpress-quick-tips-3adding-a-shortcode-to-a-sidebar-widget/
	
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

	add_filter('widget_text', 'do_shortcode');
	add_filter('widget_execphp', 'do_shortcode');

?>