<?php

/**
 * Sentry-aware Pages Widget
 * 
 * Needed to encapsulate the behavior of wp_list_pages because that function only displays public pages
 * 
 * @since 0.8
 */
if (class_exists('WP_Widget')) {
	class CPT_Sentry_Widget_Pages extends WP_Widget {
		function CPT_Sentry_Widget_Pages() {
			$widget_ops = array('classname' => 'widget_pages', 'description' => __( 'Your blog&#8217;s WordPress Pages') );
			$this->WP_Widget('pages', __('Pages'), $widget_ops);
		}
	
		function widget($args, $instance) {
	            global $wpdb, $WP_CPT_SENTRY;
	            extract( $args );
	        
	            $title = empty( $instance['title'] ) ? __( 'Pages' ) : $instance['title'];
	            $sortby = empty( $instance['sortby'] ) ? 'menu_order' : $instance['sortby'];
	            $exclude = empty( $instance['exclude'] ) ? '' : $instance['exclude'];
			
	            if ( $sortby == 'menu_order' ) {
	                $sortby = 'menu_order, post_title';
	            }
	    
	            $visible_pages_q = "SELECT * FROM {$wpdb->posts} WHERE post_type='page' AND !FIND_IN_SET(ID, '$exclude') AND (" . $WP_CPT_SENTRY->query_mod("{$wpdb->posts}.post_status = 'publish'") . ") ORDER BY $sortby";
	            $visible_pages = $wpdb->get_results($visible_pages_q);
	            if ( count($visible_pages) > 0 ) {
	                global $wp_query;
	                if ( is_page() ) {
	                    $current_page = $wp_query->get_queried_object_id();
	                }
	
	                $out .= walk_page_tree($visible_pages, 0, $current_page, array('title_li' => '', 'echo' => 0, 'sort_column' => $sortby));
	            }
	            
	            $out = apply_filters('wp_list_pages', $out);
	            if ( !empty( $out ) ) {
	        ?>
	            <?php echo $before_widget; ?>
	                <?php echo $before_title . $title . $after_title; ?>
	                <ul>
	                    <?php echo $out; ?>
	                </ul>
	            <?php echo $after_widget; ?>
	        <?php
	            }
	
		}
	
		// Copied directly from default-widgets.php (WP 2.8.4)
		function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			if ( in_array( $new_instance['sortby'], array( 'post_title', 'menu_order', 'ID' ) ) ) {
				$instance['sortby'] = $new_instance['sortby'];
			} else {
				$instance['sortby'] = 'menu_order';
			}
	
			$instance['exclude'] = strip_tags( $new_instance['exclude'] );
	
			return $instance;
		}
	
		// Copied directly from default-widgets.php (WP 2.8.4)
		function form( $instance ) {
			//Defaults
			$instance = wp_parse_args( (array) $instance, array( 'sortby' => 'post_title', 'title' => '', 'exclude' => '') );
			$title = esc_attr( $instance['title'] );
			$exclude = esc_attr( $instance['exclude'] );
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
			<p>
				<label for="<?php echo $this->get_field_id('sortby'); ?>"><?php _e( 'Sort by:' ); ?></label>
				<select name="<?php echo $this->get_field_name('sortby'); ?>" id="<?php echo $this->get_field_id('sortby'); ?>" class="widefat">
					<option value="post_title"<?php selected( $instance['sortby'], 'post_title' ); ?>><?php _e('Page title'); ?></option>
					<option value="menu_order"<?php selected( $instance['sortby'], 'menu_order' ); ?>><?php _e('Page order'); ?></option>
					<option value="ID"<?php selected( $instance['sortby'], 'ID' ); ?>><?php _e( 'Page ID' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e( 'Exclude:' ); ?></label> <input type="text" value="<?php echo $exclude; ?>" name="<?php echo $this->get_field_name('exclude'); ?>" id="<?php echo $this->get_field_id('exclude'); ?>" class="widefat" />
				<br />
				<small><?php _e( 'Page IDs, separated by commas.' ); ?></small>
			</p>
	<?php
		}
	}
	
	/**
	 *  Sentry-aware Recent Comments Widget 
	 *
	 *  Two changes made from the base install version: 
	 *    1. Queries set to use the query_mod filter to ensure that the comments come from allowed posts.
	 *    2. $post was over-written in the foreach output loop to make sure that the post titles have the correct formatting
	 *    
	 * @since 0.8
	 */        
	class CPT_Sentry_Widget_Recent_Comments extends WP_Widget {
	
		function CPT_Sentry_Widget_Recent_Comments() {
			$widget_ops = array('classname' => 'widget_recent_comments', 'description' => __( 'The most recent comments' ) );
			$this->WP_Widget('recent-comments', __('Recent Comments'), $widget_ops);
			$this->alt_option_name = 'widget_recent_comments';
	
			if ( is_active_widget(false, false, $this->id_base) )
				add_action( 'wp_head', array(&$this, 'recent_comments_style') );
	
			add_action( 'comment_post', array(&$this, 'flush_widget_cache') );
			add_action( 'wp_set_comment_status', array(&$this, 'flush_widget_cache') );
		}
	
		function recent_comments_style() { ?>
		<style type="text/css">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style>
	<?php
		}
	
		function flush_widget_cache() {
			wp_cache_delete('recent_comments', 'widget');
		}
	
		function widget( $args, $instance ) {
	            global $wpdb, $comments, $comment, $post, $WP_CPT_SENTRY;
	            
	            $orig_post = $post;
	            
	            extract($args, EXTR_SKIP);
	            $options = $instance;
				
	            $title = empty($options['title']) ? __('Recent Comments') : $options['title'];
	            if ( !$number = (int) $options['number'] )
	                $number = 5;
	            else if ( $number < 1 )
	                $number = 1;
	            else if ( $number > 15 )
	                $number = 15;
	            
	            if (current_user_can('edit_others_posts')) {
	                $visible_cmnts_q = "SELECT comment_author, comment_author_url, comment_ID, comment_post_ID FROM {$wpdb->comments} WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT $number";
	            } else {
	                $visible_posts_q = "SELECT ID FROM {$wpdb->posts} WHERE " . $WP_CPT_SENTRY->query_mod("{$wpdb->posts}.post_status = 'publish'");
	                $visible_cmnts_q = "SELECT comment_author, comment_author_url, comment_ID, comment_post_ID FROM {$wpdb->comments} WHERE comment_approved = '1' AND (comment_post_ID IN ($visible_posts_q)) ORDER BY comment_date_gmt DESC LIMIT $number";
	            }
	
	               $comments = $wpdb->get_results($visible_cmnts_q);
	        ?>
	                <?php echo $before_widget; ?>
	                    <?php echo $before_title . $title . $after_title; ?>
	                    <ul id="recentcomments"><?php
	                    if ( $comments ) : foreach ($comments as $comment) :
	                    $post = get_post($comment->comment_post_ID);
	                    echo  '<li class="recentcomments">' . sprintf(__('%1$s on %2$s'), get_comment_author_link(), '<a href="'. get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '">' . get_the_title($comment->comment_post_ID) . '</a>') . '</li>';
	                    endforeach; endif;?></ul>
	                <?php echo $after_widget; ?>
	        <?php
	        
	            $post = $orig_post;
		}
	
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['number'] = (int) $new_instance['number'];
			$this->flush_widget_cache();
	
			$alloptions = wp_cache_get( 'alloptions', 'options' );
			if ( isset($alloptions['widget_recent_comments']) )
				delete_option('widget_recent_comments');
	
			return $instance;
		}
	
		function form( $instance ) {
			$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
			$number = isset($instance['number']) ? absint($instance['number']) : 5;
	?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
	
			<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of comments to show:'); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /><br />
			<small><?php _e('(at most 15)'); ?></small></p>
	<?php
		}
	}
}