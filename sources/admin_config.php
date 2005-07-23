<?php

/*
	Copyright (C) 2003-2005 UseBB Team
	http://www.usebb.net
	
	$Header$
	
	This file is part of UseBB.
	
	UseBB is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	UseBB is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with UseBB; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

//
// Easily check if necessary fields are filled in
//
$filled_in = true;
$necessary_settings = array(
	'strings' => array('type', 'server', 'username', 'passwd', 'dbname', 'prefix', 'admin_email', 'board_descr', 'board_keywords', 'board_name', 'date_format', 'language', 'session_name', 'template'),
	'integers' => array('active_topics_count', 'email_view_level', 'flood_interval', 'members_per_page', 'online_min_updated', 'passwd_min_length', 'posts_per_page', 'search_nonindex_words_min_length', 'session_max_lifetime', 'show_edited_message_timeout', 'topicreview_posts', 'topics_per_page', 'username_max_length', 'view_detailed_online_list_min_level', 'view_forum_stats_box_min_level', 'view_hidden_email_addresses_min_level', 'view_memberlist_min_level', 'view_stafflist_min_level', 'view_stats_min_level', 'view_contactadmin_min_level')
);
foreach ( $necessary_settings['strings'] as $key ) {
	
	if ( empty($_POST[$key]) )
		$filled_in = false;
	
}
foreach ( $necessary_settings['integers'] as $key ) {
	
	if ( !isset($_POST[$key]) || !valid_int($_POST[$key]) )
		$filled_in = false;
	
}
$levels = array(
	LEVEL_GUEST,
	LEVEL_MEMBER,
	LEVEL_MOD,
	LEVEL_ADMIN
);

if ( $filled_in && preg_match(EMAIL_PREG, $_POST['admin_email']) && in_array($_POST['email_view_level'], array(0, 1, 2, 3)) && in_array($_POST['language'], $functions->get_language_packs()) && in_array($_POST['template'], $functions->get_template_sets()) && isset($_POST['timezone']) && $functions->timezone_handler('check_existance', $_POST['timezone']) && in_array(intval($_POST['view_detailed_online_list_min_level']), $levels) && in_array(intval($_POST['view_forum_stats_box_min_level']), $levels) && in_array(intval($_POST['view_hidden_email_addresses_min_level']), $levels) && in_array(intval($_POST['view_memberlist_min_level']), $levels) && in_array(intval($_POST['view_stafflist_min_level']), $levels) && in_array(intval($_POST['view_stats_min_level']), $levels) && in_array(intval($_POST['view_contactadmin_min_level']), $levels) ) {
	
	$new_settings = array();
	
	//
	// Necessary settings represented as strings
	//
	foreach ( $necessary_settings['strings'] as $setting )
		$new_settings[$setting] = stripslashes($_POST[$setting]);
	
	//
	// Necessary settings represented as integers
	//
	foreach ( $necessary_settings['integers'] as $setting )
		$new_settings[$setting] = intval($_POST[$setting]);
	
	//
	// Settings which can be enabled or disabled
	//
	foreach ( array('allow_multi_sess', 'board_closed', 'cookie_secure', 'disable_info_emails', 'dst', 'enable_contactadmin', 'enable_detailed_online_list', 'enable_forum_stats_box', 'enable_memberlist', 'enable_quickreply', 'enable_rss', 'enable_stafflist', 'enable_stats', 'friendly_urls', 'guests_can_access_board', 'guests_can_view_profiles', 'hide_avatars', 'hide_signatures', 'hide_userinfo', 'rel_nofollow', 'return_to_topic_after_posting', 'sig_allow_bbcode', 'sig_allow_smilies', 'single_forum_mode', 'target_blank', 'users_must_activate') as $setting )
		$new_settings[$setting] = ( !empty($setting) ) ? 1 : 0;
	
	$new_settings['timezone'] = (float)$_POST['timezone'];
	
	$admin_functions->set_config($new_settings);
	
}

$admin_functions->create_body('config', '');

?>
