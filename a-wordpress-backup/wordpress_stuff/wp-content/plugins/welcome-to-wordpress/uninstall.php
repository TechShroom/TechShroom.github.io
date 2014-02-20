<?php

/**
 * Copyright 2012 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

if( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

// Remove options
delete_option( 'wtwp_show_help' );
delete_option( 'wtwp_version' );
delete_option( 'wtwp_use_object_cache' );
delete_option( 'wtwp_use_caching_rules' );
delete_option( 'wtwp_use_hardening_rules' );
delete_option( 'wtwp_options' );

// Remove dismissed pointer
$user_ids = $GLOBALS['wpdb']->get_results("SELECT user_id from {$GLOBALS['wpdb']->usermeta} WHERE meta_key LIKE 'dismissed_wp_pointers' AND meta_value LIKE  '%wtwp-intro-pointer%'");
foreach ( (array) $user_ids as $user_id ) {
	$dismissed = explode( ',', (string) get_user_meta( $user_id, 'dismissed_wp_pointers', true ) );
	$idx = array_search( 'wtwp-intro-pointer', $dismissed, true );
	unset( $dismissed[$idx] );
	update_user_meta( $user_id, 'dismissed_wp_pointers', implode( ',', $dismissed ) );
}
