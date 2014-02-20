<?php

if ( !function_exists('cgmp_admin_bar_menu') ):
    function cgmp_admin_bar_menu() {

        global $wp_admin_bar;
        if ( !is_super_admin() || !is_admin_bar_showing() )
            return;

        $wp_admin_bar->add_menu( array(
            'parent' => "new-content",
            'id' => "cgmp-admin-bar-menu-new-shortcode",
            'title' => "<span class='ab-icon'></span><span class='ab-label'>Shortcodes</span>",
            'href' => "admin.php?page=cgmp-shortcodebuilder",
            'meta' => FALSE
        ) );

        $root_id = "cgmp";
        $wp_admin_bar->add_menu( array(
            'id'   => $root_id,
            'meta' => array(),
            'title' => "<span class='ab-icon'></span><span class='ab-label'>Google Map</span>",
            'href' => FALSE ));

        $wp_admin_bar->add_menu( array(
            'parent' => $root_id,
            'id' => "cgmp-admin-bar-menu-documentation",
            'title' => "Documentation",
            'href' => "admin.php?page=cgmp-documentation",
            'meta' => FALSE
        ) );

        $wp_admin_bar->add_menu( array(
            'parent' => $root_id,
            'id' => "cgmp-admin-bar-menu-shortcode-builder",
            'title' => "Shortcode Builder",
            'href' => "admin.php?page=cgmp-shortcodebuilder",
            'meta' => FALSE
        ) );

        $wp_admin_bar->add_menu( array(
            'parent' => $root_id,
            'id' => "cgmp-admin-bar-menu-saved-shortcodes",
            'title' => "Saved Shortcodes",
            'href' => "admin.php?page=cgmp-saved-shortcodes",
            'meta' => FALSE
        ) );

        $wp_admin_bar->add_menu( array(
            'parent' => $root_id,
            'id' => "cgmp-admin-bar-menu-settings",
            'title' => "Settings & Support",
            'href' => "admin.php?page=cgmp-settings",
            'meta' => FALSE
        ) );
    }
endif;

?>