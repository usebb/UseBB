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
// User wants to register
//

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

$template->set_page_title($lang['Register']);

$_POST['user'] = ( !empty($_POST['user']) ) ? $_POST['user'] : '';
$_POST['user'] = preg_replace('/ +/', ' ', $_POST['user']);
$_POST['email'] = ( !empty($_POST['email']) ) ? $_POST['email'] : '';

//
// If all necessary information has been posted
//
if ( preg_match(USER_PREG, $_POST['user']) && preg_match(EMAIL_PREG, $_POST['email']) ) {
	
	//
	// Check if this username already exists
	//
	if ( !($result = $db->query("SELECT id FROM ".TABLE_PREFIX."users WHERE name = '".$_POST['user']."'")) )
		usebb_die('SQL', 'Unable to get user information!', __FILE__, __LINE__);
	if ( $db->num_rows($result) == 1 ) {
		
		//
		// If it does, show this error
		//
		$template->parse('msgbox', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['UserAlreadyExists'], '<i>'.$_POST['user'].'</i>')
		));
		
	} else {
		
		//
		// Get banned usernames and e-mail addresses
		//
		if ( !($result = $db->query("SELECT name, email FROM ".TABLE_PREFIX."bans WHERE name <> '' OR email <> ''")) )
			usebb_die('SQL', 'Unable to get banned usernames and e-mail adresses!', __FILE__, __LINE__);
		
		$username_banned = FALSE;
		$email_banned = FALSE;
		
		if ( $db->num_rows($result) > 0 ) {
			
			$banned = array();
			$banned['usernames'] = array();
			$banned['emails'] = array();
			while ( $out = $db->fetch_result($result) ) {
				
				//
				// Store all the usernames and e-mail addresses in an array
				//
				if ( !empty($out['name']) )
					$banned['usernames'][] = $out['name'];
				if ( !empty($out['email']) )
					$banned['emails'][] = $out['email'];
				
			}
			
			foreach ( $banned['usernames'] as $banned_username ) {
				
				$banned_username = str_replace('.', '\.', $banned_username);
				$banned_username = str_replace('-', '\-', $banned_username);
				$banned_username = str_replace('+', '\+', $banned_username);
				$banned_username = str_replace('*', '[a-z0-9\.\-_\+ ]+', $banned_username);
				$banned_username = str_replace('?', '[a-z0-9\.\-_\+ ]', $banned_username);
				if ( preg_match('/^'.$banned_username.'$/i', $_POST['user']) ) {
					
					//
					// If the username matches a banned one, stop registration
					//
					$username_banned = TRUE;
					break;
					
				}
				
			}
			
			foreach ( $banned['emails'] as $banned_email ) {
				
				$banned_email = str_replace('.', '\.', $banned_email);
				$banned_email = str_replace('-', '\-', $banned_email);
				$banned_email = str_replace('*', '[a-z0-9\.\-_]+', $banned_email);
				$banned_email = str_replace('?', '[a-z0-9\.\-_]', $banned_email);
				if ( preg_match('/^'.$banned_email.'$/', $_POST['email']) ) {
					
					//
					// If the e-mail address matches a banned one, stop registration
					//
					$email_banned = TRUE;
					break;
					
				}
				
			}
			
		}
		
		if ( $username_banned ) {
			
			$template->parse('msgbox', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['BannedUsername'], '<i>'.$_POST['user'].'</i>')
			));
			
		} elseif ( $email_banned ) {
			
			$template->parse('msgbox', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['BannedEmail'], $_POST['email'])
			));
			
		} else {
			
			//
			// Generate the activation key if necessary
			//
			$active = ( $config['users_must_activate'] ) ? 0 : 1;
			$active_key = ( $config['users_must_activate'] ) ? usebb_random_key() : '';
			
			if ( !($result = $db->query("SELECT id FROM ".TABLE_PREFIX."users")) )
				usebb_die('SQL', 'Unable to get user count!', __FILE__, __LINE__);
			if ( $db->num_rows($result) == 0 )
				$level = 3;
			else
				$level = 1;
			
			$password = usebb_random_key();
			
			//
			// Create a new row in the user table
			//
			if ( !($result = $db->query("INSERT INTO ".TABLE_PREFIX."users ( id, name, email, passwd, regdate, level, active, active_key ) VALUES ( NULL, '".$_POST['user']."', '".$_POST['email']."', '".md5($password)."', ".gmmktime().", ".$level.", ".$active.", '".md5($active_key)."' )")) )
				usebb_die('SQL', 'Unable to insert user information!', __FILE__, __LINE__);
			
			if ( $config['users_must_activate'] ) {
				
				//
				// Send the activation e-mail if necessary
				//
				usebb_mail($lang['RegistrationActivationEmailSubject'], $lang['RegistrationActivationEmailBody'], array(
					'account_name' => $_POST['user'],
					'activate_link' => $config['board_url'].'panel.php?a=activate&id='.$db->last_id().'&key='.$active_key,
					'password' => $password
				), $config['board_name'], $config['admin_email'], $_POST['email']);
				
			} else {
				
				//
				// Send the activation e-mail if necessary
				//
				usebb_mail($lang['RegistrationEmailSubject'], $lang['RegistrationEmailBody'], array(
					'account_name' => $_POST['user'],
					'password' => $password
				), $config['board_name'], $config['admin_email'], $_POST['email']);
				
			}
			
			//
			// Registration was succesful!
			//
			$template->parse('msgbox', array(
				'box_title' => $lang['Register'],
				'content' => ( $config['users_must_activate'] ) ? sprintf($lang['RegisteredNotActivated'], '<i>'.$_POST['user'].'</i>', $_POST['email']) : sprintf($lang['RegisteredActivated'], '<i>'.$_POST['user'].'</i>', $_POST['email'])
			));
			
		}
		
	}
	
} else {
	
	if ( !empty($_POST['submitted']) ) {
		
		//
		// The form has been submitted but there are missing fields
		//
		
		//
		// Define missing fields
		//
		if ( !preg_match(USER_PREG, $_POST['user']) )
			$errors[] = strtolower($lang['Username']);
		if ( !preg_match(EMAIL_PREG, $_POST['email']) )
			$errors[] = strtolower($lang['Email']);
		
		//
		// Show an error message
		//
		if ( is_array($errors) ) {
			
			$template->parse('msgbox', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['MissingFields'], join(', ', $errors))
			));
			
		}
		
	} else {
		
		//
		// The form has not been submitted yet
		// Show the terms of use
		//
		$template->parse('msgbox', array(
			'box_title' => $lang['TermsOfUse'],
			'content' => nl2br($lang['TermsOfUseContent'])
		));
		
	}
	
	//
	// Show the registration form
	//
	$_POST['user'] = ( preg_match(USER_PREG, $_POST['user']) ) ? $_POST['user'] : '';
	$_POST['email'] = ( preg_match(EMAIL_PREG, $_POST['email']) ) ? $_POST['email'] : '';
	$template->parse('register_form', array(
		'form_begin'          => '<form action="'.usebb_make_url('panel.php', array('a' => 'register')).'" method="post">',
		'register_form'       => $lang['Register'],
		'user'                => $lang['Username'],
		'user_input'          => '<input type="text" name="user" size="25" maxlength="'.$config['username_max_length'].'" value="'.$_POST['user'].'" />',
		'email'               => $lang['Email'],
		'email_input'         => '<input type="text" name="email" size="25" maxlength="255" value="'.$_POST['email'].'" />',
		'everything_required' => $lang['EverythingRequired'],
		'submit_button'       => '<input type="submit" name="submit" value="'.$lang['Register'].'" />',
		'reset_button'        => '<input type="reset" value="'.$lang['Reset'].'" />',
		'form_end'            => '<input type="hidden" name="submitted" value="true" /></form>'
	));
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>