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

if ( !empty($_POST['displayed_name']) )
	$_POST['displayed_name'] = preg_replace('#\s+#', ' ', $_POST['displayed_name']);

$displayed_name_taken = $displayed_name_banned = $email_taken = $email_banned = false;
if ( !empty($_POST['displayed_name']) || ( !empty($_POST['email']) && preg_match(EMAIL_PREG, $_POST['email']) ) ) {
	
	//
	// Get banned displayed_names and e-mail addresses
	//
	$result = $db->query("SELECT name, email FROM ".TABLE_PREFIX."bans WHERE name <> '' OR email <> ''");
	$banned = array('displayed_names' => array(), 'emails' => array());
	while ( $out = $db->fetch_result($result) ) {
		
		//
		// Store all the displayed_names and e-mail addresses in an array
		//
		if ( !empty($out['name']) )
			$banned['displayed_names'][] = $out['name'];
		if ( !empty($out['email']) )
			$banned['emails'][] = $out['email'];
		
	}
	
	if ( !empty($_POST['displayed_name']) ) {
		
		//
		// Check if this displayed_name already exists
		//
		$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."members WHERE ( name = '".$_POST['displayed_name']."' OR displayed_name = '".$_POST['displayed_name']."' ) AND id <> ".$session->sess_info['user_id']);
		$out = $db->fetch_result($result);
		if ( $out['count'] )
			$displayed_name_taken = true;
		
		foreach ( $banned['displayed_names'] as $banned_displayed_name ) {
			
			$banned_displayed_name = preg_quote($banned_displayed_name, '#');
			$banned_displayed_name = preg_replace(array('#\\\\\*#', '#\\\\\?#'), array('.*', '.'), $banned_displayed_name);
			if ( preg_match('#^'.$banned_displayed_name.'$#i', $_POST['displayed_name']) )
				$displayed_name_banned = true;
			
		}
		
	}
	
	if ( !empty($_POST['email']) && preg_match(EMAIL_PREG, $_POST['email']) ) {
		
		//
		// Check if this email already exists
		//
		if ( !$functions->get_config('allow_duplicate_emails') ) {
			
			$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."members WHERE email = '".$_POST['email']."' AND id <> ".$session->sess_info['user_id']);
			$out = $db->fetch_result($result);
			if ( $out['count'] )
				$email_taken = true;
			
		}
		
		foreach ( $banned['emails'] as $banned_email ) {
			
			$banned_email = preg_quote($banned_email, '#');
			$banned_email = preg_replace(array('#\\\\\*#', '#\\\\\?#'), array('.*', '.'), $banned_email);
			if ( preg_match('#^'.$banned_email.'$#i', $_POST['email']) )
				$email_banned = true;
			
		}
		
	}
	
}

if ( !empty($_POST['displayed_name']) && !$displayed_name_taken && !$displayed_name_banned && !empty($_POST['email']) && !$email_taken && !$email_banned && entities_strlen($_POST['signature']) <= $functions->get_config('sig_max_length') && ( ( empty($_POST['birthday_month']) && empty($_POST['birthday_day']) && empty($_POST['birthday_year']) ) || ( valid_int($_POST['birthday_month']) && valid_int($_POST['birthday_day']) && valid_int($_POST['birthday_year']) && checkdate($_POST['birthday_month'], $_POST['birthday_day'], $_POST['birthday_year']) ) ) && !empty($_POST['email']) && preg_match(EMAIL_PREG, $_POST['email']) && ( empty($_POST['avatar']) || preg_match(IMG_PREG, $_POST['avatar']) ) && ( empty($_POST['website']) || preg_match(WEB_PREG, $_POST['website']) ) ) {
	
	if ( !empty($_POST['avatar']) ) {
			
		$avatar_type = 1;
		$avatar_remote = $_POST['avatar'];
		
	} else {
		
		$avatar_type = 0;
		$avatar_remote = '';
		
	}
	
	//
	// If the e-mail address changed
	//
	if ( $_POST['email'] != $session->sess_info['user_info']['email'] ) {
		
		switch ( intval($functions->get_config('activation_mode')) ) {
			
			case 0:
				$active = 1;
				$active_key_md5 = '';
				$msgbox_content = $lang['ProfileEdited'];
				break;
			case 1:
				$active = 0;
				$active_key = $functions->random_key(); # used in the email url
				$active_key_md5 = md5($active_key);
				$msgbox_content = sprintf($lang['NewEmailNotActivated'], '<em>'.$session->sess_info['user_info']['name'].'</em>', $_POST['email']);
				break;
			case 2:
				$active = 0;
				$active_key_md5 = '';
				$msgbox_content = sprintf($lang['NewEmailNotActivatedByAdmin'], '<em>'.$session->sess_info['user_info']['name'].'</em>', $_POST['email']);
			
		}
		
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
		active_key    = '".$active_key_md5."',
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
	
	//
	// Send correct e-mails
	//
	if ( $_POST['email'] != $session->sess_info['user_info']['email'] ) {
		
		if ( intval($functions->get_config('activation_mode')) === 1 ) {
			
			$functions->usebb_mail($lang['NewEmailActivationEmailSubject'], $lang['NewEmailActivationEmailBody'], array(
				'account_name' => stripslashes($session->sess_info['user_info']['name']),
				'activate_link' => $functions->get_config('board_url').$functions->make_url('panel.php', array('act' => 'activate', 'id' => $session->sess_info['user_info']['id'], 'key' => $active_key), false)
			), $functions->get_config('board_name'), $functions->get_config('admin_email'), $_POST['email']);
			
		} elseif ( intval($functions->get_config('activation_mode')) === 2 ) {
			
			$functions->usebb_mail($lang['NewEmailAdminActivationEmailSubject'], $lang['NewEmailAdminActivationEmailBody'], array(
				'account_name' => stripslashes($session->sess_info['user_info']['name'])
			), $functions->get_config('board_name'), $functions->get_config('admin_email'), $_POST['email']);
			
		}
		
	}
	
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Note'],
		'content' => $msgbox_content
	));
	
} else {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		if ( $displayed_name_taken ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Note'],
				'content' => sprintf($lang['DisplayedNameTaken'], '<em>'.unhtml(stripslashes($_POST['displayed_name'])).'</em>')
			));
			
		} elseif ( $displayed_name_banned ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Note'],
				'content' => sprintf($lang['BannedUsername'], '<em>'.unhtml(stripslashes($_POST['displayed_name'])).'</em>')
			));
			
		}
		
		if ( $email_taken ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Note'],
				'content' => sprintf($lang['EmailTaken'], $_POST['email'])
			));
			
		} elseif ( $email_banned ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Note'],
				'content' => sprintf($lang['BannedEmail'], $_POST['email'])
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
	
	list($birthday_year_input, $birthday_month_input, $birthday_day_input) = $functions->birthday_input_fields($session->sess_info['user_info']['birthday']);
	
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
