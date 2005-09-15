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

/**
 * Panel account information
 *
 * Gives an interface to change user information.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2005 UseBB Team
 * @package	UseBB
 * @subpackage	Panel
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

$displayed_name_taken = false;
$displayed_name_banned = false;
if ( !empty($_POST['displayed_name']) ) {
	
	//
	// Get banned usernames
	//
	$result = $db->query("SELECT name FROM ".TABLE_PREFIX."bans WHERE name <> ''");
	$banned_usernames = array();
	while ( $out = $db->fetch_result($result) )
		$banned_usernames[] = $out['name'];
	
	//
	// Check if this displayed name already exists
	//
	$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."members WHERE ( name = '".$_POST['displayed_name']."' OR displayed_name = '".$_POST['displayed_name']."' ) AND id <> ".$session->sess_info['user_id']);
	$out = $db->fetch_result($result);
	if ( $out['count'] )
		$displayed_name_taken = true;
	
	foreach ( $banned_usernames as $banned_username ) {
		
		$banned_username = preg_quote($banned_username, '#');
		$banned_username = preg_replace(array('#\\\\\*#', '#\\\\\?#'), array('.*', '.'), $banned_username);
		if ( preg_match('#^'.$banned_username.'$#i', $_POST['displayed_name']) )
			$displayed_name_banned = true;
		
	}
	
}

if ( !empty($_POST['displayed_name']) && !$displayed_name_taken && !$displayed_name_banned && entities_strlen($_POST['signature']) <= $functions->get_config('sig_max_length') && ( ( empty($_POST['birthday_month']) && empty($_POST['birthday_day']) && empty($_POST['birthday_year']) ) || ( valid_int($_POST['birthday_month']) && valid_int($_POST['birthday_day']) && valid_int($_POST['birthday_year']) && checkdate($_POST['birthday_month'], $_POST['birthday_day'], $_POST['birthday_year']) ) ) && !empty($_POST['email']) && preg_match(EMAIL_PREG, $_POST['email']) && ( empty($_POST['avatar']) || preg_match(IMG_PREG, $_POST['avatar']) ) && ( empty($_POST['website']) || preg_match(WEB_PREG, $_POST['website']) ) ) {
	
	if ( !empty($_POST['avatar']) ) {
			
			$avatar_type = 1;
		$avatar_remote = $_POST['avatar'];
		
	} else {
		
		$avatar_type = 0;
		$avatar_remote = '';
		
	}
	
	//
	// Set some variables needed in the query
	//
	$active = ( $_POST['email'] != $session->sess_info['user_info']['email'] && $functions->get_config('users_must_activate') ) ? 0 : 1;
	$active_key = ( $_POST['email'] != $session->sess_info['user_info']['email'] && $functions->get_config('users_must_activate') ) ? $functions->random_key() : '';
	
	if ( $_POST['email'] != $session->sess_info['user_info']['email'] && $functions->get_config('users_must_activate') ) {
		
		//
		// Send an e-mail if the user must activate
		//
		$functions->usebb_mail($lang['NewEmailActivationEmailSubject'], $lang['NewEmailActivationEmailBody'], array(
			'account_name' => stripslashes($session->sess_info['user_info']['name']),
			'activate_link' => $functions->get_config('board_url').$functions->make_url('panel.php', array('act' => 'activate', 'id' => $session->sess_info['user_info']['id'], 'key' => $active_key), false)
		), $functions->get_config('board_name'), $functions->get_config('admin_email'), $_POST['email']);
		
		$active_key = md5($active_key);
		
	}
	
	if ( !empty($_POST['birthday_month']) && valid_int($_POST['birthday_month']) && !empty($_POST['birthday_day']) && valid_int($_POST['birthday_day']) && !empty($_POST['birthday_year']) && valid_int($_POST['birthday_year']) )
		$birthday = sprintf('%04d%02d%02d', $_POST['birthday_year'], $_POST['birthday_month'], $_POST['birthday_day']);
	else
		$birthday = 0;
	
	//
	// Now update the users profile
	//
	$result = $db->query("UPDATE ".TABLE_PREFIX."members SET
		active        = ".$active.",
		active_key    = '".$active_key."',
		email         = '".$_POST['email']."',
		avatar_type   = ".$avatar_type.",
		avatar_remote = '".$avatar_remote."',
		displayed_name = '".$_POST['displayed_name']."',
		real_name     = '".$_POST['real_name']."',
		location      = '".$_POST['location']."',
		website       = '".$_POST['website']."',
		occupation    = '".$_POST['occupation']."',
		interests     = '".$_POST['interests']."',
		signature     = '".$_POST['signature']."',
		birthday      = '".$birthday."',
		msnm          = '".$_POST['msnm']."',
		yahoom        = '".$_POST['yahoom']."',
		aim           = '".$_POST['aim']."',
		icq           = '".$_POST['icq']."',
		jabber        = '".$_POST['jabber']."',
		skype         = '".$_POST['skype']."'
	WHERE id = ".$session->sess_info['user_info']['id']);
	
	if ( $_POST['email'] != $session->sess_info['user_info']['email'] && $functions->get_config('users_must_activate') ) {
		
		//
		// Show a message box if users must activate
		//
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Note'],
			'content' => sprintf($lang['NewEmailNotActivated'], '<em>'.$session->sess_info['user_info']['name'].'</em>', $_POST['email'])
		));
		
	} else {
		
		//
		// Else, jump to the index
		//
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Note'],
			'content' => $lang['ProfileEdited']
		));
		
	}
	
} else {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		if ( $displayed_name_taken ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['DisplayedNameTaken'], '<em>'.unhtml(stripslashes($_POST['displayed_name'])).'</em>')
			));
			
		} elseif ( $displayed_name_banned ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['BannedUsername'], '<em>'.unhtml(stripslashes($_POST['displayed_name'])).'</em>')
			));
			
		}
		
		$errors = array();
		if ( empty($_POST['displayed_name']) )
			$errors[] = $lang['DisplayedName'];
		if ( !( ( empty($_POST['birthday_month']) && empty($_POST['birthday_day']) && empty($_POST['birthday_year']) ) || ( valid_int($_POST['birthday_month']) && valid_int($_POST['birthday_day']) && valid_int($_POST['birthday_year']) && checkdate($_POST['birthday_month'], $_POST['birthday_day'], $_POST['birthday_year']) ) ) )
			$errors[] = $lang['Birthday'];
		if ( empty($_POST['email']) || !preg_match(EMAIL_PREG, $_POST['email']) )
			$errors[] = $lang['Email'];
		if ( !empty($_POST['avatar']) && !preg_match(IMG_PREG, $_POST['avatar']) )
			$errors[] = $lang['AvatarURL'];
		if ( !empty($_POST['website']) && !preg_match(WEB_PREG, $_POST['website']) )
			$errors[] = $lang['Website'];
		
		if ( count($errors) ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['MissingFields'], join(', ', $errors))
			));
			
		}
		
		if ( !empty($_POST['signature']) && entities_strlen($_POST['signature']) > $functions->get_config('sig_max_length') ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['StringTooLong'], $lang['Signature'], $functions->get_config('sig_max_length'))
			));
			
		}
		
	}
	
	//
	// Keep submitted info in the form, even when erroneous
	//
	$user_info = array();
	foreach ( $session->sess_info['user_info'] as $key => $val )
		$user_info[$key] = ( isset($_POST[$key]) ) ? $_POST[$key] : $val;
	
	//
	// Create the birthday fields
	//	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		$birthday_year = $_POST['birthday_year'];
		$birthday_month = $_POST['birthday_month'];
		$birthday_day = $_POST['birthday_day'];
		
	} else {
		
		$birthday = $session->sess_info['user_info']['birthday'];
		$birthday_year = ( $birthday ) ? intval(substr($birthday, 0, 4)) : '';
		$birthday_month = ( $birthday ) ? intval(substr($birthday, 4, 2)) : 0;
		$birthday_day = ( $birthday ) ? intval(substr($birthday, 6, 2)) : 0;
		
	}
	$birthday_month_input = '<select name="birthday_month"><option value="">'.$lang['Month'].'</option>';
	for ( $i = 1; $i <= 12; $i++ ) {
		
		$selected = ( $birthday_month == $i ) ? ' selected="selected"' : '';
		$birthday_month_input .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		
	}
	$birthday_month_input .= '</select>';
	$birthday_day_input = '<select name="birthday_day"><option value="">'.$lang['Day'].'</option>';
	for ( $i = 1; $i <= 31; $i++ ) {
		
		$selected = ( $birthday_day == $i ) ? ' selected="selected"' : '';
		$birthday_day_input .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		
	}
	$birthday_day_input .= '</select>';
	$birthday_year_input = '<select name="birthday_year"><option value="">'.$lang['Year'].'</option>';
	for ( $i = intval(date('Y')); $i >= 1900; $i-- ) {
		
		$selected = ( $birthday_year == $i ) ? ' selected="selected"' : '';
		$birthday_year_input .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		
	}
	$birthday_year_input .= '</select>';
	
	$template->parse('edit_profile', 'panel', array(
		'form_begin'       => '<form action="'.$functions->make_url('panel.php', array('act' => 'editprofile')).'" method="post">',
		'username'         => $user_info['name'],
		'email_input'      => '<input type="text" size="50" maxlength="255" name="email" value="'.$user_info['email'].'" />',
		'avatar_input'     => '<input type="text" size="50" maxlength="255" name="avatar" value="'.$user_info['avatar_remote'].'" />',
		'displayed_name_input'  => '<input type="text" size="50" maxlength="255" name="displayed_name" value="'.unhtml(stripslashes($user_info['displayed_name'])).'" />',
		'real_name_input'  => '<input type="text" size="50" maxlength="255" name="real_name" value="'.unhtml(stripslashes($user_info['real_name'])).'" />',
		'location_input'   => '<input type="text" size="50" maxlength="255" name="location" value="'.unhtml(stripslashes($user_info['location'])).'" />',
		'birthday_year_input' => $birthday_year_input,
		'birthday_month_input' => $birthday_month_input,
		'birthday_day_input' => $birthday_day_input,
		'website_input'    => '<input type="text" size="50" maxlength="255" name="website" value="'.$user_info['website'].'" />',
		'occupation_input' => '<input type="text" size="50" maxlength="255" name="occupation" value="'.unhtml(stripslashes($user_info['occupation'])).'" />',
		'interests_input'  => '<input type="text" size="50" maxlength="255" name="interests" value="'.unhtml(stripslashes($user_info['interests'])).'" />',
		'signature_input'  => '<textarea rows="'.$template->get_config('textarea_rows').'" cols="'.$template->get_config('textarea_cols').'" name="signature">'.unhtml(stripslashes($user_info['signature'])).'</textarea>',
		'msnm_input'       => '<input type="text" size="50" maxlength="255" name="msnm" value="'.unhtml(stripslashes($user_info['msnm'])).'" />',
		'yahoom_input'     => '<input type="text" size="50" maxlength="255" name="yahoom" value="'.unhtml(stripslashes($user_info['yahoom'])).'" />',
		'aim_input'        => '<input type="text" size="50" maxlength="255" name="aim" value="'.unhtml(stripslashes($user_info['aim'])).'" />',
		'icq_input'        => '<input type="text" size="50" maxlength="255" name="icq" value="'.unhtml(stripslashes($user_info['icq'])).'" />',
		'jabber_input'     => '<input type="text" size="50" maxlength="255" name="jabber" value="'.unhtml(stripslashes($user_info['jabber'])).'" />',
		'skype_input'      => '<input type="text" size="50" maxlength="255" name="skype" value="'.unhtml(stripslashes($user_info['skype'])).'" />',
		'submit_button'    => '<input type="submit" name="submit" value="'.$lang['OK'].'" />',
		'reset_button'     => '<input type="reset" value="'.$lang['Reset'].'" />',
		'form_end'         => '</form>'
	));
	
}

?>
