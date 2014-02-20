<?php if ((isset($_GET['action'])) && (($_GET['action'] == 'reset') || ($_GET['action'] == 'uninstall'))) {
$for = (((isset($_GET['for'])) && (is_multisite()) && (current_user_can('manage_network'))) ? $_GET['for'] : 'single');
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if ($_GET['action'] == 'reset') { reset_easy_timer(); } else { uninstall_easy_timer($for); } } ?>
<div class="wrap">
<h2>Easy Timer</h2>
<ul class="subsubsub"><li><a href="http://www.kleor.com/easy-timer"><?php _e('Documentation', 'easy-timer'); ?></a></li></ul>
<div class="clear"></div>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.($_GET['action'] == 'reset' ? __('Options reset.', 'easy-timer') : __('Options deleted.', 'easy-timer')).'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "'.($_GET['action'] == 'reset' ? 'options-general.php?page=easy-timer' : ($for == 'network' ? 'network/' : '').'plugins.php').'"\', 2000);</script>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php if ($_GET['action'] == 'reset') { _e('Do you really want to reset the options of Easy Timer?', 'easy-timer'); }
elseif ($for == 'network') { _e('Do you really want to permanently delete the options of Easy Timer for all sites in this network?', 'easy-timer'); }
else { _e('Do you really want to permanently delete the options of Easy Timer?', 'easy-timer'); } ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'easy-timer'); ?>" />
</div>
</form><?php } ?>
</div><?php }

else {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
include EASY_TIMER_PATH.'initial-options.php';
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
$_POST['cookies_lifetime'] = (int) $_POST['cookies_lifetime'];
if ($_POST['cookies_lifetime'] < 1) { $_POST['cookies_lifetime'] = $initial_options['cookies_lifetime']; }
if (!isset($_POST['javascript_enabled'])) { $_POST['javascript_enabled'] = 'no'; }
foreach ($initial_options as $key => $value) {
if ((isset($_POST[$key])) && ($_POST[$key] != '')) { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('easy_timer', $options); }
else { $options = (array) get_option('easy_timer'); }

foreach ($options as $key => $value) {
if (is_string($value)) { $options[$key] = htmlspecialchars($value); } } ?>

<div class="wrap">
<h2>Easy Timer</h2>
<ul class="subsubsub"><li><a href="http://www.kleor.com/easy-timer"><?php _e('Documentation', 'easy-timer'); ?></a></li></ul>
<div class="clear"></div>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.', 'easy-timer').'</strong></p></div>'; } ?>
<h3><?php _e('Options', 'easy-timer'); ?></h3>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<p><label><?php _e('The', 'easy-timer'); ?> <code>[timer]</code> <?php _e('shortcode is equivalent to:', 'easy-timer'); ?> 
<select name="default_timer_prefix" id="default_timer_prefix">
<?php $prefixes = array('dhms', 'dhm', 'dh', 'd', 'hms', 'hm', 'h', 'ms', 'm', 's');
foreach ($prefixes as $prefix) {
echo '<option value="'.$prefix.'"'.($options['default_timer_prefix'] == $prefix ? ' selected="selected"' : '').'>['.$prefix.'timer]</option>'."\n"; } ?>
</select></label>. <span class="description"><a href="http://www.kleor.com/easy-timer/#timer-shortcodes"><?php _e('More informations', 'easy-timer'); ?></a></span><br />
<?php $prefixes = array('total', 'elapsed', 'total-elapsed', 'remaining', 'total-remaining');
foreach ($prefixes as $prefix) {
echo __('The', 'easy-timer').' <code>['.$prefix.'-timer]</code> '.__('shortcode is equivalent to', 'easy-timer').' <code>['.$prefix.'-'.$options['default_timer_prefix'].'timer]</code>.<br />'; } ?></p>
<p><label><?php _e('Cookies lifetime (used for relative dates):', 'easy-timer'); ?> <input type="text" name="cookies_lifetime" id="cookies_lifetime" value="<?php echo $options['cookies_lifetime']; ?>" size="4" /></label> <?php _e('days', 'easy-timer'); ?>
 <span class="description"><a href="http://www.kleor.com/easy-timer/#relative-dates"><?php _e('More informations', 'easy-timer'); ?></a></span></p>
<p><label><input type="checkbox" name="javascript_enabled" id="javascript_enabled" value="yes"<?php if ($options['javascript_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Add JavaScript code', 'easy-timer'); ?><br /></label>
<span class="description"><?php _e('If you uncheck this box, Easy Timer will never add any JavaScript code to the pages of your website, but your count up/down timers will not refresh.', 'easy-timer'); ?></span></p>
<p class="submit"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes', 'easy-timer'); ?>" /></p>
</form>
</div>
<?php }