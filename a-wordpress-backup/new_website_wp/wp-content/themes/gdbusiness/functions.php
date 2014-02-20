<?php
/**
 * Title: Function
 *
 * Description: Defines theme specific functions including actions and filters.
 *
 * Please do not edit this file. This file is part of the Cyber Chimps Framework and all modifications
 * should be made in a child theme.
 *
 * @category Cyber Chimps Framework
 * @package  Framework
 * @since    1.0
 * @author   CyberChimps
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     http://www.cyberchimps.com/
 */

// Load Core
require_once( get_template_directory() . '/cyberchimps/init.php' );

// Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) )
	$content_width = 640; /* pixels */

// Define site info
function cyberchimps_add_site_info() { ?>
	<p>&copy; Company Name</p>	
<?php }
add_action('cyberchimps_site_info', 'cyberchimps_add_site_info');

if ( ! function_exists( 'cyberchimps_comment' ) ) :

// Template for comments and pingbacks.
// Used as a callback by wp_list_comments() for displaying the comments.
function cyberchimps_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'cyberchimps' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'cyberchimps' ), ' ' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer>
				<div class="comment-author vcard">
					<?php echo get_avatar( $comment, 40 ); ?>
					<?php printf( '%s <span class="says">' . __( 'says:', 'cyberchimps' ) . '</span>', sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
				</div><!-- .comment-author .vcard -->
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em><?php _e( 'Your comment is awaiting moderation.', 'cyberchimps' ); ?></em>
					<br />
				<?php endif; ?>

				<div class="comment-meta commentmetadata">
					<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time pubdate datetime="<?php comment_time( 'c' ); ?>">
					<?php
						/* translators: 1: date, 2: time */
						printf( __( '%1$s at %2$s', 'cyberchimps' ), get_comment_date(), get_comment_time() ); ?>
					</time></a>
					<?php edit_comment_link( __( '(Edit)', 'cyberchimps' ), ' ' );
					?>
				</div><!-- .comment-meta .commentmetadata -->
			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for cyberchimps_comment()

if ( ! function_exists( 'cyberchimps_posted_on' ) ) :

//Prints HTML with meta information for the current post-date/time and author.
function cyberchimps_posted_on() {
	
	if( is_single() ) {
		$show_date = ( cyberchimps_get_option( 'single_post_byline_date', 1 ) ) ? cyberchimps_get_option( 'single_post_byline_date', 1 ) : false;
		$show_author = ( cyberchimps_get_option( 'single_post_byline_author', 1 ) ) ? cyberchimps_get_option( 'single_post_byline_author', 1 ) : false; 
		$show_categories = ( cyberchimps_get_option( 'single_post_byline_categories', 1 ) ) ? cyberchimps_option( 'single_post_byline_categories', 1 ) : false; 
	}
	elseif( is_archive() ) {
		$show_date = ( cyberchimps_get_option( 'archive_post_byline_date', 1 ) ) ? cyberchimps_get_option( 'archive_post_byline_date', 1 ) : false;  
		$show_author = ( cyberchimps_get_option( 'archive_post_byline_author', 1 ) ) ? cyberchimps_get_option( 'archive_post_byline_author', 1 ) : false;
		$show_categories = ( cyberchimps_get_option( 'archive_post_byline_categories', 1 ) ) ? cyberchimps_get_option( 'archive_post_byline_categories', 1 ) : false; 
	}
	else {
		$show_date = ( cyberchimps_get_option( 'post_byline_date', 1 ) ) ? cyberchimps_get_option( 'post_byline_date', 1 ) : false; 
		$show_author = ( cyberchimps_get_option( 'post_byline_author', 1 ) ) ? cyberchimps_get_option( 'post_byline_author', 1 ) : false; 
		$show_categories = ( cyberchimps_get_option( 'post_byline_categories', 1 ) ) ? cyberchimps_get_option( 'post_byline_categories', 1 ) : false; 
	}
	
	$posted_on = sprintf( '%8$s<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>%10$s %9$s<span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span>%11$s',
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		( $show_date ) ? esc_html( get_the_date() ) : '',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by', 'cyberchimps' ) . ' %s', get_the_author() ) ),
		( $show_author ) ? esc_html( get_the_author() ) : '',
		( $show_date ) ? __( 'Posted on ', 'cyberchimps' ) : '',
		( $show_author ) ? __( ' by ', 'cyberchimps' ) : '',
		( $show_author || $show_categories ) ? '<span class="byline">' : '',
		( $show_author || $show_categories ) ? '</span>' : ''
	);
	apply_filters( 'cyberchimps_posted_on', $posted_on );
	echo $posted_on;
}
endif;

//add meta entry category to single post, archive and blog list if set in options
function cyberchimps_posted_in() {
	global $post;

	if( is_single() ) {
		$show = ( cyberchimps_get_option( 'single_post_byline_categories', 1 ) ) ? cyberchimps_get_option( 'single_post_byline_categories', 1 ) : false; 
	}
	elseif( is_archive() ) {
		$show = ( cyberchimps_get_option( 'archive_post_byline_categories', 1 ) ) ? cyberchimps_get_option( 'archive_post_byline_categories', 1 ) : false;  
	}
	else {
		$show = ( cyberchimps_get_option( 'post_byline_categories', 1 ) ) ? cyberchimps_get_option( 'post_byline_categories', 1 ) : false;  
	}
	if( $show ):
				$categories_list = get_the_category_list( ', ' );
				if ( $categories_list ) :
				$cats = sprintf( __( 'Posted in', 'cyberchimps' ) . ' %1$s', $categories_list );
			?>
			<span class="cat-links">
				<?php echo apply_filters( 'cyberchimps_post_categories', $cats ); ?>
			</span>
      <span class="sep"> <?php echo apply_filters( 'cyberchimps_entry_meta_sep', '|' ); ?> </span>
	<?php endif;
	endif;
}

//add meta entry tags to single post, archive and blog list if set in options
function cyberchimps_post_tags() {
	global $post;
	
	if( is_single() ) {
		$show = ( cyberchimps_get_option( 'single_post_byline_tags', 1 ) ) ? cyberchimps_get_option( 'single_post_byline_tags', 1 ) : false; 
	}
	elseif( is_archive() ) {
		$show = ( cyberchimps_get_option( 'archive_post_byline_tags', 1 ) ) ? cyberchimps_get_option( 'archive_post_byline_tags', 1 ) : false;  
	}
	else {
		$show = ( cyberchimps_get_option( 'post_byline_tags', 1 ) ) ? cyberchimps_get_option( 'post_byline_tags', 1 ) : false;  
	}
	if( $show ):
	$tags_list = get_the_tag_list( '', ', ' );
				if ( $tags_list ) :
				$tags = sprintf( __( 'Tags:', 'cyberchimps' ) . ' %1$s', $tags_list );
			?>
			<span class="tag-links">
				<?php echo apply_filters( 'cyberchimps_post_tags', $tags ); ?>
			</span>
      <span class="sep"> <?php echo apply_filters( 'cyberchimps_entry_meta_sep', '|' ); ?> </span>
			<?php endif; // End if $tags_list
	endif;
}

//add meta entry comments to single post, archive and blog list if set in options
function cyberchimps_post_comments() {
	global $post;
	
	if( is_single() ) {
		$show = ( cyberchimps_get_option( 'single_post_byline_comments', 1 ) ) ? cyberchimps_get_option( 'single_post_byline_comments', 1 ) : false; 
	}
	elseif( is_archive() ) {
		$show = ( cyberchimps_get_option( 'archive_post_byline_comments', 1 ) ) ? cyberchimps_get_option( 'archive_post_byline_comments', 1 ) : false;  
	}
	else {
		$show = ( cyberchimps_get_option( 'post_byline_comments', 1 ) ) ? cyberchimps_get_option( 'post_byline_comments', 1 ) : false;  
	}
	$leave_comment = ( is_single() || is_page() ) ? '' : __( 'Leave a comment', 'cyberchimps' );
	if( $show ):
		if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
			<span class="comments-link"><?php comments_popup_link( $leave_comment, __( '1 Comment', 'cyberchimps' ), '% ' . __( 'Comments', 'cyberchimps' ) ); ?></span>
      <span class="sep"> <?php echo ( $leave_comment != '' ) ? apply_filters( 'cyberchimps_entry_meta_sep', '|' ) : ''; ?> </span>
    <?php endif;
	endif;
}

// set up next and previous post links for lite themes only
function cyberchimps_next_previous_posts() {
	if( get_next_posts_link() || get_previous_posts_link() ): ?>
	<div class="more-content">
		<div class="row-fluid">
			<div class="span6 previous-post">
				<?php previous_posts_link( __( 'Previous Entries', 'cyberchimps' ) ); ?>
			</div>
			<div class="span6 next-post">
				<?php next_posts_link( __( 'Next Entries', 'cyberchimps' ) ); ?>
			</div>
		</div>
	</div>
<?php
	endif;
}
add_action( 'cyberchimps_after_content', 'cyberchimps_next_previous_posts' );

// core options customization Names and URL's
//Pro or Free
function cyberchimps_theme_check() {
	$level = 'free';
	return $level;
}

//Theme Name
function cyberchimps_options_theme_name(){
	$text = 'CyberChimps';
	return $text;
}
//Theme Pro Name
function cyberchimps_upgrade_bar_pro_title() {
	$text = 'CyberChimps Pro';
	return $text;
}
//Upgrade link
function cyberchimps_upgrade_bar_pro_link() {
	$url = 'http://cyberchimps.com/responsepro/';
	return $url;
}
//Doc's URL
function cyberchimps_options_documentation_url() {
	$url = 'http://cyberchimps.com/responsepro/docs/';
	return $url;
}
// Support Forum URL
function cyberchimps_options_support_forum() {
	$admin = cyberchimps_get_option( 'admin', array( 'support_url' => 'http://support.godaddy.com/help/category/217/my-account' ) );
	$url = $admin['support_url'];
	return $url;
}
//Page Options Help URL
function cyberchimps_options_page_options_help() {
	$url = 'http://cyberchimps.com/responsepro/docs/';
	return $url;
}
// Slider Options Help URL
function cyberchimps_options_slider_options_help() {
	$url = 'http://cyberchimps.com/responsepro/docs/';
	return $url;
}
add_filter( 'cyberchimps_current_theme_name', 'cyberchimps_options_theme_name', 1 );
add_filter( 'cyberchimps_upgrade_pro_title', 'cyberchimps_upgrade_bar_pro_title' );
add_filter( 'cyberchimps_upgrade_link', 'cyberchimps_upgrade_bar_pro_link' );
add_filter( 'cyberchimps_documentation', 'cyberchimps_options_documentation_url' );
add_filter( 'cyberchimps_support_forum', 'cyberchimps_options_support_forum' );
add_filter( 'cyberchimps_page_options_help', 'cyberchimps_options_page_options_help' );
add_filter( 'cyberchimps_slider_options_help', 'cyberchimps_options_slider_options_help' );

// Help Section
function cyberchimps_options_help_header() {
	$text = 'CyberChimps';
	return $text;
}
function cyberchimps_options_help_sub_header(){
	$text = __( 'CyberChimps Professional Responsive WordPress Theme', 'cyberchimps' );
	return $text;
}
add_filter( 'cyberchimps_help_heading', 'cyberchimps_options_help_header' );
add_filter( 'cyberchimps_help_sub_heading', 'cyberchimps_options_help_sub_header' );

// Branding images and defaults

//theme header options
function business_header_drag_options() {
	$options = array(
						'cyberchimps_logo_search'		=> __( 'Logo + Search', 'cyberchimps' ),
						'cyberchimps_logo'				=> __( 'Logo', 'cyberchimps' ),
						'cyberchimps_sitename_contact'	=> __( 'Logo + Contact', 'cyberchimps' )
					);
	return $options;
}
add_action( 'header_drag_and_drop_options', 'business_header_drag_options' );
//theme header defaults
function business_header_drag_default() {
	$default = array(
										'cyberchimps_sitename_contact'	=> __( 'Logo + Contact', 'cyberchimps' )
									);
	return $default;
}
add_action( 'header_drag_and_drop_default', 'business_header_drag_default' );

// Banner default
function cyberchimps_banner_default() {
	$url = '/images/branding/banner.jpg';
	return $url;
}
add_filter( 'cyberchimps_banner_img', 'cyberchimps_banner_default' );

//theme specific skin options in array. Must always include option default
function cyberchimps_skin_color_options( $options ) {
	$imagepath = get_template_directory_uri(). '/inc/css/skins/images/';
	$options = array(
		'default'	=> $imagepath . 'default.png',
		'blue'		=> $imagepath . 'blue.png',
		'green'		=> $imagepath . 'green.png',
		'orange'		=> $imagepath . 'orange.png'
		);
	return $options;
}
add_filter( 'cyberchimps_skin_color', 'cyberchimps_skin_color_options' );

// theme specific background images
function cyberchimps_background_image( $options ) {
	$imagepath =  get_template_directory_uri() . '/cyberchimps/lib/images/';
	$options = array(
			'none' => $imagepath . 'backgrounds/thumbs/none.png',
			'noise' => $imagepath . 'backgrounds/thumbs/noise.png',
			'blue' => $imagepath . 'backgrounds/thumbs/blue.png',
			'dark' => $imagepath . 'backgrounds/thumbs/dark.png',
			'space' => $imagepath . 'backgrounds/thumbs/space.png'
			);
	return $options;
}
add_filter( 'cyberchimps_background_image', 'cyberchimps_background_image' );

// theme specific typography options
function cyberchimps_typography_sizes( $sizes ) {
	$sizes = array( '8','9','10','12','14','16','20' );
	return $sizes;
}
function cyberchimps_typography_faces( $faces ) {
	$faces = array(
				'Arial, Helvetica, sans-serif'						 => 'Arial',
				'Arial Black, Gadget, sans-serif'					 => 'Arial Black',
				'Comic Sans MS, cursive'							 => 'Comic Sans MS',
				'Courier New, monospace'							 => 'Courier New',
				'Georgia, serif'									 => 'Georgia',
				'Impact, Charcoal, sans-serif'						 => 'Impact',
				'Lucida Console, Monaco, monospace'					 => 'Lucida Console',
				'Lucida Sans Unicode, Lucida Grande, sans-serif'	 => 'Lucida Sans Unicode',
				'Palatino Linotype, Book Antiqua, Palatino, serif'	 => 'Palatino Linotype',
				'Tahoma, Geneva, sans-serif'						 => 'Tahoma',
				'Times New Roman, Times, serif'						 => 'Times New Roman',
				'Trebuchet MS, sans-serif'							 => 'Trebuchet MS',
				'Verdana, Geneva, sans-serif'						 => 'Verdana',
				'Symbol'											 => 'Symbol',
				'Webdings'											 => 'Webdings',
				'Wingdings, Zapf Dingbats'							 => 'Wingdings',
				'MS Sans Serif, Geneva, sans-serif'					 => 'MS Sans Serif',
				'MS Serif, New York, serif'							 => 'MS Serif',
				);
	return $faces;
}
function cyberchimps_typography_styles( $styles ) {
	$styles = array( 'normal' => 'Normal','bold' => 'Bold' );
	return $styles;
}
add_filter( 'cyberchimps_typography_sizes', 'cyberchimps_typography_sizes' );
add_filter( 'cyberchimps_typography_faces', 'cyberchimps_typography_faces' );
add_filter( 'cyberchimps_typography_styles', 'cyberchimps_typography_styles' );

//add option for header image
function cyberchimps_add_theme_options( $original ) {
	$new_field[][1] = array( 
													'name' => __('Header Image', 'cyberchimps'),
													'id' => 'header_image',
													'std'			=> get_template_directory_uri() . '/images/header.jpg',
													'type' => 'upload',
													'desc' => __( 'The image used for the header needs to be a large image. We recommend a width of 1200px and height of 550px', 'cyberchimps' ),
													'section' => 'cyberchimps_header_options_section',
													'heading' => 'cyberchimps_header_heading'
												 );
	$new_fields = cyberchimps_array_field_organizer( $original, $new_field );
	return $new_fields;
}
add_filter( 'cyberchimps_field_filter', 'cyberchimps_add_theme_options', 10 );

// add featured image size
if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'business', 999, 300, true );
}
function cyberchimps_featured_image_size() {
	$size = 'business';
	return $size;
}
add_filter( 'cyberchimps_post_thumbnail_size', 'cyberchimps_featured_image_size' );

//sidebar options
function business_sidebar_options() {
	$imagepath =  get_template_directory_uri() . '/cyberchimps/lib/images/';
	$options = array(
										'full_width' => $imagepath . '1col.png',
										'right_sidebar' => $imagepath . '2cr.png',
										'left_sidebar' => $imagepath . '2cl.png'
									);
	return $options;
}
add_filter( 'sidebar_layout_options', 'business_sidebar_options' );

function cyberchimps_get_layout( $layout_type ) {
	
	$layout_type = ( $layout_type ) ? $layout_type : 'right_sidebar';
	
		switch($layout_type) {
			case 'full_width' :
				add_filter( 'cyberchimps_content_class', 'cyberchimps_class_span12');
			break;
			case 'right_sidebar' :
				add_action( 'cyberchimps_after_content_container', 'cyberchimps_add_sidebar_right');
				add_filter( 'cyberchimps_content_class', 'cyberchimps_class_span8');
				add_filter( 'cyberchimps_content_class', 'cyberchimps_content_sbr_class' );
				add_filter( 'cyberchimps_sidebar_right_class', 'cyberchimps_class_span4');
			break;
			case 'left_sidebar' :
				add_action( 'cyberchimps_before_content_container', 'cyberchimps_add_sidebar_left');
				add_filter( 'cyberchimps_content_class', 'cyberchimps_class_span8');
				add_filter( 'cyberchimps_content_class', 'cyberchimps_content_sbl_class' );
				add_filter( 'cyberchimps_sidebar_left_class', 'cyberchimps_class_span4');
			break;
		}
}

/*************************** SOCIAL OPTIONS CUSTOMIZATION STARTS *******************************/

// Remove social option section from header
function cyberchimps_remove_section( $orig ) {
	$new_sections = cyberchimps_remove_options( $orig, array( 'cyberchimps_header_social_section' ) );
	return $new_sections;
}
add_filter( 'cyberchimps_sections_filter', 'cyberchimps_remove_section' );

// Add social option section to footer.
function cyberchimps_add_section( $original ) {
	$new_section[][1] = array( 
							'id'		=> 'cyberchimps_footer_social_section',
							'label'		=> __('Social Options', 'cyberchimps'),
							'heading'	=> 'cyberchimps_footer_heading'
							);
	
	$new_sections = cyberchimps_array_section_organizer( $original, $new_section );
	
	return $new_sections;
}
add_filter( 'cyberchimps_sections_filter', 'cyberchimps_add_section' );

// Add social options to social section of footer.
function cyberchimps_add_social_options( $original ) {

	$imagepath =  get_template_directory_uri() . '/cyberchimps/lib/images/';
	
	/********** SOCIAL OPTIONS STARTS ************/
	$fields_list[][1]	= array(
		'name'		=> __('Choose your icon style', 'cyberchimps'),
		'id'		=> 'theme_backgrounds',
		'std'		=> apply_filters( 'cyberchimps_social_icon_default', 'default' ),
		'type'		=> 'images',
		'options'	=> apply_filters( 'cyberchimps_social_icon_options', array(
									'default'	=> $imagepath . 'social/thumbs/icons-default.png',
									'legacy'	=> $imagepath . 'social/thumbs/icons-classic.png',
									'round'		=> $imagepath . 'social/thumbs/icons-round.png' 
									) ),
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
	
	$fields_list[][2]	= array(
		'name'		=> __('Twitter', 'cyberchimps'),
		'id' 		=> 'social_twitter',
		'std'		=> 'checked',
		'type'		=> 'toggle',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
		
	$fields_list[][3]	= array(
		'name'		=> __('Twitter URL', 'cyberchimps'),
		'id'		=> 'twitter_url',
		'class'		=> 'social_twitter_toggle',
		'std'		=> 'http://www.twitter.com/',
		'type'		=> 'text',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
	
	$fields_list[][4]	= array(
		'name'		=> __('Facebook', 'cyberchimps'),
		'id'		=> 'social_facebook',
		'std'		=> 'checked',
		'type'		=> 'toggle',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
		
	$fields_list[][5]	= array(
		'name'		=> __('Facebook URL', 'cyberchimps'),
		'id'		=> 'facebook_url',
		'class'		=> 'social_facebook_toggle',
		'std'		=> 'http://www.facebook.com/',
		'type'		=> 'text',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
	
	$fields_list[][6]	= array(
		'name'		=> __('Google+', 'cyberchimps'),
		'id'		=> 'social_google',
		'std'		=> 'checked',
		'type'		=> 'toggle',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
		
	$fields_list[][7]	= array(
		'name'		=> __('Google+ URL', 'cyberchimps'),
		'id'		=> 'google_url',
		'class'		=> 'social_google_toggle',
		'std'		=> 'http://www.google.com/',
		'type'		=> 'text',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
	
	$fields_list[][8]	= array(
		'name'		=> __('Flickr', 'cyberchimps'),
		'id'		=> 'social_flickr',
		'type'		=> 'toggle',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
		
	$fields_list[][9]	= array(
		'name'		=> __('Flickr URL', 'cyberchimps'),
		'id'		=> 'flickr_url',
		'class'		=> 'social_flickr_toggle',
		'std'		=> 'http://www.flickr.com/',
		'type'		=> 'text',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
	
	$fields_list[][10]	= array(
		'name'		=> __('Pinterest', 'cyberchimps'),
		'id'		=> 'social_pinterest',
		'type'		=> 'toggle',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
		
	$fields_list[][11]	= array(
		'name'		=> __('Pinterest URL', 'cyberchimps'),
		'id'		=> 'pinterest_url',
		'class'		=> 'social_pinterest_toggle',
		'std'		=> 'http://www.pinterest.com/',
		'type'		=> 'text',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
	
	$fields_list[][12]	= array(
		'name'		=> __('LinkedIn', 'cyberchimps'),
		'id'		=> 'social_linkedin',
		'type'		=> 'toggle',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
		
	$fields_list[][13] = array(
		'name'		=> __('LinkedIn URL', 'cyberchimps'),
		'id'		=> 'linkedin_url',
		'class'		=> 'social_linkedin_toggle',
		'std'		=> 'http://www.linkedin.com/',
		'type'		=> 'text',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
	
	$fields_list[][14]	= array(
		'name'		=> __('YouTube', 'cyberchimps'),
		'id'		=> 'social_youtube',
		'type'		=> 'toggle',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
		
	$fields_list[][15]	= array(
		'name'		=> __('YouTube URL', 'cyberchimps'),
		'id'		=> 'youtube_url',
		'class'		=> 'social_youtube_toggle',
		'std'		=> 'http://www.youtube.com/',
		'type'		=> 'text',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
	
	$fields_list[][16]	= array(
		'name'		=> __('RSS', 'cyberchimps'),
		'id'		=> 'social_rss',
		'type'		=> 'toggle',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
		
	$fields_list[][17]	= array(
		'name'		=> __('RSS URL', 'cyberchimps'),
		'id'		=> 'rss_url',
		'class'		=> 'social_rss_toggle',
		'std'		=> get_bloginfo_rss( 'rss_url' ),
		'type'		=> 'text',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
	
	$fields_list[][18]	= array(
		'name'		=> __('Email', 'cyberchimps'),
		'id'		=> 'social_email',
		'type'		=> 'toggle',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
		
	$fields_list[][19]	= array(
		'name'		=> __('Email Address', 'cyberchimps'),
		'id'		=> 'email_url',
		'class'		=> 'social_email_toggle',
		'type'		=> 'text',
		'section'	=> 'cyberchimps_footer_social_section',
		'heading'	=> 'cyberchimps_footer_heading'
	);
	/********** SOCIAL OPTIONS ENDS ************/					
											
	$fields_list = cyberchimps_array_field_organizer( $original, $fields_list );
	return $fields_list;
}
add_filter( 'cyberchimps_field_filter', 'cyberchimps_add_social_options' );

/*************************** SOCIAL OPTIONS CUSTOMIZATION ENDS *******************************/
?>