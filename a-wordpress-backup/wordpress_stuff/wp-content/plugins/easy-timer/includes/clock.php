<?php if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
if (easy_timer_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'format' => '', 'offset' => ''), $atts));
if ($format == '') { $format = (isset($atts['form']) ? $atts['form'] : ''); }
$offset = strtolower($offset); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); break;
case 'local': break;
default: $offset = round(str_replace(',', '.', $offset), 2); }
$T = extract_timestamp($offset);

$format = strtolower($format); switch ($format) {
case 'hms': $clock = date('H:i:s', $T); break;
default: $format = 'hm'; $clock = date('H:i', $T); }
$clock = easy_timer_filter_data($filter, $clock);

if (is_numeric($offset)) { $clock = '<span class="'.$format.'clock" data-offset="'.$offset.'">'.$clock.'</span>'; }
else { $clock = '<span class="local'.$format.'clock">'.$clock.'</span>'; }