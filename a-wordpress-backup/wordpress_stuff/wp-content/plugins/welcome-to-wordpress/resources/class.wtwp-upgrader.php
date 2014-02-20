<?php

/**
 * Copyright 2012 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH') )
	die( 'Forbidden' );

/**
 * Upgrade plugins from a third party system
 */
class WTWP_Upgrader {

	/**
	 * The target plugin's slug, according to WordPress
	 * @var string
	 */
	protected $_slug = '';
	
	/**
	 * Plugin data, fetched from an external API
	 * @var mixed
	 */
	protected $_api_data = null;
	
	/**
	 * Plugin data, read from get_plugin_data
	 * @var array
	 */
	protected $_plugin_data = null;
	
	/**
	 * A safe key for variants
	 * @var string
	 */
	protected $_key = '';

	/**
	 * Class Constructor
	 * @return WTWP_Upgrader
	 */
	public function __construct( $slug = '' ) {

		// Add data
		$this->_slug = $slug;
		$this->_key = $slug;
		if ( false !== strpos( $this->_key, '/' ) ) {
			$tmp = explode( '/', $this->_key );
			$tmp = array_pop( $tmp );
			$this->_key = basename( strtolower( $tmp ), '.php' );
		}

		// Add hooks
		add_filter( 'admin_init', array( $this, 'init' ) );
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'api_check' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
	}

	/**
	 * Initialize, fetch current plugin data and new plugin data
	 * @return void
	 */
	public function init() {
		$data = null;
		$path = WTWP_PLUGIN_DIR . '/welcome-to-wordpress.php';
		if ( file_exists( $path ) ) {
			$data = get_plugin_data( $path );
		}
		$this->_plugin_data = $data;
		$this->_api_data = $this->get_api_data( $data );		
	}

	/**
	 * Plugin information
	 * @param bool $false always false
	 * @param string $action the API function being performed
	 * @param object $args plugin arguments
	 * @return object $response the plugin info
	 */
	public function plugin_info( $false, $action, $args ) {
		if ( !isset( $args->slug ) || !isset( $this->_key ) || $args->slug != $this->_key ) {
			return $false;
		}
		if ( !isset( $this->_api_data ) ) {
			$this->init();
		}
		$response                = new stdClass();
		$response->slug          = $this->_key;
		$response->plugin_name   = $this->_api_data->plugin_name;
		$response->version       = $this->_api_data->new_version;
		$response->author        = $this->_api_data->author;
		$response->homepage      = $this->_api_data->homepage;
		$response->requires      = $this->_api_data->requires;
		$response->tested        = $this->_api_data->tested;
		$response->downloaded    = 0;
		$response->last_updated  = $this->_api_data->last_updated;
		if ( isset( $this->_api_data->sections ) && !empty( $this->_api_data->sections ) ) {
			$response->sections = array_merge(
				array(
					'description' => $this->_api_data->description
				),
				(array) $this->_api_data->sections
			);
		} else {
			$response->sections      = array(
				'description' => $this->_api_data->description
			);			
		}
		$response->download_link = $this->_api_data->package;		
		return $response;
	}
	
	/**
	 * Get latest plugin data from API
	 * Cached in a transient for 6 hours
	 * @return mixed
	 */
	protected function get_api_data() {
		global $wp_version, $wpdb;
		$_options = get_option( 'wtwp_options' );
		$api_data = get_site_transient( $this->_key . '_api_data' );
		$skin = '';
		if ( function_exists( 'cyberchimps_get_option' ) ) {
			$skin = cyberchimps_get_option( 'cyberchimps_skin_color', '' );
		}
		if ( empty( $api_data ) ) {
			$options = array(
				'headers'   => array(
					'X-Plugin-Api-Key'        => $_options['key'],
					'X-Plugin-Theme'          => wp_get_theme()->get_stylesheet(),
					'X-Plugin-Theme-Version'  => wp_get_theme()->get( 'Version' ),
					'X-Plugin-Theme-Skin'     => $skin,
					'X-Plugin-URL'            => get_home_url(),
					'X-Plugin-WP-Version'     => $wp_version,
					'X-Plugin-Plugins'        => json_encode( get_option( 'active_plugins' ) ),
					'X-Plugin-MySQL-Version'  => $wpdb->db_version(),
					'X-Plugin-PHP-Version'    => PHP_VERSION,
					'X-Plugin-Locale'         => get_locale(),
					'X-Plugin-WP-Lang'        => ( defined( 'WP_LANG' ) ? WP_LANG : 'en_US' ),
					'X-Plugin-Version'        => $this->_plugin_data['Version'],
					'X-Plugin-Slug'           => $this->_key,
					'X-Plugin-PLID'           => $_options['plid'],
				)
			);
			$url = trailingslashit( $_options['api_url'] ) . 'updates/plugin/' . $this->_key;
			add_filter( 'https_ssl_verify', '__return_false' );
			$response = wp_remote_get( $url, $options );
			remove_filter( 'https_ssl_verify', '__return_false' );
			if ( is_wp_error( $response) || ( $response['response']['code'] != 200 ) ) {
				$api_data = new stdClass();
			} else {
				$api_data = json_decode( $response['body'] );
			}
			set_site_transient( $this->_key . '_api_data', $api_data, 60 * 60 * 6 );
		}
		return $api_data;
	}

	/**
	 * Hook into the plugin update check
	 * @param mixed $transient
	 * @return mixed
	 */
	public function api_check( $transient ) {
		if ( !isset( $this->_api_data ) ) {
			$this->init();
		}
		if ( isset( $this->_api_data ) && isset( $this->_api_data->new_version ) && isset( $this->_plugin_data ) && isset( $this->_plugin_data['Version'] ) && 1 === version_compare( $this->_api_data->new_version, $this->_plugin_data['Version'] ) ) {
			$transient->response[ $this->_slug ] = $this->_api_data;
		}
		return $transient;
	}
}
