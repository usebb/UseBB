<?php

/*
    Copyright (C) 2003-2004 UseBB Team
	http://usebb.sourceforge.net

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

if ( preg_match(EMAIL_PREG, $_POST['email']) ) {
	
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
	$active = ( $_POST['email'] != $sess_info['user_info']['email'] && $config['users_must_activate'] ) ? 0 : 1;
	$active_key = ( $_POST['email'] != $sess_info['user_info']['email'] && $config['users_must_activate'] ) ? usebb_random_key() : '';
	$password = ( $_POST['email'] != $sess_info['user_info']['email'] && $config['users_must_activate'] ) ? usebb_random_key() : '';
	$to_add_for_pwd = '';
	
	if ( $_POST['email'] != $sess_info['user_info']['email'] && $config['users_must_activate'] ) {
		
		//
		// Send an e-mail if the user must activate
		//
		usebb_mail($lang['NewEmailActivationEmailSubject'], $lang['NewEmailActivationEmailBody'], array(
			'account_name' => $sess_info['user_info']['name'],
			'activate_link' => $config['board_url'].'panel.php?a=activate&id='.$sess_info['user_info']['id'].'&key='.$active_key,
			'password' => $password
		), $config['board_name'], $config['admin_email'], $_POST['email']);
		
		$active_key = md5($active_key);
		$password = md5($password);
		$to_add_for_pwd = " passwd = '".$password."',";
		
	}
	
	//
	// Now update the users profile
	//
	if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."users SET
		active        = ".$active.",
		active_key    = '".$active_key."',
		".$to_add_for_pwd."
		email         = '".$_POST['email']."',
		avatar_type   = ".$avatar_type.",
		avatar_remote = '".htmlentities($avatar_remote)."',
		location      = '".htmlentities($_POST['location'])."',
		website       = '".htmlentities($_POST['website'])."',
		occupation    = '".htmlentities($_POST['occupation'])."',
		interests     = '".htmlentities($_POST['interests'])."',
		msnm          = '".htmlentities($_POST['msnm'])."',
		yahoom        = '".htmlentities($_POST['yahoom'])."',
		aim           = '".htmlentities($_POST['aim'])."',
		icq           = '".htmlentities($_POST['icq'])."',
		jabber        = '".htmlentities($_POST['jabber'])."',
		signature     = '".htmlentities($_POST['signature'])."'
	WHERE id = ".$sess_info['user_info']['id'])) )
		usebb_die('SQL', 'Unable to update user information!', __FILE__, __LINE__);
	
	if ( $_POST['email'] != $sess_info['user_info']['email'] && $config['users_must_activate'] ) {
		
		//
		// Show a message box if users must activate
		//
		$template->parse('msgbox', array(
			'box_title' => $lang['Note'],
			'content' => sprintf($lang['NewEmailNotActivated'], '<i>'.$sess_info['user_info']['name'].'</i>', $_POST['email'])
		));
		
	} else {
		
		//
		// Else, jump to the index
		//
		header('Location: index.php');
		exit();
		
	}
	
} else {
	
	if ( !empty($_POST['submitted']) ) {
		
		$template->parse('msgbox', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['MissingFields'], strtolower($lang['Email']))
		));
		
	}
	
	switch ( $sess_info['user_info']['level'] ) {
		
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
	
	$template->parse('edit_profile', array(
		'form_begin'       => '<form action="'.usebb_make_url('panel.php', array('a' => 'editprofile')).'" method="post">',
		'edit_profile'      => $lang['EditProfile'],
		'required'         => $lang['Required'],
		'email'            => $lang['Email'],
		'email_input'      => '<input type="text" size="50" maxlength="255" name="email" value="'.$sess_info['user_info']['email'].'" />',
		'avatar'           => $lang['Avatar'],
		'avatar_input'     => '<input type="text" size="50" maxlength="255" name="avatar" value="'.$sess_info['user_info']['avatar_remote'].'" />',
		'location'         => $lang['Location'],
		'location_input'   => '<input type="text" size="50" maxlength="255" name="location" value="'.stripslashes($sess_info['user_info']['location']).'" />',
		'website'          => $lang['Website'],
		'website_input'    => '<input type="text" size="50" maxlength="255" name="website" value="'.$sess_info['user_info']['website'].'" />',
		'occupation'       => $lang['Occupation'],
		'occupation_input' => '<input type="text" size="50" maxlength="255" name="occupation" value="'.stripslashes($sess_info['user_info']['occupation']).'" />',
		'interests'        => $lang['Interests'],
		'interests_input'  => '<input type="text" size="50" maxlength="255" name="interests" value="'.stripslashes($sess_info['user_info']['interests']).'" />',
		'signature'        => $lang['Signature'],
		'signature_input'  => '<textarea rows="4" cols="60" name="signature">'.stripslashes(str_replace('\n', '\r', $sess_info['user_info']['signature'])).'</textarea>',
		'msnm'             => $lang['MSNM'],
		'msnm_input'       => '<input type="text" size="50" maxlength="255" name="msnm" value="'.stripslashes($sess_info['user_info']['msnm']).'" />',
		'yahoom'           => $lang['YahooM'],
		'yahoom_input'     => '<input type="text" size="50" maxlength="255" name="yahoom" value="'.stripslashes($sess_info['user_info']['yahoom']).'" />',
		'aim'              => $lang['AIM'],
		'aim_input'        => '<input type="text" size="50" maxlength="255" name="aim" value="'.stripslashes($sess_info['user_info']['aim']).'" />',
		'icq'              => $lang['ICQ'],
		'icq_input'        => '<input type="text" size="50" maxlength="255" name="icq" value="'.stripslashes($sess_info['user_info']['icq']).'" />',
		'jabber'           => $lang['Jabber'],
		'jabber_input'     => '<input type="text" size="50" maxlength="255" name="jabber" value="'.stripslashes($sess_info['user_info']['jabber']).'" />',
		'submit_button'    => '<input type="submit" name="submit" value="'.$lang['EditProfile'].'" />',
		'reset_button'     => '<input type="reset" value="'.$lang['Reset'].'" />',
		'form_end'         => '<input type="hidden" name="submitted" value="true" /></form>'
	));
	
}

?>