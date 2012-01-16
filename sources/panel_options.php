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
 * Panel options
 *
 * Gives an interface to change account settings.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2012 UseBB Team
 * @package	UseBB
 * @subpackage	Panel
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

if ( $_SERVER['REQUEST_METHOD'] == 'POST' && $functions->verify_form() ) {
	
	//
	// Update the user's preferences
	//
	
	$_POST['language'] = ( !empty($_POST['language']) && in_array($_POST['language'], $functions->get_language_packs()) ) ? $_POST['language'] : $functions->get_config('language');
	$_POST['template'] = ( !empty($_POST['template']) && in_array($_POST['template'], $functions->get_template_sets()) ) ? $_POST['template'] : $functions->get_config('template');
	$_POST['email_show'] = ( !empty($_POST['email_show']) ) ? 1 : 0;
	$_POST['last_login_show'] = ( !empty($_POST['last_login_show']) ) ? 1 : 0;
	$_POST['hide_from_online_list'] = ( !empty($_POST['hide_from_online_list']) ) ? 1 : 0;
	$_POST['date_format'] = ( !empty($_POST['date_format']) ) ? $_POST['date_format'] : $functions->get_config('date_format');
	$_POST['timezone'] = ( isset($_POST['timezone']) && $functions->timezone_handler('check_existance', $_POST['timezone']) ) ? (float)$_POST['timezone'] : $functions->get_config('timezone');
	$_POST['dst'] = ( !empty($_POST['dst']) ) ? 1 : 0;
	$_POST['quickreply'] = ( !empty($_POST['quickreply']) ) ? 1 : 0;
	$_POST['return_to_topic'] = ( !empty($_POST['return_to_topic']) ) ? 1 : 0;
	$_POST['target_blank'] = ( !empty($_POST['target_blank']) ) ? 1 : 0;
	$_POST['hide_avatars'] = ( !empty($_POST['hide_avatars']) ) ? 1 : 0;
	$_POST['hide_userinfo'] = ( !empty($_POST['hide_userinfo']) ) ? 1 : 0;
	$_POST['hide_signatures'] = ( !empty($_POST['hide_signatures']) ) ? 1 : 0;
	$_POST['auto_subscribe_topic'] = ( !empty($_POST['auto_subscribe_topic']) ) ? 1 : 0;
	$_POST['auto_subscribe_reply'] = ( !empty($_POST['auto_subscribe_reply']) ) ? 1 : 0;
	
	$result = $db->query("UPDATE ".TABLE_PREFIX."members SET
		language = '".$_POST['language']."',
		template = '".$_POST['template']."',
		email_show = ".$_POST['email_show'].",
		last_login_show = ".$_POST['last_login_show'].",
		hide_from_online_list = ".$_POST['hide_from_online_list'].",
		date_format = '".$_POST['date_format']."',
		timezone = '".$_POST['timezone']."',
		dst = ".$_POST['dst'].",
		enable_quickreply = ".$_POST['quickreply'].",
		return_to_topic_after_posting = ".$_POST['return_to_topic'].",
		target_blank = ".$_POST['target_blank'].",
		hide_avatars = ".$_POST['hide_avatars'].",
		hide_userinfo = ".$_POST['hide_userinfo'].",
		hide_signatures = ".$_POST['hide_signatures'].",
		auto_subscribe_topic = ".$_POST['auto_subscribe_topic'].",
		auto_subscribe_reply = ".$_POST['auto_subscribe_reply']."
	WHERE id = ".$session->sess_info['user_info']['id']);
	
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Note'],
		'content' => $lang['OptionsEdited']
	));
	
} else {
	
	$available_languages = $functions->get_language_packs();
	if ( count($available_languages) < 2 ) {
		
		$single_language = $available_languages;
		$language_input = $single_language[0];
		
	} else {
		
		$language_input = '<select name="language">';
		foreach ( $available_languages as $single_language ) {
			
			$selected = ( $functions->get_config('language') == $single_language ) ? ' selected="selected"' : '';
			$language_input .= '<option value="'.$single_language.'"'.$selected.'>'.$single_language.'</option>';
			
		}
		$language_input .= '</select>';
		
	}
	
	$available_templates = $functions->get_template_sets();
	if ( count($available_templates) < 2 ) {
		
		$single_template = $available_templates;
		$template_input = $single_template[0];
		
	} else {
		
		$template_input = '<select name="template">';
		foreach ( $available_templates as $single_template ) {
			
			$selected = ( $functions->get_config('template') == $single_template ) ? ' selected="selected"' : '';
			$template_input .= '<option value="'.$single_template.'"'.$selected.'>'.$single_template.'</option>';
			
		}
		$template_input .= '</select>';
		
	}
	
	$email_show_checked = ( $session->sess_info['user_info']['email_show'] ) ? ' checked="checked"' : '';
	$last_login_show_checked = ( $session->sess_info['user_info']['last_login_show'] ) ? ' checked="checked"' : '';
	$hide_from_online_list_checked = ( $session->sess_info['user_info']['hide_from_online_list'] ) ? ' checked="checked"' : '';

	$timezone_input = 'UTC/GMT <select name="timezone">';
	foreach ( $functions->timezone_handler('get_zones') as $key => $val ) {
		
		$selected = ( $functions->get_config('timezone') == $key ) ? ' selected="selected"' : '';
		$timezone_input .= '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
		
	}
	$timezone_input .= '</select>';

	$dst_checked = ( $functions->get_config('dst') ) ? ' checked="checked"' : '';
	$quickreply_checked = ( $session->sess_info['user_info']['enable_quickreply'] ) ? ' checked="checked"' : '';
	$return_to_topic_checked = ( $session->sess_info['user_info']['return_to_topic_after_posting'] ) ? ' checked="checked"' : '';
	$target_blank_checked = ( $session->sess_info['user_info']['target_blank'] ) ? ' checked="checked"' : '';
	$hide_avatars_checked = ( $session->sess_info['user_info']['hide_avatars'] ) ? ' checked="checked"' : '';
	$hide_userinfo_checked = ( $session->sess_info['user_info']['hide_userinfo'] ) ? ' checked="checked"' : '';
	$hide_signatures_checked = ( $session->sess_info['user_info']['hide_signatures'] ) ? ' checked="checked"' : '';
	$auto_subscribe_topic_checked = ( $session->sess_info['user_info']['auto_subscribe_topic'] ) ? ' checked="checked"' : '';
	$auto_subscribe_reply_checked = ( $session->sess_info['user_info']['auto_subscribe_reply'] ) ? ' checked="checked"' : '';
	
	$template->parse('edit_options', 'panel', array(
		'form_begin'            => '<form action="'.$functions->make_url('panel.php', array('act' => 'editoptions')).'" method="post">',
		'language_input'        => $language_input,
		'template_input'        => $template_input,
		'email_show_input'      => '<label><input type="checkbox" name="email_show" value="1"'.$email_show_checked.' /> '.$lang['Yes'].'</label>',
		'last_login_show_input' => '<label><input type="checkbox" name="last_login_show" value="1"'.$last_login_show_checked.' /> '.$lang['Yes'].'</label>',
		'hide_from_online_list_input' => '<label><input type="checkbox" name="hide_from_online_list" value="1"'.$hide_from_online_list_checked.' /> '.$lang['Yes'].'</label>',
		'date_format_input'     => '<input type="text" name="date_format" size="25" maxlength="255" value="'.unhtml(stripslashes($functions->get_config('date_format'))).'" />',
		'date_format_help'		=> sprintf($lang['DateFormatHelp'], '<a href="http://www.php.net/date">date()</a>'),
		'timezone_input'	    => $timezone_input,
		'dst_input'	         	=> '<label><input type="checkbox" name="dst" value="1"'.$dst_checked.' /> '.$lang['Enabled'].'</label>',
		'quickreply_input'		=> '<label><input type="checkbox" name="quickreply" value="1"'.$quickreply_checked.' /> '.$lang['Enabled'].'</label>',
		'return_to_topic_input'	=> '<label><input type="checkbox" name="return_to_topic" value="1"'.$return_to_topic_checked.' /> '.$lang['Yes'].'</label>',
		'target_blank_input'    => '<label><input type="checkbox" name="target_blank" value="1"'.$target_blank_checked.' /> '.$lang['Yes'].'</label>',
		'hide_avatars_input'    => '<label><input type="checkbox" name="hide_avatars" value="1"'.$hide_avatars_checked.' /> '.$lang['Yes'].'</label>',
		'hide_userinfo_input'   => '<label><input type="checkbox" name="hide_userinfo" value="1"'.$hide_userinfo_checked.' /> '.$lang['Yes'].'</label>',
		'hide_signatures_input' => '<label><input type="checkbox" name="hide_signatures" value="1"'.$hide_signatures_checked.' /> '.$lang['Yes'].'</label>',
		'auto_subscribe_topic_input' => '<label><input type="checkbox" name="auto_subscribe_topic" value="1"'.$auto_subscribe_topic_checked.' /> '.$lang['OnPostingNewTopics'].'</label>',
		'auto_subscribe_reply_input' => '<label><input type="checkbox" name="auto_subscribe_reply" value="1"'.$auto_subscribe_reply_checked.' /> '.$lang['OnPostingNewReplies'].'</label>',
		'submit_button'         => '<input type="submit" name="submit" value="'.$lang['OK'].'" />',
		'form_end'              => '</form>'
	), false, true);
	
}

?>
