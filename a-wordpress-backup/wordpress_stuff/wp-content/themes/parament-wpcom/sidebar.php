<?php
/**
 * @package Parament
 */

do_action( 'before_sidebar' ); ?>
<ul id="sidebar" role="complementary">
	<?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>

	<?php the_widget( 'WP_Widget_Recent_Posts', array( 'number' => 10 ), array(
		'before_widget' => '<li id="recent-posts" class="widget widget_recent_entries">',
		'after_widget'  => '</li>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>'
	) ); ?>

	<?php the_widget( 'WP_Widget_Recent_Comments', array( 'number' => 5 ), array(
		'before_widget' => '<li id="recent-comments" class="widget widget_recent_comments">',
		'after_widget'  => '</li>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>'
	) ); ?>

	<?php the_widget( 'WP_Widget_Meta', array(), array(
		'before_widget' => '<li id="meta" class="widget widget_meta">',
		'after_widget'  => '</li>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>'
	) ); ?>

	<?php endif; ?>
</ul><!-- end sidebar -->