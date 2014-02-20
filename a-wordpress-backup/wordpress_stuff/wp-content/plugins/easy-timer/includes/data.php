<?php global $easy_timer_options;
if (empty($easy_timer_options)) { $easy_timer_options = (array) get_option('easy_timer'); }
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; }
else {
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
$field = (isset($atts[0]) ? $atts[0] : '');
foreach (array('default', 'filter') as $key) { $$key = (isset($atts[$key]) ? $atts[$key] : ''); } }
$field = str_replace('-', '_', easy_timer_format_nice_name($field));
$data = (isset($easy_timer_options[$field]) ? $easy_timer_options[$field] : '');
$data = (string) do_shortcode($data);
if ($data === '') { $data = $default; }
$data = easy_timer_filter_data($filter, $data);