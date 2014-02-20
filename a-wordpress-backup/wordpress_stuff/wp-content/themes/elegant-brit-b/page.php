<?php get_header(); ?>
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>

      <h1><?php the_title(); ?></h1>
		<p><?php the_content(); ?></p>
		<p><?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?></p>

<?php endwhile; ?>
<?php else : ?>
	<h1>Not Found</h1>
	<p>Sorry, but you are looking for something that isn't here.</p>
<?php endif; ?></div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
