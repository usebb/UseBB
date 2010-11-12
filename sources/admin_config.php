<?php

/*
	Copyright (C) 2003-2010 UseBB Team
	http://www.usebb.net
	
	$Id$
	
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
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * ACP configuration
 *
 * Gives an interface to change board configuration.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2010 UseBB Team
 * @package	UseBB
 * @subpackage	ACP
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
	'strings' => array('admin_email', 'board_descr', 'board_name', 'date_format', 'language', 'session_name', 'template'),
	'integers' => array('activation_mode', 'active_topics_count', 'debug', 'edit_post_timeout', 'email_view_level', 'flood_interval', 'mass_email_msg_recipients', 'members_per_page', 'online_min_updated', 'output_compression', 'passwd_min_length', 'posts_per_page', 'rss_items_count', 'search_limit_results', 'search_nonindex_words_min_length', 'session_max_lifetime', 'show_edited_message_timeout', 'sig_max_length', 'antispam_question_mode', 'topicreview_posts', 'topics_per_page', 'username_min_length', 'username_max_length', 'view_active_topics_min_level', 'view_detailed_online_list_min_level', 'view_forum_stats_box_min_level', 'view_hidden_email_addresses_min_level', 'view_memberlist_min_level', 'view_search_min_level', 'view_stafflist_min_level', 'view_stats_min_level', 'view_contactadmin_min_level')
);

if ( !$functions->get_config('hide_db_config_acp') )
	$necessary_settings = array_merge($necessary_settings, array('type', 'server', 'username', 'dbname'));

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
$onoff_settings = array('allow_multi_sess', 'allow_duplicate_emails', 'board_closed', 'cookie_httponly', 'cookie_secure', 'disable_registrations', 'disable_xhtml_header', 'dst', 'email_reply-to_header', 'enable_acp_modules', 'enable_badwords_filter', 'enable_contactadmin', 'enable_detailed_online_list', 'enable_email_dns_check', 'enable_error_log', 'enable_forum_stats_box', 'enable_ip_bans', 'enable_memberlist', 'enable_quickreply', 'enable_registration_log', 'enable_rss', 'enable_rss_per_forum', 'enable_rss_per_topic', 'enable_stafflist', 'enable_stats', 'error_log_log_hidden', 'friendly_urls', 'guests_can_access_board', 'guests_can_see_contact_info', 'guests_can_view_profiles', 'hide_avatars', 'hide_signatures', 'hide_userinfo', 'rel_nofollow', 'return_to_topic_after_posting', 'sendmail_sender_parameter', 'show_never_activated_members', 'show_raw_entities_in_code', 'sig_allow_bbcode', 'sig_allow_smilies', 'single_forum_mode', 'target_blank');
$optional_strings = array('board_closed_reason', 'board_keywords', 'board_url', 'cookie_domain', 'cookie_path', 'disable_registrations_reason', 'session_save_path', 'registration_log_file', 'antispam_question_questions');

if ( !$functions->get_config('hide_db_config_acp') )
	$optional_strings = array_merge($optional_strings, array('passwd', 'prefix'));

//
// First convert antispam_question_questions to an array
//
$antispam_question_questions_valid = false;
if ( !empty($_POST['conf-antispam_question_questions']) ) {
	
	$tmp_questions = preg_split("#[\r\n]+#", $_POST['conf-antispam_question_questions']);
	
	if ( count($tmp_questions) ) {
		
		$antispam_question_questions = array();
		foreach ( $tmp_questions as $question ) {
			
			if ( strpos($question, '|') === false )
				continue;
			
			$question = explode('|', $question);			
			$antispam_question_questions[trim($question[0])] = trim($question[1]);
			
		}
		
		if ( count($antispam_question_questions) )
			$antispam_question_questions_valid = true;
		
	}
	
	unset($tmp_questions);
	
}

if (
	$filled_in && // checks necessary strings and integers
	
	in_array($_POST['conf-activation_mode'], array(0, 1, 2)) &&
	in_array($_POST['conf-debug'], array(0, 1, 2)) &&
	in_array($_POST['conf-antispam_question_mode'], array(ANTI_SPAM_DISABLE, ANTI_SPAM_MATH, ANTI_SPAM_CUSTOM)) &&
	
	//
	// Check if custom questions are set
	//
	( $_POST['conf-antispam_question_mode'] != ANTI_SPAM_CUSTOM || $antispam_question_questions_valid ) &&
	
	//
	// Only the following are checked (because they are entered, not selected)
	//
	preg_match(EMAIL_PREG, $_POST['conf-admin_email']) &&
	preg_match('#^[A-Za-z0-9]+$#', $_POST['conf-session_name']) &&
	!preg_match('#^[0-9]+$#', $_POST['conf-session_name']) &&
	
	in_array($_POST['conf-language'], $functions->get_language_packs()) &&
	in_array($_POST['conf-template'], $functions->get_template_sets()) &&
	
	isset($_POST['conf-timezone']) && 
	$functions->timezone_handler('check_existance', $_POST['conf-timezone']) &&
	
	in_array($_POST['conf-email_view_level'], $user_levels) &&
	in_array($_POST['conf-output_compression'], $user_levels) &&
	in_array($_POST['conf-view_active_topics_min_level'], $user_levels) &&
	in_array($_POST['conf-view_detailed_online_list_min_level'], $user_levels) &&
	in_array($_POST['conf-view_forum_stats_box_min_level'], $user_levels) &&
	in_array($_POST['conf-view_hidden_email_addresses_min_level'], $user_levels) &&
	in_array($_POST['conf-view_memberlist_min_level'], $user_levels) &&
	in_array($_POST['conf-view_search_min_level'], $user_levels) &&
	in_array($_POST['conf-view_stafflist_min_level'], $user_levels) &&
	in_array($_POST['conf-view_stats_min_level'], $user_levels) &&
	in_array($_POST['conf-view_contactadmin_min_level'], $user_levels)
) {
	
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
	foreach ( $optional_strings as $setting ) {
		
		if ( $setting == 'antispam_question_questions' )
			continue;
		
		$new_settings[$setting] = ( !empty($_POST['conf-'.$setting]) ) ? stripslashes($_POST['conf-'.$setting]) : '';
		
	}
	
	//
	// Other settings
	//
	$new_settings['exclude_forums_active_topics'] = ( isset($_POST['conf-exclude_forums_active_topics']) && is_array($_POST['conf-exclude_forums_active_topics']) ) ? $_POST['conf-exclude_forums_active_topics'] : array();
	$new_settings['exclude_forums_rss'] = ( isset($_POST['conf-exclude_forums_rss']) && is_array($_POST['conf-exclude_forums_rss']) ) ? $_POST['conf-exclude_forums_rss'] : array();
	$new_settings['exclude_forums_stats'] = ( isset($_POST['conf-exclude_forums_stats']) && is_array($_POST['conf-exclude_forums_stats']) ) ? $_POST['conf-exclude_forums_stats'] : array();
	$new_settings['timezone'] = (float)$_POST['conf-timezone'];
	$new_settings['antispam_question_questions'] = ( $antispam_question_questions_valid ) ? $antispam_question_questions : array();
	
	//
	// Avatar dimensions
	//
	if ( !empty($_POST['conf-avatars_force_width']) && !empty($_POST['conf-avatars_force_height']) && valid_int($_POST['conf-avatars_force_width']) && valid_int($_POST['conf-avatars_force_height']) ) {
		
		$new_settings['avatars_force_width'] = (int)$_POST['conf-avatars_force_width'];
		$new_settings['avatars_force_height'] = (int)$_POST['conf-avatars_force_height'];
		
	} else {
		
		$new_settings['avatars_force_width'] = $new_settings['avatars_force_height'] = 0;
		
	}
	
	//
	// Now set the board settings
	//
	$admin_functions->set_config($new_settings);
	$content = '<p>'.$lang['ConfigSet'].'</p>';
	
} else {
	
	if ( !empty($_POST['conf-admin_email']) && !preg_match(EMAIL_PREG, $_POST['conf-admin_email']) && !in_array('admin_email', $missing) )
		$missing[] = 'admin_email';
	if ( !empty($_POST['conf-session_name']) && ( !preg_match('#^[A-Za-z0-9]+$#', $_POST['conf-session_name']) || preg_match('#^[0-9]+$#', $_POST['conf-session_name']) ) && !in_array('session_name', $missing) )
		$missing[] = 'session_name';
	if ( isset($_POST['conf-antispam_question_mode']) && $_POST['conf-antispam_question_mode'] == ANTI_SPAM_CUSTOM && !$antispam_question_questions_valid )
		$missing[] = 'antispam_question_questions';
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' && count($missing) ) {
		
		$content = '<p id="adminconfigtop"><strong>'.$lang['ConfigMissingFields'].'</strong></p><ul>';
		foreach ( $missing as $key )
			$content .= '<li>'.$lang['ConfigBoard-'.$key].'</li>';
		
		$content .= '</ul>';
		
	} else {
		
		$content = '<p id="adminconfigtop">'.$lang['ConfigInfo'].'</p>';
		
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
			'cookie_httponly',
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
			'mass_email_msg_recipients',
			'email_reply-to_header',
			'sendmail_sender_parameter',
		),
		'user_rights' => array(
			'guests_can_access_board',
			'guests_can_view_profiles',
			'guests_can_see_contact_info',
			'sig_allow_bbcode',
			'sig_allow_smilies',
			'sig_max_length',
			'allow_duplicate_emails',
			'activation_mode',
			'disable_registrations',
			'disable_registrations_reason',
		),
		'min_levels' => array(
			'view_active_topics_min_level',
			'view_contactadmin_min_level',
			'view_detailed_online_list_min_level',
			'view_forum_stats_box_min_level',
			'view_memberlist_min_level',
			'view_search_min_level',
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
			'disable_xhtml_header',
		),
		'additional' => array(
			'enable_contactadmin',
			'enable_detailed_online_list',
			'enable_forum_stats_box',
			'enable_memberlist',
			'enable_quickreply',
			'enable_rss',
			'exclude_forums_rss',
			'enable_rss_per_forum',
			'enable_rss_per_topic',
			'enable_stafflist',
			'enable_stats',
			'exclude_forums_stats',
		),
		'security' => array(
			'enable_ip_bans',
			'enable_badwords_filter',
			'enable_registration_log',
			'registration_log_file',
			'show_never_activated_members',
			'enable_email_dns_check',
			'antispam_question_mode',
			'antispam_question_questions',
		),
		'advanced' => array(
			'friendly_urls',
			'target_blank',
			'rel_nofollow',
			'show_raw_entities_in_code',
			'return_to_topic_after_posting',
			'single_forum_mode',
			'enable_acp_modules',
			'output_compression',
			'debug',
			'exclude_forums_active_topics',
			'flood_interval',
			'online_min_updated',
			'search_limit_results',
			'search_nonindex_words_min_length',
			'edit_post_timeout',
			'show_edited_message_timeout',
			'username_min_length',
			'username_max_length',
			'passwd_min_length',
			'enable_error_log',
			'error_log_log_hidden',
		)
	);
	
	if ( !$functions->get_config('hide_db_config_acp') )
		$sections['database'] = array('type', 'server', 'username', 'passwd', 'dbname', 'prefix');
	
	//
	// These are all the current config settings
	//
	foreach ( $sections as $section_name => $parts ) {
		
		foreach ( $parts as $key ) {
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
				$_POST['conf-'.$key] = ( isset($_POST['conf-'.$key]) ) ? $_POST['conf-'.$key] : '';
			else
				$_POST['conf-'.$key] = $functions->get_config($key, true);
			
			if ( $key == 'antispam_question_questions' && is_array($_POST['conf-'.$key]) ) {
				
				$new_value = '';
				foreach ( $_POST['conf-'.$key] as $arkey => $arval )
					$new_value .= $arkey.'|'.$arval."\n";
				$_POST['conf-'.$key] = trim($new_value);
				
			}
			
		}
		
	}
	
	$input = array();
	
	//
	// Necessary string settings
	//
	foreach ( $necessary_settings['strings'] as $key ) {
		
		if ( in_array($key, array('type', 'server', 'username', 'dbname', 'language', 'template')) )
			continue;
		
		$moreinfo = ( !empty($lang['ConfigBoard-'.$key.'-info']) ) ? '<div class="moreinfo">'.$lang['ConfigBoard-'.$key.'-info'].'</div>' : '';
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].' <small>*</small></td><td><input type="text" size="30" name="conf-'.$key.'" value="'.unhtml(stripslashes($_POST['conf-'.$key])).'" />'.$moreinfo.'</td></tr>';
		
	}
	
	//
	// Necessary integer settings
	//
	foreach ( $necessary_settings['integers'] as $key ) {
		
		if ( in_array($key, array('activation_mode', 'debug', 'email_view_level', 'output_compression', 'antispam_question_mode', 'view_detailed_online_list_min_level', 'view_forum_stats_box_min_level', 'view_hidden_email_addresses_min_level', 'view_memberlist_min_level', 'view_stafflist_min_level', 'view_stats_min_level', 'view_contactadmin_min_level')) )
			continue;
		
		$moreinfo = ( !empty($lang['ConfigBoard-'.$key.'-info']) ) ? '<div class="moreinfo">'.$lang['ConfigBoard-'.$key.'-info'].'</div>' : '';
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].' <small>*</small></td><td><input type="text" size="5" name="conf-'.$key.'" value="'.unhtml(stripslashes($_POST['conf-'.$key])).'" />'.$moreinfo.'</td></tr>';
		
	}
	
	//
	// On/off settings
	//
	foreach ( $onoff_settings as $key ) {
		
		$enabled = ( !empty($_POST['conf-'.$key]) ) ? ' checked="checked"' : '';
		$moreinfo = ( !empty($lang['ConfigBoard-'.$key.'-info']) ) ? '<div class="moreinfo">'.$lang['ConfigBoard-'.$key.'-info'].'</div>' : '';
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].'</td><td><label><input type="checkbox" name="conf-'.$key.'" value="1"'.$enabled.' /> '.$lang['Yes'].'</label>'.$moreinfo.'</td></tr>';
		
	}
	
	//
	// Optional string settings
	//
	foreach ( $optional_strings as $key ) {
		
		if ( in_array($key, array('passwd', 'prefix')) )
			continue;
		
		$moreinfo = ( !empty($lang['ConfigBoard-'.$key.'-info']) ) ? '<div class="moreinfo">'.$lang['ConfigBoard-'.$key.'-info'].'</div>' : '';
		$input[$key] = ( in_array($key, array('board_closed_reason', 'disable_registrations_reason', 'antispam_question_questions')) ) ? '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].'</td><td><textarea name="conf-'.$key.'" rows="5" cols="50">'.unhtml(stripslashes($_POST['conf-'.$key])).'</textarea>'.$moreinfo.'</td></tr>' : '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].'</td><td><input type="text" size="30" name="conf-'.$key.'" value="'.unhtml(stripslashes($_POST['conf-'.$key])).'" />'.$moreinfo.'</td></tr>';
		
	}
	
	//
	// Database config
	//
	if ( !$functions->get_config('hide_db_config_acp') ) {
		
		foreach ( $dbs as $key => $val ) {
			
			$_POST['conf-'.$key] = ( !empty($_POST['conf-'.$key]) ) ? $_POST['conf-'.$key] : $val;
			$required = ( in_array($key, $necessary_settings['strings']) ) ? ' <small>*</small>' : '';
			$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].$required.'</td><td><input type="text" size="15" name="conf-'.$key.'" value="'.unhtml(stripslashes($_POST['conf-'.$key])).'" /></td></tr>';
			
		}
		
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
	$input['language'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-language'].'</td><td>'.$language_input.'<div class="moreinfo">'.$lang['ConfigBoard-language-info'].'</div></td></tr>';
	
	//
	// Template
	//
	$template_input = '<select name="conf-template">';
	foreach ( $functions->get_template_sets() as $single_template ) {
		
		$selected = ( $_POST['conf-template'] == $single_template ) ? ' selected="selected"' : '';
		$template_input .= '<option value="'.$single_template.'"'.$selected.'>'.$single_template.'</option>';
		
	}
	$template_input .= '</select>';
	$input['template'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-template'].'</td><td>'.$template_input.'<div class="moreinfo">'.$lang['ConfigBoard-template-info'].'</div></td></tr>';
	
	//
	// Debug
	//
	$debug_input = '<select name="conf-debug">';
	foreach ( array(0, 1, 2) as $debug_mode ) {
		
		$selected = ( $_POST['conf-debug'] == $debug_mode ) ? ' selected="selected"' : '';
		$debug_input .= '<option value="'.$debug_mode.'"'.$selected.'>'.$lang['ConfigBoard-debug'.$debug_mode].'</option>';
		
	}
	$debug_input .= '</select>';
	$input['debug'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-debug'].'</td><td>'.$debug_input.'<div class="moreinfo">'.$lang['ConfigBoard-debug-info'].'</div></td></tr>';
	
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
	// Activation mode
	//
	$activation_mode_input = '<select name="conf-activation_mode">';
	foreach ( array(0, 1, 2) as $activation_mode ) {
		
		$selected = ( $_POST['conf-activation_mode'] == $activation_mode ) ? ' selected="selected"' : '';
		$activation_mode_input .= '<option value="'.$activation_mode.'"'.$selected.'>'.$lang['ConfigBoard-activation_mode'.$activation_mode].'</option>';
		
	}
	$activation_mode_input .= '</select>';
	$input['activation_mode'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-activation_mode'].'</td><td>'.$activation_mode_input.'</td></tr>';
	
	//
	// Several *_min_level settings
	//
	foreach ( array('view_active_topics_min_level', 'view_detailed_online_list_min_level', 'view_forum_stats_box_min_level', 'view_hidden_email_addresses_min_level', 'view_memberlist_min_level', 'view_search_min_level', 'view_stafflist_min_level', 'view_stats_min_level', 'view_contactadmin_min_level') as $key ) {
		
		$level_input = '<select name="conf-'.$key.'">';
		foreach ( $user_levels as $level_mode ) {
			
			$selected = ( $_POST['conf-'.$key] == $level_mode ) ? ' selected="selected"' : '';
			$level_input .= '<option value="'.$level_mode.'"'.$selected.'>'.$lang['ConfigBoard-level'.$level_mode].'</option>';
			
		}
		$level_input .= '</select>';
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].'</td><td>'.$level_input.'</td></tr>';
		
	}
	
	//
	// Avatar dimensions
	//
	foreach ( array('avatars_force_width', 'avatars_force_height') as $key ) {
		
		$moreinfo = ( !empty($lang['ConfigBoard-'.$key.'-info']) ) ? '<div class="moreinfo">'.$lang['ConfigBoard-'.$key.'-info'].'</div>' : '';
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].'</td><td><input type="text" size="5" name="conf-'.$key.'" value="'.unhtml(stripslashes($_POST['conf-'.$key])).'" />'.$moreinfo.'</td></tr>';
		
	}
	
	//
	// Anti-spam question mode
	//
	$antispam_question_mode_input = '<select name="conf-antispam_question_mode">';
	foreach ( array(ANTI_SPAM_DISABLE, ANTI_SPAM_MATH, ANTI_SPAM_CUSTOM) as $antispam_question_mode_mode ) {
		
		$selected = ( $_POST['conf-antispam_question_mode'] == $antispam_question_mode_mode ) ? ' selected="selected"' : '';
		$antispam_question_mode_input .= '<option value="'.$antispam_question_mode_mode.'"'.$selected.'>'.$lang['ConfigBoard-antispam_question_mode'.$antispam_question_mode_mode].'</option>';
		
	}
	$antispam_question_mode_input .= '</select>';
	$input['antispam_question_mode'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-antispam_question_mode'].'</td><td>'.$antispam_question_mode_input.'<div class="moreinfo">'.$lang['ConfigBoard-antispam_question_mode-info'].'</div></td></tr>';
	
	//
	// Now create the navigation and form
	//
	$content .= '<ul id="adminfunctionsmenu">';
	foreach ( $sections as $section_name => $null )
		$content .= '<li><a href="#'.$section_name.'" onclick="acp_config_toggle(\''.$section_name.'\');return false;">'.$lang['ConfigBoardSection-'.$section_name].'</a></li> ';
	$content .= '</ul>';
	
	$template->set_js_onload("acp_config_toggle('general')");
	
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'config')).'" method="post">';
	
	//
	// Implement sections
	//
	foreach ( $sections as $section_name => $parts ) {
		
		$content .= '<div class="adminconfigtablecell" id="'.$section_name.'"><table class="adminconfigtable">';
		$content .= '<tr><th colspan="2">'.$lang['ConfigBoardSection-'.$section_name].'</th></tr>';
		
		if ( !empty($lang['ConfigBoardSection-'.$section_name.'-info']) )
			$content .= '<tr><td colspan="2">'.$lang['ConfigBoardSection-'.$section_name.'-info'].'</td></tr>';
		
		foreach ( $parts as $part ) {
			
			$content .= $input[$part];
			unset($input[$part]);
			
		}
		
		$content .= '</table></div>';
		
	}
	
	$content .= '<p class="submit" id="adminconfigsubmit"><input type="submit" value="'.$lang['Save'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></p></form>';
	
}

$admin_functions->create_body('config', $content);

?>
