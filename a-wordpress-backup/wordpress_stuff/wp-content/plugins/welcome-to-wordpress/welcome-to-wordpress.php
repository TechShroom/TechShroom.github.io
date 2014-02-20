<?php
/*
Plugin Name: Welcome to WordPress
Author: Starfield Technologies
Author URI: http://www.starfieldtech.com/
Plugin URI: 
Description: Optimal configuration and additional help for your WordPress site.  This was installed when your account was setup.
Version: 1.0
Text Domain: welcome-to-wordpress
Domain Path: /lang
Network: true
*/

/**
 * Copyright 2012 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH') )
	die( 'Forbidden' );

// Load languages
load_plugin_textdomain( 'welcome-to-wordpress', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

// Our path
define( 'WTWP_PLUGIN_DIR', realpath( dirname( __FILE__ ) ) );

// Run the plugin
$wtwp_plugin = new WTWP_Plugin();

// Activation hook -- reload the environment settings
register_activation_hook( __FILE__, array( $wtwp_plugin, 'load_environment_settings' ) );

// Cleanup -- roll back environment changes
register_deactivation_hook( __FILE__, array( $wtwp_plugin, 'deactivate' ) );

/**
 * Welcome Plugin for WordPress
 */
class WTWP_Plugin {

	/**
	 * Plugin slug
	 * @var string
	 */
	public $slug = '';
	
	/**
	 * Path to htaccess
	 * @var string
	 */
	protected $_htaccess_path = '';
	
	/**
	 * Path to web.config
	 * @var string
	 */
	protected $_web_config_path = '';

	/**
	 * Backup copy of our options
	 * @var array
	 */
	protected $_options_backup = array();

	/**
	 * Constructor
	 * @return WTWP_Plugin
	 */
	public function __construct() {
		$this->slug = basename( WTWP_PLUGIN_DIR );
		$this->_htaccess_path = trailingslashit( ABSPATH ) . '.htaccess';
		$this->_web_config_path = trailingslashit( ABSPATH ) . 'web.config';
		if ( defined('WP_ADMIN') && WP_ADMIN && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) ) {
			$this->hooks();
			
			// Manage our own upgrades
			require_once( WTWP_PLUGIN_DIR . '/resources/class.wtwp-upgrader.php');
			$plugin_slug = plugin_basename( __FILE__ );
			$wtwp_upgrader = new WTWP_Upgrader( $plugin_slug );
		}
		$this->preview_dns_hooks();
	}

	/**
	 * Add all of our admnin hooks
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_init',            array( $this, 'upgrade' ) );
		add_action( 'admin_init',            array( $this, 'first_login' ) );
		add_action( 'admin_init',            array( $this, 'register_settings' ) );
		add_action( 'admin_menu' ,           array( $this, 'admin_menu' ) );
		add_action( 'admin_menu',            array( $this, 'settings_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'admin_footer',          array( $this, 'show_pointer') );
		add_filter( 'gd_quick_setup_reactivate_plugins', array( $this, 'reactivate_me' ) );
		add_action( 'gd_quicksetup_installed_plugin-welcome-to-wordpress', array( $this, 'restore_options' ) );
	}

	/**
	 * Work with the Go Daddy Quick Setup plugin
	 * @param array $active_plugins
	 * @return array
	 */
	public function reactivate_me( $active_plugins ) {
		$this->_options_backup = get_option( 'wtwp_options' );
		$active_plugins[] = plugin_basename( __FILE__ );
		return $active_plugins;
	}

	/**
	 * Restore the options after Go Daddy Quick Setup
	 * has reset the database
	 */
	public function restore_options() {
		update_option( 'wtwp_options', $this->_options_backup );
	}

	/**
	 * Upgrade between versions
	 * @return void
	 */
	public function upgrade() {

		// Get the current version
		$version = get_option( 'wtwp_version' );

		// Set default options
		if ( empty( $version ) || version_compare( $version, '1.0' ) < 0 ) {
			update_option( 'wtwp_show_help',           'Y' );
			update_option( 'wtwp_use_hardening_rules', '' );
			update_option( 'wtwp_version',             '1.0' );
			update_option( 'wtwp_use_object_cache',    '' );
			update_option( 'wtwp_use_caching_rules',   '' );
			$options = array(
				'key'               => 'uaojhXaIVRbWk2E8zyInIU0ILUXJ9GHOeA3CXODDW',
				'first_login'       => false,
				'plid'              => 1592,
				'isc'               => 'WPHosting1',
				'api_url'           => 'https://wpqs.secureserver.net/v1/',
				'help_url'          => 'https://help.securepaynet.net',
				'control_panel_url' => 'https://hostingmanager.secureserver.net/Login.aspx',
			);
			if ( !get_option( 'wtwp_options' ) ) {
				update_option( 'wtwp_options', $options );
			}
		}
	}

	/**
	 * Hook into the admin menu
	 * @return void
	 */
	public function settings_page() {
		add_options_page( __( 'Welcome Plugin', 'welcome-to-wordpress' ), __( 'Welcome Plugin', 'welcome-to-wordpress' ), 'manage_options', $this->slug . '-settings', array( $this, 'plugin_options' ) );
	}

	/**
	 * Set options
	 * @return void
	 */
	public function plugin_options() {

		// Check permissions
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		// Show settings page
		include_once( WTWP_PLUGIN_DIR . '/resources/settings.php' );
	}

	/**
	 * Load jQuery UI
	 * @return void
	 */
	public function load_scripts() {
		wp_enqueue_script( 'jquery-ui-core' );
	}

	/**
	 * Add our menu into the admin menu
	 * @return void
	 */
	public function admin_menu() {
		if ( 'Y' !== get_option( 'wtwp_show_help' ) ) {
			return;
		}
		add_menu_page( __( 'Welcome', 'welcome-to-wordpress' ), __( 'Welcome', 'welcome-to-wordpress' ), 'manage_options' , $this->slug , array( $this, 'welcome_page' ) , '' , -99999 );
		add_submenu_page( $this->slug , __( 'Welcome', 'welcome-to-wordpress' ) , __( 'Welcome', 'welcome-to-wordpress' ), 'manage_options' , $this->slug , array( $this, 'welcome_page' ) );
		add_submenu_page( $this->slug , __( 'Help', 'welcome-to-wordpress' ), __( 'Help', 'welcome-to-wordpress' ), 'manage_options' , $this->slug . '-help' , array( $this, 'help_page' ) );
		add_submenu_page( $this->slug , __( 'Security', 'welcome-to-wordpress' ), __( 'Security', 'welcome-to-wordpress' ), 'manage_options' , $this->slug . '-security' , array( $this, 'security_page' ) );
		add_submenu_page( $this->slug , __( 'Hosting Control Center', 'welcome-to-wordpress' ), __( 'Hosting Control Center', 'welcome-to-wordpress' ), 'manage_options' , $this->slug . '-control-panel' , array( $this, 'control_panel' ) );
	}

	/**
	 * Display the welcome page
	 * @return void
	 */
	public function welcome_page() {
		$label = __( 'Welcome', 'welcome-to-wordpress' );
		require_once( WTWP_PLUGIN_DIR . '/resources/header.php' );
		require_once( WTWP_PLUGIN_DIR . '/resources/welcome.php' );
		require_once( WTWP_PLUGIN_DIR . '/resources/footer.php' );
	}

	/**
	 * Display the help page
	 * @return void
	 */
	public function help_page() {
		$label = __( 'Help', 'welcome-to-wordpress' );
		require_once( WTWP_PLUGIN_DIR . '/resources/header.php' );
		require_once( WTWP_PLUGIN_DIR . '/resources/help.php' );
		require_once( WTWP_PLUGIN_DIR . '/resources/footer.php' );
	}

	/**
	 * Display the security page
	 * @return void
	 */
	public function security_page() {
		$label = __( 'Security', 'welcome-to-wordpress' );
		require_once( WTWP_PLUGIN_DIR . '/resources/header.php' );
		require_once( WTWP_PLUGIN_DIR . '/resources/security.php' );
		require_once( WTWP_PLUGIN_DIR . '/resources/footer.php' );
	}
	
	/**
	 * Open the host's control panel
	 * @return void
	 */
	public function control_panel () {
		$label = __( 'Hosting Control Center', 'welcome-to-wordpress' );
		require_once( WTWP_PLUGIN_DIR . '/resources/header.php' );
		require_once( WTWP_PLUGIN_DIR . '/resources/control-panel.php' );
		require_once( WTWP_PLUGIN_DIR . '/resources/footer.php' );
	}

	public function register_settings() {

			$_section = 'wtwp_settings_main';
			$group   = 'wtwp_settings_group';

			add_settings_section( $_section, __( 'Settings', 'welcome-to-wordpress' ), '__return_null', $this->slug . '-settings' );

			add_settings_field(
			    $id       = 'wtwp_use_hardening_rules',
			    $title    = __( 'Secure .htaccess rules', 'welcome-to-wordpress' ),
			    $callback = array( $this, 'show_secure_htaccess_field' ),
			    $page     = $this->slug . '-settings',
			    $section  = $_section
			);
			register_setting( $option_group = $group, $option_name = $id, array( $this, 'save_secure_htaccess_field' ) );

			add_settings_field(
			    $id       = 'wtwp_use_caching_rules',
			    $title    = __( 'Browser caching rules', 'welcome-to-wordpress' ),
			    $callback = array( $this, 'show_browser_caching_field' ),
			    $page     = $this->slug . '-settings',
			    $section  = $_section
			);
			register_setting( $option_group = $group, $option_name = $id, array( $this, 'save_browser_caching_field' ) );

			add_settings_field(
			    $id       = 'wtwp_use_object_cache',
			    $title    = __( 'Object cache', 'welcome-to-wordpress' ),
			    $callback = array( $this, 'show_object_cache_field' ),
			    $page     = $this->slug . '-settings',
			    $section  = $_section
			);
			register_setting( $option_group = $group, $option_name = $id, array( $this, 'save_object_cache_field' ) );

			add_settings_field(
			    $id       = 'wtwp_show_help',
			    $title    = __( 'Show help', 'welcome-to-wordpress' ),
			    $callback = array( $this, 'show_help_field' ),
			    $page     = $this->slug . '-settings',
			    $section  = $_section
			);
			register_setting( $option_group = $group, $option_name = $id, array( $this, 'save_help_field' ) );

	}
	
	/**
	 * Check if it's the admin's first login.
	 * If so, set up W3 Total Cache and flag so this function doesn't run again.
	 * @return void
	 */
	public function first_login() {
		$options = get_option( 'wtwp_options' );
		if ( empty( $options['first_login'] ) ) {

			// Set the first_login flag so this code doesn't run again
			$options['first_login'] = time();
			update_option( 'wtwp_options', $options );
			$this->load_environment_settings();
			wp_redirect( get_admin_url() );
			exit();
		}
	}
	
	/**
	 * Load environment optimizations
	 * @return void
	 */
	public function load_environment_settings() {
		global $is_iis7, $is_apache;

		// Update the htaccess rules
		if ( $is_apache ) {
			$this->_apply_htaccess_caching();
			$this->_apply_htaccess_security();
		}
		
		// Otherwise, add windows config options
		elseif ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 && $is_iis7 && extension_loaded( 'wincache' ) && function_exists( 'wincache_ucache_add' ) ) {

			// Enable wincache's opcode cache
			$this->_enable_wincache_ocache();
		}

		// Add in web.config optimizations
		if ( $is_iis7 ) {
			$this->_apply_web_config_caching();
		}
	}

	/**
	 * Show a pointer that highlights the Welcome plugin
	 */
	public function show_pointer() {
		
		// Dismiss any pointers from the query string
		if ( isset( $_REQUEST['clear_pointer'] ) && !empty( $_REQUEST['clear_pointer'] ) ) {
			$dismissed = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) ) );
			if ( !in_array( $_REQUEST['clear_pointer'], $dismissed ) ) {
				$dismissed[] = $_REQUEST['clear_pointer'];
				$dismissed = implode( ',', $dismissed );
				update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', $dismissed );
			}
		}
		
		// Show any pointers we have that aren't already dismissed
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		$options = get_option( 'wtwp_options' );
		if ( $options['first_login'] && !in_array( 'wtwp-intro-pointer', $dismissed ) )  {
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_style( 'wp-pointer' );
			include_once( WTWP_PLUGIN_DIR . '/resources/pointer.php' );
		}
	}

	/**
	 * Clean up
	 */
	public function deactivate() {
		global $is_apache, $is_iis7;
		
		// Remove config changes
		if ( $is_apache ) {
			$this->_remove_htaccess_caching();
			$this->_remove_htaccess_security();			
		} elseif ( $is_iis7 ) {
			$this->_rollback_web_config();
		}
		
		// Remove object cache
		$this->_remove_object_cache();
		
		// Disable wincache's opcode cache
		$this->_disable_wincache_ocache();
		
	}
	
	/**************************************************************************/
	/** .htaccess methods
	/**************************************************************************/
	
	/**
	 * Add caching rules to .htaccess
	 * @return void
	 */
	protected function _apply_htaccess_caching() {
		if ( !insert_with_markers( $this->_htaccess_path, 'wtwp_cache', $this->parse_htaccess( trailingslashit( WTWP_PLUGIN_DIR ) . 'resources/htaccess/wtwp_cache.txt' ) ) ) {
			return new WP_Error( 'generic', sprintf( __( 'Failed to update %s', 'welcome-to-wordpress' ), $this->_htaccess_path ) );
		}
		return true;
	}
	
	/**
	 * Add security rules to .htaccess
	 * @return void
	 */
	protected function _apply_htaccess_security() {
		if ( !insert_with_markers( $this->_htaccess_path, 'wtwp_security', $this->parse_htaccess( trailingslashit( WTWP_PLUGIN_DIR ) . 'resources/htaccess/wtwp_security.txt' ) ) ) {
			return new WP_Error( 'generic', sprintf( __( 'Failed to update %s', 'welcome-to-wordpress' ), $this->_htaccess_path ) );
		}
		return true;
	}

	/**
	 * Remove caching rules from .htaccess
	 * @return void
	 */
	protected function _remove_htaccess_caching() {
		if ( !insert_with_markers( $this->_htaccess_path, 'wtwp_cache', array() ) ) {
			return new WP_Error( 'generic', sprintf( __( 'Failed to update %s', 'welcome-to-wordpress' ), $this->_htaccess_path ) );
		}
		return true;
	}
	
	/**
	 * Remove security rules from .htaccess
	 * @return void
	 */
	protected function _remove_htaccess_security() {
		if ( !insert_with_markers( $this->_htaccess_path, 'wtwp_security', array() ) ) {
			return new WP_Error( 'generic', sprintf( __( 'Failed to update %s', 'welcome-to-wordpress' ), $this->_htaccess_path ) );
		}
		return true;
	}

	/**
	 * Customize our local .htaccess rules
	 * @param strin $filename
	 * @return array
	 */
	public function parse_htaccess( $filename ) {
		if ( !file_exists( $filename ) ) {
			return array();
		}
		$lines = file( $filename, FILE_IGNORE_NEW_LINES );
		array_shift( $lines ); // remove copyright notice
		foreach ( $lines as &$line ) {
			$line = str_replace(
				array( '$$HOME_PATH$$' ),
				array( trailingslashit( parse_url( home_url(), PHP_URL_PATH ) ) ),
			$line );
		}
		return $lines;
	}

	/**************************************************************************/
	/** web.config methods
	/**************************************************************************/

	/**
	 * Add caching rules to web.config
	 * @return void
	 */
	protected function _apply_web_config_caching() {

		if ( !file_exists( $this->_web_config_path ) ) {
			@touch( $this->_web_config_path );
		}
		if ( !file_exists( $this->_web_config_path ) || !win_is_writable( $this->_web_config_path ) ) {
			return new WP_Error( 'generic', sprintf( __( 'Failed to update %s', 'welcome-to-wordpress' ), $this->_web_config_path ) );
		}

		$flag =         $this->_merge_xml( $this->_web_config_path, trailingslashit( WTWP_PLUGIN_DIR ) . 'resources/web.config/browser_caching.xml' );
		$flag = $flag & $this->_merge_xml( $this->_web_config_path, trailingslashit( WTWP_PLUGIN_DIR ) . 'resources/web.config/compression.xml' );
		$flag = $flag & $this->_merge_xml( $this->_web_config_path, trailingslashit( WTWP_PLUGIN_DIR ) . 'resources/web.config/iis_caching.xml' );

		if ( !$flag ) {
			return new WP_Error( 'generic', sprintf( __( 'Failed to update %s', 'welcome-to-wordpress' ), $this->_web_config_path ) );
		}
		return true;
	}

	/**
	 * Rollback *all* web.config settings
	 * This removes the specific xpaths we added.
	 * Unmerging XML docs is ugly and complex.  We don't have the ability to ermove "chunks"
	 * like in the .htaccess files.  It's all or nothing.
	 * @return void
	 */
	protected function _rollback_web_config() {

		if ( !file_exists( $this->_web_config_path ) ) {
			return true;
		}
		if ( file_exists( $this->_web_config_path) && !win_is_writable( $this->_web_config_path ) ) {
			return false;
		}

		// Get the parent doc
		libxml_use_internal_errors( true );
		$DOMParent = new DOMDocument();
		$DOMParent->formatOutput = true;
		try {
			$DOMParent->load( $this->_web_config_path );
		} catch ( Exception $e ) {
			return false;
		}
		
		// Remove any matching nodes in the parent
		$xpath = new DOMXPath( $DOMParent );
		$paths = array(
			"//*/caching/profiles/add[@duration='00:01:00']",
			"//*/urlCompression[@doDynamicCompression='true']",
			"//*/clientCache[@cacheControlMaxAge='14.00:00:00']"
		);
		foreach ( $paths as $path ) {
			$query = $xpath->query( $path );
			if ( $query->length > 0 ) {
				for ( $i = 0 ; $i < $query->length ; $i++ ) {
					$query->item( $i )->parentNode->removeChild( $query->item( $i ) );
				}
			}
		}

		// Remove empty nodes
		$paths = array(
			"//configuration/system.webServer/caching/profiles",
			"//configuration/system.webServer/caching",
			"//configuration/system.webServer/staticContent",
			"//configuration/system.webServer",
			"//configuration"
		);
		foreach ( $paths as $path ) {
			$query = $xpath->query( $path );
			if ( $query->length > 0 ) {
				for ( $i = 0 ; $i < $query->length ; $i++ ) {
					$node = $query->item( $i );
					$nodeHasEmptyText = true;
					for ( $i = 0 ; $i < $node->childNodes->length ; $i++ ) {
						if ( ! ( $node->childNodes->item( $i ) instanceof DOMText ) || '' !== trim( $node->childNodes->item( $i )->nodeValue ) ) {								
							$nodeHasEmptyText = false;
							break;
						}
					}
					if ( !$node->hasAttributes() && ( !$node->hasChildNodes() || $nodeHasEmptyText ) ) {
						$node->parentNode->removeChild( $node );
					}
				}
			}
		}
		
		// If there is no root element, then save a dummy web.config
		if ( !$DOMParent->hasChildNodes() ) {
			if ( 0 === @file_put_contents( $this->_web_config_path, '<' . '?xml version="1.0"?' . ">\n<configuration><system.webServer/></configuration>" ) ) {
				return false;
			}
		} else {

			// Save the XML to the parent doc's file
			try {
				$DOMParent->save( $this->_web_config_path );
			} catch ( Exception $e ) {
				return false;
			}
		}

		return true;
	}
	
	/**
	 * Merge an XML fragment into a parent document (overwrite the matching element)
	 * This is used for adding rules to a web.config file
	 * @param string $parent Filename
	 * @param string $child Filename
	 * @param string|null $xpath_query e.g. "/configuration/system.webServer"
	 */
	protected function _merge_xml( $parent, $child, $xpath_query = '/configuration/system.webServer' ) {

		// If configuration file does not exist then we create one.
		clearstatcache();
		if ( ! file_exists( $parent ) || 0 === filesize( $parent ) ) {
			if ( 0 === @file_put_contents( $parent, '<' . '?xml version="1.0"?' . ">\n<configuration><system.webServer/></configuration>" ) ) {
				return false;
			}
		}

		// Get the child doc
		libxml_use_internal_errors( true );
		$DOMChild = new DOMDocument();
		try {
			$DOMChild->load( $child );
		} catch ( Exception $e ) {
			return false;
		}

		// Get the parent doc
		$DOMParent = new DOMDocument();
		$DOMParent->formatOutput = true;
		try {
			$DOMParent->load( $parent );
		} catch ( Exception $e ) {
			return false;
		}

		// Remove any matching nodes in the parent
		$xpath = new DOMXPath( $DOMParent );
		$query = $xpath->query( $xpath_query . '/' . $DOMChild->documentElement->tagName );
		if ( $query->length > 0 ) {
			for ( $i = 0 ; $i < $query->length ; $i++ ) {
				$query->item( $i )->parentNode->removeChild( $query->item( $i ) );
			}
		}

		// Add the new node to the parent
		$node = $DOMParent->importNode( $DOMChild->documentElement, true );
		$query = $xpath->query( $xpath_query );
		if ( $query->length > 0 ) {
			$query->item( 0 )->appendChild( $node );
		}

		// Save the XML to the parent doc's file
		try {
			$DOMParent->save( $parent );
		} catch ( Exception $e ) {
			return false;
		}

		return true;
	}

	/**************************************************************************/
	/** object cache methods
	/**************************************************************************/
	
	/**
	 * Add the object cache
	 * @return void
	 */
	protected function _add_object_cache() {
		if ( !$this->_is_object_cache_capable() ) {
			return new WP_Error( 'generic', __( 'This site is not capable of using an object cache', 'welcome-to-wordpress' ) );
		}
		if ( extension_loaded( 'apc' ) && function_exists( 'apc_add' ) ) {
			@copy( trailingslashit( WTWP_PLUGIN_DIR ) . 'resources/object_cache/apc.object-cache.php' , trailingslashit( WP_CONTENT_DIR ) . 'object-cache.php' );
		} elseif ( extension_loaded( 'wincache' ) && function_exists( 'wincache_ucache_add' ) ) {
			@copy( WTWP_PLUGIN_DIR . '/resources/object_cache/wincache.object-cache.php' , trailingslashit( WP_CONTENT_DIR ) . 'object-cache.php' );
		}
		if ( !file_exists( trailingslashit( WP_CONTENT_DIR ) . 'object-cache.php' ) ) {
			return new WP_Error( 'generic', sprintf( __( 'Failed to write to %s', 'welcome-to-wordpress' ), trailingslashit( WP_CONTENT_DIR ) . 'object-cache.php' ) );			
		}
		return true;
	}

	/**
	 * Check to see if the object cache is installed
	 * @return bool
	 */
	protected function _is_object_cache_installed() {
		$object_cache = trailingslashit( WP_CONTENT_DIR ) . 'object-cache.php';
		if ( extension_loaded( 'apc' ) && function_exists( 'apc_add' ) ) {
			$orig_object_cache = WTWP_PLUGIN_DIR . '/resources/object_cache/apc.object-cache.php';
		} elseif ( extension_loaded( 'wincache' ) && function_exists( 'wincache_ucache_add' ) ) {
			$orig_object_cache = WTWP_PLUGIN_DIR . '/resources/object_cache/wincache.object-cache.php';
		}
		return ( file_exists( $object_cache ) && file_exists( $orig_object_cache ) && md5_file( $object_cache ) == md5_file( $orig_object_cache ) );
	}

	/**
	 * Is the site capable of using our object caches and there isn't an existing object cache installed
	 * @return bool
	 */
	protected function _is_object_cache_capable() {
		$apc             = extension_loaded( 'apc' ) && function_exists( 'apc_add' );
		$wincache        = extension_loaded( 'wincache' ) && function_exists( 'wincache_ucache_add' );
		$other_installed = file_exists( trailingslashit( WP_CONTENT_DIR ) . 'object-cache.php' ) && !$this->_is_object_cache_installed();
		return ( $apc || $wincache ) && !$other_installed;
	}

	/**
	 * Remove the object cache
	 * @return void
	 */
	protected function _remove_object_cache() {
		if ( $this->_is_object_cache_installed() ) {
			if ( !unlink( trailingslashit( WP_CONTENT_DIR ) . 'object-cache.php' ) ) {
				return new WP_Error( 'generic', sprintf( __( 'Failed to remove %s', 'welcome-to-wordpress' ), trailingslashit( WP_CONTENT_DIR ) . 'object-cache.php' ) );
			}
			register_shutdown_function( 'wp_cache_flush' );
		}
		return true;
	}

	/**************************************************************************/
	/** preview dns methods
	/**************************************************************************/

	/**
	 * Replace the site's hostname with the previewdns hostname
	 * @param string $arg
	 * @return string
	 */
	public function previewdns_rewrite_url( $arg ) {
		return preg_replace_callback('/http[s]?:\/\/[a-zA-Z0-9\.-]+/i', array( $this, 'previewdns_fix_link' ), $arg );
	}

	/**
	 * Rewrite URLs that don't already contain previewdns.com
	 * @param string $url
	 * @return string
	 */
	public function previewdns_fix_link( $url ) {
		if ( is_array( $url ) ) {
			$url = array_shift( $url );
		}
		if ( false === stripos( $url, 'previewdns.com' ) ) {
			$url = str_replace( '://' . $_SERVER['HTTP_HOST'] , '://' . $_SERVER['HTTP_X_FORWARDED_HOST'] , $url );	
		}
		return $url;
	}

	/**
	 * Rewrite all links in outgoing e-mails
	 * @param array $mail
	 * @return array
	 */
	public function previewdns_rewrite_mail_links( $mail ) {
		$mail[ 'message' ] = $this->previewdns_rewrite_url( $mail['message'] );
		return $mail;
	}

	/**
	 * Enhance functionality between WordPress and previewdns
	 * @return void
	 */
	public function preview_dns_hooks() {
		if ( array_key_exists( 'HTTP_X_FORWARDED_SERVER' , $_SERVER ) && 'previewdns.com' == $_SERVER['HTTP_X_FORWARDED_SERVER'] ) {
			
			// Reset the REMOTE_ADDR header
			if ( array_key_exists( 'HTTP_X_FORWARDED_FOR' , $_SERVER ) && !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )
				$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];

			// Fix outbound e-mail links
			add_filter( 'wp_mail', array( $this, 'previewdns_rewrite_mail_links' ) );

			// Rewrite the encoded URLs on the login forms that aren't picked up by the reverse proxy
			add_filter( 'login_redirect' , array( $this, 'previewdns_rewrite_url'), 9999 );
			add_filter( 'login_url' , array( $this, 'previewdns_rewrite_url'), 9999 );
		}
	}
	
	/**************************************************************************/
	/** php.ini methods
	/**************************************************************************/
	
	/**
	 * Write an ini file
	 * @param string $filename
	 * @param array $ini
	 */
	protected function _write_ini_file( $filename, array $ini ) {
		$contents = '';
        foreach ( $ini as $k => $v ) { 
            if ( is_array( $v ) ) {
				for( $i = 0 ; $i < count( $v ) ; $i++ ) { 
					$contents .= "{$k}[] = \"{$v[$i]}\"\n";
                }
            } elseif ( empty( $v ) ) {
				$contents .= "$k = \n";
			} elseif ( is_scalar( $v ) ) {
				$contents .= "$k = \"$v\"\n";
			}
		}
		file_put_contents( $filename, $contents );
	}
	
	/**
	 * Enable wincache's opcode cache for Windows PHP 5.3+
	 * @return void
	 */
	protected function _enable_wincache_ocache() {
		global $is_iis7;
		if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 && $is_iis7 && extension_loaded( 'wincache' ) && function_exists( 'wincache_ucache_add' ) ) {

			// Enable wincache's opcode cache
			$file = trailingslashit( ABSPATH ) . 'php5.ini';
			if ( !file_exists( $file ) || win_is_writable( $file ) ) {
				$directives = array();
				if ( file_exists( $file ) ) {
					$directives = parse_ini_file( $file );
				}
				$directives = array_merge( $directives, array(
					'wincache.ocenabled'   => 'On'
				));
				$this->_write_ini_file( $file, $directives );
			}
		}
	}

	/**
	 * Disable wincache's opcode cache
	 * @return void
	 */
	protected function _disable_wincache_ocache() {
		global $is_iis7;
		if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 && $is_iis7 && function_exists( 'wincache_ucache_add' ) ) {
			$file = trailingslashit( ABSPATH ) . 'php5.ini';
			if ( !file_exists( $file ) || win_is_writable( $file ) ) {
				$directives = array();
				if ( file_exists( $file ) ) {
					$directives = parse_ini_file( $file );
				}
				unset( $directives['wincache.ocenabled'] );
				$this->_write_ini_file( $file, $directives );
			}
		}
	}

	public function show_secure_htaccess_field() {
		global $is_apache;
		?>
			<fieldset>
				<legend class="screen-reader-text secure-title">
					<?php _e( 'Secure .htaccess rules', 'welcome-to-wordpress' ); ?>
				</legend>
			</fieldset>
			<label for="wtwp_use_hardening_rules" class="secure-label">
				<?php $hardened = ( array() != extract_from_markers( $this->_htaccess_path, 'wtwp_security' ) ); ?>
				<input type="checkbox" id="wtwp_use_hardening_rules" name="wtwp_use_hardening_rules" <?php disabled( !$is_apache ); ?> <?php checked( $hardened ); ?> />
				<?php if ( $is_apache ) : ?>
					<?php _e('Use hardened WordPress .htaccess rules', 'welcome-to-wordpress' ); ?>
				<?php else : ?>
					<?php _e('Hardening rules are not available on this platform', 'welcome-to-wordpress' ); ?>
				<?php endif; ?>
			</label>
			<p class="description secure-description"><?php _e( 'This feature is only available for apache web servers.', 'welcome-to-wordpress' ); ?></p>
		<?php
	}
	
	public function save_secure_htaccess_field( $value ) {
		global $is_apache;
		if ( !$is_apache ) {
			return 'N';
		}
		if ( 'on' === $value ) {
			$result = $this->_apply_htaccess_security();
			if ( !is_wp_error( $result ) ) {
				return 'N';
			} else {
				add_settings_error( 'wtwp_use_hardening_rules', '', $result->get_error_message(), 'error' );
				return 'Y';	
			}
		} else {
			$result = $this->_remove_htaccess_security();
			if ( !is_wp_error( $result ) ) {
				return 'Y';
			} else {
				add_settings_error( 'wtwp_use_hardening_rules', '', $result->get_error_message(), 'error' );
				return 'N';	
			}
		}
	}
	
	public function show_browser_caching_field() {
		global $is_iis7, $is_apache;
		if ( $is_iis7 ) {
			if ( file_exists( $this->_web_config_path ) && is_readable( $this->_web_config_path ) ) {
				$caching = ( false !== strpos( file_get_contents( $this->_web_config_path ), '<urlCompression' ) );
			} else {
				$caching = false;
			}
		} elseif ( $is_apache ) {
			$caching = ( array() != extract_from_markers( $this->_htaccess_path, 'wtwp_cache' ) );
		} else {
			$caching = false;
		}
		?>
			<fieldset>
				<legend class="screen-reader-text browser-title">
					<?php _e( 'Browser caching rules', 'welcome-to-wordpress' ); ?>
				</legend>
			</fieldset>
			<label for="wtwp_use_caching_rules" class="browser-label">
				<input type="checkbox" id="wtwp_use_caching_rules" name="wtwp_use_caching_rules" <?php disabled( !$is_iis7 && !$is_apache ); ?> <?php checked( $caching ); ?> />
				<?php if ( $is_iis7 ) : ?>
					<?php _e( 'Enable browser caching web.config rules', 'welcome-to-wordpress' ); ?>
				<?php elseif ( $is_apache ) : ?>
					<?php _e( 'Enable browser caching .htaccess rules', 'welcome-to-wordpress' ); ?>
				<?php else : ?>
					<?php _e( 'Browser caching rules are not available on this platform', 'welcome-to-wordpress' ); ?>
				<?php endif; ?>
			</label>
			<p class="description browser-description"><?php _e( 'Caching WordPress rules are only available on IIS7 and apache', 'welcome-to-wordpress' ); ?></p>
		<?php
	}

	public function save_browser_caching_field( $value ) {
		global $is_apache, $is_iis7;
		if ( 'on' === $value ) {
			if ( $is_apache) {
				$result = $this->_apply_htaccess_caching();
				if ( !is_wp_error( $result ) ) {
					return 'Y';
				} else {
					add_settings_error( 'wtwp_use_caching_rules', '', $result->get_error_message(), 'error' );
					return 'N';	
				}
			} elseif ( $is_iis7 ) {
				$result = $this->_apply_web_config_caching( );
				if ( !is_wp_error( $result ) ) {
					return 'Y';
				} else {
					add_settings_error( 'wtwp_use_caching_rules', '', $result->get_error_message(), 'error' );
					return 'N';	
				}
			}
		} else {
			if ( $is_apache) {
				$result = $this->_remove_htaccess_caching();
				if ( !is_wp_error( $result ) ) {
					return 'N';
				} else {
					add_settings_error( 'wtwp_use_caching_rules', '', $result->get_error_message(), 'error' );
					return 'Y';	
				}
			} elseif ( $is_iis7 ) {
				$result = $this->_rollback_web_config();
				if ( !is_wp_error( $result ) ) {
					return 'N';
				} else {
					add_settings_error( 'wtwp_use_caching_rules', '', $result->get_error_message(), 'error' );
					return 'Y';	
				}
			}
		}
	}

	public function show_object_cache_field() {
		$capable = $this->_is_object_cache_capable();
		$installed = $this->_is_object_cache_installed();
		?>
		<fieldset>
			<legend class="screen-reader-text object-title">
				<?php _e( 'Object cache', 'welcome-to-wordpress' ); ?>
			</legend>
		</fieldset>
		<label for="wtwp_use_object_cache" class="object-label">
			<input type="checkbox" id="wtwp_use_object_cache" name="wtwp_use_object_cache" <?php disabled( !$capable ); ?> <?php checked( $installed ); ?> />
			<?php _e( 'Enable object caching', 'welcome-to-wordpress' ); ?>
		</label>
		<p class="description object-description"><?php _e( 'This feature is only available with WinCache or APC', 'welcome-to-wordpress' ); ?></p>
		<?php
	}

	public function save_object_cache_field( $value ) {
		if ( 'on' === $value && $this->_is_object_cache_capable() ) {
			$result = $this->_add_object_cache();
			if ( !is_wp_error( $result ) ) {
				return 'Y';
			} else {
				add_settings_error( 'wtwp_use_object_cache', '', $result->get_error_message(), 'error');
				return 'N';
			}
		} else {
			$result = $this->_remove_object_cache();
			if ( !is_wp_error( $result ) ) {
				return 'N';
			} else {
				add_settings_error( 'wtwp_use_object_cache', '', $result->get_error_message(), 'error' );
				return 'Y';
			}
		}
	}

	public function show_help_field() {
		?>
		<fieldset>
			<legend class="screen-reader-text help-title">
				<?php _e( 'Show help', 'welcome-to-wordpress' ); ?>
			</legend>
		</fieldset>
		<label for="wtwp_show_help" class="help-label">
			<input type="checkbox" id="wtwp_show_help" name="wtwp_show_help" <?php checked( 'Y' === get_option( 'wtwp_show_help' ) ); ?> />
			<?php _e( 'Show "Welcome" menu in the WordPress admin', 'welcome-to-wordpress' ); ?>
		</label>
		<?php
	}
	
	public function save_help_field( $value ) {
		return ( 'on' === $value ) ? 'Y' : 'N';
	}
}
