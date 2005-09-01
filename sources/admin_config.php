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
$missing = array();
$necessary_settings = array(
	'strings' => array('type', 'server', 'username', 'dbname', 'admin_email', 'board_descr', 'board_name', 'date_format', 'language', 'session_name', 'template'),
	'integers' => array('active_topics_count', 'avatars_force_width', 'avatars_force_height', 'debug', 'email_view_level', 'flood_interval', 'members_per_page', 'online_min_updated', 'output_compression', 'passwd_min_length', 'posts_per_page', 'rss_items_count', 'search_limit_results', 'search_nonindex_words_min_length', 'session_max_lifetime', 'show_edited_message_timeout', 'topicreview_posts', 'topics_per_page', 'view_detailed_online_list_min_level', 'view_forum_stats_box_min_level', 'view_hidden_email_addresses_min_level', 'view_memberlist_min_level', 'view_stafflist_min_level', 'view_stats_min_level', 'view_contactadmin_min_level')
);
foreach ( $necessary_settings['strings'] as $key ) {
	
	if ( empty($_POST['conf-'.$key]) ) {
		
		$filled_in = false;
		$missing[] = $key;
		
	}
	
}
foreach ( $necessary_settings['integers'] as $key ) {
	
	if ( !isset($_POST['conf-'.$key]) || !valid_int($_POST['conf-'.$key]) ) {
		
		$filled_in = false;
		$missing[] = $key;
		
	}
	
}

//
// Some extra arrays used
//
$user_levels = array(LEVEL_GUEST, LEVEL_MEMBER, LEVEL_MOD, LEVEL_ADMIN);
$onoff_settings = array('allow_multi_sess', 'board_closed', 'cookie_secure', 'disable_info_emails', 'dst', 'enable_acp_modules', 'enable_contactadmin', 'enable_detailed_online_list', 'enable_forum_stats_box', 'enable_memberlist', 'enable_quickreply', 'enable_rss', 'enable_stafflist', 'enable_stats', 'friendly_urls', 'guests_can_access_board', 'guests_can_view_profiles', 'hide_avatars', 'hide_signatures', 'hide_userinfo', 'rel_nofollow', 'return_to_topic_after_posting', 'sig_allow_bbcode', 'sig_allow_smilies', 'single_forum_mode', 'target_blank', 'users_must_activate');
$optional_strings = array('passwd', 'prefix', 'board_closed_reason', 'board_keywords', 'board_url', 'cookie_domain', 'cookie_path', 'session_save_path');

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
	foreach ( $optional_strings as $setting )
		$new_settings[$setting] = ( !empty($_POST['conf-'.$setting]) ) ? stripslashes($_POST['conf-'.$setting]) : '';
	
	//
	// Other settings
	//
	$new_settings['exclude_forums_active_topics'] = ( isset($_POST['conf-exclude_forums_active_topics']) && is_array($_POST['conf-exclude_forums_active_topics']) ) ? $_POST['conf-exclude_forums_active_topics'] : array();
	$new_settings['exclude_forums_rss'] = ( isset($_POST['conf-exclude_forums_rss']) && is_array($_POST['conf-exclude_forums_rss']) ) ? $_POST['conf-exclude_forums_rss'] : array();
	$new_settings['exclude_forums_stats'] = ( isset($_POST['conf-exclude_forums_stats']) && is_array($_POST['conf-exclude_forums_stats']) ) ? $_POST['conf-exclude_forums_stats'] : array();
	$new_settings['timezone'] = (float)$_POST['conf-timezone'];
	
	//
	// Now set the board settings
	//
	$admin_functions->set_config($new_settings);
	
	$content = '<p>'.$lang['ConfigSet'].'</p>';
	
} else {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		$content = '<p><strong>'.$lang['ConfigMissingFields'].'</strong></p><ul>';
		foreach ( $missing as $key )
			$content .= '<li>'.$lang['ConfigBoard-'.$key].'</li>';
		
		$content .= '</ul>';
		
	} else {
		
		$content = '<p>'.$lang['ConfigInfo'].'</p>';
		
	}
	
	//
	// All configuration variables
	//
	$sections = array(
		'general' => array(
			'board_name',
			'board_descr',
			'board_keywords',
			'board_url',
			'board_closed',
			'board_closed_reason',
			'admin_email',
			'language',
		),
		'cookies' => array(
			'cookie_domain',
			'cookie_path',
			'cookie_secure',
		),
		'sessions' => array(
			'session_name',
			'allow_multi_sess',
			'session_max_lifetime',
			'session_save_path',
		),
		'page_counts' => array(
			'active_topics_count',
			'topics_per_page',
			'posts_per_page',
			'topicreview_posts',
			'members_per_page',
			'rss_items_count',
		),
		'date_time' => array(
			'date_format',
			'timezone',
			'dst',
		),
		'email' => array(
			'email_view_level',
			'view_hidden_email_addresses_min_level',
			'disable_info_emails',
		),
		'user_rights' => array(
			'guests_can_access_board',
			'guests_can_view_profiles',
			'sig_allow_bbcode',
			'sig_allow_smilies',
			'users_must_activate',
			'view_contactadmin_min_level',
			'view_detailed_online_list_min_level',
			'view_forum_stats_box_min_level',
			'view_memberlist_min_level',
			'view_stafflist_min_level',
			'view_stats_min_level',
		),
		'layout' => array(
			'template',
			'avatars_force_height',
			'avatars_force_width',
			'hide_avatars',
			'hide_signatures',
			'hide_userinfo',
		),
		'additional' => array(
			'enable_contactadmin',
			'enable_detailed_online_list',
			'enable_forum_stats_box',
			'enable_memberlist',
			'enable_quickreply',
			'enable_rss',
			'exclude_forums_rss',
			'enable_stafflist',
			'enable_stats',
			'exclude_forums_stats',
		),
		'advanced' => array(
			'friendly_urls',
			'rel_nofollow',
			'return_to_topic_after_posting',
			'single_forum_mode',
			'target_blank',
			'enable_acp_modules',
			'output_compression',
			'debug',
			'exclude_forums_active_topics',
			'flood_interval',
			'online_min_updated',
			'search_limit_results',
			'search_nonindex_words_min_length',
			'show_edited_message_timeout',
			'passwd_min_length',
		),
		'database' => array(
			'type',
			'server',
			'username',
			'passwd',
			'dbname',
			'prefix'
		)
	);
	
	$content .= '<ul id="adminconfigcontent">';
	
	foreach ( $sections as $section_name => $null )
		$content .= '<li><a href="#'.$section_name.'">'.$lang['ConfigBoardSection-'.$section_name].'</a></li> ';
	
	$content .= '</ul>';
	
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'config')).'" method="post">';
	$content .= '<table id="adminconfigtable">';
	
	//
	// These are all the current config settings
	//
	foreach ( $sections as $section_name => $parts ) {
		
		foreach ( $parts as $key ) {
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
				$_POST['conf-'.$key] = ( isset($_POST['conf-'.$key]) ) ? $_POST['conf-'.$key] : '';
			else
				$_POST['conf-'.$key] = ( isset($functions->board_config_original[$key]) ) ? $functions->board_config_original[$key] : '';
			
		}
		
	}
	
	$input = array();
	
	//
	// Necessary string settings
	//
	foreach ( $necessary_settings['strings'] as $key ) {
		
		if ( in_array($key, array('type', 'server', 'username', 'dbname', 'language', 'template')) )
			continue;
		
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].' <small>*</small></td><td><input type="text" size="30" name="conf-'.$key.'" value="'.unhtml(stripslashes($_POST['conf-'.$key])).'" /></td></tr>';
		
	}
	
	//
	// Necessary integer settings
	//
	foreach ( $necessary_settings['integers'] as $key ) {
		
		if ( in_array($key, array('debug', 'email_view_level', 'output_compression', 'view_detailed_online_list_min_level', 'view_forum_stats_box_min_level', 'view_hidden_email_addresses_min_level', 'view_memberlist_min_level', 'view_stafflist_min_level', 'view_stats_min_level', 'view_contactadmin_min_level')) )
			continue;
		
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].' <small>*</small></td><td><input type="text" size="5" name="conf-'.$key.'" value="'.unhtml(stripslashes($_POST['conf-'.$key])).'" /></td></tr>';
		
	}
	
	//
	// On/off settings
	//
	foreach ( $onoff_settings as $key ) {
		
		$enabled = ( !empty($_POST['conf-'.$key]) ) ? ' checked="checked"' : '';
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].'</td><td><input type="checkbox" name="conf-'.$key.'" id="conf-'.$key.'" value="1"'.$enabled.' /><label for="conf-'.$key.'"> '.$lang['Yes'].'</label></td></tr>';
		
	}
	
	//
	// Optional string settings
	//
	foreach ( $optional_strings as $key ) {
		
		if ( in_array($key, array('passwd', 'prefix')) )
			continue;
		
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].'</td><td><input type="text" size="30" name="conf-'.$key.'" value="'.unhtml(stripslashes($_POST['conf-'.$key])).'" /></td></tr>';
		
	}
	
	//
	// Database config
	//
	foreach ( $dbs as $key => $val ) {
		
		$_POST['conf-'.$key] = ( !empty($_POST['conf-'.$key]) ) ? $_POST['conf-'.$key] : $val;
		$required = ( in_array($key, $necessary_settings['strings']) ) ? ' <small>*</small>' : '';
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].$required.'</td><td><input type="text" size="15" name="conf-'.$key.'" value="'.unhtml(stripslashes($_POST['conf-'.$key])).'" /></td></tr>';
		
	}
	
	//
	// Exclude from active topics
	//
	$input['exclude_forums_active_topics'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-exclude_forums_active_topics'].'</td><td>'.$admin_functions->forum_select_box('conf-exclude_forums_active_topics').'</td></tr>';
	
	//
	// Exclude from RSS
	//
	$input['exclude_forums_rss'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-exclude_forums_rss'].'</td><td>'.$admin_functions->forum_select_box('conf-exclude_forums_rss').'</td></tr>';
	
	//
	// Exclude from stats
	//
	$input['exclude_forums_stats'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-exclude_forums_stats'].'</td><td>'.$admin_functions->forum_select_box('conf-exclude_forums_stats').'</td></tr>';
	
	//
	// Timezone
	//
	$timezone_input = 'UTC/GMT <select name="conf-timezone">';
	foreach ( $functions->timezone_handler('get_zones') as $key => $val ) {
		
		$selected = ( $_POST['conf-timezone'] == $key ) ? ' selected="selected"' : '';
		$timezone_input .= '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
		
	}
	$timezone_input .= '</select>';
	$input['timezone'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-timezone'].' <small>*</small></td><td>'.$timezone_input.'</td></tr>';
	
	//
	// Language
	//
	$language_input = '<select name="conf-language">';
	foreach ( $functions->get_language_packs() as $single_language ) {
		
		$selected = ( $_POST['conf-language'] == $single_language ) ? ' selected="selected"' : '';
		$language_input .= '<option value="'.$single_language.'"'.$selected.'>'.$single_language.'</option>';
		
	}
	$language_input .= '</select>';
	$input['language'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-language'].'</td><td>'.$language_input.'</td></tr>';
	
	//
	// Template
	//
	$template_input = '<select name="conf-template">';
	foreach ( $functions->get_template_sets() as $single_template ) {
		
		$selected = ( $_POST['conf-template'] == $single_template ) ? ' selected="selected"' : '';
		$template_input .= '<option value="'.$single_template.'"'.$selected.'>'.$single_template.'</option>';
		
	}
	$template_input .= '</select>';
	$input['template'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-template'].'</td><td>'.$template_input.'</td></tr>';
	
	//
	// Debug
	//
	$debug_input = '<select name="conf-debug">';
	foreach ( array(0, 1, 2) as $debug_mode ) {
		
		$selected = ( $_POST['conf-debug'] == $debug_mode ) ? ' selected="selected"' : '';
		$debug_input .= '<option value="'.$debug_mode.'"'.$selected.'>'.$lang['ConfigBoard-debug'.$debug_mode].'</option>';
		
	}
	$debug_input .= '</select>';
	$input['debug'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-debug'].'</td><td>'.$debug_input.'</td></tr>';
	
	//
	// E-mail view level
	//
	$email_view_level_input = '<select name="conf-email_view_level">';
	foreach ( array(0, 1, 2, 3) as $email_view_level_mode ) {
		
		$selected = ( $_POST['conf-email_view_level'] == $email_view_level_mode ) ? ' selected="selected"' : '';
		$email_view_level_input .= '<option value="'.$email_view_level_mode.'"'.$selected.'>'.$lang['ConfigBoard-email_view_level'.$email_view_level_mode].'</option>';
		
	}
	$email_view_level_input .= '</select>';
	$input['email_view_level'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-email_view_level'].'</td><td>'.$email_view_level_input.'</td></tr>';
	
	//
	// Output compression
	//
	$output_compression_input = '<select name="conf-output_compression">';
	foreach ( array(0, 1, 2, 3) as $output_compression_mode ) {
		
		$selected = ( $_POST['conf-output_compression'] == $output_compression_mode ) ? ' selected="selected"' : '';
		$output_compression_input .= '<option value="'.$output_compression_mode.'"'.$selected.'>'.$lang['ConfigBoard-output_compression'.$output_compression_mode].'</option>';
		
	}
	$output_compression_input .= '</select>';
	$input['output_compression'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-output_compression'].'</td><td>'.$output_compression_input.'</td></tr>';
	
	//
	// Several *_min_level settings
	//
	foreach ( array('view_detailed_online_list_min_level', 'view_forum_stats_box_min_level', 'view_hidden_email_addresses_min_level', 'view_memberlist_min_level', 'view_stafflist_min_level', 'view_stats_min_level', 'view_contactadmin_min_level') as $key ) {
		
		$level_input = '<select name="conf-'.$key.'">';
		foreach ( $user_levels as $level_mode ) {
			
			$selected = ( $_POST['conf-'.$key] == $level_mode ) ? ' selected="selected"' : '';
			$level_input .= '<option value="'.$level_mode.'"'.$selected.'>'.$lang['ConfigBoard-level'.$level_mode].'</option>';
			
		}
		$level_input .= '</select>';
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].'</td><td>'.$level_input.'</td></tr>';
		
	}
	
	//
	// Implement sections
	//
	foreach ( $sections as $section_name => $parts ) {
		
		$content .= '<tr><th colspan="2"><a name="'.$section_name.'"></a>'.$lang['ConfigBoardSection-'.$section_name].'</th></tr>';
		
		foreach ( $parts as $part ) {
			
			$content .= $input[$part];
			unset($input[$part]);
			
		}
		
	}
	
	$content .= '<tr><td colspan="2" class="submit"><input type="submit" value="'.$lang['Save'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></td></tr></table></form>';
	
}

$admin_functions->create_body('config', $content);

?>
