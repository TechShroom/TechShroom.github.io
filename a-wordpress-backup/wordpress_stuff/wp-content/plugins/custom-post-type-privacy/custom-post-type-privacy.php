<?php
/*
Plugin Name: Custom Post Type Privacy
Plugin URI: http://www.ki-media.co.uk/wordpress/custom-post-type-privacy/
Version: 0.3
Description: Granular user and group access controls for all content.
Author: Kev Price
Author URI: http://www.ki-media.co.uk/

Credits:

   Pete Holiday for writing WP CPT Sentry which I have stripped back and then built onto.


Helpful links for plugin internationalization:

    http://www.symbolcraft.com/blog/how_to_i18n_your_wordpress_plugin/29/
    http://pressedwords.com/6-tips-for-localizing-your-wordpress-plugin/

*/

if (!class_exists('wp_cpt_sentry')) {
    $wpversion_full = get_bloginfo('version');
    $wpversion = preg_replace('/([0-9].[0-9])(.*)/', '$1', $wpversion_full); //Boil down version number to X.X

   // include('feedkey.php');
	include('cpt-sentry-widgets.php');

    /**
     * The wp_cpt_sentry class wraps the plugin's functions to help avoid function collisions.
     *
     * Lots of great ideas and tips gleaned from Filipe Fortes' excellent plugin "Post Levels".
     *
     * @author      Pete Holiday <pete.holiday@gmail.com>
     * @copyright   Pete Holiday 2009
     * @version     0.8
     */
    class wp_cpt_sentry {
        // WP CPT Sentry Database schema versioning
        var $db_version = '0.25';
        var $textdomain = 'wpcptsentry';

        // Name of the Database Table
        var $group_table_name;

        // For outputting results to the user
        var $error_list;
        var $status_list;

        // Keys for the Users/posts meta-data
        var $post_metakey_groups = 'cpt_sentry_groups';
        var $post_metakey_users = 'cpt_sentry_users';
        var $replacement_widgets;
        var $preview_types;

        // These are variables for WP-stored Options
        var $opt_previews;
        var $opt_preview_all_users_only;
        var $opt_denied_message;
        var $opt_default_post_groups;
        var $opt_default_post_users;
        var $opt_default_user_groups;
        var $opt_post_title_prefix;
        var $opt_post_title_postfix;
        var $opt_preview_permalink;
      //  var $opt_feedkey_enabled;
		//var $opt_feedkey_url_rewrite_enabled;
        var $opt_post_user_list_enabled;


// Setup & Maintenance Functions

       /**
        *  This function handles plugin initialization and hooking into the WordPress API
        */
        function init() {
            // $wpdb is a database object from WordPress
            global $wpdb, $wpversion;

            // This is the name of the database table that holds group definition information
            $this->group_table_name = $wpdb->prefix . "cpt_sentry_groups";

            // These two variables are used for back-endd UI displays only.
            $this->error_list = array();
            $this->status_list = array();

            $cpt_sentry_dir = dirname(plugin_basename(__FILE__));
            load_plugin_textdomain($this->textdomain, 'DEPRECATED IN 2.7', $cpt_sentry_dir . '/lang');

            $this->preview_types = array(
                'None' => __('None', $this->textdomain),
                'Title' => __('Title', $this->textdomain),
                'Excerpt' => __('Excerpt', $this->textdomain),
                'Above-the-fold' => __('Above-the-fold', $this->textdomain)
            );

            $this->replacement_widgets = array(
                'pages'             => array('title' => 'Pages', 'name' => 'widget_pages'),
                // 'tag_cloud'         => array('title' => 'Tag Cloud', 'name' => 'tag_cloud'),
                'recent_comments'   => array('title' => 'Recent Comments', 'name' => 'widget_recent_comments')
            );


            // Retrieve blog-wide plugin options, set values if the option does not yet exist:
            $this->opt_previews =                    $this->initialize_wp_option('cpt_sentry_preview_type', 'None');
            $this->opt_preview_all_users_only =      $this->initialize_wp_option('cpt_sentry_preview_all_users_only', 'No');
            $this->opt_preview_permalink =           $this->initialize_wp_option('cpt_sentry_preview_permalink', '');
            $this->opt_denied_message =              $this->initialize_wp_option('cpt_sentry_denied_message', '<p>' . __('You do not have permission to view this post.', $this->textdomain) . '</p>');
            $this->opt_post_title_prefix =           $this->initialize_wp_option('cpt_sentry_post_title_prefix', __('Private', $this->textdomain) . ': ');
            $this->opt_post_title_postfix =          $this->initialize_wp_option('cpt_sentry_post_title_postfix', '');
            $this->opt_default_post_groups =         $this->initialize_wp_option('cpt_sentry_default_post_groups', '');
            $this->opt_default_post_users =          $this->initialize_wp_option('cpt_sentry_default_post_users', '');
            $this->opt_default_user_groups =         $this->initialize_wp_option('cpt_sentry_default_user_groups', '');
          //  $this->opt_feedkey_enabled =             $this->initialize_wp_option('cpt_sentry_feedkey_enabled', 'No');
		//	$this->opt_feedkey_url_rewrite_enabled = $this->initialize_wp_option('cpt_sentry_feedkey_url_rewrite_enabled', 'No');
            $this->opt_post_user_list_enabled =      $this->initialize_wp_option('cpt_sentry_post_user_list_enabled', 'Yes');

            $this->replacement_widgets['pages']['enabled'] = $this->initialize_wp_option('widget_pages_enabled', 'No');
            $this->replacement_widgets['recent_comments']['enabled'] = $this->initialize_wp_option('widget_recent_comments_enabled', 'Yes');
            // $this->replacement_widgets['tag_cloud']['enabled'] = $this->initialize_wp_option('widget_tag_cloud_enabled', 'Yes');

            // Disable embedded feed key plugin if the real thing exists.
          //  if ($sentryerror_feedkey_exists) { $this->opt_feedkey_enabled = 'No'; }


        	//filter all posts types to ensure they are correctly displayed
        	add_filter('the_posts', array(&$this, 'privacy_check'),10, 1 );

            // Hook into the API to add admin panels to WordPress
            add_action('admin_menu', array(&$this, 'add_admin_panels') );

            // Hook into the API to do different things
            //  - edit_form_advanced:         Adds the group/user access box to the post editing screen
            //  - save_post:                  Update a posts access settings on save, including when
			//                                scheduled posts are published.
            //  - status_save_pre:            Make sure posts with group/user access set are private
            //                                This prevents the accidental publishing of sensitive information
            //  - the_title:                  Allows for the use of custom pre/postfixes for private posts
            //  - user_register:              Places new users in the default categories
            //  - widgets_init:               Register our own custom widgets
            //  - admin_head:                 Inserts some code in the <head></head> of the admin pages
			//  - manage_posts_columns:       Adds a column to the posts management screen with Sentry info
			//  - manage_pages_columns:       Adds a column to the pages management screen with sentry ifno
			//  - manage_users_columns:       Adds a column to the pages management screen with sentry info
			//  - manage_posts_custom_column:
			//  - manage_pages_custom_column:
			//  - manage_users_custom_column:

            add_action('save_post', array(&$this, 'save_post'), 5, 2);
            // add_filter('status_save_pre', array(&$this, 'save_status'));
            add_filter('the_title', array(&$this, 'filter_title'));
            add_action('user_register', array(&$this, 'user_signup'));
			add_action('delete_user', array(&$this, 'user_cleanup'));
           // add_action('widgets_init', array(&$this, 'register_widgets'));
            add_action('admin_head', array(&$this, 'admin_head_insert'));
			add_filter('manage_posts_columns', array(&$this, 'add_custom_columns'));
			add_filter('manage_pages_columns', array(&$this, 'add_custom_columns'));
			add_filter('manage_users_columns', array(&$this, 'add_custom_columns'));
			add_action('manage_posts_custom_column', array(&$this, 'do_posts_columns'), 5, 2);
			add_action('manage_pages_custom_column', array(&$this, 'do_pages_columns'), 5, 2);
			add_filter('manage_users_custom_column', array(&$this, 'do_users_columns'), 5, 3);
			add_action('save_post', array(&$this, 'on_save_inherit_privacy'), 5, 2);
			

            // Feed Key Hooks`
           /* if ($this->opt_feedkey_enabled == 'Yes') {
                add_action('pre_get_posts', 'feedkey');
                add_action('init', 'feedkey_init');
                add_action('show_user_profile', 'feedkey_display');
                add_action('edit_user_profile', 'feedkey_display');
                add_action('profile_update', 'feedkey_reset');

				// Enable private comment feeds
				add_filter('comment_feed_where', array(&$this, 'comments_query_mod'));

				// Rewrite RSS URLs for feedkey
				if ($this->opt_feedkey_url_rewrite_enabled == 'Yes') {
					add_filter('feed_link', array(&$this, 'filter_feed_url'));
					add_filter('post_comments_feed_link', array(&$this, 'filter_feed_url'));
					add_filter('category_feed_link', array(&$this, 'filter_feed_url'));
					add_filter('tag_feed_link', array(&$this, 'filter_feed_url'));
					add_filter('search_feed_link', array(&$this, 'filter_feed_url'));
					add_filter('author_feed_link', array(&$this, 'filter_feed_url'));
				}
            } */


            // Hook into the API to allow private post previews:
            if (in_array($this->opt_previews, array('Title', 'Excerpt', 'Above-the-fold'))) {
				add_filter('post_link', array(&$this, 'filter_permalink'));
				add_filter('page_link', array(&$this, 'filter_permalink'));
                add_filter('the_content', array(&$this, 'filter_content'));
                add_filter('comments_array', array(&$this, 'filter_comments_array'));
                add_filter('the_posts', array(&$this, 'filter_post_comment_status'));

                // If we're just showing titles, hide the excerpt as well.
                if ($this->opt_previews == 'Title') {
                    add_filter('get_the_excerpt', array(&$this, 'filter_excerpt'));
                }
            }


            if (!is_admin()) {
                // Hook into the API for displaying posts (not applicable in the admin area)
                //  - posts_where:   Modify the where clause to show private posts where appropriate
                //  - getarchives_where: Modify the where clause to show private posts where appropriate in archives
                //  - user_has_cap:  Allow the display of individual private posts where appropriate
                add_filter('posts_where', array(&$this, 'query_mod'));
                add_filter('getarchives_where', array(&$this, 'query_mod'));
                add_filter('user_has_cap', array(&$this, 'has_capability'), 10, 3);
            }

            // Handle Installation and Database Upgrades
            register_activation_hook( __FILE__, array(&$this, 'install'));

        }

       /**
        *  This function handles database installation and upgrades. With a little luck, the user should
        *  have to do absolutely nothing besides uploading the plugin and clicking 'Activate'. Upgrades
        *  Should be as simple as deactivating and activating again.
        */
        function install() {
           global $wpdb;

           // TODO: Check for compatibility with WP3.0 wrt variable wp-admin locations
           require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

           // Get database versioning information for comparison's sake.
           $current_version = $this->db_version;
           $installed_version = get_option('cpt_sentry_db_version');

           // This block of code tells dbDelta (a WordPress internal)
           // how to arrange the database. It also seamlessly handles upgrades
           $table_name = $this->group_table_name;
           if ($current_version != $installed_version) {
                // dbDelta is very finicky about the SQL statement it is passed. Edit with care.
                $sql = "CREATE TABLE " . $table_name . " (
                  id mediumint(9) NOT NULL AUTO_INCREMENT,
                  name varchar(64) NOT NULL,
                  member_list TEXT NOT NULL,
                  member_of TEXT NOT NULL,
                  UNIQUE KEY id (id)
                );";

                // This function does magic things.
                dbDelta($sql);
                update_option("cpt_sentry_db_version", $current_version);
            }

           /* if (!$sentryerror_feedkey_exists) {
                feedkey_setup_options();
            } */
        }

// Permissions-enforcing functions

       /**
        *  This filter gets passed a partial WHERE clause from WordPress and adds the checks
        *  necessary to display otherwise hidden private posts to users with the proper credentials.
        *  What little complexity exists in this plugin can be found here.
        *
        *  This section makes use of subqueries and therefore requires MySQL 4.1 or greater.
        */
		function comments_query_mod($where) { return($this->query_mod($where, true)); }
        function query_mod($where, $kill_previews=false) {
            // $user_ID is an integer and comes from WordPress
            global $wpdb, $user_ID;
            $old_where = $where;

            // This function doesn't even need to be run in the admin area, but in case it ever is, we want to
            // keep it from mucking things up.
            if (!is_admin()) {

                // We want them to be able to see published posts, don't we?
                $new_clause  = "{$wpdb->posts}.post_status = 'publish'";

                // If the user is logged in, show him all the posts he has the right to see
                if (is_user_logged_in()) {
					$new_clause .= " OR (\n\t{$wpdb->posts}.post_status = 'private' AND (\n\t\tFALSE\n";

                    // Get list of groups to which the user belongs
                    $users_groups = $wpdb->get_results("SELECT * FROM {$this->group_table_name} WHERE FIND_IN_SET('$user_ID', member_list)");

                    // Working backwards, from the deepest nesting outward, here's how the query works:
                    //  - PHP generates a FIND_IN_SET function for each group of which the user is a member.
                    //  - If any of those groups are found in the post's cpt_sentry_groups metadata, the post's ID
                    //    is included in the subquery result set.
                    //  - Private posts are displayed if their ID matches an ID in the subquery result set.
                    if (count($users_groups) > 0) {
                        $new_clause .= "\t\tOR ({$wpdb->posts}.ID IN (\n";
                        $new_clause .=  "\t\t\tSELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='{$this->post_metakey_groups}' && (";
                        $set_searches = array();
                        $already_seen_groups = array();
                        while ($g = array_shift($users_groups)) {
                            // Short-circuit circular references
                            $already_seen_groups[] = $g->id;

                            // Pull in sub-groups:
                            $sub_q = "SELECT id FROM {$this->group_table_name} WHERE FIND_IN_SET(id, '{$g->member_of}') AND !FIND_IN_SET(id, '" . implode(',', $already_seen_groups) .  "')";
                            $sub_groups = $wpdb->get_results($sub_q);

                            if (count($sub_groups) > 0) {
                                foreach($sub_groups as $sg) {
                                    array_push($users_groups, $sg);
                                }
                            }

                            $set_searches[] = "FIND_IN_SET('{$g->id}', meta_value)";
                        }
                        $new_clause .= implode(' || ', $set_searches);
                        $new_clause .= ")\n\t\t))\n";
                    }

                    // This query is very similar to the one above, but it handles posts for which the user is
                    // granted explicit, individual permission (the right-hand box on the post-edit screen)
                    if (is_user_logged_in()) {
                        $allow_all_users_feature = "|| FIND_IN_SET('all', meta_value)";
                    } else {
                        $allow_all_users_feature = '';
                    }
                    $new_clause .= "\t\tOR ({$wpdb->posts}.ID IN (\n";
                    $new_clause .= "\t\t\tSELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='{$this->post_metakey_users}' && (FIND_IN_SET('$user_ID', meta_value) $allow_all_users_feature )\n\t\t))\n";
					$new_clause .= "\t))\n\t";
                }

                // If we asked for some sort of preview, add the previewed posts to the query
                if ($this->opt_previews != 'None' && $kill_previews==false) {

                    if ($this->opt_preview_all_users_only == 'Yes') {
                        // We're only showing previews for posts that are tagged 'all_users'

                        $new_clause .= " OR ({$wpdb->posts}.ID IN (";
                        $new_clause .= "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='{$this->post_metakey_users}' && FIND_IN_SET('all', meta_value ) ))";
                    } else {
                        // Some sort of preview option is set. We'll return all of the private posts, and let the output filters
                        // worry about what data actually makes it to the user.

                        $new_clause .= " OR {$wpdb->posts}.post_status = 'private'";
                    }
                }


                // Snazzy regular expression shamelessly stolen from Filipe Fortes' 'Post Levels' plugin.
                $where = preg_replace("/({$wpdb->posts}\.)?post_status[\s]*=[\s]*[\'|\"]publish[\'|\"]/", $new_clause, $where);

                /*  Sample query:
                    SELECT * FROM `wp_posts` WHERE ID IN (
                       SELECT post_id FROM wp_postmeta WHERE meta_key='cpt_sentry_groups' && (
                          FIND_IN_SET('3', meta_value) ||
                          FIND_IN_SET('6', meta_value)
                       )
                    )
                */
            }
            // echo("Original\n\t$old_where\n\n");
			// echo("New clause\n\t$new_clause\n\n");
			// echo("New Where\n\t$where\n\n");
            // die();
            // This is a filter, so we need to pass the resulting WHERE clause back out to WP.
            return $where;
        }

       /**
        *  Enable users with the right credentials to see individual private posts.
        *
        *  Without this code, posts would show up on the main page, category listings, etc.
        *  but the permalinks would give a "not found" error. This is required because
        *  single post viewing has special logic and isn't covered by the WHERE filter.
        */
        function has_capability($all_capabilities, $capabilities, $args) {
            // $all_capabilities:  Capabilities the user currently has
            // $capabilities:      Primitive capabilities being tested / requested
            // $args[0]:           original meta capability requested
            // $args[1]:           ID of the user being tested
            // $args[2]:           ID of the post being viewed

            global $wpdb;

                        // Since this code only applies to viewing individual pages and posts,
                        // don't modify the privileges in other situations.
                        // http://wordpress.org/support/topic/197543
                        if (!is_single() && !is_page()) {
                            return $all_capabilities;
                        }

            if ($args[0] == 'unfiltered_html' && $args[1] == 0) {
                $all_capabilities['unfiltered_html'] == true;
                return $all_capabilities;
            }

            // Short circuit capabilities we're not worried about
            if (!in_array('read_private_posts', $capabilities) && !in_array('read_private_pages', $capabilities)) {
                return $all_capabilities;
            }

            // If previews are turned on, always allow the individual post page to be seen,
            // let the filters control the content.
            if ((in_array($this->opt_previews, array('Title', 'Excerpt', 'Above-the-fold'))) || (is_user_logged_in() && $this->user_is_allowed($args[1], $args[2]))) {
                $all_capabilities['read_private_posts'] = $all_capabilities['read_private_pages'] = true;
                return $all_capabilities;
            }


            // If we get here, the user doesn't appear to have permission, but we'll
            // let her leave with whatever she came in with.
            return $all_capabilities;
        }

// Widgets

       /**
        *  Disable sidebar widgets that do not play nice with WP CPT Sentry
        */
        function register_widgets() {
            if (function_exists('register_widget')) {
                // Clear out the current widgets
                // Register our own display functions, re-register the default control functions

                if ($this->replacement_widgets['recent_comments']['enabled'] == 'Yes') {
                	register_widget('CPT_Sentry_Widget_Recent_Comments');
                }

                if ($this->replacement_widgets['pages']['enabled'] == 'Yes') {
					register_widget('CPT_Sentry_Widget_Pages');
                }

                if (false) {
                    wp_unregister_sidebar_widget('tag_cloud');
                    $widget_ops = array('classname' => 'widget_tag_cloud', 'description' => __( "Your most used tags in cloud format") );
                    wp_register_sidebar_widget('tag_cloud', __('Tag Cloud'), array(&$this, 'widget_tag_cloud'), $widget_ops);
                    wp_register_widget_control('tag_cloud', __('Tag Cloud'), 'wp_widget_tag_cloud_control' );
                    if (!is_admin())
                        add_filter('get_tags', array(&$this, 'filter_tags'));
                }
            }
        }

        /**
        *  Sentry-aware Tags Cloud Widget
        *
        *  Needed to encapsulate the behavior of wp_tag_cloud, because that function only displays public tags.
        */
        function widget_tag_cloud($args)
        {
            extract($args);
            $options = get_option('widget_tag_cloud');
            $title = empty($options['title']) ? __('Tags') : apply_filters('widget_title', $options['title']);

            echo $before_widget;
            echo $before_title . $title . $after_title;

            // Here is the trick : we want to get all tags, even if they appears only in privates posts
            wp_tag_cloud(array('hide_empty' => false));

            echo $after_widget;
        }


// Admin Panels

       /**
        *  Add the submenu page to the WP Administration
        *
        */
        function add_admin_panels() {
        global $wpdb;
            // New Settings

           /* add_menu_page(
                __('WP CPT Sentry Options', $this->textdomain),
                __('WP CPT Sentry', $this->textdomain),
                'edit_plugins', __FILE__, array(&$this, 'main_admin_panel')
            ); */

        	add_menu_page(
        		__('WP Privacy Controls', $this->textdomain),
        		__('Privacy Controls', $this->textdomain),
        		'edit_plugins', __FILE__, array(&$this, 'group_admin_panel')
        		);
           /* add_submenu_page(__FILE__,
                __('WP CPT Sentry Options', $this->textdomain),
                __('General Options', $this->textdomain),
                'edit_plugins', __FILE__, array(&$this, 'main_admin_panel')
            );*/

            add_submenu_page(__FILE__,
                __('User Groups', $this->textdomain),
                __('User Groups', $this->textdomain),
                'edit_users', '__FILE__', array(&$this, 'group_admin_panel')
            );

          /*  add_submenu_page(__FILE__,
                __('WP CPT Sentry Post Previews', $this->textdomain),
                __('Preview Settings', $this->textdomain),
                'edit_plugins', 'cpt-sentry-previews', array(&$this, 'previews_admin_panel')
            ); */

           /* add_submenu_page(__FILE__,
                __('WP CPT Sentry Default Access', $this->textdomain),
                __('Default Access', $this->textdomain),
                'edit_plugins', 'cpt-sentry-defaults', array(&$this, 'defaults_admin_panel')
            ); */

           /* add_submenu_page(__FILE__,
                __('WP CPT Sentry Post Access Tool', $this->textdomain),
                __('Post Access', $this->textdomain),
                'edit_others_posts', 'cpt-sentry-posts', array(&$this, 'posts_admin_panel')
            ); */

              $cpt_postTypes = $wpdb->get_results("SELECT post_type FROM {$wpdb->posts} GROUP BY post_type");
            foreach($cpt_postTypes as $cpt) {
			$cpt_title = ucwords(str_replace("_",' ',$cpt->post_type));
                  if($cpt->post_type != 'nav_menu_item' && $cpt->post_type != 'revision')
                  {
                  add_submenu_page(__FILE__,
                __(''.$cpt_title.' Access Control', $this->textdomain),
                __(''.$cpt_title.' Access', $this->textdomain),
                'edit_others_posts', 'cpt-sentry-'.$cpt->post_type, array(&$this, 'posts_admin_panel')
            );

                  }
            }

           /* if ($this->opt_feedkey_enabled == 'Yes') {
                add_submenu_page(__FILE__,
               __('Sentry Enabled Feed Key Plugin', $this->textdomain),
               __('Private Feeds', $this->textdomain),
               'edit_users', 'cpt-sentry-feedkey', 'feedkey_options_page');
            } */


            if (function_exists('add_meta_box')) {
                add_meta_box('wpcptsentry', __('User &amp; Group Access', $this->textdomain), array(&$this, 'post_edit_form'), 'post', 'normal', 'high');
                add_meta_box('wpcptsentry', __('User &amp; Group Access', $this->textdomain), array(&$this, 'post_edit_form'), 'page', 'normal', 'high');
            } else {
                add_action('edit_form_advanced', array(&$this, 'post_edit_form'));
                add_action('simple_edit_form', array(&$this, 'post_edit_form'));
                add_action('edit_page_form', array(&$this, 'post_edit_form'));
            }

        }

       /**
        *  This function handles the back-end editing of user groups.
        *
        */
        function main_admin_panel() {
            global $wpdb;
            if ($_POST['mode'] == 'update') {

                $this->opt_post_title_prefix = stripslashes($_POST['title_prefix']);
                update_option('cpt_sentry_post_title_prefix', $this->opt_post_title_prefix);

                $this->opt_post_title_postfix = stripslashes($_POST['title_postfix']);
                update_option('cpt_sentry_post_title_postfix', $this->opt_post_title_postfix);

                /*if ($_POST['feedkey_enabled'] != 'Yes') {
                    $this->opt_feedkey_enabled = 'No';
                } else {
                    $this->opt_feedkey_enabled = 'Yes';
                }    update_option('cpt_sentry_feedkey_enabled',$this->opt_feedkey_enabled); */

				/*if ($_POST['feedkey_url_rewrite_enabled'] != 'Yes') {
                    $this->opt_feedkey_url_rewrite_enabled = 'No';
                } else {
                    $this->opt_feedkey_url_rewrite_enabled = 'Yes';
                }    update_option('cpt_sentry_feedkey_url_rewrite_enabled',$this->opt_feedkey_url_rewrite_enabled); */

                if ($_POST['post_user_list_enabled'] != 'Yes') {
                    $this->opt_post_user_list_enabled = 'No';
                } else {
                    $this->opt_post_user_list_enabled = 'Yes';
                }    update_option('cpt_sentry_post_user_list_enabled',$this->opt_post_user_list_enabled);

                foreach ($this->replacement_widgets as $k => $w) {
                    $widget_setting_id = $w['name'] . '_enabled';
                    if ($_POST[$widget_setting_id] == 'Yes') {
                        $this->replacement_widgets[$k]['enabled'] = 'Yes';
                        update_option($widget_setting_id, 'Yes');
                    } else {
                        $this->replacement_widgets[$k]['enabled'] = 'No';
                        update_option($widget_setting_id, 'No');
                    }
                }


                $this->status_list[] = __('Options updated.', $this->textdomain);
            }

            echo('<div class="wrap">');

            // Show the the status updates
            if (count($this->status_list) > 0) {
                echo("<div class=\"updated\"><ul><li>");
                echo(implode('</li><li>', $this->status_list));
                echo("</li></ul></div>");
            }

            // Show the errors
            if (count($this->error_list) > 0) {
                echo("<div class=\"error\"><b>" . __('ERROR', $this->textdomain) . ":</b><ul><li>");
                echo(implode('</li><li>', $this->error_list));
                echo("</li></ul></div>");
            }

            echo('<h2>' . __('WP CPT Sentry Options', $this->textdomain) . '</h2>');

            echo('<h3>' . __('General Options', $this->textdomain) . '</h3>');
            echo('<form method="post"><input type="hidden" name="mode" value="update" />');
            echo('<table class="form-table">');

            // Feed Key Enabled
           /* echo('<tr><th scope="row">' . __('Enable Feed Key?', $this->textdomain) . '</th>');
            if ($this->opt_feedkey_enabled == 'Yes') { $feedkey_enabled = 'CHECKED'; } else { $feedkey_enabled = ''; }
            echo('<td><input type="checkbox" name="feedkey_enabled" value="Yes" ' . $feedkey_enabled . '/> ' . __('Enable the embedded, Sentry-aware, version of Feed Key.', $this->textdomain));
            echo('</td></tr>');

			// RSS URL Rewriting Enabled
            echo('<tr><th scope="row">' . __('Enable RSS URL rewriting?', $this->textdomain) . '</th>');
            if ($this->opt_feedkey_url_rewrite_enabled == 'Yes') { $feedkey_url_rewrite_enabled = 'CHECKED'; } else { $feedkey_url_rewrite_enabled = ''; }
            echo('<td><input type="checkbox" name="feedkey_url_rewrite_enabled" value="Yes" ' . $feedkey_url_rewrite_enabled . '/> ' . __('Will re-write feed URLs with their Feed Key URLs where available. (Requires Feed Key to be enabled)', $this->textdomain));
            echo('</td></tr>'); */

            // User List Box
            echo('<tr><th scope="row">' . __('Enable User List?', $this->textdomain) . '</th>');
            if ($this->opt_post_user_list_enabled == 'Yes') { $post_user_list_enabled = 'CHECKED'; } else { $post_user_list_enabled = ''; }
            echo('<td><input type="checkbox" name="post_user_list_enabled" value="Yes" ' . $post_user_list_enabled . '/> ' . __('If you have many users, disabling this may speed your post edit screen load time.', $this->textdomain));
            echo('</td></tr>');


            echo('<tr><th scope="row">' . __('Private Titles', $this->textdomain) . ':</th><td><input type="text" style="text-align: right;" name="title_prefix" value="' . htmlspecialchars($this->opt_post_title_prefix, ENT_QUOTES, 'utf-8') . '" /> <b>' . __(' Here', $this->textdomain) . '</b> <input type="text" name="title_postfix" value="' . htmlspecialchars($this->opt_post_title_postfix, ENT_QUOTES, 'utf-8') . '" /><br /><b>' . __('Warning', $this->textdomain) . ':</b> ' . __('HTML is allowed and un-filtered. Be careful.', $this->textdomain) . '</td></tr>');

            echo('</table>');


            echo('<h3>' . __('Widget Replacement', $this->textdomain) . '</h3>');
			if (class_exists('WP_Widget')) {
	            echo('<table class="form-table">');
	            foreach ($this->replacement_widgets as $w) {
	                $widget_setting_id = $w['name'] . '_enabled';
	                if ($w['enabled'] == 'Yes') {
	                    $yes_value = 'CHECKED'; $no_value = '';
	                } else {
	                    $yes_value = ''; $no_value = 'CHECKED';
	                }
	                echo('<tr><th scope="row">' . __($w['title']) . '</th><td><input type="Radio" name="' . $widget_setting_id . '" value="Yes" ' . $yes_value . ' /> ' . __('Replace', $this->textdomain) . ' &nbsp; &nbsp; <input type="Radio" name="' . $widget_setting_id . '" value="No" ' . $no_value . ' /> ' . __('Disable', $this->textdomain) . '</td></tr>');
	            }
	            echo('<tr><th scope="row"></th><td><input type="submit" value="' . __('Update Options', $this->textdomain) . '" /></td></tr>');
	            echo('</table>');
			}

            echo('</form>');
            $this->paypal_html();
            echo('</div>'); // Wrapper DIV
        }

       /**
        *  This function handles the back-end editing of user groups.
        *
        */
        function group_admin_panel() {
            global $wpdb;
			$cpt_sentry_dir = get_bloginfo('wpurl') . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)) . '/';

            // A new group is being added.
            if ($_POST['mode'] == 'add') {
                // Sanitize input
                    $new_group_name = stripslashes($_POST['new_group_name']);
                    $new_group_name = $wpdb->escape(trim($new_group_name));

                // Does a category with that name already exist?
                $already_exists = $wpdb->get_var("SELECT count(*) FROM {$this->group_table_name} WHERE name='$new_group_name'");
                if (intval($already_exists) !=  0) {
                    $this->error_list[] = sprintf(__("A group named <i>'%s'</i> already exists.", $this->textdomain), $new_group_name);
                } else if ($new_group_name == '') {
                    $this->error_list[] = __('Group names must not be blank.', $this->textdomain);
                } else {

                    // Everything looks good... add the group
                    $wpdb->query("INSERT INTO {$this->group_table_name} (name, member_list, member_of) VALUES ('$new_group_name', '', '')");
                    if ($wpdb->last_error != '') {
                        $this->error_list[] = $wpdb->last_error;
                    } else {
                        $this->status_list[] = sprintf(__("Group '%s' added.", $this->textdomain), $new_group_name);
                    }
                }
            } // End Group Addition

            if ($_POST['mode'] == 'delconfirmed') {
                $group_list = $_POST['delete_groups'];
                $group_arr = explode(',', $group_list);

                // Iterate through groups to be deleted and remove the posts from those groups -- this helps keep the DB clean
                foreach ($group_arr as $g) {
                    // Pull the posts with that group out of the meta table.
                    $affected_posts = $wpdb->get_results("SELECT * FROM {$wpdb->postmeta} WHERE meta_key='{$this->post_metakey_groups}' && FIND_IN_SET('$g', meta_value)");

                    // Loop through each affected post
                    foreach($affected_posts as $post) {
                        $cur_groups = explode(',', $post->meta_value);
                        $new_groups = array();

                        // Loop through each group assigned to that post
                        foreach ($cur_groups as $cg) {
                            // If the group isn't going to be deleted, add it to the new groups list
                            // This deletes ALL of the to-be-deleted groups, not just the one we're currently iterating through
                            // This saves us some database updates.
                            if (!in_array($cg, $group_arr)) { $new_groups[] = $cg; }
                        }

                        update_post_meta($post->post_id, $this->post_metakey_groups, implode(',', $new_groups));
                    }
                }

                // Delete the groups
                $wpdb->query("DELETE FROM {$this->group_table_name} WHERE FIND_IN_SET(id, '$group_list')");
                $this->status_list[] = sprintf(__('%s groups deleted.', $this->textdomain), count($group_arr));
            }

            // An already existing group is being updated
            if ($_POST['mode'] == 'update') {
                // Group ID
                $gid = intval($_GET['id']);

                // Sanitize Input
                    $group_name = stripslashes($_POST['group_name']);
                    $group_name = $wpdb->escape(trim($group_name));

                $group_members = $this->sanitize_select_data($_POST['group_members']);
                $member_of = $this->sanitize_select_data($_POST['member_of']);

                // Update the group
                $wpdb->query("UPDATE {$this->group_table_name} SET name='$group_name', member_list='$group_members', member_of='$member_of' WHERE id=$gid");
                $this->status_list[] = sprintf(__("Group '%s' updated.", $this->textdomain), $group_name);
            } // End Group Update

            // ************************************** //

            // Show the the status updates
            echo('<div class="wrap">');
            if (count($this->status_list) > 0) {
                echo("<div class=\"updated\"><ul><li>");
                echo(implode('</li><li>', $this->status_list));
                echo("</li></ul></div>");
            }

            // Show the errors
            if (count($this->error_list) > 0) {
                echo("<div class=\"error\"><b>" . __('ERROR', $this->textdomain) . ":</b><ul><li>");
                echo(implode('</li><li>', $this->error_list));
                echo("</li></ul></div>");
            }

            echo('<h2>' . __('User Groups', $this->textdomain) . '</h2>');


            // Group Delete Confirmation Page
            if ($_POST['mode'] == 'delete') {
                echo('<h3>Delete User Groups:</h3>');
                if (is_array($_POST['delete_groups']) && count($_POST['delete_groups']) > 0) {
                    _e("Do you really wish to delete the following user groups?", $this->textdomain);
                    echo("<br /><ul>");
                    $group_list = implode(',', $_POST['delete_groups']);
                    $groups = $wpdb->get_results("SELECT * FROM {$this->group_table_name} WHERE FIND_IN_SET(id, '$group_list')");
                    foreach($groups as $g) {
                        $mc = __("No members", $this->textdomain);
                        if (!empty($g->member_list)) {
                            $mc = sprintf(__('%s members', $this->textdomain), count(explode(',', $g->member_list)));
                        }
                        echo("<li>{$g->name} ($mc)</li>\n");
                    }
                    echo("</ul>");
                    echo('<form method="post"><input type="hidden" name="delete_groups" value="' . $group_list . '" /><input type="hidden" name="mode" value="delconfirmed" /><input type="submit" class="button-secondary delete" value="' . __('Yes, delete them', $this->textdomain) . '" /></form>');
                } else {
                    _e("You didn't select any groups to delete.", $this->textdomain);
                }

            // Group edit form code
            } else if ($_GET['mode'] == 'edit') {
                $gid = intval($_GET['id']);
                $group = $wpdb->get_row("SELECT * FROM {$this->group_table_name} WHERE id=$gid");

                if (empty($group->member_list)) {
                    $group_members = array();
                } else {
                    $group_members = explode(',', $group->member_list);
                }

                if (empty($group->member_of)) {
                    $member_of = array();
                } else {
                    $member_of = explode(',', $group->member_of);
                }


                echo("<h3>Edit Group '{$group->name}'</h3>");
                echo('<form method="post"><input type="hidden" name="mode" value="update" />');
                echo('<table class="form-table">');
                echo('<tr><th scope="row">' . __('Group Name', $this->textdomain) . ':</th><td><input type="text" name="group_name" size="40" value="' . $group->name . '" /></td></tr>');
                echo('<tr><th scope="row">' . __('Members', $this->textdomain) . ':</th><td>');

                    // Spit out a list of users
                    $user_list = get_users_of_blog();

                    if (count($user_list) > 0) {

                        echo('<SELECT name="group_members[]" style="height: 200px; width: 280px;" MULTIPLE>');
                        foreach ($user_list as $u) {
                            if (in_array($u->user_id, $group_members)) {
                                $is_member = 'SELECTED';
                            } else {
                                $is_member = '';
                            }
                            echo("<option value=\"{$u->user_id}\" $is_member>{$u->user_id}. {$u->display_name} &lt;$u->user_email&gt;</option>");
                        }
                        echo("</select>");
                    } else {
                        _e('No users found.', $this->textdomain);
                    }
                echo('<tr><th scope="row">' . __('This group is a member of:', $this->textdomain) . '</th><td>');

                    // Spit out a list of users
                    $group_list = $wpdb->get_results("SELECT * FROM {$this->group_table_name} ORDER BY ID");

                    if (count($group_list) > 0) {
                        echo('<SELECT name="member_of[]" style="height: 200px; width: 280px" MULTIPLE>');
                        foreach ($group_list as $g) {
                            if (in_array($g->id, $member_of)) {
                                $is_member = 'SELECTED';
                            } else {
                                $is_member = '';
                            }
                            echo("<option value=\"{$g->id}\" $is_member>{$g->id}. {$g->name}</option>");
                        }
                        echo("</select>");
                    } else {
                        _e('No groups found.', $this->textdomain);
                    }

                echo('<br /><input type="submit" value="' . __('Update Group', $this->textdomain) . '" /></td></tr>');
                echo('</table></form>');


            // Group list display code
            } else {
                $group_list = $wpdb->get_results("SELECT * FROM {$this->group_table_name} ORDER BY name");

                echo('<h3>' . __('Group List', $this->textdomain) . '</h3>');
                    if (count($group_list) > 0) {
                        echo('<form method="post"><input type="hidden" name="mode" value="delete" /><input type="submit" class="button-secondary delete" value="' . __('Delete Checked Groups', $this->textdomain) . '" />');
                        echo('<table class="widefat">');
                        echo('<tr class="thead"><th width="20">&nbsp;</th><th>' . __('Name') . '</th><th>' . __('Users') . '</th><th>' . __('Posts') . '</th><th>' . __('Memberships', $this->textdomain) . '</th></tr>');

                        foreach ($group_list as $g) { $gc++;

                            // How many users are in each group?
                            if (!empty($g->member_list)) {
                                $u_count = count(explode(',', $g->member_list));
                            } else {
                                $u_count = '0';
                            }

                            // How many groups is it a member of:
                            if (!empty($g->member_of)) {
                                $g_count = count(explode(',', $g->member_of));
                            } else {
                                $g_count = 0;
                            }

                            // How many posts are viewable by each group?
                            $p_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key='{$this->post_metakey_groups}' && FIND_IN_SET('{$g->id}', meta_value)");

                            // Edit Link:
                            $url = "?page={$_GET[page]}&mode=edit&id={$g->id}";

                            // Pretty striping
                            if ($gc % 2 == 0) { $ALT = ''; } else { $ALT = 'class="alternate"'; }

                            // Finally.
                            echo("<tr $ALT><td><input type=\"checkbox\" name=\"delete_groups[]\" value=\"$g->id\" /></td><td><a href=\"$url\">{$g->name}</a></td><td>$u_count [<a href=\"{$cpt_sentry_dir}csv.php?id={$g->id}\">csv</a>]</td><td>$p_count</td><td>$g_count</td></tr>");
                        }
                        echo("</table></form>");
                    } else {
                        _e('No groups found.', $this->textdomain);
                    }
                echo('<form method="post"><input type="hidden" name="mode" value="add" />');
                echo('<table class="form-table">');
                echo('<tr><th scope="row">' . __('New Group', $this->textdomain) . ':</th><td><input type="text" name="new_group_name" size="40" /> <input type="submit" value="' . __('Add Group', $this->textdomain) . '" /></td></tr>');
                echo('</table></form>');
            }
            $this->paypal_html();
            echo('</div>');
        }

       /**
        *  This function handles the previews option screen.
        *
        */
        function previews_admin_panel() {
            global $wpdb;

            if ($_POST['mode'] == 'update') {
                $new_preview = $wpdb->escape($_POST['preview']);
                if (in_array($new_preview, array_keys($this->preview_types))) {
                    update_option('cpt_sentry_preview_type', $new_preview);
                    $this->opt_previews = $new_preview;
                } else {
                    $this->error_list[] = __('Invalid preview-type specified.', $this->textdomain);
                }

                if ($_POST['preview_all_users_only'] != 'Yes') {
                    $this->opt_preview_all_users_only = 'No';
                } else {
                    $this->opt_preview_all_users_only = 'Yes';
                }    update_option('cpt_sentry_preview_all_users_only',$this->opt_preview_all_users_only);

                $this->opt_denied_message = stripslashes(trim($_POST['deny_message']));
                update_option('cpt_sentry_denied_message', $this->opt_denied_message);

                $this->opt_preview_permalink = stripslashes(trim($_POST['preview_permalink']));
                update_option('cpt_sentry_preview_permalink', $this->opt_preview_permalink);

                $this->status_list[] = __('Options updated.', $this->textdomain);
            }

            echo('<div class="wrap">');

            // Show the the status updates
            if (count($this->status_list) > 0) {
                echo("<div class=\"updated\"><ul><li>");
                echo(implode('</li><li>', $this->status_list));
                echo("</li></ul></div>");
            }

            // Show the errors
            if (count($this->error_list) > 0) {
                echo("<div class=\"error\"><b>" . __('ERROR', $this->textdomain) . ":</b><ul><li>");
                echo(implode('</li><li>', $this->error_list));
                echo("</li></ul></div>");
            }

            echo('<h2>' . __('WP CPT Sentry Options', $this->textdomain) . '</h2>');
            echo('<form method="post"><input type="hidden" name="mode" value="update" />');
            echo('<table class="form-table">');
            echo('<tr><th scope="row">' . __('Show Previews?', $this->textdomain) . '</th>');
            echo('<td><select name="preview">');
            foreach ($this->preview_types as $t => $lt) {
                if ($t == $this->opt_previews) { $selected = 'SELECTED'; } else { $selected = ''; }
                echo("<option value=\"$t\" $selected>$lt</option>");
            }
            echo('</select><br />');

            if ($this->opt_preview_all_users_only == 'Yes') { $all_users_only = 'CHECKED'; } else { $all_users_only = ''; }
            echo('<input type="checkbox" name="preview_all_users_only" value="Yes" ' . $all_users_only . '/> ' . __('Show preview only for content available to all registered users.', $this->textdomain));
            echo('<br /><br /><b>' . __('Warning', $this->textdomain) . ':</b> ' . __('These options determine what unauthorized users can see. "Unauthorized users" include web spiders, users who are not logged in, and logged in users who do not have permission to see the post in question. Use with caution.', $this->textdomain) . '</td></tr>');

            echo('<tr><th scope="row">' . __('Preview Link', $this->textdomain) . ':</th><td><input type="text" name="preview_permalink" size="40" value="' . $this->opt_preview_permalink . '" /> <br /><b>' . __('Note', $this->textdomain) . ':</b> ' . __('This URL will be used for permalinks the user does not have access to view, avoiding sending them to a 404 Page. Leave blank to disable.', $this->textdomain) . '</td></tr>');

            echo('<tr><th scope="row">' . __('Denied Message', $this->textdomain) . ':</th><td><textarea name="deny_message" style="width: 350px; height: 150px;">' . htmlentities($this->opt_denied_message) . '</textarea>');
            echo('<br /><input type="submit" value="' . __('Update Options', $this->textdomain) . '" /></td></tr>');
            echo('</table></form>');
            $this->paypal_html();
            echo('</div>'); // Wrapper DIV
        }


       /**
        *  This function handles the defaults option screen.
        *
        */
        function defaults_admin_panel() {
            global $wpdb;
 /*TODO: create a default groups / post interface for all custom post types */

            if ($_POST['mode'] == 'update') {

                $this->opt_default_post_groups = $this->sanitize_select_data($_POST['default_post_groups']);
                update_option('cpt_sentry_default_post_groups', $this->opt_default_post_groups);

                $this->opt_default_post_users = $this->sanitize_select_data($_POST['default_post_users']);
                update_option('cpt_sentry_default_post_users', $this->opt_default_post_users);

                $this->opt_default_user_groups = $this->sanitize_select_data($_POST['default_user_groups']);
                update_option('cpt_sentry_default_user_groups', $this->opt_default_user_groups);

                $this->status_list[] = __('Options updated.', $this->textdomain);
            }

            echo('<div class="wrap">');

            // Show the the status updates
            if (count($this->status_list) > 0) {
                echo("<div class=\"updated\"><ul><li>");
                echo(implode('</li><li>', $this->status_list));
                echo("</li></ul></div>");
            }

            // Show the errors
            if (count($this->error_list) > 0) {
                echo("<div class=\"error\"><b>" . __('ERROR', $this->textdomain). ":</b><ul><li>");
                echo(implode('</li><li>', $this->error_list));
                echo("</li></ul></div>");
            }

            echo('<h2>WP CPT Sentry Options</h2>');
            echo('<form method="post"><input type="hidden" name="mode" value="update" />');
            echo('<table class="form-table">');
            echo('<tr><th scope="row">' . __('Default Posting Groups', $this->textdomain) . ':</th><td>');

            // Group Access Select Box
            $groups_selected = explode(',', $this->opt_default_post_groups);
            $groups_list = $wpdb->get_results("SELECT * FROM {$this->group_table_name} ORDER BY name");
            echo('<select name="default_post_groups[]" style="height: 150px; width: 280px;" MULTIPLE>');
            foreach ($groups_list as $g) {
                if (in_array($g->id, $groups_selected)) {
                    $is_allowed = 'SELECTED';
                } else {
                    $is_allowed = '';
                }
                echo("<option value=\"{$g->id}\" $is_allowed>{$g->name}</option>");
            }

            echo('</select></td></tr>');

            echo('<tr><th scope="row">' . __('Default Posting Users', $this->textdomain) . ':</th><td>');

            // User Access Select Box
            $users_selected = explode(',', $this->opt_default_post_users);
            $users_list = get_users_of_blog();
            echo('<select name="default_post_users[]" style="height: 150px; width: 280px;" MULTIPLE>');

            // All Registered Users Option
            if (in_array('all', $users_selected)) {
                $is_allowed = 'SELECTED';
            } else {
                $is_allowed = '';
            }
            echo("<option style=\"font-weight: bold;\" value=\"all\" $is_allowed>" . __('Allow All Registered Users', $this->textdomain) . "</option>");

            // Users list
            foreach ($users_list as $u) {
                if (in_array($u->user_id, $users_selected)) {
                    $is_allowed = 'SELECTED';
                } else {
                    $is_allowed = '';
                }

                echo("<option value=\"$u->user_id\" $is_allowed>$u->display_name  &lt;$u->user_email&gt;</option>");
            }

            echo('</select></td></tr>');
            echo('<tr><th scope="row">' . __('Default Groups for New Users', $this->textdomain) . ':</th><td>');
            // Group Access Select Box
            $groups_selected = explode(',', $this->opt_default_user_groups);
            echo('<select name="default_user_groups[]" style="height: 150px; width: 280px;" MULTIPLE>');
            foreach ($groups_list as $g) {
                if (in_array($g->id, $groups_selected)) {
                    $is_allowed = 'SELECTED';
                } else {
                    $is_allowed = '';
                }
                echo("<option value=\"{$g->id}\" $is_allowed>{$g->name}</option>");
            }

            echo('</select>');


            echo('<br /><input type="submit" value="' . __('Update Options', $this->textdomain) . '" /></td></tr>');
            echo('</table></form>');
            $this->paypal_html();
            echo('</div>'); // Wrapper DIV
        }


function on_save_inherit_privacy( $post_id ) {
	////var_dump($_POST);
	
	//exit;
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
 // //var_dump(DOING_AUTOSAVE);
 
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  // Check permissions
 
    if ( !current_user_can( 'edit_post', $post_id ) )
    return;
    
						  $parent = get_post_ancestors( $post_id );
						  //var_dump($parent);
						  if(count($parent) > 0)
						  {
    					  $group_list = get_post_meta($parent[0], $this->post_metakey_groups, true);
    					  $users_list = get_post_meta($parent[0], $this->post_metakey_users, true);
    					  $sentry_prvt = get_post_meta($parent[0], 'sentry_prvt', true);
    					  	if($sentry_prvt)
    					  	{
    					  		delete_post_meta($post_id, $this->post_metakey_groups);
                          		delete_post_meta($post_id, $this->post_metakey_users);
                          		delete_post_meta($post_id, 'sentry_prvt');
    					  		add_post_meta($post_id, $this->post_metakey_groups, $group_list);
                          		add_post_meta($post_id, $this->post_metakey_users, $users_list);
                          		add_post_meta($post_id, 'sentry_prvt', true);
                          	}
    
 						}

	
}
       /**
        *  This function handles mass post group assignment.
        *
        */
        function posts_admin_panel($cpt) {
            global $wpdb, $wp_locale;


            if ($_POST['mode'] == 'update') {
                $target_posts = $_POST['update_posts'];
                $group_list = $this->sanitize_select_data($_POST['sentry_groups_allowed']);
                $users_list = $this->sanitize_select_data($_POST['sentry_users_allowed']);
                $added = $replaced = 0;


                if (count($target_posts) <= 0) {
                    $this->error_list[] = __('No posts selected.', $this->textdomain);
                } else if ($_POST['method'] != 'replace' && $_POST['method'] != 'add') {
                    $this->error_list[] = __('Improper method specified.', $this->textdomain);
                } else {
                    foreach($target_posts as $p) {
                        // Current settings
                        $current_groups = get_post_meta($p, $this->post_metakey_groups, true);
                        $current_users = get_post_meta($p, $this->post_metakey_users, true);

                        // Clear all user/group assignments
                        delete_post_meta($p, $this->post_metakey_groups);
                        delete_post_meta($p, $this->post_metakey_users);
                        delete_post_meta($p, 'sentry_prvt');

                        if ($_POST['method'] == 'replace') {

                          add_post_meta($p, $this->post_metakey_groups, $group_list);
                          add_post_meta($p, $this->post_metakey_users, $users_list);
                          if(strlen($group_list) > 0 || strlen($users_list))
                          {
                          	add_post_meta($p, 'sentry_prvt', true);
                          }


                             $cpt_postTypes = $wpdb->get_results("SELECT post_type FROM {$wpdb->posts} GROUP BY post_type");
                             $arrpost[] = array();
				            foreach($cpt_postTypes as $cpt) {
				             $arrpost[] =$cpt->post_type;
				            }
                            	$args = array('post_parent' => (int)$p, 'post_type' => $arrpost);
                            	$children = new WP_Query($args);
								//$children = get_children( $args );

								while($children->have_posts()) : $children->the_post();

									 $cid = get_the_ID();

									 delete_post_meta($cid, $this->post_metakey_groups);
                        			 delete_post_meta($cid, $this->post_metakey_users);
                        			 delete_post_meta($cid, 'sentry_prvt');

									add_post_meta($cid, $this->post_metakey_groups, $group_list);
                            		add_post_meta($cid, $this->post_metakey_users, $users_list);
                            		add_post_meta($cid, 'sentry_prvt', true);
                            		 //$wpdb->query("UPDATE {$wpdb->posts} SET post_status='private' WHERE ID='$child->ID' LIMIT 1");
                            		  $replaced++;
								endwhile;



                            if (!empty($group_list) || !empty($users_list)) {
                                //$wpdb->query("UPDATE {$wpdb->posts} SET post_status='private' WHERE ID='$p' LIMIT 1");
                            }

                            $replaced++;
                        } else {

                            // Merge submitted group list with existing groups.
                            if (!empty($current_groups)) {
                                $new_groups = explode(',', $current_groups);
                            } else {
                                $new_groups = array();
                            }
                            foreach (explode(',', $group_list) as $new_g) {
                                if (!empty($new_g) && !in_array($new_g, $new_groups)) { $new_groups[] = $new_g; }
                            }
                            add_post_meta($p, $this->post_metakey_groups, implode(',', $new_groups));


                            // Merge submitted user list with existing users.
                            if (!empty($current_users)) {
                                $new_users = explode(',', $current_users);
                            } else {
                                $new_users = array();
                            }

                            foreach (explode(',', $users_list) as $new_u) {
                                if (!empty($new_u) && !in_array($new_u, $new_users)) { $new_users[] = $new_u; }
                            }
                            add_post_meta($p, $this->post_metakey_users, implode(',', $new_users));

							add_post_meta($p, 'sentry_prvt', true);

							 	$cpt_postTypes = $wpdb->get_results("SELECT post_type FROM {$wpdb->posts} GROUP BY post_type");
                             	$arrpost[] = array();
				            
				            foreach($cpt_postTypes as $cpt) {
				            	 $arrpost[] =$cpt->post_type;
				            }
				            
							$args = array('post_parent' => (int)$p, 'post_type' => $arrpost);
                            $children = new WP_Query($args);
								//$children = get_children( $args );

								while($children->have_posts()) : $children->the_post();

									 $cid = get_the_ID();

									 delete_post_meta($cid, $this->post_metakey_groups);
                        			 delete_post_meta($cid, $this->post_metakey_users);
                        			 delete_post_meta($cid, 'sentry_prvt');

									add_post_meta($cid, $this->post_metakey_groups, implode(',', $new_groups));
									add_post_meta($cid, $this->post_metakey_users, implode(',', $new_users));
									add_post_meta($cid, 'sentry_prvt', true);
                            		 //$wpdb->query("UPDATE {$wpdb->posts} SET post_status='private' WHERE ID='$child->ID' LIMIT 1");
                            		  $added++;
								endwhile;

                            if (count($new_users) > 0 || count($new_groups) > 0) {
                                //$wpdb->query("UPDATE {$wpdb->posts} SET post_status='private' WHERE ID='$p' LIMIT 1");
                            }


                            $added++;
                        }
                    }

                    if ($replaced > 0) {
                        $this->status_list[] = sprintf(__('Permissions replaced for %s posts.', $this->textdomain), $replaced);
                    }

                    if ($added > 0) {
                        $this->status_list[] = sprintf(__('Permissions merged for %s posts.', $this->textdomain), $added);
                    }
                }
            }

            $pag = $_GET["page"];
            $pag = str_replace('cpt-sentry-','',$pag);
			$pp = ucwords(str_replace("_",' ',$pag));
              // var_dump($pg);
            echo('<div class="wrap">');

            // Show the the status updates
            if (count($this->status_list) > 0) {
                echo("<div class=\"updated fade\"><ul><li>");
                echo(implode('</li><li>', $this->status_list));
                echo("</li></ul></div>");
            }

            // Show the errors
            if (count($this->error_list) > 0) {
                echo("<div class=\"error fade\"><b>" . __('ERROR', $this->textdomain) . ":</b><ul><li>");
                echo(implode('</li><li>', $this->error_list));
                echo("</li></ul></div>");
            }

            echo('<h2>'.$pp . __(' Access Settings', $this->textdomain) . '</h2>');
                $test_parent_field = $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE post_type = '{$pag}' LIMIT 1");
                $cols = $wpdb->get_col_info('name');
                foreach($cols as $c) {
                    $fieldlist[] = $c;
                }
                 $no_children = '';
                if (in_array('post_parent', $fieldlist)) {
                   // $no_children = ' AND post_parent=\'0\' ';
                } else {
                    $no_children = '';
                }

                $pp_public = $pp_private = $pp_both = '';
                if ($_GET['post_status'] == 'private') {
               // SELECT s1 FROM t1 WHERE s1 IN    (SELECT s1 FROM t2);
                    $public_private = " AND ID IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'sentry_prvt' AND meta_value = 1) ";
                    $pp_private = ' SELECTED ';
                } else if ($_GET['post_status'] == 'public') {
                    $public_private = " AND ID NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'sentry_prvt' AND meta_value = 1) ";
                    $pp_public = ' SELECTED ';
                } else {
                    $public_private = ' ';
                    $pp_both = ' SELECTED ';
                }

                if (isset($_GET['m']) && $_GET['m'] != '0') {
                    $Y = substr($_GET['m'], 0, 4);
                    $M = intval(substr($_GET['m'], -2, 2));

                    $month = " AND YEAR(post_date)='$Y' AND MONTH(post_date)='$M' ";
                }

                $where = "(post_type='{$pag}') AND post_status != 'auto-draft' AND post_status != 'trash' $month $no_children $public_private";

                $posts_per_page = 25;

                $num_posts = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE $where");
                $num_pages = ceil($num_posts / $posts_per_page);

                $pg = intval($_GET['pg']);
                if ($pg < 1) { $pg = 1; }
                if ($pg > $num_pages) { $pg = $num_pages; }

                $start = ($pg-1)*$posts_per_page;

                $post_list = $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE $where ORDER BY post_date DESC LIMIT $start, $posts_per_page");
               // var_dump("SELECT * FROM {$wpdb->posts} WHERE $where ORDER BY post_date DESC LIMIT $start, $posts_per_page");
                // echo('<h3>' . __('Post List', $this->textdomain) . '</h3>');
                    echo('<div class="tablenav">');

                        $page_links = paginate_links( array(
                            'base' => add_query_arg( 'pg', '%#%' ),
                            'format' => '',
                            'total' => $num_pages,
                            'current' => $pg
                        ));

                        if ( $page_links ) {
                            echo "<div class='tablenav-pages'>$page_links</div>";
                        }

                    echo('<div class="alignleft"><form><input type="hidden" name="page" value="sentry-posts" />');


                    echo('<select name="post_status">');
                    echo("<option value=\"\" $pp_both>" . __('All '.$pp.'s', $this->textdomain) . '</option>');
                    echo("<option value=\"private\" $pp_private>" . __('Private') . '</option>');
                    echo("<option value=\"public\" $pp_public>" . __('Public') . '</option>');
                    echo('</select>&nbsp;');

                    $arc_query = "SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type = '{$pag}' ORDER BY post_date DESC";

                    $arc_result = $wpdb->get_results( $arc_query );

                    $month_count = count($arc_result);

                    if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) { ?>
                        <select name='m'>
                        <option<?php selected( @$_GET['m'], 0 ); ?> value='0'><?php _e('Show all dates'); ?></option>
                        <?php
                        foreach ($arc_result as $arc_row) {
                            if ( $arc_row->yyear == 0 )
                                continue;
                            $arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

                            if ( $arc_row->yyear . $arc_row->mmonth == $_GET['m'] )
                                $default = ' selected="selected"';
                            else
                                $default = '';

                            echo "<option$default value='$arc_row->yyear$arc_row->mmonth'>";
                            echo $wp_locale->get_month($arc_row->mmonth) . " $arc_row->yyear";
                            echo "</option>\n";
                        }
                        echo('</select>&nbsp;');
                    }
                    echo('<input id="post-query-submit" class="button-secondary" type="submit" value="' . __('Filter') . '"/>');
                    echo('</form></div>');

                    echo('<br class="clear" /></div><br />');
                    if (count($post_list) > 0) {
                        echo('<form method="post" ><input type="hidden" name="mode" value="update" />');
                        echo('<table class="widefat">');
                        echo('<tr class="thead"><th width="20">&nbsp;</th><th>' . __('Date') . '</th><th>' . __('Title') . '</th><th>' . __('Groups', $this->textdomain) . '</th><th>' . __('Users') . '</th></tr>');

                        foreach ($post_list as $p) { $pc++;
                            // Pretty striping
                            if ($pc % 2 == 0) { $ALT = ''; } else { $ALT = 'class="alternate"'; }


                            // Status
                            if ($p->post_status == 'private') {
                                $post_status = __('Private');
                            } else {
                                $post_status = __('Public');
                            }

                            // Group Count
                            $current_groups = get_post_meta($p->ID, $this->post_metakey_groups, true);
                            $current_users = get_post_meta($p->ID, $this->post_metakey_users, true);

                            $g_count = $u_count = '0';
                            if (!empty($current_groups)) { $g_count = count(explode(',', $current_groups)); }
                            if (!empty($current_users))  { $u_count = count(explode(',', $current_users)); }
                            if (in_array('all', explode(',', $current_users))) { $u_count = 'All'; }

                            // Finally.
                            echo("<tr $ALT><td><input type=\"checkbox\" name=\"update_posts[]\" value=\"$p->ID\" /></td>
                                    <td>" . mysql2date(__('Y/m/d'), $p->post_date) . "</td>
                                    <td><a href=\"" . get_permalink($p->ID) . "\">{$p->post_title}</a></td>
                                    <td>$g_count</td><td>$u_count</td></tr>");
                        }
                        echo("</table>");
                    } else {
                        _e('No posts found.', $this->textdomain);
                    }
                echo('<h4>' . __('Allow access to', $this->textdomain) . ':</h4>');
				echo('<input type="hidden" name="sentry_permission_fields" value ="present"  />');
                echo('<table cellpadding="3" cellspacing="0" border="0" width="100%"><tr>');
                echo('<td>Group and User access settings will be inherited by all children. <br />eg. setting permissions on a forum, will change the access settings on all topics and replies within that forum<br />However you can change access on individual children without effecting the parent.</td></tr>');

                    // Group Access Select Box
                    $groups_listc = $wpdb->get_results("SELECT * FROM {$this->group_table_name} ORDER BY name");
                    echo('<td width="50%" valign="top" align="center">' . __('These groups', $this->textdomain) . ':<select name="sentry_groups_allowed[]" title="Select Group" style="height: 150px; width: 280px;" multiple="multiple">');
                    foreach ($groups_listc as $gc) {
                        echo("<option value=\"{$gc->id}\">{$gc->name}</option>");
                    }
                    echo('</select></td>');


                    // User Access Select Box
                    $users_listc = get_users_of_blog();

                    echo('<td width="50%" valign="top" align="center">' . __('And these users', $this->textdomain) . ':<select name="sentry_users_allowed[]" title="Public" style="height: 150px; width: 280px;" multiple="multiple">');

                    // All Registered Users Option
                    echo("<option style=\"font-weight: bold;\" value=\"all\">" . __('Allow All Registered Users', $this->textdomain) . "</option>");

                    // Users list
                    foreach ($users_listc as $uc) {
                        echo("<option value=\"$uc->user_id\">$uc->display_name  &lt;$uc->user_email&gt;</option>");
                    }
                    echo('</select></td>');
                    echo('</tr></table><div align="center">');
                   // echo('<b>' . __('Note', $this->textdomain) . ':</b> ' . __('Selecting any users or groups here forces the post to be private, but clearing them will not make a private post public again.', $this->textdomain) . '<br /><br />');
                    echo('<button type="submit" class="button-secondary" name="method" value="replace">' . __('Replace Permissions', $this->textdomain) . '</button>&nbsp;&nbsp;');
                    echo('<button type="submit" class="button-secondary" name="method" value="add">' . __('Add Permissions', $this->textdomain) . '</button>');
                echo('</div></form>');

            $this->paypal_html();
            echo('</div>'); // Wrapper DIV


        }

       /**
        *  This function places the box at the bottom of the post edit screen
        *
        */
        function post_edit_form() {
            // $post is a post object exposed by WordPress
            global $wpdb, $post;

            // Defaults
            $groups_allowed = array();
            $users_allowed = array();

            // $post->ID == 0 means that this is a new post, if so, it clearly does not yet blong to any groups.
            if ($post->ID != 0) {

                // Fetch the list of groups that have access to the post
                $groups_allowed = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id={$post->ID} && meta_key='{$this->post_metakey_groups}'");
                if (!empty($groups_allowed)) {
                    $groups_allowed = explode(',', $groups_allowed);
                } else {
                    $groups_allowed = array();
                }

                // Fetch the list of users that have access to the post as individuals
                $users_allowed = $wpdb->get_var("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id={$post->ID} && meta_key='{$this->post_metakey_users}'");
                if (!empty($users_allowed)) {
                    $users_allowed = explode(',', $users_allowed);
                } else {
                    $users_allowed = array();
                }

            } else {
                $groups_allowed = explode(',', $this->opt_default_post_groups);
                $users_allowed = explode(',', $this->opt_default_post_users);
            }

            if (!function_exists('add_meta_box')) {
                // HTML and CSS classes chosen so the interface will mesh with the default.
                echo('<div id="sentrydiv" class="postbox " ><div class="handlediv" title="Click to toggle"><br /></div>');
                echo('<h3 class="hndle"><span>' . __('User &amp; Group Access', $this->textdomain) . '</span></h3>');
                echo('<div class="inside">');
            }

            echo('<h4>' . __('Allow access to', $this->textdomain) . ':</h4>');
			echo('<input type="hidden" name="cpt_sentry_permission_fields" value ="present"  />');
            echo('<table cellpadding="3" cellspacing="0" border="0" width="100%"><tr>');

            // Group Access Select Box
            $groups_list = $wpdb->get_results("SELECT * FROM {$this->group_table_name} ORDER BY name");
            echo('<td width="45%" valign="top">' . __('These groups', $this->textdomain) . ':<br /><select name="cpt_sentry_groups_allowed[]" style="height: 150px; width: 280px;" MULTIPLE>');
            foreach ($groups_list as $g) {
                if (in_array($g->id, $groups_allowed)) {
                    $is_allowed = 'SELECTED';
                } else {
                    $is_allowed = '';
                }

                echo("<option value=\"{$g->id}\" $is_allowed>{$g->name}</option>");
            }
            echo('</select></td>');

            // User Access Select Box
            if ($this->opt_post_user_list_enabled == 'Yes') {
                $users_list = get_users_of_blog();
                echo('<td width="50%" valign="top">' . __('And these users', $this->textdomain) . ':<br /><select name="cpt_sentry_users_allowed[]" style="height: 150px; width: 280px;" MULTIPLE>');

                // All Registered Users Option
                if (in_array('all', $users_allowed)) {
                    $is_allowed = 'SELECTED';
                } else {
                    $is_allowed = '';
                }
                echo("<option style=\"font-weight: bold;\" value=\"all\" $is_allowed>" . __('Allow All Registered Users', $this->textdomain) . "</option>");


                // Users list
                foreach ($users_list as $u) {
                    if (in_array($u->user_id, $users_allowed)) {
                        $is_allowed = 'SELECTED';
                    } else {
                        $is_allowed = '';
                    }

                    echo("<option value=\"$u->user_id\" $is_allowed>$u->display_name  &lt;$u->user_email&gt;</option>");
                }
                echo('</select></td>');
            } else {
                echo('<td width="50%" valign="top" align="center">' . __('User list editing disabled.', $this->textdomain) . '');
								$u_count = count($users_allowed);
								if ($u_count) {
								echo('<div id="asmContainer1" class="asmContainer"><ol id="asmList1" class="asmList">');

                // All Registered Users Option
                if (in_array('all', $users_allowed)) {
                		$u_count--;
										echo('<li class="asmListItem" rel="asm1option0" style="display: list-item;"><span class="asmListItemLabel">');
										echo(__('Allow All Registered Users',$this->textdomain).'</span></li>');
                }
								// Other users
                if ($u_count) {
										echo('<li class="asmListItem" rel="asm1option0" style="display: list-item;"><span class="asmListItemLabel">');
										echo(__('Individual Users: ',$this->textdomain).$u_count.'</span></li>');
                }
								echo ('</ol></div>');
							}
							echo '</td>';
            }

            echo('</tr></table>');
           // echo('<b>' . __('Note', $this->textdomain) . ':</b> ' . __('Selecting any users or groups here forces the post to be Private when published', $this->textdomain) . '.<br /><br />');
            if (!function_exists('add_meta_box')) { echo('</div></div>'); }

        }

       /**
        *  Adds a WP CPT Sentry column to the users (v2.71+), posts, and pages admin pages.
        */
		function add_custom_columns($columns) {
			$columns['wp-sentry'] = 'WP CPT Sentry';
			return $columns;
		}

       /**
        *  Calls the function that populates the custom column on the posts and pages admin screens.
        */
		function do_posts_columns($column_name, $id) {
			if ($column_name = 'wp-sentry') {
				$this->do_posts_pages_columns($id, 'posts');
			}
		}

       /**
        *  Calls the function that populates the custom column on the posts and pages admin screens.
        */
		function do_pages_columns($column_name, $id) {
			if ($column_name = 'wp-sentry') {
				$this->do_posts_pages_columns($id, 'pages');
			}
		}

       /**
        *  Calls the function that populates the custom column on the posts and pages admin screens.
        */
       function do_users_columns($null, $column_name, $id) {
           global $wpdb;
           if ($column_name == 'wp-sentry') {
               $group_count = $wpdb->get_var("SELECT count(*) FROM {$this->group_table_name} WHERE FIND_IN_SET('$id', member_list)");
               if ($group_count) {
                   return("Groups: $group_count");
               } else {
                   return("None.");
               }

           }
       }


		function do_posts_pages_columns($id, $type='posts') {
			// Get post permissions:
			$post = get_post($id);

			switch ($post->post_status) {
				case 'future':
				case 'private':
					$group_list = get_post_meta($id, $this->post_metakey_groups, true); $groups = array();
					$user_list = get_post_meta($id, $this->post_metakey_users, true);   $users  = array();

					if (trim($group_list)) { $groups = explode(',', $group_list); }
					if (trim($user_list))  { $users  = explode(',', $user_list ); }

					$g_count = count($groups);
					$u_count = count($users);

					if (($g_count <= 0) && ($u_count <= 0)) {
						echo(ucwords($post->post_status));
						break;
					}

					if (in_array('all', $users)) {
						echo("All Registered Users.");
						break;
					}

					echo("Groups: $g_count<br />\n");
					echo("Users: $u_count<br />\n");

					break;
				default:
					echo(ucwords($post->post_status));
					break;
			}
		}

// Wordpress API Action Hooks

       /**
        *  Forces posts with group or user access to be published privately.
		*  DEPRECATED in 0.70
        */
        function save_status($new_status) {
            // Turn select box data into CSV data
            $groups_allowed = $this->sanitize_select_data($_POST['cpt_sentry_groups_allowed']);
            $users_allowed = $this->sanitize_select_data($_POST['cpt_sentry_users_allowed']);

            if ( ($new_status == 'publish') && (($groups_allowed != '') || ($users_allowed != ''))) {
                return 'private';
            } else {
                return $new_status;
            }
        }

       /**
        *  Updates the posts group and user access restrictions when the post is saved.
        */
        function save_post($post_ID,$post) {
        	global $wpdb;
            // get_post is a WordPress function that returns a post object.
            
             if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      		return;
      
            while (isset($post->post_parent) && $post->post_type == 'revision' && $post->post_parent != 0) {
                $post = get_post($post->post_parent);
            }


        	$permissions_set = isset($_POST['cpt_sentry_permission_fields']); // is this called with a valid permission form? (Could be a cron job)
        	if ($permissions_set) {

	            $groups_allowed = $this->sanitize_select_data($_POST['cpt_sentry_groups_allowed']);
	            $users_allowed = $this->sanitize_select_data($_POST['cpt_sentry_users_allowed']);

	            // If there are group/user access restrictions set, or if the post is private,
	            // update the post meta data
	            if (($groups_allowed != '') || ($users_allowed != '') || ($post->post_status == 'private')) {
	                // Get the current listing
	                $current_groups = get_post_meta($post->ID, $this->post_metakey_groups, true);
	                $current_users = get_post_meta($post->ID, $this->post_metakey_users, true);

	                // Update groups metadata if it exists, add if it doesn't.


	                if ($current_groups != '') {
	                    update_post_meta($post->ID, $this->post_metakey_groups, $groups_allowed);

	                } else {
	                    add_post_meta($post->ID, $this->post_metakey_groups, $groups_allowed);
	                }

	                // Update users metadata if it exists, add if it doesn't.
	                if ($current_users != '') {
	                    update_post_meta($post->ID, $this->post_metakey_users, $users_allowed);
	                } else {
	                    add_post_meta($post->ID, $this->post_metakey_users, $users_allowed);
	                }



	            	if(strlen($groups_allowed) > 0 || strlen($users_allowed))
	            	{
	            		add_post_meta($post->ID, 'sentry_prvt', true);


	            		$cpt_postTypes = $wpdb->get_results("SELECT post_type FROM {$wpdb->posts} GROUP BY post_type");
	            		$arrpost[] = array();
	            		foreach($cpt_postTypes as $cpt) {
	            			$arrpost[] =$cpt->post_type;
	            		}
	            		$args = array('post_parent' => (int)$post->ID, 'post_type' => $arrpost);
	            		$children = new WP_Query($args);
	            		//$children = get_children( $args );

	            		while($children->have_posts()) : $children->the_post();

	            			$cid = get_the_ID();

	            			delete_post_meta($cid, $this->post_metakey_groups);
	            			delete_post_meta($cid, 'sentry_prvt');
	            			if(strlen($groups_allowed) > 0){
	            				add_post_meta($cid, $this->post_metakey_groups, $groups_allowed);
	            				add_post_meta($cid, 'sentry_prvt', true);
	            			}

	            			delete_post_meta($cid, $this->post_metakey_users);
	            			if(strlen($users_allowed) > 0){

	            				add_post_meta($cid, $this->post_metakey_users, $users_allowed);
	            				add_post_meta($cid, 'sentry_prvt', true);
	            			}
	            		endwhile;
	            	}

	            }
	            // Delete the blank metadata rows
	            // If multiple metadata entries get put in the database somehow, clearing all
	            // fields will delete each one... multiple entries shouldn't happen, though.
	            if ($groups_allowed == '') {
	                delete_post_meta($post->ID, $this->post_metakey_groups);
	            }
	            if ($users_allowed == '') {
	                delete_post_meta($post->ID, $this->post_metakey_users);
	            }
	          } else {
				  // Get the current listing
				  $groups_allowed = get_post_meta($post->ID, $this->post_metakey_groups, true);
				  $users_allowed = get_post_meta($post->ID, $this->post_metakey_users, true);
	          }

            //
            // Fix post status:
            // (If status is future, leave it.)
            // If status is 'private' but date is future, convert to future.
            // If status is 'publish' but there are restrictions, convert to private.
            //
            // This conversion takes place both after post editing, and during cron job that
            // publishes future posts.
            //

		   /* $new_status = $previous_status = $post->post_status;
            if ($new_status == 'private') {
            	// Check for future date and convert to 'future'
							$time = strtotime( $post->post_date_gmt . ' GMT' );
							if ( $time > time() ) { // It's not ready to be posted. Convert to future!
								wp_clear_scheduled_hook( 'publish_future_post', $post->ID ); // clear cur hook
								wp_schedule_single_event( $time, 'publish_future_post', array( $post->ID ) );
								$new_status = 'future';
							}

            } elseif ($new_status == 'publish' && (($groups_allowed != '') || ($users_allowed != ''))) {
            	// Now being published, but there are restrictions: convert to 'private'
            	$new_status = 'private';
            }
						if ($new_status != $previous_status) {
							global $wpdb;
							// Update post status in the database
							$wpdb->update( $wpdb->posts, array( 'post_status' => $new_status ), array( 'ID' => $post->ID ) );
							$post->post_status = $new_status;
							wp_transition_post_status($new_status, $old_status, $post);
						} */

        }


       /**
        *  Put users into default groups.
        */
        function user_signup($user_ID) {
            global $wpdb;

            if (!empty($this->opt_default_user_groups)) {
                $groups_to_update = $wpdb->get_results("SELECT * FROM {$this->group_table_name} WHERE FIND_IN_SET(id, '{$this->opt_default_user_groups}')");

                foreach ($groups_to_update as $g) {
                    if (empty($g->member_list)) {
                        $new_list = $user_ID;
                    } else {
                        $new_list = "{$g->member_list},$user_ID";
                    }

                    $wpdb->query("UPDATE {$this->group_table_name} SET member_list='$new_list' WHERE id={$g->id}");
                }
            }

            return;
        }

       /**
        *  Remove User Permissions
        */
        function user_cleanup($user_ID) {
            global $wpdb;

			// Get the groups to which the user belongs.
            $users_groups = $wpdb->get_results("SELECT * FROM {$this->group_table_name} WHERE FIND_IN_SET('$user_ID', member_list)");

			foreach ($users_groups as $g) {
				$user_list = explode(',', $g->member_list);
				$new_user_list = array();

				foreach ($user_list as $u) {
					if ($u != $user_ID) { $new_user_list[] = $u; }
				}

				$new_user_list = implode(',', $new_user_list);

				$wpdb->query("UPDATE {$this->group_table_name} SET member_list='$new_user_list' WHERE id={$g->id}");
			}


            return;
        }


// Content Filters

       /**
        *  Handles the custom post title pre- and post-fix
        */
        function filter_title($title) {
            global $wpdb;
            global $post;

            if($post->post_status != 'private') { return $title; }

            $localized_prefix = __('Private');

            if (strpos($title, $localized_prefix) === 0) {
                $title = substr($title, strpos($title, ':')+1);

                return $this->opt_post_title_prefix . $title . $this->opt_post_title_postfix;
            } else {
                return $title;
            }
        }

       /**
         *  Determines whether to show the user the content or some preview of the content.
        */
        function filter_content($content) {
            global $wpdb;
            global $post, $user_ID;

            // Show public posts all the time
            if($post->post_status != 'private' || current_user_can('edit_others_posts')) { return $content; }

            if (is_user_logged_in() && $this->user_is_allowed($user_ID, $post->ID)) {
                return $content;
            }

            // Haven't found a reason to show them the post yet, so show what was allowed:
            if (($this->opt_previews == 'Above-the-fold') && preg_match('/<!--more(.+?)?-->/', $post->post_content)) {
                // Show the portion above the break. If no break is not present in content, it must already have been filtered
                if (strpos($content, '<!--more') !== false) {
                    $content = substr($content, 0, strpos($content, '<!--more'));
                } elseif (strpos($content, '<span id="more') !== false) {
					$content = substr($content, 0, strpos($content, '<span id="more'));
				}

                $content .= $this->opt_denied_message;
                return $content;

            } else if (($this->opt_previews  == 'Excerpt') && ($post->post_excerpt != '')) {
                // Show an excerpt if it exists. If not, show nothing.
                $content  = "<p class='cpt-sentry-excerpt'>" . $post->post_excerpt . '</p>';
                $content .= $this->opt_denied_message;
                return $content;

            } else {
                // Show the access denied error message.
                return $this->opt_denied_message;

            }
        }

        /**
         *  This filter ensures that comments to previewed but not allowed posts will never be seen.
         *  It is only used when some kind of preview is allowed.
         */
        function filter_comments_array($comments)
        {
            global $post, $user_ID;

            // Show comments of public posts all the time
            if($post->post_status != 'private' || current_user_can('edit_others_posts')) { return $comments; }

            if (is_user_logged_in() && $this->user_is_allowed($user_ID, $post->ID)) {
                return $comments;
            }

            // If the user has not the right to fully see this post, do not show him any comments
            return array();
        }

        /**
         *  This filter ensures that comments are closed for previewed but not allowed posts.
         *  It is only used when some kind of preview is allowed.
         */
        function filter_post_comment_status($posts)
        {
            global $user_ID;

            // Bypass filter if we are not viewing a single post
            if (!is_single() && !is_page())
                return $posts;

            // Do not attempt to filter anything if there is no posts
            if(!$posts)
                return;

            $post =& $posts[0];

            // Don't change open status for a public post
            if($post->post_status != 'private' || current_user_can('edit_others_posts')) { return $posts; }

            if (is_user_logged_in() && $this->user_is_allowed($user_ID, $post->ID)) {
                return $posts;
            }

            // If the user has not the right to fully see this post, forbide comment post
            $post->comment_status = false;

            return $posts;
        }

       /**
        *  This filter is only used when title previews are being used.
        */
        function filter_excerpt($excerpt) {
            global $wpdb;
            global $post, $user_ID;

            // Show public posts all the time
            if($post->post_status != 'private') { return $excerpt; }

            if (is_user_logged_in() && $this->user_is_allowed($user_ID, $post->ID)) {
                return $excerpt;
            }

            return $this->opt_denied_message;
        }

       /**
        *  Displays a user-defined URL for the permalink to "previewed" posts.
        */
        function filter_permalink($url) {
            global $post, $user_ID;

            if (    $post->post_status == 'private' &&
                    !current_user_can('edit_others_posts') &&
                    $this->opt_previews != 'None' &&
                    !empty($this->opt_preview_permalink) &&
                    (!$user_ID || !$this->user_is_allowed($user_ID, $post->ID))) {
                return($this->opt_preview_permalink);
            }

            return $url;
        }

       /**
        *  Integrates Feed Key URLs into the site so a user doesn't have to get
		*  them from their user profile page.
        */
	/*	function filter_feed_url($url) {
			global $user_ID;
			if (is_user_logged_in()) {
				$feedkey = get_usermeta($user_ID,'feed_key');
				if ($feedkey) {
					if (strpos($url, '?')) {
						$append_char = '&amp;';
					} else {
						$append_char = '?';
					}
					return $url . $append_char . "feedkey=$feedkey";
				}
			}
		  return $url;
		}		*/

        /**
         * Show only tags that belongs to at least one content the user can see.
         *
         * This filter use the query_mod() function to determine what is the content the user can see.
         * Thus the displayed tags are affected by the "opt_previews" setting : if some kind
         * of preview option is set, the tags will include the ones associated with the posts that
         * the user can preview (which is probably the wanted behavior).
         */
        function filter_tags($tags)
        {
            global $wpdb;

            // Use query_mod to get all the content the user can access
            $where = '';
            if (current_user_can('administrator')) {
                // Administrators see all content anyway, so we search tags in all posts
                $where = "{$wpdb->posts}.post_status = 'publish' OR {$wpdb->posts}.post_status = 'private'";
            } else {
                // The user is not an administrator : search tags in posts he has the right to see
                $where = $this->query_mod("{$wpdb->posts}.post_status = 'publish'");
            }

            // Get all the terms that belongs to a content the user can see
            $terms_q = "SELECT DISTINCT term_taxonomy_id FROM {$wpdb->term_relationships} WHERE object_id IN (SELECT id FROM {$wpdb->posts} WHERE $where)";
            $terms = $wpdb->get_results($terms_q);

            $term_taxonomy_ids = array();
            foreach($terms as $term)
                $term_taxonomy_ids[] = $term->term_taxonomy_id;

            // Filter : delete tags that do not belong to a content the user can see
            foreach($tags as $key => $tag) {
                if (!in_array($tag->term_taxonomy_id, $term_taxonomy_ids))
                    unset($tags[$key]);
            }

            return $tags;
        }

// Miscellaneous Grunt-work Functions
        function admin_head_insert() {
            $cpt_sentry_dir = get_bloginfo('wpurl') . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)) . '/';
            echo("\n<!-- Added by WP-Sentry: -->\n");
            echo("<script type=\"text/javascript\" src=\"{$cpt_sentry_dir}jquery.asmselect.js\"></script>\n");
            echo("<link rel=\"stylesheet\" type=\"text/css\" href=\"{$cpt_sentry_dir}jquery.asmselect.css\" />\n");
            echo('<script type="text/javascript">jQuery(document).ready(function(){ jQuery("select[multiple]").asmSelect(); });</script>' . "\n");
        }

      /**
        *  Takes user and post ID Information, returns true if the user has access, false otherwise.
        */
        function user_is_allowed($user_ID, $post_ID) {
            global $wpdb;

            $post = get_post($post_ID);
            if ($post->post_status != 'private') { return true; }

            // Allowed users & groups
            $allowed_groups = explode(',', get_post_meta($post_ID, $this->post_metakey_groups, true));
            $allowed_users = explode(',', get_post_meta($post_ID, $this->post_metakey_users, true));

            // Is the user explicitly allowed? If so, return the content.
            // This check is done first because if it's successful it saves a database query.
            if (in_array($user_ID, $allowed_users) || in_array('all', $allowed_users)) {
                return true;
            }

            // Check each of the user's groups to see if that group is allowed to see the post
            $users_groups = $wpdb->get_results("SELECT id FROM {$this->group_table_name} WHERE FIND_IN_SET('$user_ID', member_list)");
            foreach ($users_groups as $ug) {
                if (in_array($ug->id, $allowed_groups)) {
                    return true;
                }
            }

            // Check in the child groups of the $allowed_groups
            $query = "SELECT id FROM {$this->group_table_name} WHERE FIND_IN_SET(" . implode(', member_of) OR FIND_IN_SET(', $allowed_groups) . ", member_of)";
            $asg = $wpdb->get_results($query);
            $allowed_sub_groups = array();

            foreach ($asg as $g) {
              $allowed_sub_groups[] = $g->id;
            }
            foreach ($users_groups as $ug) {
                if (in_array($ug->id, $allowed_sub_groups)) {
                    return true;
                }
            }

            // No access-granting permissions found
            return false;
        }

       /**
        *  Takes data from a multiple select statement and turns it into CSV data.
        */
        function sanitize_select_data($select_data) {
            $temp = array();
            if (is_array($select_data)) {
                foreach ($select_data as $X) {
                    if ($X == 'all' && !in_array('all', $temp)) {
                        $temp[] = 'all';
                    } else if (!in_array($X, $temp)) {
                        $temp[] = intval($X);
                    }
                }    $temp = implode(',', $temp);
            } else {
                $temp = '';
            }

            return $temp;
        }

       /**
        *  Loads a WordPress option, sets it to the supplied default value if the option does not yet exist.
        */
        function initialize_wp_option($name, $default_value=null) {
            $value = get_option($name);
            if (!is_null($default_value) && $value === false) {
                $value = $default_value;
                add_option($name, $value);
            }

            return $value;
        }



function privacy_check($posts)
{
global $wpdb;


		if(!is_admin())
		{

	$strids = '-1';

	if(count($posts) > 0)
	{

		foreach ( $posts as $i => $post ){
	//	var_dump($post);
		$strids .= ','.$post->ID;

		}
	}
	$current_user = wp_get_current_user();


$querystr = "SELECT  wposts.*
	FROM  {$wpdb->posts} wposts
	WHERE ((FIND_IN_SET(wposts.ID,'{$strids}')
	AND wposts.ID NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'sentry_prvt' AND meta_value = 1)
	AND wposts.post_date < NOW())
	OR
	wposts.ID IN (
		SELECT wposts2.ID FROM {$wpdb->posts} wposts2, {$wpdb->postmeta} wpostmeta
			WHERE FIND_IN_SET(wposts2.ID,'{$strids}')
			AND wpostmeta.post_id = wposts2.ID
			AND wpostmeta.meta_key = '{$this->post_metakey_users}'
			AND (FIND_IN_SET('{$current_user->ID}',wpostmeta.meta_value)  OR wpostmeta.meta_value = 'all')
			AND wposts2.post_date < NOW()
		))
	OR
	wposts.ID IN (
		SELECT wposts3.ID FROM {$wpdb->posts} wposts3, {$wpdb->postmeta} wpostmeta3, {$this->group_table_name} gtn
			WHERE FIND_IN_SET(wposts3.ID,'{$strids}')
			AND wpostmeta3.post_id = wposts3.ID
			AND wpostmeta3.meta_key = '{$this->post_metakey_groups}'
			AND FIND_IN_SET(gtn.id,wpostmeta3.meta_value)
			AND FIND_IN_SET('{$current_user->ID}', gtn.member_list)
			AND wposts3.post_date < NOW()
		)
	GROUP BY wposts.ID
	ORDER BY wposts.post_date DESC
	";

		$p = $wpdb->get_results($querystr);
		//var_dump($p);
		//exit();
		return $p;

		}
		else
		{
			return $posts;
		}

		}

function paypal_html() {
	// Paypal Donate Button
	echo('
	<table border="0" style="border-top: solid 1px #dadada; margin-top: 35px" width="100%"><tr><td align="center" width="25%">
	    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	    <input type="hidden" name="cmd" value="_s-xclick">
	    <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	    <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
	    <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIHRwYJKoZIhvcNAQcEoIIHODCCBzQCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYCfd1NhdGiIrC1UMpbXDeU0e84gJM+egmhPV4DhO4loljZ+0OxdaLDtqj9Ja47D1+jCJ+CNkZE1IHURpMo2EHHcwADKd/gxScEBKifWd5SZoAir1lXvuslgnW0hRd6mW7+Z5wrNlLb05ndNvawI7gmYcrgp8+AjKMX9xyxDiCTqhTELMAkGBSsOAwIaBQAwgcQGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQISauj/HZt7Q2AgaBcXsNVX7woWStagdngEYO8JRH9Aj31f7NOfpNPjAB9HLLkWGtisUcRXdnaLpqDS6NgiADYgJGw40NvybJSZquOvgWgbgmrvt03FZswEoK2WzfUzQ/54DOmhQ5sxAbacW2EcCmaNvH2bzKRE1JPMQ253ndmK0af9mjeaIoi09mcBgutSq7ExquzTfHZA5SoA2CIv1NbAzhK08lSFhogMX8KoIIDhzCCA4MwggLsoAMCAQICAQAwDQYJKoZIhvcNAQEFBQAwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tMB4XDTA0MDIxMzEwMTMxNVoXDTM1MDIxMzEwMTMxNVowgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDBR07d/ETMS1ycjtkpkvjXZe9k+6CieLuLsPumsJ7QC1odNz3sJiCbs2wC0nLE0uLGaEtXynIgRqIddYCHx88pb5HTXv4SZeuv0Rqq4+axW9PLAAATU8w04qqjaSXgbGLP3NmohqM6bV9kZZwZLR/klDaQGo1u9uDb9lr4Yn+rBQIDAQABo4HuMIHrMB0GA1UdDgQWBBSWn3y7xm8XvVk/UtcKG+wQ1mSUazCBuwYDVR0jBIGzMIGwgBSWn3y7xm8XvVk/UtcKG+wQ1mSUa6GBlKSBkTCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb22CAQAwDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQUFAAOBgQCBXzpWmoBa5e9fo6ujionW1hUhPkOBakTr3YCDjbYfvJEiv/2P+IobhOGJr85+XHhN0v4gUkEDI8r2/rNk1m0GA8HKddvTjyGw/XqXa+LSTlDYkqI8OwR8GEYj4efEtcRpRYBxV8KxAW93YDWzFGvruKnnLbDAF6VR5w/cCMn5hzGCAZowggGWAgEBMIGUMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbQIBADAJBgUrDgMCGgUAoF0wGAYJKoZIhvcNAQkDMQsGCSqGSIb3DQEHATAcBgkqhkiG9w0BCQUxDxcNMDgwNzEyMTg0MjQ0WjAjBgkqhkiG9w0BCQQxFgQUkaydhpYhwq9xNGlsl+8azAg6LUswDQYJKoZIhvcNAQEBBQAEgYCZWOp0KuX4gzlVqCctl/Cw+bte8BSe2/cU7DhHLj+9BNc7FTt8+WzsBQCAe5UimHcbhpUF54MicvAUvVlMoZ1b+lrvFIJLfj99M/wwoVzA3sis+ngvjZYbDe+4SzuQhH0gGz5XLV7omcda0GT/dsfNSxSFnqI3S/WbSW03k+otFg==-----END PKCS7-----
	    ">
	    </form>
	</td><td>' . __('This will donate to Pete Holiday for his work on WP Sentry, the core of this plugin. If you are looking for a way to to give back to WP Sentry, feel free to show your gratitude via paypal.', $this->textdomain) . '</td>
	</tr></table>');
	// End Paypal Donate Button
}


    }

   // add_filter('template', array(&$this, 'privacy_check'),-1, 1 );
}

if(class_exists('wp_cpt_sentry')) {
  $WP_CPT_SENTRY = new wp_cpt_sentry();
  $WP_CPT_SENTRY->init();
}
