<?php if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'format' => '', 'offset' => ''), $atts));
if ($format == '') { $format = (isset($atts['form']) ? $atts['form'] : ''); }
$T = extract_timestamp($offset);
$w = date('w', $T);

$format = strtolower($format); switch ($format) {
case 'lower': case 'upper': break;
default: $format = ''; }

$stringweekday = array(
0 => __('SUNDAY', 'easy-timer'),
1 => __('MONDAY', 'easy-timer'),
2 => __('TUESDAY', 'easy-timer'),
3 => __('WEDNESDAY', 'easy-timer'),
4 => __('THURSDAY', 'easy-timer'),
5 => __('FRIDAY', 'easy-timer'),
6 => __('SATURDAY', 'easy-timer'),
7 => __('SUNDAY', 'easy-timer'));

if ($format == '') { $weekday = ucfirst(strtolower($stringweekday[$w])); }
elseif ($format == 'lower') { $weekday = strtolower($stringweekday[$w]); }
elseif ($format == 'upper') { $weekday = $stringweekday[$w]; }
$weekday = easy_timer_filter_data($filter, $weekday);

if (strtolower($offset) == 'local') {
if (easy_timer_data('javascript_enabled') == 'yes') {
add_action('wp_footer', 'easy_timer_lang_js');
add_action('wp_footer', 'easy_timer_js'); }
$weekday = '<span class="local'.$format.'weekday">'.$weekday.'</span>'; }