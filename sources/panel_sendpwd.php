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
// User wants a new password
//

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

$template->set_page_title($lang['SendPassword']);

$_POST['user'] = ( !empty($_POST['user']) ) ? $_POST['user'] : '';
$_POST['user'] = preg_replace('/ +/', ' ', $_POST['user']);
$_POST['email'] = ( !empty($_POST['user']) ) ? $_POST['email'] : '';

if ( preg_match(USER_PREG, $_POST['user']) && preg_match(EMAIL_PREG, $_POST['email']) ) {
	
	//
	// Check if this username already exists
	//
	if ( !($result = $db->query("SELECT id, email, banned, banned_reason FROM ".TABLE_PREFIX."users WHERE name = '".$_POST['user']."'")) )
		usebb_die('SQL', 'Unable to get user information!', __FILE__, __LINE__);
	$userdata = $db->fetch_result($result);
	
	if ( $db->num_rows($result) == 0 ) {
		
		//
		// This user does not exist, show an error
		//
		$template->parse('msgbox', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchUser'], '<i>'.htmlentities($_POST['user']).'</i>')
		));
		
	} elseif ( $userdata['banned'] ) {
		
		//
		// It does exist, but it is banned
		// thus, show another warning...
		//
		$template->parse('msgbox', array(
			'box_title' => $lang['BannedUser'],
			'content' => sprintf($lang['BannedUserExplain'], '<i>'.$_POST['user'].'</i>') . '<br />' . $userdata['banned_reason']
		));
		
	} else {
		
		if ( $_POST['email'] == $userdata['email'] ) {
			
			//
			// Generate the activation key if necessary
			//
			$active = ( $config['users_must_activate'] ) ? 0 : 1;
			$active_key = ( $config['users_must_activate'] ) ? usebb_random_key() : '';
			
			$new_password = usebb_random_key();
			
			//
			// Update the row in the user table
			//
			if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."users SET passwd = '".md5($new_password)."', active = ".$active.", active_key = '".md5($active_key)."' WHERE id = ".$userdata['id'])) )
				usebb_die('SQL', 'Unable to update user information!', __FILE__, __LINE__);
			
			if ( $config['users_must_activate'] ) {
				
				//
				// Send the activation e-mail if necessary
				//
				usebb_mail($lang['SendpwdActivationEmailSubject'], $lang['SendpwdActivationEmailBody'], array(
					'account_name' => $_POST['user'],
					'activate_link' => $config['board_url'].'panel.php?a=activate&id='.$userdata['id'].'&key='.$active_key,
					'password' => $new_password
				), $config['board_name'], $config['admin_email'], $_POST['email']);
				
			} else {
				
				//
				// Send email containing the new password
				//
				usebb_mail($lang['SendpwdEmailSubject'], $lang['SendpwdEmailBody'], array(
					'account_name' => $_POST['user'],
					'password' => $new_password
				), $config['board_name'], $config['admin_email'], $_POST['email']);
				
			}
			
			$template->parse('msgbox', array(
				'box_title' => $lang['SendPassword'],
				'content' => ( $config['users_must_activate'] ) ? sprintf($lang['SendpwdNotActivated'], '<i>'.htmlentities($_POST['user']).'</i>', $_POST['email']) : sprintf($lang['SendpwdActivated'], '<i>'.htmlentities($_POST['user']).'</i>', $_POST['email'])
			));
			
		} else {
			
			$template->parse('msgbox', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['WrongEmail'], $_POST['email'], '<i>'.htmlentities($_POST['user']).'</i>')
			));
			
		}
		
	}
	
} else {
	
	if ( !empty($_POST['submitted']) ) {
		
		if ( !preg_match(USER_PREG, $_POST['user']) )
			$errors[] = strtolower($lang['Username']);
		if ( !preg_match(EMAIL_PREG, $_POST['email']) )
			$errors[] = strtolower($lang['Email']);
		
		if ( is_array($errors) ) {
			
			$template->parse('msgbox', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['MissingFields'], join(', ', $errors))
			));
			
		}
		
	}
	
	//
	// Show the sendpwd form
	//
	$_POST['user'] = ( preg_match(USER_PREG, $_POST['user']) ) ? $_POST['user'] : '';
	$_POST['email'] = ( preg_match(EMAIL_PREG, $_POST['email']) ) ? $_POST['email'] : '';
	$template->parse('sendpwd_form', array(
		'form_begin'          => '<form action="'.usebb_make_url('panel.php', array('a' => 'sendpwd')).'" method="post">',
		'sendpwd'             => $lang['SendPassword'],
		'user'                => $lang['Username'],
		'user_input'          => '<input type="text" name="user" size="25" maxlength="'.$config['username_max_length'].'" value="'.$_POST['user'].'" />',
		'email'               => $lang['Email'],
		'email_input'         => '<input type="text" name="email" size="25" maxlength="255" value="'.$_POST['email'].'" />',
		'everything_required' => $lang['EverythingRequired'],
		'submit_button'       => '<input type="submit" value="'.$lang['SendPassword'].'" />',
		'reset_button'        => '<input type="reset" value="'.$lang['Reset'].'" />',
		'form_end'            => '<input type="hidden" name="submitted" value="true" /></form>'
	));
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>