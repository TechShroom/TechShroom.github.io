<?php

if(defined('WP_UNINSTALL_PLUGIN')) {
    global $wpdb;

    $options_table = $wpdb->options;
    $wpdb->query( "DELETE FROM ".$options_table." WHERE option_name LIKE '".CGMP_ALL_MAP_CACHED_CONSTANTS_PREFIX."%';" );

    //legacy
    delete_option(CGMP_DB_PUBLISHED_POST_MARKERS);
    delete_option(CGMP_DB_POST_COUNT);
    delete_option(CGMP_DB_PUBLISHED_POST_IDS);
    delete_option(CGMP_DB_PUBLISHED_PAGE_IDS);
    delete_option(CGMP_DB_SETTINGS_SHOULD_BASE_OBJECT_RENDER);
    delete_option(CGMP_DB_SETTINGS_WAS_BASE_OBJECT_RENDERED);
    delete_option(CGMP_DB_PURGE_GEOMASHUP_CACHE);
    delete_option(CGMP_DB_GEOMASHUP_CONTENT);
}

?>


//SELECT option_name FROM wp_options WHERE option_name LIKE 'cgmp_cache_%'
