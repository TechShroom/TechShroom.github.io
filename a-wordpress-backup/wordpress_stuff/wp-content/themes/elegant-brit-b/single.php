<?php get_header(); ?>
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>

	<div class="post-header">
		<span class="dateicon">
		<span class="dateicon-month"><?php the_time('M') ?></span>
		<span class="dateicon-day"><?php the_time('j') ?></span>
		</span>
	<div class="titlearea"><h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h1>
	<div class="post-info"><span class="alignleft"><img src="<?php bloginfo('template_url'); ?>/images/caticon.gif" /> <?php the_category(', '); ?> <?php if (get_the_tags()) the_tags('<span class="tags">', ', ', '</span>'); ?></span><span class="alignright"><img src="<?php bloginfo('template_url'); ?>/images/comicon.gif" /> <?php comments_popup_link('Add Comment (0)', 'Add Comment (1)', 'Add Comment (%)'); ?></span>
	</div>
	</div>
	</div>
	<?php the_content(); ?>
<br />

<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>

<?php comments_template(); ?>

<?php endwhile; else: ?>
<p>Sorry, no posts matched your criteria.</p>
<?php endif; ?></div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
