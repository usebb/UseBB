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
// User wants a new password
//
$session->update('sendpwd');

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
	if ( !($result = $db->query("SELECT id, email, banned, banned_reason FROM ".TABLE_PREFIX."members WHERE name = '".$_POST['user']."'")) )
		$functions->usebb_die('SQL', 'Unable to get user information!', __FILE__, __LINE__);
	$userdata = $db->fetch_result($result);
	
	if ( $db->num_rows($result) == 0 ) {
		
		//
		// This user does not exist, show an error
		//
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchMember'], '<em>'.htmlspecialcharsstripslashes(($_POST['user'])).'</em>')
		));
		
	} elseif ( $userdata['banned'] ) {
		
		//
		// It does exist, but it is banned
		// thus, show another warning...
		//
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['BannedUser'],
			'content' => sprintf($lang['BannedUserExplain'], '<em>'.$_POST['user'].'</em>') . '<br />' . $userdata['banned_reason']
		));
		
	} else {
		
		if ( $_POST['email'] == $userdata['email'] ) {
			
			//
			// Generate the activation key if necessary
			//
			$active = ( $functions->get_config('users_must_activate') ) ? 0 : 1;
			$active_key = ( $functions->get_config('users_must_activate') ) ? $functions->random_key() : '';
			
			$new_password = $functions->random_key();
			
			//
			// Update the row in the user table
			//
			if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."members SET passwd = '".md5($new_password)."', active = ".$active.", active_key = '".md5($active_key)."' WHERE id = ".$userdata['id'])) )
				$functions->usebb_die('SQL', 'Unable to update user information!', __FILE__, __LINE__);
			
			if ( $functions->get_config('users_must_activate') ) {
				
				//
				// Send the activation e-mail if necessary
				//
				$functions->usebb_mail($lang['SendpwdActivationEmailSubject'], $lang['SendpwdActivationEmailBody'], array(
					'account_name' => stripslashes($_POST['user']),
					'activate_link' => $functions->get_config('board_url').$functions->make_url('panel.php', array('act' => 'activate', 'id' => $userdata['id'], 'key' => $active_key), false),
					'password' => $new_password
				), $functions->get_config('board_name'), $functions->get_config('admin_email'), $_POST['email']);
				
			} elseif ( !$functions->get_config('disable_info_emails') ) {
				
				$functions->usebb_mail($lang['SendpwdEmailSubject'], $lang['SendpwdEmailBody'], array(
					'account_name' => stripslashes($_POST['user']),
					'password' => $new_password
				), $functions->get_config('board_name'), $functions->get_config('admin_email'), $_POST['email']);
				
			}
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['SendPassword'],
				'content' => ( $functions->get_config('users_must_activate') ) ? sprintf($lang['SendpwdNotActivated'], '<em>'.unhtml(stripslashes($_POST['user'])).'</em>', $_POST['email']) : sprintf($lang['SendpwdActivated'], '<em>'.unhtml(stripslashes($_POST['user'])).'</em>', $_POST['email'])
			));
			
		} else {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['WrongEmail'], $_POST['email'], '<em>'.unhtml(stripslashes($_POST['user'])).'</em>')
			));
			
		}
		
	}
	
} else {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		$errors = array();
		if ( !preg_match(USER_PREG, $_POST['user']) )
			$errors[] = $lang['Username'];
		if ( !preg_match(EMAIL_PREG, $_POST['email']) )
			$errors[] = $lang['Email'];
		
		if ( count($errors) ) {
			
			$template->parse('msgbox', 'global', array(
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
	$template->parse('sendpwd_form', 'various', array(
		'form_begin'          => '<form action="'.$functions->make_url('panel.php', array('act' => 'sendpwd')).'" method="post">',
		'user_input'          => '<input type="text" name="user" size="25" maxlength="'.$functions->get_config('username_max_length').'" value="'.$_POST['user'].'" />',
		'email_input'         => '<input type="text" name="email" size="25" maxlength="255" value="'.$_POST['email'].'" />',
		'submit_button'       => '<input type="submit" value="'.$lang['SendPassword'].'" />',
		'reset_button'        => '<input type="reset" value="'.$lang['Reset'].'" />',
		'form_end'            => '</form>'
	));
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
