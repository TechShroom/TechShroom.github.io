<?php if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'format' => '', 'offset' => ''), $atts));
if ($format == '') { $format = (isset($atts['form']) ? $atts['form'] : ''); }
$T = extract_timestamp($offset);
$n = date('n', $T);

$format = strtolower($format); switch ($format) {
case '1': $month = $n; break;
case '2': $month = date('m', $T); break;
case 'lower': case 'upper': break;
default: $format = ''; }

if (($format == '') || ($format == 'lower') || ($format == 'upper')) {
$stringmonth = array(
0 => __('DECEMBER', 'easy-timer'),
1 => __('JANUARY', 'easy-timer'),
2 => __('FEBRUARY', 'easy-timer'),
3 => __('MARCH', 'easy-timer'),
4 => __('APRIL', 'easy-timer'),
5 => __('MAY', 'easy-timer'),
6 => __('JUNE', 'easy-timer'),
7 => __('JULY', 'easy-timer'),
8 => __('AUGUST', 'easy-timer'),
9 => __('SEPTEMBER', 'easy-timer'),
10 => __('OCTOBER', 'easy-timer'),
11 => __('NOVEMBER', 'easy-timer'),
12 => __('DECEMBER', 'easy-timer')); }

if ($format == '') { $month = ucfirst(strtolower($stringmonth[$n])); }
elseif ($format == 'lower') { $month = strtolower($stringmonth[$n]); }
elseif ($format == 'upper') { $month = $stringmonth[$n]; }
$month = easy_timer_filter_data($filter, $month);

if (strtolower($offset) == 'local') {
if (easy_timer_data('javascript_enabled') == 'yes') {
if (($format == '') || ($format == 'lower') || ($format == 'upper')) { add_action('wp_footer', 'easy_timer_lang_js'); }
add_action('wp_footer', 'easy_timer_js'); }
$month = '<span class="local'.$format.'month">'.$month.'</span>'; }