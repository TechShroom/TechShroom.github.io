<?php
/**
 * Callout element
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

// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }

if ( !class_exists( 'CyberChimpsCallout' ) ) {
	class CyberChimpsCallout {
		
		protected static $instance;
		public $options;
		
		/* Static Singleton Factory Method */
		public static function instance() {
			if (!isset(self::$instance)) {
				$className = __CLASS__;
				self::$instance = new $className;
			}
			return self::$instance;
		}	
		
		/**
		 * Initializes plugin variables and sets up WordPress hooks/actions.
		 *
		 * @return void
		 */
		protected function __construct( ) {
			add_action( 'callout_section', array( $this, 'render_display' ) );
			$this->options = get_option( 'cyberchimps_options' );
		}

		public function render_display() {
		
			// Declare global variable.
			global $post;
			
			// Get all callout options.
			if( is_page() ){
				$callout_title = (get_post_meta($post->ID, 'callout_title', true)) ? get_post_meta($post->ID, 'callout_title', true) : '';
				$callout_text = (get_post_meta($post->ID, 'callout_text', true)) ? get_post_meta($post->ID, 'callout_text', true) : '';
				$callout_button = (get_post_meta($post->ID, 'disable_callout_button', true)) ? get_post_meta($post->ID, 'disable_callout_button', true) : '';
				$callout_button_text = (get_post_meta($post->ID, 'callout_button_text', true)) ? get_post_meta($post->ID, 'callout_button_text', true) : '';
				$callout_button_url = (get_post_meta($post->ID, 'callout_url', true)) ? get_post_meta($post->ID, 'callout_url', true) : '';
				$custom_callout_options = (get_post_meta($post->ID, 'extra_callout_options', true)) ? get_post_meta($post->ID, 'extra_callout_options', true) : '';
				$custom_callout_button = (get_post_meta($post->ID, 'callout_image', true)) ? get_post_meta($post->ID, 'callout_image', true) : ''; 
				$custom_callout_background = (get_post_meta($post->ID, 'custom_callout_color', true)) ? 'background-color:'.get_post_meta($post->ID, 'custom_callout_color', true) : '';
				$custom_callout_title_color = (get_post_meta($post->ID, 'custom_callout_title_color', true)) ? 'color:'.get_post_meta($post->ID, 'custom_callout_title_color', true) : '';
				$custom_callout_text_color = (get_post_meta($post->ID, 'custom_callout_text_color', true)) ? 'color:'.get_post_meta($post->ID, 'custom_callout_text_color', true) : '';
				$custom_callout_button_color = (get_post_meta($post->ID, 'custom_callout_button_color', true)) ? 'background:'.get_post_meta($post->ID, 'custom_callout_button_color', true) : '';
				$custom_callout_button_text_color = (get_post_meta($post->ID, 'custom_callout_button_text_color', true)) ? 'color:'.get_post_meta($post->ID, 'custom_callout_button_text_color', true) : '';
			}
			else {
				$callout_title = $this->options['callout_title'];
				$callout_text = $this->options['callout_text'];
				$callout_button = $this->options['callout_button'];
				$callout_button_text = ( $this->options['callout_button_text'] != '' ) ? esc_html( $this->options['callout_button_text'] ) : 'Click Here';
				$callout_button_url = $this->options['callout_button_url'];
				$custom_callout_options = $this->options['custom_callout_options'];
				$custom_callout_button = ( $custom_callout_options && $this->options['custom_callout_button'] != '' ) ? $this->options['custom_callout_button'] : ''; 
				$custom_callout_background = ( $this->options['custom_callout_background_color'] != '' ) ? 'background-color:'.$this->options['custom_callout_background_color'] : '';
				$custom_callout_title_color = ( $this->options['custom_callout_title_color'] != '' ) ? 'color:'.$this->options['custom_callout_title_color'] : '';
				$custom_callout_text_color = ( $this->options['custom_callout_text_color'] != '' ) ? 'color:'.$this->options['custom_callout_text_color'] : '';
				$custom_callout_button_color = ( $this->options['custom_callout_button_color'] != '' ) ? 'background:'.$this->options['custom_callout_button_color'] : '';
				$custom_callout_button_text_color = ( $this->options['custom_callout_button_text_color'] != '' ) ? 'color:'.$this->options['custom_callout_button_text_color'] : '';
			}
		
			// Set all custom options to empty string if the custom option toggle is checked off
			if( empty( $custom_callout_options ) ) {
				$custom_callout_background			= "";
				$custom_callout_title_color			= "";
				$custom_callout_text_color			= "";
				$custom_callout_button_color		= "";
				$custom_callout_button_text_color	= "";
			}
			
			// Check if callout button is present.
			if( empty( $callout_button ) ) {
			?>
				<!-- Callout without button -->
				<div id="callout-container" class="row-fluid">
					<div id="callout" class="span12" style="<?php echo $custom_callout_background; ?>">
						<div class="callout-text">
							<h2 class="callout-title" style="<?php echo $custom_callout_title_color; ?>">
								<?php echo esc_html( $callout_title ); ?>
							</h2>
							<p style="<?php echo $custom_callout_text_color; ?>">
								<?php echo esc_html( $callout_text ); ?>
							</p>
						</div><!-- #callout-text -->
					</div><!-- .row-fluid .span12 -->
				</div><!-- row-fluid -->
			<?php
			}
			else {
			?>
				<!-- Callout with button -->
				<div id="callout-container" class="row-fluid">
					<div id="callout" class="span12" style="<?php echo $custom_callout_background; ?>">
						<div class="row-fluid">
							<div class="span9">
								<div class="callout-text">
									<h2 class="callout-title" style="<?php echo $custom_callout_title_color; ?>">
										<?php echo esc_html( $callout_title ); ?>
									</h2>
									<p style="<?php echo $custom_callout_text_color; ?>">
										<?php echo wp_kses_post( $callout_text ); ?>
									</p>
								</div><!-- #callout-text -->
							</div><!-- span9 -->
							<div id="callout_button" class="span3">
								<?php
								if( $custom_callout_button == '' ) {
								?>
									<a href="<?php echo $callout_button_url; ?>" title="<?php echo $callout_button_text; ?>">
										<button style="<?php echo $custom_callout_button_color; ?>" class="btn btn-large btn-primary" type="button">
											<p style="<?php echo $custom_callout_button_text_color; ?>">
												<?php echo esc_html( $callout_button_text ); ?>
											</p>
										</button>
									</a>
								<?php
								}
								else {
								?>
									<!-- Callout button with custom image -->
									<a href="<?php echo $callout_button_url; ?>" title="<?php echo esc_html( $callout_button_text ); ?>">
										<img src="<?php echo $custom_callout_button; ?>" alt="<?php echo esc_html( $custom_callout_text ); ?>" />
									</a>
								<?php
								}
								?>
							</div><!-- span3 -->
						</div><!-- row-fluid -->
					</div><!-- .row-fluid .span12 -->
				</div><!-- row-fluid -->
			<?php
			}
		}
	}
}
CyberChimpsCallout::instance();
?>