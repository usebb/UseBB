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
// Set the page title
//
$template->set_page_title($lang['EditProfile']);

$_POST['email'] = ( !empty($_POST['email']) ) ? $_POST['email'] : '';

if ( preg_match(EMAIL_PREG, $_POST['email']) && ( empty($_POST['avatar']) || preg_match(IMG_PREG, $_POST['avatar']) ) && ( empty($_POST['website']) || preg_match(WEB_PREG, $_POST['website']) ) ) {
	
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
	$password = ( $_POST['email'] != $session->sess_info['user_info']['email'] && $functions->get_config('users_must_activate') ) ? $functions->random_key() : '';
	$to_add_for_pwd = '';
	
	if ( $_POST['email'] != $session->sess_info['user_info']['email'] && $functions->get_config('users_must_activate') ) {
		
		//
		// Send an e-mail if the user must activate
		//
		$functions->usebb_mail($lang['NewEmailActivationEmailSubject'], $lang['NewEmailActivationEmailBody'], array(
			'account_name' => stripslashes($session->sess_info['user_info']['name']),
			'activate_link' => $functions->get_config('board_url').$functions->make_url('panel.php', array('act' => 'activate', 'id' => $session->sess_info['user_info']['id'], 'key' => $active_key), false),
			'password' => $password
		), $functions->get_config('board_name'), $functions->get_config('admin_email'), $_POST['email']);
		
		$active_key = md5($active_key);
		$password = md5($password);
		$to_add_for_pwd = " passwd = '".$password."',";
		
	}
	
	//
	// Now update the users profile
	//
	if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."members SET
		active        = ".$active.",
		active_key    = '".$active_key."',
		".$to_add_for_pwd."
		email         = '".$_POST['email']."',
		avatar_type   = ".$avatar_type.",
		avatar_remote = '".$avatar_remote."',
		real_name     = '".$_POST['real_name']."',
		location      = '".$_POST['location']."',
		website       = '".$_POST['website']."',
		occupation    = '".$_POST['occupation']."',
		interests     = '".$_POST['interests']."',
		signature     = '".$_POST['signature']."',
		msnm          = '".$_POST['msnm']."',
		yahoom        = '".$_POST['yahoom']."',
		aim           = '".$_POST['aim']."',
		icq           = '".$_POST['icq']."',
		jabber        = '".$_POST['jabber']."',
		skype         = '".$_POST['skype']."'
	WHERE id = ".$session->sess_info['user_info']['id'])) )
		$functions->usebb_die('SQL', 'Unable to update user information!', __FILE__, __LINE__);
	
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
		
		$errors = array();
		if ( !preg_match(EMAIL_PREG, $_POST['email']) )
			$errors[] = $lang['Email'];
		if ( !empty($_POST['avatar']) && !preg_match(IMG_PREG, $_POST['avatar']) )
			$errors[] = $lang['AvatarURL'];
		if ( !empty($_POST['website']) && !preg_match(WEB_PREG, $_POST['website']) )
			$errors[] = $lang['Website'];
		
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['MissingFields'], join(', ', $errors))
		));
		
	}
	
	switch ( $functions->get_user_level() ) {
		
		case 3:
			$level = $lang['Administrator'];
			break;
		case 2:
			$level = $lang['Moderator'];
			break;
		case 1:
			$level = $lang['Member'];
			break;
		
	}
	
	$template->parse('edit_profile', 'panel', array(
		'form_begin'       => '<form action="'.$functions->make_url('panel.php', array('act' => 'editprofile')).'" method="post">',
		'edit_profile'     => $lang['EditProfile'],
		'required'         => $lang['Required'],
		'email'            => $lang['Email'],
		'email_input'      => '<input type="text" size="50" maxlength="255" name="email" value="'.$session->sess_info['user_info']['email'].'" />',
		'avatar'           => $lang['AvatarURL'],
		'avatar_input'     => '<input type="text" size="50" maxlength="255" name="avatar" value="'.$session->sess_info['user_info']['avatar_remote'].'" />',
		'real_name'        => $lang['RealName'],
		'real_name_input'  => '<input type="text" size="50" maxlength="255" name="real_name" value="'.htmlspecialchars(stripslashes($session->sess_info['user_info']['real_name'])).'" />',
		'location'         => $lang['Location'],
		'location_input'   => '<input type="text" size="50" maxlength="255" name="location" value="'.htmlspecialchars(stripslashes($session->sess_info['user_info']['location'])).'" />',
		'website'          => $lang['Website'],
		'website_input'    => '<input type="text" size="50" maxlength="255" name="website" value="'.$session->sess_info['user_info']['website'].'" />',
		'occupation'       => $lang['Occupation'],
		'occupation_input' => '<input type="text" size="50" maxlength="255" name="occupation" value="'.htmlspecialchars(stripslashes($session->sess_info['user_info']['occupation'])).'" />',
		'interests'        => $lang['Interests'],
		'interests_input'  => '<input type="text" size="50" maxlength="255" name="interests" value="'.htmlspecialchars(stripslashes($session->sess_info['user_info']['interests'])).'" />',
		'signature'        => $lang['Signature'],
		'signature_input'  => '<textarea rows="'.$template->get_config('textarea_rows').'" cols="'.$template->get_config('textarea_cols').'" name="signature">'.htmlspecialchars(stripslashes($session->sess_info['user_info']['signature'])).'</textarea>',
		'msnm'             => $lang['MSNM'],
		'msnm_input'       => '<input type="text" size="50" maxlength="255" name="msnm" value="'.htmlspecialchars(stripslashes($session->sess_info['user_info']['msnm'])).'" />',
		'yahoom'           => $lang['YahooM'],
		'yahoom_input'     => '<input type="text" size="50" maxlength="255" name="yahoom" value="'.htmlspecialchars(stripslashes($session->sess_info['user_info']['yahoom'])).'" />',
		'aim'              => $lang['AIM'],
		'aim_input'        => '<input type="text" size="50" maxlength="255" name="aim" value="'.htmlspecialchars(stripslashes($session->sess_info['user_info']['aim'])).'" />',
		'icq'              => $lang['ICQ'],
		'icq_input'        => '<input type="text" size="50" maxlength="255" name="icq" value="'.htmlspecialchars(stripslashes($session->sess_info['user_info']['icq'])).'" />',
		'jabber'           => $lang['Jabber'],
		'jabber_input'     => '<input type="text" size="50" maxlength="255" name="jabber" value="'.htmlspecialchars(stripslashes($session->sess_info['user_info']['jabber'])).'" />',
		'skype'            => $lang['Skype'],
		'skype_input'      => '<input type="text" size="50" maxlength="255" name="skype" value="'.htmlspecialchars(stripslashes($session->sess_info['user_info']['skype'])).'" />',
		'submit_button'    => '<input type="submit" name="submit" value="'.$lang['OK'].'" />',
		'reset_button'     => '<input type="reset" value="'.$lang['Reset'].'" />',
		'form_end'         => '</form>'
	));
	
}

?>
