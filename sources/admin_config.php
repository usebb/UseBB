<?php

/*
	Copyright (C) 2003-2012 UseBB Team
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
 * @copyright	Copyright (C) 2003-2012 UseBB Team
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
	'integers' => array('acp_auto_logout', 'activation_mode', 'active_topics_count', 'active_topics_max_age', 'antispam_status_max_posts', 'debug', 'edit_post_timeout', 'email_view_level', 'flood_interval', 'ga_mode', 'mass_email_msg_recipients', 'members_per_page', 'online_min_updated', 'output_compression', 'passwd_min_length', 'posts_per_page', 'rss_items_count', 'search_limit_results', 'search_nonindex_words_min_length', 'session_max_lifetime', 'sfs_max_lastseen', 'sfs_min_frequency', 'show_edited_message_timeout', 'sig_max_length', 'antispam_question_mode', 'topicreview_posts', 'topics_per_page', 'username_min_length', 'username_max_length', 'view_active_topics_min_level', 'view_detailed_online_list_min_level', 'view_forum_stats_box_min_level', 'view_hidden_email_addresses_min_level', 'view_memberlist_min_level', 'view_search_min_level', 'view_stafflist_min_level', 'view_stats_min_level', 'view_contactadmin_min_level')
);

if ( !$functions->get_config('hide_db_config_acp') )
	$necessary_settings['strings'] = array_merge($necessary_settings['strings'], array('type', 'server', 'username', 'dbname'));

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
$onoff_settings = array('allow_multi_sess', 'allow_multi_sess_per_user', 'allow_duplicate_emails', 'antispam_disable_post_links', 'antispam_disable_profile_links', 'antispam_status_for_guests', 'board_closed', 'cookie_httponly', 'cookie_secure', 'disable_registrations', 'disable_xhtml_header', 'dst', 'email_reply-to_header', 'enable_acp_modules', 'enable_badwords_filter', 'enable_contactadmin', 'enable_contactadmin_form', 'enable_detailed_online_list', 'enable_dnsbl_powered_banning', 'enable_email_dns_check', 'enable_error_log', 'enable_forum_stats_box', 'enable_ip_bans', 'enable_memberlist', 'enable_quickreply', 'enable_registration_log', 'enable_rss', 'enable_rss_per_forum', 'enable_rss_per_topic', 'enable_stafflist', 'enable_stats', 'error_log_log_hidden', 'friendly_urls', 'guests_can_access_board', 'guests_can_see_contact_info', 'guests_can_view_profiles', 'hide_avatars', 'hide_signatures', 'hide_userinfo', 'rel_nofollow', 'return_to_topic_after_posting', 'sendmail_sender_parameter', 'sfs_email_check', 'sfs_save_bans', 'show_never_activated_members', 'show_posting_links_to_guests', 'show_raw_entities_in_code', 'sig_allow_bbcode', 'sig_allow_smilies', 'single_forum_mode', 'target_blank');
$optional_strings = array('board_closed_reason', 'board_keywords', 'board_url', 'contactadmin_custom_url', 'cookie_domain', 'cookie_path', 'disable_registrations_reason', 'ga_account', 'ga_domain', 'session_save_path', 'registration_log_file', 'sfs_api_key', 'antispam_question_questions');

if ( !$functions->get_config('hide_db_config_acp') ) {
	
	$onoff_settings[] = 'persistent';
	$optional_strings = array_merge($optional_strings, array('prefix'));

}

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

$db_servers = ( version_compare(phpversion(), '5.0.0', '<') || !extension_loaded('mysqli') ) ? array('mysql' => 'MySQL') : array('mysqli' => 'MySQL 4.1/5.x &mdash; mysqli', 'mysql' => 'MySQL 3.x/4.0 &mdash; mysql');

if (
	$filled_in && // checks necessary strings and integers
	
	in_array($_POST['conf-activation_mode'], array(0, 1, 2)) &&
	in_array($_POST['conf-debug'], array(0, 1, 2)) &&
	in_array($_POST['conf-antispam_question_mode'], array(ANTI_SPAM_DISABLE, ANTI_SPAM_MATH, ANTI_SPAM_CUSTOM)) &&
	in_array($_POST['conf-ga_mode'], array(GA_SINGLE_DOMAIN, GA_MULTIPLE_SUBDOMAINS, GA_MULTIPLE_DOMAINS)) &&
	
	//
	// Check if custom questions are set
	//
	( $_POST['conf-antispam_question_mode'] != ANTI_SPAM_CUSTOM || $antispam_question_questions_valid ) &&

	//
	// Check if GA domain is set
	//
	( $_POST['conf-ga_mode'] == GA_SINGLE_DOMAIN || !empty($_POST['conf-ga_domain']) ) &&
	
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
	in_array($_POST['conf-view_contactadmin_min_level'], $user_levels) &&

	( $functions->get_config('hide_db_config_acp') || isset($db_servers[$_POST['conf-type']]) ) &&

	$functions->verify_form()
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
	// Database password
	//
	if ( !$functions->get_config('hide_db_config_acp') ) {

		$_POST['passwd-act'] = ( !empty($_POST['passwd-act']) ) ? $_POST['passwd-act'] : 'keep';
		switch ( $_POST['passwd-act'] ) {

			case 'keep':
				// Do nothing
				break;
			case 'set':
				if ( !empty($_POST['passwd']) )
					$new_settings['passwd'] = stripslashes($_POST['passwd']);
				break;
			case 'clear':
				$new_settings['passwd'] = '';
				break;
			default:
				// Cannot happen

		}

	}
	
	//
	// Now set the board settings
	//
	$admin_functions->set_config($new_settings);
	$content = '<p>'.$lang['ConfigSet'].'</p>';
	
} else {
	
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
			'allow_multi_sess_per_user',
			'session_max_lifetime',
			'session_save_path',
			'acp_auto_logout',
		),
		'page_counts' => array(
			'active_topics_count',
			'topics_per_page',
			'posts_per_page',
			'topicreview_posts',
			'members_per_page',
			
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
			'view_search_min_level',
			'view_forum_stats_box_min_level',
			'view_detailed_online_list_min_level',
			'view_memberlist_min_level',
			'view_stafflist_min_level',
			'view_stats_min_level',
			'view_contactadmin_min_level',
		),
		'layout' => array(
			'template',
			'avatars_force_height',
			'avatars_force_width',
			'hide_avatars',
			'hide_signatures',
			'hide_userinfo',
			'show_posting_links_to_guests',
			'disable_xhtml_header',
		),
		'rss' => array(
			'enable_rss',
			'exclude_forums_rss',
			'enable_rss_per_forum',
			'enable_rss_per_topic',
			'rss_items_count',
		),
		'additional' => array(
			'enable_forum_stats_box',
			'enable_detailed_online_list',
			'enable_memberlist',
			'enable_quickreply',
			'enable_stafflist',
			'enable_stats',
			'exclude_forums_stats',
			'enable_contactadmin',
			'enable_contactadmin_form',
		),
		'security' => array(
			'enable_ip_bans',
			'enable_dnsbl_powered_banning',
			'show_never_activated_members',
			'flood_interval',
			'enable_badwords_filter',
			'enable_registration_log',
			'registration_log_file',
			'enable_email_dns_check',
		),
		'anti_spam' => array(
			'antispam_question_mode',
			'antispam_question_questions',
			'antispam_disable_profile_links',
			'antispam_disable_post_links',
			'antispam_status_max_posts',
			'antispam_status_for_guests',
			'sfs_email_check',
			'sfs_min_frequency',
			'sfs_max_lastseen',
			'sfs_save_bans',
			'sfs_api_key',
		),
		'advanced' => array(
			'friendly_urls',
			'target_blank',
			'rel_nofollow',
			'ga_account',
			'ga_mode',
			'ga_domain',
			'show_raw_entities_in_code',
			'return_to_topic_after_posting',
			'single_forum_mode',
			'enable_acp_modules',
			'output_compression',
			'debug',
			'active_topics_max_age',
			'exclude_forums_active_topics',
			'online_min_updated',
			'search_limit_results',
			'search_nonindex_words_min_length',
			'edit_post_timeout',
			'show_edited_message_timeout',
			'username_min_length',
			'username_max_length',
			'passwd_min_length',
			'contactadmin_custom_url',
			'enable_error_log',
			'error_log_log_hidden',
		)
	);
	
	if ( !$functions->get_config('hide_db_config_acp') )
		$sections['database'] = array('type', 'server', 'username', 'passwd', 'dbname', 'prefix', 'persistent');
	
	//
	// Check missing
	//
	if ( !empty($_POST['conf-admin_email']) && !preg_match(EMAIL_PREG, $_POST['conf-admin_email']) && !in_array('admin_email', $missing) )
		$missing[] = 'admin_email';
	if ( !empty($_POST['conf-session_name']) && ( !preg_match('#^[A-Za-z0-9]+$#', $_POST['conf-session_name']) || preg_match('#^[0-9]+$#', $_POST['conf-session_name']) ) && !in_array('session_name', $missing) )
		$missing[] = 'session_name';
	if ( isset($_POST['conf-antispam_question_mode']) && $_POST['conf-antispam_question_mode'] == ANTI_SPAM_CUSTOM && !$antispam_question_questions_valid )
		$missing[] = 'antispam_question_questions';
	if ( isset($_POST['conf-ga_mode']) && $_POST['conf-ga_mode'] != GA_SINGLE_DOMAIN && empty($_POST['conf-ga_domain']) )
		$missing[] = 'ga_domain';
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' && count($missing) ) {
		
		function usebb_missing_field_section($key, &$sections) {
			
			foreach ( $sections as $section => $keys ) {
				
				if ( in_array($key, $keys) )
					return $section;

			}

		}
		
		$content = '<p id="adminconfigtop"><strong>'.$lang['ConfigMissingFields'].'</strong></p><ul>';
		foreach ( $missing as $key )
			$content .= '<li>'.$lang['ConfigBoardSection-'.usebb_missing_field_section($key, $sections)].': '.$lang['ConfigBoard-'.$key].'</li>';
		
		$content .= '</ul>';
		
	} else {
		
		$content = '<p id="adminconfigtop">'.$lang['ConfigInfo'].'</p>';
		
	}
	
	//
	// These are all the current config settings
	//
	foreach ( $sections as $section_name => $parts ) {
		
		foreach ( $parts as $key ) {
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
				$_POST['conf-'.$key] = ( isset($_POST['conf-'.$key]) ) ? stripslashes($_POST['conf-'.$key]) : '';
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
		
		if ( in_array($key, array('type', 'language', 'template')) )
			continue;
		
		$moreinfo = ( !empty($lang['ConfigBoard-'.$key.'-info']) ) ? '<div class="moreinfo">'.$lang['ConfigBoard-'.$key.'-info'].'</div>' : '';
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].' <small>*</small></td><td><input type="text" size="30" name="conf-'.$key.'" value="'.unhtml($_POST['conf-'.$key]).'" />'.$moreinfo.'</td></tr>';
		
	}
	
	//
	// Necessary integer settings
	//
	foreach ( $necessary_settings['integers'] as $key ) {
		
		if ( in_array($key, array('activation_mode', 'debug', 'email_view_level', 'output_compression', 'antispam_question_mode', 'ga_mode', 'view_detailed_online_list_min_level', 'view_forum_stats_box_min_level', 'view_hidden_email_addresses_min_level', 'view_memberlist_min_level', 'view_stafflist_min_level', 'view_stats_min_level', 'view_contactadmin_min_level')) )
			continue;
		
		$moreinfo = ( !empty($lang['ConfigBoard-'.$key.'-info']) ) ? '<div class="moreinfo">'.$lang['ConfigBoard-'.$key.'-info'].'</div>' : '';
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].' <small>*</small></td><td><input type="text" size="5" name="conf-'.$key.'" value="'.unhtml($_POST['conf-'.$key]).'" />'.$moreinfo.'</td></tr>';
		
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

		if ( $key == 'passwd' )
			continue;
		
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].'</td><td>';
		
		if ( !empty($lang['ConfigBoard-'.$key.'-info']) )
			$moreinfo = $lang['ConfigBoard-'.$key.'-info'];
		elseif ( in_array($key, array('board_closed_reason', 'disable_registrations_reason')) )
			$moreinfo = $lang['HTMLEnabledField'];
		else
			$moreinfo = '';
		$moreinfo = ( !empty($moreinfo) ) ? '<div class="moreinfo">'.$moreinfo.'</div>' : '';

		$input[$key] .= ( in_array($key, array('board_closed_reason', 'disable_registrations_reason', 'antispam_question_questions')) ) ? '<textarea name="conf-'.$key.'" rows="10" cols="50">'.unhtml($_POST['conf-'.$key]).'</textarea>' : '<input type="text" size="30" name="conf-'.$key.'" value="'.unhtml($_POST['conf-'.$key]).'" />';

		$input[$key] .= $moreinfo.'</td></tr>';
		
	}
	
	if ( !$functions->get_config('hide_db_config_acp') ) {
		
		//
		// Database type
		//
		$dbtype_input = '<select name="conf-type">';
		foreach ( $db_servers as $db_server => $db_info ) {
			
			$selected = ( $_POST['conf-type'] == $db_server ) ? ' selected="selected"' : '';
			$dbtype_input .= '<option value="'.$db_server.'"'.$selected.'>'.$db_info.'</option>';
			
		}
		$dbtype_input .= '</select>';
		$input['type'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-type'].'</td><td>'.$dbtype_input.'</td></tr>';

		//
		// Database password
		//
		$input['passwd'] = '<tr><td class="fieldtitle" rowspan="3">'.$lang['ConfigBoard-passwd'].'</td>'
			    .'<td><label><input type="radio" name="passwd-act" value="keep" checked="checked" /> '.( $functions->get_config('passwd', TRUE) ? $lang['ConfigBoard-Passwd-KeepCurrent'] : $lang['ConfigBoard-Passwd-NotSet'] ).'</label></td></tr>'
			.'<tr><td><label><input type="radio" name="passwd-act" value="set" /> '.$lang['ConfigBoard-Passwd-Set'].':</label> <input type="password" name="passwd" size="20" /></td></tr>'
			.'<tr><td><label><input type="radio" name="passwd-act" value="clear" /> '.$lang['ConfigBoard-Passwd-Clear'].'</label></td></tr>';
		
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
		$input[$key] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-'.$key].'</td><td><input type="text" size="5" name="conf-'.$key.'" value="'.unhtml($_POST['conf-'.$key]).'" />'.$moreinfo.'</td></tr>';
		
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

	$ga_mode_input = '<select name="conf-ga_mode">';
	foreach ( array(GA_SINGLE_DOMAIN, GA_MULTIPLE_SUBDOMAINS, GA_MULTIPLE_DOMAINS) as $ga_mode_mode ) {
		
		$selected = ( $_POST['conf-ga_mode'] == $ga_mode_mode ) ? ' selected="selected"' : '';
		$ga_mode_input .= '<option value="'.$ga_mode_mode.'"'.$selected.'>'.$lang['ConfigBoard-ga_mode'.$ga_mode_mode].'</option>';
		
	}
	$ga_mode_input .= '</select>';
	$input['ga_mode'] = '<tr><td class="fieldtitle">'.$lang['ConfigBoard-ga_mode'].'</td><td>'.$ga_mode_input.'<div class="moreinfo">'.$lang['ConfigBoard-ga_mode-info'].'</div></td></tr>';
	
	//
	// Now create the navigation and form
	//
	$content .= '<ul id="adminfunctionsmenu">';
	foreach ( $sections as $section_name => $null )
		$content .= '<li><a href="#'.$section_name.'" onclick="acp_config_toggle(\''.$section_name.'\')">'.$lang['ConfigBoardSection-'.$section_name].'</a></li> ';
	$content .= '</ul>';
	
	$template->set_js_onload('acp_config_onload()');
	
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
	
	if ( !is_writable(ROOT_PATH.'config.php') )
		$content .= '<p>'.sprintf($lang['IndexUnwritableConfig'], '<code>config.php</code>').'</p>';
	
	$content .= '<p class="submit" id="adminconfigsubmit"><input type="submit" value="'.$lang['Save'].'" />'.$admin_functions->form_token().' <input type="reset" value="'.$lang['Reset'].'" /></p></form>';
	
}

$admin_functions->create_body('config', $content);

?>
