<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title>
<?php
if (is_home()) {
	echo bloginfo('name');
} else {
	echo bloginfo('name');
	echo ' - ';
	echo wp_title('');
}
?>
</title>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="all" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="shortcut icon" type="image/ico" href="<?php bloginfo('template_url'); ?>/images/favicon.ico" />

<!-- Docking boxes (dbx) by brothercake - http://www.brothercake.com/ -->
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/dbx.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/dbx-key.js"></script>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/dbx.css" media="screen, projection" />
<?php wp_head(); ?>
</head>

<body>
<div id="container">

<?php include (TEMPLATEPATH . '/searchform.php'); ?>

<div id="header">
<h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
<div class="description"><?php bloginfo('description'); ?></div>
</div>

<div id="content">

<div id="navbar">
<div id="nav">		
<ul>
  <li><a href="<?php bloginfo('url'); ?>">Home</a></li>
  <?php wp_list_pages('title_li=&depth=1'); ?>
</ul>
</div>
<span class="rssicon"><a href="<?php bloginfo('rss2_url'); ?>" class="alignright"><img src="<?php bloginfo('template_url'); ?>/images/rssicon.gif" alt="RSS" /></a></span>
</div>

<div class="padded">
