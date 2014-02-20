<?php if (!function_exists('adodb_mktime')) { include_once EASY_TIMER_PATH.'libraries/adodb-time.php'; }
global $blog_id, $post;
if (!isset($blog_id)) { $blog_id = 1; }
if ((!isset($post)) || (!is_object($post))) { $post_id = 0; }
else { $post_id = $post->ID; }
$id = $blog_id.'-'.$post_id;
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('date' => '', 'delimiter' => '', 'filter' => '', 'offset' => '', 'origin' => '', 'period' => '', 'way' => ''), $atts));
switch ($origin) {
case 'last': case 'last-visit': $origin = 'last-visit'; break;
default: $origin = 'first-visit'; }
if ($way != 'down') { $way = 'up'; }
if ($delimiter == 'before') { $delimiter = '[before]'; } else { $delimiter = '[after]'; }

if ((substr($date, 0, 1) == '-') || (strstr($date, '//-')) || (strstr($date, '+'))) {
if (($origin == 'first-visit') && (!isset($_COOKIE['first-visit-'.$id]))) { global $easy_timer_cookies; $easy_timer_cookies[$id] = $id; } }

if ($delimiter == '[after]') { $date = '0//'.$date; } else { $date = $date.'//0'; }
$date = explode('//', $date);
if ($delimiter == '[before]') { $date = array_reverse($date); }
$n = count($date);

$time = time();
$S = array(0); $T = array($time);

if ($period != '') {
$period = preg_split('#[^0-9]#', $period, 0, PREG_SPLIT_NO_EMPTY);
for ($j = 0; $j < 4; $j++) { $period[$j] = (int) (isset($period[$j]) ? $period[$j] : 0); }
$P = 86400*$period[0] + 3600*$period[1] + 60*$period[2] + $period[3];
if ($P > 0) {
if ($delimiter == '[after]') { $last_date = $date[$n - 1]; } else { $last_date = $date[1]; }
$last_date = preg_split('#[^0-9]#', $last_date, 0, PREG_SPLIT_NO_EMPTY);
for ($j = 0; $j < 6; $j++) { $last_date[$j] = (int) (isset($last_date[$j]) ? $last_date[$j] : 0); }
$last_T = adodb_mktime($last_date[3], $last_date[4], $last_date[5], $last_date[1], $last_date[2], $last_date[0]) - extract_offset($offset);
if ($delimiter == '[after]') { $last_S = $time - $last_T; } else { $last_S = $last_T - $time; }
if ($last_S > 0) { $r = ceil($last_S/$P); } } }

$is_positive = array(false);
$is_negative = array(false);
$is_relative = array(false);

for ($i = 1; $i < $n; $i++) {
	if (substr($date[$i], 0, 1) == '+') { $is_positive[$i] = true; } else { $is_positive[$i] = false; }
	if (substr($date[$i], 0, 1) == '-') { $is_negative[$i] = true; } else { $is_negative[$i] = false; }
	$is_relative[$i] = (($is_positive[$i]) || ($is_negative[$i]));
	$date[$i] = preg_split('#[^0-9]#', $date[$i], 0, PREG_SPLIT_NO_EMPTY);
	$original_date[$i] = $date[$i];
	for ($j = 0; $j < 6; $j++) { $date[$i][$j] = (int) (isset($date[$i][$j]) ? $date[$i][$j] : 0); }
	
	if ($is_relative[$i]) {
	if (($origin == 'first-visit') && (isset($_COOKIE['first-visit-'.$id]))) { $origin_time = (int) $_COOKIE['first-visit-'.$id]; }
	else { $origin_time = $time; }
	$S[$i] = 86400*$date[$i][0] + 3600*$date[$i][1] + 60*$date[$i][2] + $date[$i][3];
	if ($is_positive[$i]) { $S[$i] = $time - $origin_time - $S[$i]; }
	if ($is_negative[$i]) { $S[$i] = $time - $origin_time + $S[$i]; }
	$T[$i] = $time - $S[$i]; }
	
	else {
	switch (count($original_date[$i])) {
	case 0: case 1: $S[$i] = $date[$i][0]; $T[$i] = $time - $S[$i]; break;
	case 2: $S[$i] = 60*$date[$i][0] + $date[$i][1]; $T[$i] = $time - $S[$i]; break;
	default:
	$T[$i] = adodb_mktime($date[$i][3], $date[$i][4], $date[$i][5], $date[$i][1], $date[$i][2], $date[$i][0]) - extract_offset($offset);
	foreach (array('P', 'r') as $variable) { if (!isset($$variable)) { $$variable = 0; } }
	if ($delimiter == '[after]') { $T[$i] = $T[$i] + $r*$P; } else { $T[$i] = $T[$i] - $r*$P; }
	$S[$i] = $time - $T[$i]; } }
}

$i = 0; while (($i < $n) && ($S[$i] >= 0)) { $k = $i; $i = $i + 1; }
if ($i == $n) { $i = $n - 1; }

$content = do_shortcode($content);
if (!strstr($content, $delimiter)) { $content = $content.$delimiter; }
$content = explode($delimiter, $content);
if ($delimiter == '[before]') { $content = array_reverse($content); }
if (!isset($content[$k])) { $content[$k] = ''; }

if ((easy_timer_data('javascript_enabled') == 'yes') && (strstr($content[$k], 'timer]'))) {
add_action('wp_footer', 'easy_timer_lang_js');
add_action('wp_footer', 'easy_timer_js'); }

if ($way == 'up') {
$content[$k] = timer_replace($S[$k], $T[$k], '', 'up', $content[$k]);
$content[$k] = timer_replace($S[1], $T[1], 'total-', 'up', $content[$k]); }
if ($way == 'down') {
$content[$k] = timer_replace(-$S[$i], $T[$i], '', 'down', $content[$k]);
$content[$k] = timer_replace(-$S[$n - 1], $T[$n - 1], 'total-', 'down', $content[$k]); }

$content[$k] = timer_replace($S[$k], $T[$k], 'elapsed-', 'up', $content[$k]);
$content[$k] = timer_replace($S[1], $T[1], 'total-elapsed-', 'up', $content[$k]);
$content[$k] = timer_replace(-$S[$i], $T[$i], 'remaining-', 'down', $content[$k]);
$content[$k] = timer_replace(-$S[$n - 1], $T[$n - 1], 'total-remaining-', 'down', $content[$k]);

$content[$k] = easy_timer_filter_data($filter, $content[$k]);