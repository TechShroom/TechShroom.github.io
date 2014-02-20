<?php

include ('../../../wp-config.php');

if (!current_user_can ('edit_plugins')) {
	die ('<p style="color: red">You are not allowed access to this resource</p>');
}

$group_id   = intval($_GET['id']);
$group_info = $wpdb->get_row("SELECT * FROM {$WP_SENTRY->group_table_name} WHERE id=$group_id");
$sub_group_info = $wpdb->get_results("SELECT * FROM {$WP_SENTRY->group_table_name} WHERE FIND_IN_SET($group_id, member_of)");

$group_members = explode(',', $group_info->member_list);
$sub_group_members = array();

foreach ($sub_group_info as $sg) {
	$sg_members = explode(',', $sg->member_list);
	foreach($sg_members as $m) {
		if (!in_array($m, $sub_group_members)) {
			$sub_group_members[] = $m;
		}
	}
}

$blog_users_list = get_users_of_blog();
$group_users_list['direct']['title_row'] = "Members of '{$group_info->name}'";
$group_users_list['child']['title_row'] = "Members of sub-groups of '{$group_info->name}'";
$header_row = "ID, Login, Display Name, Email Address";

// Populate lists
foreach ($blog_users_list as $u) {
	if (in_array($u->ID, $group_members)) {
		$group_users_list['direct']['members'][] = $u;
	} elseif (in_array($u->ID, $sub_group_members)) {
		$group_users_list['child']['members'][] = $u;
	}
}

header ("Content-Type: application/vnd.ms-excel");
header ("Cache-Control: no-cache, must-revalidate");
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ('Content-Disposition: attachment; filename="wp-sentry-group.csv"');
foreach($group_users_list as $gul) {
	echo("$gul[title_row]\n");
	echo("$header_row\n");
	if (is_array($gul['members'])) {foreach($gul['members'] as $m) {
		echo("{$m->ID}, {$m->user_login}, {$m->display_name}, {$m->user_email}\n");
	}} else {
		echo("None found.");
	}
	echo("\n");
}