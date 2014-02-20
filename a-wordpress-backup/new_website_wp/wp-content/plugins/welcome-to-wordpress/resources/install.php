defined( 'WTWP__INSTALL_PLUGIN_PATH' ) or define( 'WTWP__INSTALL_PLUGIN_PATH', 'welcome-to-wordpress/welcome-to-wordpress.php' );

function install_wtwp()
{
	global $pagenow;

	if ( !( 'install.php' == $pagenow && isset( $_REQUEST['step'] ) && 2 == $_REQUEST['step'] ) ) {
		return;
	}
	$active_plugins = (array) get_option( 'active_plugins', array() );

	// Shouldn't happen, but avoid duplicate entries just in case.
	if ( !empty( $active_plugins ) && false !== array_search( WTWP__INSTALL_PLUGIN_PATH, $active_plugins ) ) {
		return;
	}

	$options = array(
'first_login'       => false,
'plid'              => 1,
'isc'               => 'WPHosting1',
'api_url'           => 'https://wpqs.secureserver.net/v1/',
'help_url'          => 'https://help.securepaynet.net',
'control_panel_url' => 'https://hostingmanager.secureserver.net/Login.aspx',
'key'               => 'McAuZyoR2IkY9+EtjErNmQ7gjMkXIhWaAEMMyxHr3OWTfET3DNdH+vq60MWIDDXw'
);
	
	$active_plugins[] = WTWP__INSTALL_PLUGIN_PATH;
	update_option( 'active_plugins', $active_plugins );
	update_option( 'wtwp_options', $options );
}

add_action( 'shutdown', 'install_wtwp' );
