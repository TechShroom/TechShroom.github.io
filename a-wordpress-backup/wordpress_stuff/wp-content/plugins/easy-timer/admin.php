<?php if (((isset($_GET['page'])) && (strstr($_GET['page'], 'easy-timer'))) || (strstr($_SERVER['REQUEST_URI'], '/plugins.php'))) {
load_plugin_textdomain('easy-timer', false, EASY_TIMER_FOLDER.'/languages'); }


function easy_timer_options_page() {
add_options_page('Easy Timer', 'Easy Timer', 'manage_options', 'easy-timer', create_function('', 'include_once EASY_TIMER_PATH."options-page.php";')); }

add_action('admin_menu', 'easy_timer_options_page');


function easy_timer_options_page_css() { ?>
<style type="text/css">
.wrap h2 { float: left; }
.wrap input.button-secondary, .wrap select { vertical-align: 0; }
.wrap p.submit { margin: 0 20%; }
.wrap ul.subsubsub { margin: 1em 0 1.5em 6em; float: left; white-space: normal; }
</style>
<?php }

if ((isset($_GET['page'])) && (strstr($_GET['page'], 'easy-timer'))) { add_action('admin_head', 'easy_timer_options_page_css'); }


function easy_timer_meta_box($post) {
load_plugin_textdomain('easy-timer', false, EASY_TIMER_FOLDER.'/languages');
$links = array(
'' => __('Documentation', 'easy-timer'),
'#countdown-timers' => __('Display a countdown timer', 'easy-timer'),
'#date' => __('Display the time or the date', 'easy-timer'),
'#screen-options-wrap' => __('Hide this box', 'easy-timer')); ?>
<p><a target="_blank" href="http://www.kleor.com/easy-timer/"><?php echo $links['']; ?></a>
 | <a style="color: #808080;" href="#screen-options-wrap" onclick="document.getElementById('show-settings-link').click(); document.getElementById('easy-timer-hide').click();"><?php echo $links['#screen-options-wrap']; ?></a></p>
<ul>
<?php foreach (array('', '#screen-options-wrap') as $url) { unset($links[$url]); }
foreach ($links as $url => $text) {
echo '<li><a target="_blank" href="http://www.kleor.com/easy-timer/'.$url.'">'.$text.'</a></li>'; } ?>
</ul>
<?php }

add_action('add_meta_boxes', create_function('', 'foreach (array("page", "post") as $type) {
add_meta_box("easy-timer", "Easy Timer", "easy_timer_meta_box", $type, "side"); }'));


function easy_timer_action_links($links) {
if (!is_network_admin()) {
$links = array_merge($links, array(
'<span class="delete"><a href="options-general.php?page=easy-timer&amp;action=uninstall" title="'.__('Delete the options of Easy Timer', 'easy-timer').'">'.__('Uninstall', 'easy-timer').'</a></span>',
'<span class="delete"><a href="options-general.php?page=easy-timer&amp;action=reset" title="'.__('Reset the options of Easy Timer', 'easy-timer').'">'.__('Reset', 'easy-timer').'</a></span>',
'<a href="options-general.php?page=easy-timer">'.__('Options', 'easy-timer').'</a>')); }
else {
$links = array_merge($links, array(
'<span class="delete"><a href="../options-general.php?page=easy-timer&amp;action=uninstall&amp;for=network" title="'.__('Delete the options of Easy Timer for all sites in this network', 'easy-timer').'">'.__('Uninstall', 'easy-timer').'</a></span>')); }
return $links; }

foreach (array('', 'network_admin_') as $prefix) { add_filter($prefix.'plugin_action_links_'.EASY_TIMER_FOLDER.'/easy-timer.php', 'easy_timer_action_links', 10, 2); }


function easy_timer_row_meta($links, $file) {
if ($file == EASY_TIMER_FOLDER.'/easy-timer.php') {
$links = array_merge($links, array(
'<a href="http://www.kleor.com/easy-timer">'.__('Documentation', 'easy-timer').'</a>')); }
return $links; }

add_filter('plugin_row_meta', 'easy_timer_row_meta', 10, 2);


function reset_easy_timer() {
load_plugin_textdomain('easy-timer', false, EASY_TIMER_FOLDER.'/languages');
include EASY_TIMER_PATH.'initial-options.php';
update_option('easy_timer', $initial_options); }


function uninstall_easy_timer($for = 'single') { include EASY_TIMER_PATH.'includes/uninstall.php'; }