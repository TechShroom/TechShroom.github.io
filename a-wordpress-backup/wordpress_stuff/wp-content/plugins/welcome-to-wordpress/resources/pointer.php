<?php

/**
 * Copyright 2012 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH') )
	die( 'Forbidden' );

?>
<style type="text/css">
#wtwp-pointer-primary {
	margin: 0 5px 0 0;
}	
</style>
<script type="text/javascript"> 
//<![CDATA[ 
jQuery(document).ready( function() { 
	jQuery('#toplevel_page_<?php echo $this->slug; ?>').pointer({ 
		'content': "<h3 class=pointer-title><?php echo esc_js( __( 'Welcome to WordPress', 'welcome-to-wordpress'  ) ); ?></h3><p class=pointer-description><?php echo esc_js( __( "New to WordPress? You should know some things before you get started with your site." , 'welcome-to-wordpress' ) ); ?></p>",
		'close' : function() {
			jQuery.post( ajaxurl, {
				pointer: 'wtwp-intro-pointer',
				action: 'dismiss-wp-pointer'
			});
		},
		'buttons': function( event, t ) {
			button = jQuery('<a id="wtwp-pointer-close" class="button-secondary"><?php echo esc_js( __( 'Cancel', 'welcome-to-wordpress' ) ); ?></a>');
			button.bind( 'click.wtwp-pointer', function() {
				t.element.pointer('close');
			});
			return button;
		}
	}).pointer('open');
	jQuery('#wtwp-pointer-close').after('<a id="wtwp-pointer-primary" class="button-primary"><?php echo esc_js( __( 'Show Me', 'welcome-to-wordpress' ) ); ?></a>');
	jQuery('#wtwp-pointer-primary').click( function() {
		document.location="<?php echo add_query_arg( array (
			'page'          => $this->slug,
			'clear_pointer' => 'wtwp-intro-pointer'
		), 'admin.php' ) ; ?>";
	});
});
//]]>
</script>
