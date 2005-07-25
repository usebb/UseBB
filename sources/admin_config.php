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
	'integers' => array('active_topics_count', 'avatars_force_width', 'avatars_force_height', 'debug', 'email_view_level', 'flood_interval', 'members_per_page', 'online_min_updated', 'output_compression', 'passwd_min_length', 'posts_per_page', 'rss_items_count', 'search_nonindex_words_min_length', 'session_max_lifetime', 'show_edited_message_timeout', 'topicreview_posts', 'topics_per_page', 'username_max_length', 'view_detailed_online_list_min_level', 'view_forum_stats_box_min_level', 'view_hidden_email_addresses_min_level', 'view_memberlist_min_level', 'view_stafflist_min_level', 'view_stats_min_level', 'view_contactadmin_min_level')
);
foreach ( $necessary_settings['strings'] as $key ) {
	
	if ( empty($_POST['conf-'.$key]) )
		$filled_in = false;
	
}
foreach ( $necessary_settings['integers'] as $key ) {
	
	if ( !isset($_POST['conf-'.$key]) || !valid_int($_POST['conf-'.$key]) )
		$filled_in = false;
	
}
$user_levels = array(LEVEL_GUEST, LEVEL_MEMBER, LEVEL_MOD, LEVEL_ADMIN);
$onoff_settings = array('allow_multi_sess', 'board_closed', 'cookie_secure', 'disable_info_emails', 'dst', 'enable_contactadmin', 'enable_detailed_online_list', 'enable_forum_stats_box', 'enable_memberlist', 'enable_quickreply', 'enable_rss', 'enable_stafflist', 'enable_stats', 'friendly_urls', 'guests_can_access_board', 'guests_can_view_profiles', 'hide_avatars', 'hide_signatures', 'hide_userinfo', 'rel_nofollow', 'return_to_topic_after_posting', 'sig_allow_bbcode', 'sig_allow_smilies', 'single_forum_mode', 'target_blank', 'users_must_activate');

if ( $filled_in && preg_match(EMAIL_PREG, $_POST['conf-admin_email']) && in_array(intval($_POST['conf-debug']), array(0, 1, 2)) && in_array($_POST['conf-email_view_level'], array(0, 1, 2, 3)) && in_array($_POST['conf-language'], $functions->get_language_packs()) && in_array(intval($_POST['conf-output_compression']), array(0, 1, 2, 3)) && in_array($_POST['conf-template'], $functions->get_template_sets()) && isset($_POST['conf-timezone']) && $functions->timezone_handler('check_existance', $_POST['conf-timezone']) && in_array(intval($_POST['conf-view_detailed_online_list_min_level']), $user_levels) && in_array(intval($_POST['conf-view_forum_stats_box_min_level']), $user_levels) && in_array(intval($_POST['conf-view_hidden_email_addresses_min_level']), $user_levels) && in_array(intval($_POST['conf-view_memberlist_min_level']), $user_levels) && in_array(intval($_POST['conf-view_stafflist_min_level']), $user_levels) && in_array(intval($_POST['conf-view_stats_min_level']), $user_levels) && in_array(intval($_POST['conf-view_contactadmin_min_level']), $user_levels) ) {
	
	$new_settings = array();
	
	//
	// Necessary settings represented as strings
	//
	foreach ( $necessary_settings['strings'] as $setting )
		$new_settings[$setting] = stripslashes($_POST['conf-'.$setting]);
	
	//
	// Necessary settings represented as integers
	//
	foreach ( $necessary_settings['integers'] as $setting )
		$new_settings[$setting] = intval($_POST['conf-'.$setting]);
	
	//
	// Settings which can be enabled or disabled
	//
	foreach ( $onoff_settings as $setting )
		$new_settings[$setting] = ( !empty($_POST['conf-'.$setting]) ) ? 1 : 0;
	
	//
	// Strings which can be empty
	//
	foreach ( array('board_closed_reason', 'board_url', 'cookie_domain', 'cookie_path', 'session_save_path') as $setting )
		$new_settings[$setting] = ( !empty($_POST['conf-'.$setting]) ) ? stripslashes($_POST['conf-'.$setting]) : '';
	
	//
	// Other settings
	//
	$new_settings['exclude_forums_active_topics'] = ( isset($_POST['conf-exclude_forums_active_topics']) && is_array($_POST['conf-exclude_forums_active_topics']) ) ? $_POST['conf-exclude_forums_active_topics'] : array();
	$new_settings['exclude_forums_rss'] = ( isset($_POST['conf-exclude_forums_rss']) && is_array($_POST['conf-exclude_forums_rss']) ) ? $_POST['conf-exclude_forums_rss'] : array();
	$new_settings['exclude_forums_stats'] = ( isset($_POST['conf-exclude_forums_stats']) && is_array($_POST['conf-exclude_forums_stats']) ) ? $_POST['conf-exclude_forums_stats'] : array();
	$new_settings['timezone'] = (float)$_POST['conf-timezone'];
	
	$admin_functions->set_config($new_settings);
	
} else {
	
	$content = '<p>'.$lang['ConfigInfo'].'</p>
	
	<form action="'.$functions->make_url('admin.php', array('act' => 'config')).'" method="post">
	
	<h2>'.$lang['ConfigDBConfig'].'</h2>
	<table class="adminconfigtable">
	';
	
	//
	// Easy output of database config
	//
	foreach ( $dbs as $key => $val ) {
		
		$_POST['conf-'.$key] = ( isset($_POST['conf-'.$key]) ) ? $_POST['conf-'.$key] : $val;
		$content .= '	<tr>
			<td class="fieldtitle">'.$lang['ConfigDB-'.$key].' <small>*</small></td><td><input type="text" size="15" name="conf-'.$key.'" value="'.unhtml(stripslashes($_POST['conf-'.$key])).'" /></td>
		</tr>
	';
		
	}
	
	$content .= '	<tr>
			<td colspan="2" class="submit"><input type="submit" value="'.$lang['Send'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></td>
		</tr>
	</table>
	
	<h2>'.$lang['ConfigBoardConfig'].'</h2>
	<table class="adminconfigtable">
	';
	
	//
	// These are all the current config settings
	// Here we build the input tags for strings and integers, except for some values
	//
	foreach ( $functions->board_config as $key => $val ) {
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
			$_POST['conf-'.$key] = ( isset($_POST['conf-'.$key]) ) ? $_POST['conf-'.$key] : '';
		else
			$_POST['conf-'.$key] = $val;
		
	}
	
	$input = array();
	
	foreach ( $necessary_settings['strings'] as $key ) {
		
		if ( in_array($key, array('type', 'server', 'username', 'passwd', 'dbname', 'prefix', 'language', 'template')) )
			continue;
		
		$input[$key] = '	<tr>
			<td class="fieldtitle">'.$lang['ConfigBoard-'.$key].' <small>*</small></td><td><input type="text" size="30" name="conf-'.$key.'" value="'.unhtml(stripslashes($_POST['conf-'.$key])).'" /></td>
		</tr>
	';
		
	}
	foreach ( $necessary_settings['integers'] as $key ) {
		
		if ( in_array($key, array('debug', 'email_view_level', 'output_compression', 'view_detailed_online_list_min_level', 'view_forum_stats_box_min_level', 'view_hidden_email_addresses_min_level', 'view_memberlist_min_level', 'view_stafflist_min_level', 'view_stats_min_level', 'view_contactadmin_min_level')) )
			continue;
		
		$input[$key] = '	<tr>
			<td class="fieldtitle">'.$lang['ConfigBoard-'.$key].' <small>*</small></td><td><input type="text" size="5" name="conf-'.$key.'" value="'.unhtml(stripslashes($_POST['conf-'.$key])).'" /></td>
		</tr>
	';
		
	}
	foreach ( $onoff_settings as $key ) {
		
		$enabled = ( !empty($_POST['conf-'.$key]) ) ? ' checked="checked"' : '';
		$input[$key] = '	<tr>
			<td class="fieldtitle">'.$key.'</td><td><input type="checkbox" name="conf-'.$key.'" value="1"'.$enabled.' /></td>
		</tr>
	';
		
	}
	
	$content .= join('', $input);
	
	$content .= '	<tr>
			<td colspan="2" class="submit"><input type="submit" value="'.$lang['Send'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></td>
		</tr>
	</table>
	</form>
	';
	
}

$admin_functions->create_body('config', $content);

?>
