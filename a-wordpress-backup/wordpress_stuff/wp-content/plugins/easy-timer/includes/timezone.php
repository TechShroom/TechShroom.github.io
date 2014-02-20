<?php $atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'offset' => ''), $atts));
$offset = strtolower($offset); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); break;
case 'local': break;
default: $offset = round(str_replace(',', '.', $offset), 2); }

if (is_numeric($offset)) {
if ($offset == 0) { $timezone = 'UTC'; }
elseif ($offset > 0) { $timezone = 'UTC+'.$offset; }
elseif ($offset < 0) { $timezone = 'UTC'.$offset; }
$timezone = easy_timer_filter_data($filter, $timezone); }

else {
if (easy_timer_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
$timezone = '<span class="localtimezone">UTC</span>'; }