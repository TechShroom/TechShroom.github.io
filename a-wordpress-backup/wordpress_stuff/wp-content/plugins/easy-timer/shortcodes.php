<?php load_plugin_textdomain('easy-timer', false, EASY_TIMER_FOLDER.'/languages');


function clock($atts) { include EASY_TIMER_PATH.'includes/clock.php'; return $clock; }


function counter($atts, $content) { include EASY_TIMER_PATH.'includes/counter.php'; return $content[$k]; }


function countdown($atts, $content) {
$atts['way'] = 'down';
$atts['delimiter'] = 'after';
return counter($atts, $content); }


function countup($atts, $content) {
$atts['way'] = 'up';
$atts['delimiter'] = 'before';
return counter($atts, $content); }


function easy_timer_js() { ?>
<script type="text/javascript" src="<?php echo EASY_TIMER_URL; ?>libraries/easy-timer.js?version=<?php echo EASY_TIMER_VERSION; ?>"></script>
<?php }


function easy_timer_lang_js() { include EASY_TIMER_PATH.'includes/lang-js.php'; }


function extract_offset($offset) {
$offset = strtolower($offset); switch ($offset) {
case '': case 'local': $offset = 3600*get_option('gmt_offset'); break;
default: $offset = 36*(round(100*str_replace(',', '.', $offset))); }
return $offset; }


function extract_timestamp($offset) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
return time() + extract_offset($offset); }


function isoyear($atts) { include EASY_TIMER_PATH.'includes/isoyear.php'; return $isoyear; }


function month($atts) { include EASY_TIMER_PATH.'includes/month.php'; return $month; }


function monthday($atts) { include EASY_TIMER_PATH.'includes/monthday.php'; return $monthday; }


function timer_replace($S, $T, $prefix, $way, $content) { include EASY_TIMER_PATH.'includes/timer-replace.php'; return $content; }


function timer_string($S) { include EASY_TIMER_PATH.'includes/timer-string.php'; return $timer; }


function timezone($atts) { include EASY_TIMER_PATH.'includes/timezone.php'; return $timezone; }


function weekday($atts) { include EASY_TIMER_PATH.'includes/weekday.php'; return $weekday; }


function year($atts) { include EASY_TIMER_PATH.'includes/year.php'; return $year; }


function yearday($atts) { include EASY_TIMER_PATH.'includes/yearday.php'; return $yearday; }


function yearweek($atts) { include EASY_TIMER_PATH.'includes/yearweek.php'; return $yearweek; }