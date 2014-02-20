<?php
/*
Plugin Name: Comprehensive Google Map Plugin
Plugin URI: http://wordpress.org/support/plugin/comprehensive-google-map-plugin
Description: A simple and intuitive, yet elegant and fully documented Google map plugin that installs as a widget and a short code. The plugin is packed with useful features. Widget and shortcode enabled. Offers extensive configuration options for markers, over 250 custom marker icons, marker Geo mashup, controls, size, KML files, location by latitude/longitude, location by address, info window, directions, traffic/bike lanes and more. 
Version: 9.0.20
Author: Alex Zagniotov
Author URI: http://wordpress.org/support/plugin/comprehensive-google-map-plugin
License: GPLv2


This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

if ( !function_exists('cgmp_define_constants') ):
	function cgmp_define_constants() {
		define('CGMP_PLUGIN_BOOTSTRAP', __FILE__ );
		define('CGMP_PLUGIN_DIR', dirname(CGMP_PLUGIN_BOOTSTRAP));
		define('CGMP_PLUGIN_URI', plugin_dir_url(CGMP_PLUGIN_BOOTSTRAP));

		$json_constants_string = file_get_contents(CGMP_PLUGIN_DIR."/data/plugin.constants.json");
		$json_constants = json_decode($json_constants_string, true);
		$json_constants = $json_constants[0];

		if (is_array($json_constants)) {
			foreach ($json_constants as $constant_key => $constant_value) {
				$constant_value = str_replace("CGMP_PLUGIN_DIR", CGMP_PLUGIN_DIR, $constant_value);
				$constant_value = str_replace("CGMP_PLUGIN_URI", CGMP_PLUGIN_URI, $constant_value);
				define($constant_key, $constant_value);
			}
		}
	}
endif;

if ( !function_exists('cgmp_require_dependancies') ):
	function cgmp_require_dependancies() {
		require_once (CGMP_PLUGIN_DIR . '/functions.php');
		require_once (CGMP_PLUGIN_DIR . '/widget.php');
		require_once (CGMP_PLUGIN_DIR . '/shortcode.php');
		require_once (CGMP_PLUGIN_DIR . '/metabox.php');
		require_once (CGMP_PLUGIN_DIR . '/admin-menu.php');
        require_once (CGMP_PLUGIN_DIR . '/admin-bar-menu.php');
		require_once (CGMP_PLUGIN_DIR . '/head.php');
	}
endif;

if ( !function_exists('cgmp_register_hooks') ):
	function cgmp_register_hooks() {
		register_activation_hook( CGMP_PLUGIN_BOOTSTRAP, 'cgmp_on_activate_hook');
	}
endif;

if ( !function_exists('cgmp_add_actions') ):
	function cgmp_add_actions() {
		//http://scribu.net/wordpress/optimal-script-loading.html
		add_action('init', 'cgmp_google_map_register_scripts');
		add_action('init', 'cgmp_load_plugin_textdomain');
		add_action('admin_notices', 'cgmp_show_message');
		add_action('admin_notices', 'cgmp_show_initial_warning_message');
		add_action('admin_init', 'cgmp_google_map_admin_add_style');
		add_action('admin_init', 'cgmp_google_map_admin_add_script');
		add_action('admin_footer', 'cgmp_google_map_init_global_admin_html_object');
		add_action('admin_menu', 'cgmp_google_map_plugin_menu');

        if ( is_admin() ) {
            $setting_plugin_menu_bar_menu = get_option(CGMP_DB_SETTINGS_PLUGIN_ADMIN_BAR_MENU);
            if (!isset($setting_plugin_menu_bar_menu) || (isset($setting_plugin_menu_bar_menu) && $setting_plugin_menu_bar_menu != "false")) {
                add_action('admin_bar_menu', 'cgmp_admin_bar_menu', 99999);
            }
        }

		add_action('widgets_init', create_function('', 'return register_widget("ComprehensiveGoogleMap_Widget");'));
		add_action('wp_head', 'cgmp_google_map_deregister_scripts', 200);
		add_action('wp_head', 'cgmp_generate_global_options');

        if ( is_admin() ) {
            $setting_tiny_mce_button = get_option(CGMP_DB_SETTINGS_TINYMCE_BUTTON);
            if (!isset($setting_tiny_mce_button) || (isset($setting_tiny_mce_button) && $setting_tiny_mce_button != "false")) {
                if (cgmp_should_load_admin_scripts()) {
                    add_action('init', 'cgmp_register_mce');
                    add_action('wp_ajax_cgmp_mce_ajax_action', 'cgmp_mce_ajax_action_callback');
                }
            }
        }

        add_action('wp_ajax_nopriv_cgmp_ajax_cache_map_action', 'cgmp_ajax_cache_map_action_callback');
        add_action('wp_ajax_cgmp_ajax_cache_map_action', 'cgmp_ajax_cache_map_action_callback');
        add_action('wp_ajax_cgmp_insert_shortcode_to_post_action', 'cgmp_insert_shortcode_to_post_action_callback');

        add_action('save_post', 'cgmp_save_post_hook' );
        add_action('save_page', 'cgmp_save_page_hook' );

        add_action('publish_post', 'cgmp_publish_post_hook' );
        add_action('publish_page', 'cgmp_publish_page_hook' );

        add_action('deleted_post', 'cgmp_deleted_post_hook' );
        add_action('deleted_page', 'cgmp_deleted_page_hook' );

        add_action('publish_to_draft', 'cgmp_publish_to_draft_hook' );
	}
endif;

if ( !function_exists('cgmp_add_shortcode_support') ):
	function cgmp_add_shortcode_support() {
		add_shortcode('google-map-v3', 'cgmp_shortcode_googlemap_handler');
	}
endif;

if ( !function_exists('cgmp_add_filters') ):
	function cgmp_add_filters() {
		add_filter( 'widget_text', 'do_shortcode');
		add_filter( 'plugin_row_meta', 'cgmp_plugin_row_meta', 10, 2 );
        add_filter( 'plugin_action_links', 'cgmp_plugin_action_links', 10, 2 );
	}
endif;


global $cgmp_global_map_language;
$cgmp_global_map_language = "en";

/* BOOTSTRAPPING STARTS */
cgmp_define_constants();
cgmp_require_dependancies();
cgmp_add_actions();
cgmp_register_hooks();
cgmp_add_shortcode_support();
cgmp_add_filters();
/* BOOTSTRAPPING ENDS */

?>
