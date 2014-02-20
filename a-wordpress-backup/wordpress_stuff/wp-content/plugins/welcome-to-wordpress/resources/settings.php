<?php

/**
 * Copyright 2012 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

if ( !defined( 'ABSPATH') ) {
    exit();
}

?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br/></div>
	<h2 class="page-title"><?php _e( 'Welcome to WordPress', 'welcome-to-wordpress' ); ?></h2>

	<form id="wtwp-settings-form" name="wtwp-settings-form" method="post" action="options.php">
		
		<?php settings_fields( 'wtwp_settings_group' ); ?>
		<?php do_settings_sections( $this->slug . '-settings' ); ?>

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" name="wtwp_submit" id="wtwp-submit" />
		</p>
	</form>
</div>