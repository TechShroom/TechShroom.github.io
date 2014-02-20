<?php

/**
 * Copyright 2012 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

if ( !defined( 'ABSPATH') ) {
    exit();
}

$options = get_option( 'wtwp_options' );

?>

<p class ="wtwp-control-panel-description">
	<?php _e( 'Your Hosting Control Center is where you can create new databases, subdomains, FTP users, and perform other actions related to your hosting account.', 'welcome-to-wordpress' ); ?>
</p>

<div id="hcc-div">
	<a class="button-primary" target="_blank" href="<?php echo esc_attr( $options['control_panel_url'] ); ?>" id="wtwp-hcc-link"><?php esc_attr_e( 'Open your Hosting Control Center in a new window', 'welcome-to-wordpress' ); ?></a>
</div>
