<?php
/*
Plugin Name: Easy Timer
Plugin URI: http://www.kleor.com/easy-timer
Description: Allows you to easily display a count down/up timer, the time or the current date on your website, and to schedule an automatic content modification.
Version: 3.6.4
Author: Kleor
Author URI: http://www.kleor.com
Text Domain: easy-timer
License: GPL2
*/

/* 
Copyright 2010 Kleor (http://www.kleor.com)

This program is a free software. You can redistribute it and/or 
modify it under the terms of the GNU General Public License as 
published by the Free Software Foundation, either version 2 of 
the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, 
but without any warranty, without even the implied warranty of 
merchantability or fitness for a particular purpose. See the 
GNU General Public License for more details.
*/


define('EASY_TIMER_PATH', plugin_dir_path(__FILE__));
define('EASY_TIMER_URL', plugin_dir_url(__FILE__));
define('EASY_TIMER_FOLDER', str_replace('/easy-timer.php', '', plugin_basename(__FILE__)));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('EASY_TIMER_VERSION', $plugin_data['Version']);

if (is_admin()) { include_once EASY_TIMER_PATH.'admin.php'; }

function install_easy_timer() { include EASY_TIMER_PATH.'includes/install.php'; }

register_activation_hook(__FILE__, 'install_easy_timer');

$easy_timer_options = (array) get_option('easy_timer');
if ((!isset($easy_timer_options['version'])) || ($easy_timer_options['version'] != EASY_TIMER_VERSION)) { install_easy_timer(); }

$easy_timer_cookies = array();


function easy_timer_cookies_js() { include EASY_TIMER_PATH.'includes/cookies-js.php'; }

if (easy_timer_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'easy_timer_cookies_js'); }


function easy_timer_data($atts) { include EASY_TIMER_PATH.'includes/data.php'; return $data; }


function easy_timer_do_shortcode($string) { include EASY_TIMER_PATH.'includes/do-shortcode.php'; return $string; }


function easy_timer_filter_data($filter, $data) { include EASY_TIMER_PATH.'includes/filter-data.php'; return $data; }


function easy_timer_format_nice_name($string) {
$string = easy_timer_strip_accents(strtolower(trim(strip_tags($string))));
$string = str_replace(' ', '-', $string);
$string = preg_replace('/[^a-z0-9_-]/', '', $string);
return $string; }


function easy_timer_i18n($string) {
load_plugin_textdomain('easy-timer', false, EASY_TIMER_FOLDER.'/languages');
return __(__($string), 'easy-timer'); }


function easy_timer_strip_accents($string) {
return str_replace(
explode(' ', 'á à â ä ã å ç é è ê ë í ì î ï ñ ó ò ô ö õ ø ú ù û ü ý ÿ Á À Â Ä Ã Å Ç É È Ê Ë Í Ì Î Ï Ñ Ó Ò Ô Ö Õ Ø Ú Ù Û Ü Ý Ÿ'),
explode(' ', 'a a a a a a c e e e e i i i i n o o o o o o u u u u y y A A A A A A C E E E E I I I I N O O O O O O U U U U Y Y'),
$string); }

for ($i = 0; $i < 4; $i++) {
foreach (array('counter', 'countdown', 'countup') as $tag) {
add_shortcode($tag.($i == 0 ? '' : $i), create_function('$atts, $content', 'include_once EASY_TIMER_PATH."shortcodes.php"; return '.$tag.'($atts, $content);')); } }
foreach (array('clock', 'isoyear', 'monthday', 'month', 'timezone', 'weekday', 'yearday', 'yearweek', 'year') as $tag) {
add_shortcode($tag, create_function('$atts', 'include_once EASY_TIMER_PATH."shortcodes.php"; return '.$tag.'($atts);')); }
add_shortcode('easy-timer', 'easy_timer_data');


foreach (array(
'get_the_excerpt',
'get_the_title',
'single_post_title',
'the_excerpt',
'the_excerpt_rss',
'the_title',
'the_title_attribute',
'the_title_rss',
'widget_text',
'widget_title') as $function) { add_filter($function, 'do_shortcode'); }