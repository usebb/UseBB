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
 * ACP mass email
 *
 * Send mass email to all your members or members with a certain level
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

if ( ( !empty($_POST['recipients_admins']) || !empty($_POST['recipients_mods']) || !empty($_POST['recipients_members']) ) && !empty($_POST['subject']) && !empty($_POST['body']) && $functions->verify_form() ) {
	
	$levels = array();
	if ( !empty($_POST['recipients_admins']) )
		$levels[] = LEVEL_ADMIN;
	if ( !empty($_POST['recipients_mods']) )
		$levels[] = LEVEL_MOD;
	if ( !empty($_POST['recipients_members']) )
		$levels[] = LEVEL_MEMBER;
	
	$public_only = ( !empty($_POST['public_emails_only']) ) ? " AND email_show = 1" : '';
	$exclude_banned = ( !empty($_POST['exclude_banned']) ) ? " AND banned = 0" : '';
	$result = $db->query("SELECT DISTINCT email FROM ".TABLE_PREFIX."members WHERE level IN(".join(', ', $levels).")".$public_only.$exclude_banned);
	
	$bcc_email = array();
	while ( $user_data = $db->fetch_result($result) )
		$bcc_email[] = $user_data['email'];
	$rec_num = count($bcc_email);
	
	$bcc_email = array_chunk($bcc_email, $functions->get_config('mass_email_msg_recipients'));
	$msg_num = count($bcc_email);
	
	//
	// Use the board's default language
	//
	$lang_email = $functions->fetch_language($functions->get_config('language', true));
	
	foreach ( $bcc_email as $bcc_chunk )
		$functions->usebb_mail(stripslashes($_POST['subject']), $lang_email['MassEmailTemplate'], array('body' => stripslashes($_POST['body'])), $functions->get_config('board_name'), $functions->get_config('admin_email'), $functions->get_config('admin_email'), join(', ', $bcc_chunk));
	
	$content = '<p>'.sprintf($lang['MassEmailSent'], $rec_num, $msg_num).'</p>';
	
} else {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		$errors = array();
		
		if ( empty($_POST['recipients_admins']) && empty($_POST['recipients_mods']) && empty($_POST['recipients_members']) )
			$errors[] = $lang['MassEmailRecipients'];
		if ( empty($_POST['subject']) )
			$errors[] = $lang['MassEmailSubject'];
		if ( empty($_POST['body']) )
			$errors[] = $lang['MassEmailBody'];
		
		if ( count($errors) )
			$content = '<p><strong>'.sprintf($lang['MissingFields'], join(', ', $errors)).'</strong></p>';
		
		$recipients_admins_checked = ( !empty($_POST['recipients_admins']) ) ? ' checked="checked"' : '';
		$recipients_mods_checked = ( !empty($_POST['recipients_mods']) ) ? ' checked="checked"' : '';
		$recipients_members_checked = ( !empty($_POST['recipients_members']) ) ? ' checked="checked"' : '';
		$public_emails_only_checked = ( !empty($_POST['public_emails_only']) ) ? ' checked="checked"' : '';
		$exclude_banned_checked = ( !empty($_POST['exclude_banned']) ) ? ' checked="checked"' : '';
		
	} else {
		
		$content = '<p>'.$lang['MassEmailInfo'].'</p>';
		
		$recipients_admins_checked = '';
		$recipients_mods_checked = '';
		$recipients_members_checked = ' checked="checked"';
		$public_emails_only_checked = ' checked="checked"';
		$exclude_banned_checked = '';
		
	}
	
	$_POST['subject'] = ( !empty($_POST['subject']) ) ? $_POST['subject'] : '';
	$_POST['body'] = ( !empty($_POST['body']) ) ? $_POST['body'] : '';
	
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'mass_email')).'" method="post">';
	$content .= '<table id="adminregulartable">';
		$content .= '<tr><td class="fieldtitle">'.$lang['MassEmailRecipients'].'</td><td>';
			$content .= '<label><input type="checkbox" name="recipients_admins" value="1"'.$recipients_admins_checked.' /> '.$lang['MassEmailRecipients-admins'].'</label> ';
			$content .= '<label><input type="checkbox" name="recipients_mods" value="1"'.$recipients_mods_checked.' /> '.$lang['MassEmailRecipients-mods'].'</label> ';
			$content .= '<label><input type="checkbox" name="recipients_members" value="1"'.$recipients_members_checked.' /> '.$lang['MassEmailRecipients-members'].'</label>';
		$content .= '</td></tr>';
		$content .= '<tr><td class="fieldtitle">'.$lang['MassEmailSubject'].'</td><td><input type="text" name="subject" size="35" maxlength="255" value="'.unhtml(stripslashes($_POST['subject'])).'" /></td></tr>';
		$content .= '<tr><td class="fieldtitle">'.$lang['MassEmailBody'].'</td><td><textarea name="body" rows="15" cols="50">'.unhtml(stripslashes($_POST['body'])).'</textarea></td></tr>';
		$content .= '<tr><td class="fieldtitle">'.$lang['MassEmailOptions'].'</td><td>';
			$content .= '<div><label><input type="checkbox" name="public_emails_only" value="1"'.$public_emails_only_checked.' /> '.$lang['MassEmailPublicEmailsOnly'].'</label></div>';
			$content .= '<div><label><input type="checkbox" name="exclude_banned" value="1"'.$exclude_banned_checked.' /> '.$lang['MassEmailExcludeBanned'].'</label></div>';
		$content .= '</td></tr>';
	$content .= '<tr><td colspan="2" class="submit"><input type="submit" value="'.$lang['Send'].'" />'.$admin_functions->form_token().' <input type="reset" value="'.$lang['Reset'].'" /></td></tr></table></form>';
	
}

$admin_functions->create_body('mass_email', $content);

?>
