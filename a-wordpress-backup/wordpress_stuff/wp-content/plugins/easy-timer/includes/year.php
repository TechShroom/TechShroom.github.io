<?php if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'format' => '', 'offset' => ''), $atts));
if ($format == '') { $format = (isset($atts['form']) ? $atts['form'] : ''); }
$T = extract_timestamp($offset);

switch ($format) {
case '2': $year = date('y', $T); break;
default: $format = '4'; $year = date('Y', $T); }
$year = easy_timer_filter_data($filter, $year);

if (strtolower($offset) == 'local') {
if (easy_timer_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
$year = '<span class="local'.$format.'year">'.$year.'</span>'; }