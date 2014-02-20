<?php
/**
 * @package Parament
 */

$header_image = get_header_image();
$tagline      = get_bloginfo( 'description' );
$tag_markup   = empty( $header_image ) ? '<h2 id="site-description">%2$s</h2>' : '<h2 id="site-description"><a href="%1$s">%2$s</a></h2>';

?><!DOCTYPE html>
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
	<![endif]-->
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div id="page-wrap" class="contain">
<?php do_action( 'before' ); ?>
	<header id="branding" role="banner">
		<h1 id="site-title"><a href="<?php echo esc_url( home_url() ); ?>"><?php echo get_option( 'blogname' ); ?></a></h1>
		<?php if ( ! empty( $tagline ) ) :  ?>
			<?php printf( $tag_markup, esc_url( home_url() ), $tagline );  ?>
		<?php endif; ?>

		<?php if ( ! empty( $header_image ) ) : ?>
			<a id="header-image" href="<?php echo esc_url( home_url() ); ?>"><img src="<?php echo esc_url( $header_image ); ?>" alt="" /></a>
		<?php endif; ?>
	</header><!-- #branding -->

	<nav id="menu" role="navigation"><?php wp_nav_menu( array( 'container' => false, 'menu_id' => 'primary-menu', 'theme_location' => 'primary-menu' ) ); ?></nav>