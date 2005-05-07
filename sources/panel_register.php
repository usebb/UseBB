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
// User wants to register
//
$session->update('register');

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

$template->set_page_title($lang['Register']);

$_POST['user'] = ( !empty($_POST['user']) ) ? preg_replace('#\s+#', '_', $_POST['user']) : '';

$username_taken = false;
$username_banned = false;
$email_banned = false;
if ( ( !empty($_POST['user']) && preg_match(USER_PREG, $_POST['user']) ) || ( !empty($_POST['email']) && preg_match(EMAIL_PREG, $_POST['email']) ) ) {
	
	//
	// Get banned usernames and e-mail addresses
	//
	$result = $db->query("SELECT name, email FROM ".TABLE_PREFIX."bans WHERE name <> '' OR email <> ''");
	
	$banned = array('usernames' => array(), 'emails' => array());
	if ( $db->num_rows($result) ) {
		
		while ( $out = $db->fetch_result($result) ) {
			
			//
			// Store all the usernames and e-mail addresses in an array
			//
			if ( !empty($out['name']) )
				$banned['usernames'][] = $out['name'];
			if ( !empty($out['email']) )
				$banned['emails'][] = $out['email'];
			
		}
		
	}
	
	if ( !empty($_POST['user']) && preg_match(USER_PREG, $_POST['user']) ) {
		
		//
		// Check if this username already exists
		//
		$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."members WHERE name = '".$_POST['user']."'");
		$out = $db->fetch_result($result);
		if ( $out['count'] )
			$username_taken = true;
		
		foreach ( $banned['usernames'] as $banned_username ) {
			
			$banned_username = preg_quote($banned_username, '#');
			$banned_username = preg_replace(array('#\\\\\*#', '#\\\\\?#'), array('.*', '.'), $banned_username);
			if ( preg_match('#^'.$banned_username.'$#i', $_POST['user']) )
				$username_banned = true;
			
		}
		
	}
	
	if ( !empty($_POST['email']) && preg_match(EMAIL_PREG, $_POST['email']) ) {
		
		foreach ( $banned['emails'] as $banned_email ) {
			
			$banned_email = preg_quote($banned_email, '#');
			$banned_email = preg_replace(array('#\\\\\*#', '#\\\\\?#'), array('.*', '.'), $banned_email);
			if ( preg_match('#^'.$banned_email.'$#', $_POST['email']) )
				$email_banned = true;
			
		}
		
	}
	
}

//
// If all necessary information has been posted and the user accepted the terms
//
if ( !empty($_POST['user']) && !$username_taken && !$username_banned && !empty($_POST['email']) && !$email_banned && !empty($_POST['passwd1']) && !empty($_POST['passwd2']) && preg_match(USER_PREG, $_POST['user']) && strlen($_POST['user']) <= $functions->get_config('username_max_length') && preg_match(EMAIL_PREG, $_POST['email']) && strlen($_POST['passwd1']) >= $functions->get_config('passwd_min_length') && preg_match(PWD_PREG, $_POST['passwd1']) && $_POST['passwd1'] == $_POST['passwd2'] && !empty($_POST['acceptedterms']) && !empty($_SESSION['saltcode']) && !empty($_POST['saltcode']) && $_SESSION['saltcode'] == $_POST['saltcode'] ) {
	
	//
	// Generate the activation key if necessary
	//
	$active = ( $functions->get_config('users_must_activate') ) ? 0 : 1;
	$active_key = ( $functions->get_config('users_must_activate') ) ? $functions->random_key() : '';
	
	$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."members");
	$out = $db->fetch_result($result);
	if ( !$out['count'] )
		$level = 3;
	else
		$level = 1;
	
	//
	// Create a new row in the user table
	//
	$result = $db->query("INSERT INTO ".TABLE_PREFIX."members ( id, name, email, passwd, regdate, level, active, active_key, template, language, date_format, enable_quickreply, return_to_topic_after_posting, target_blank, hide_avatars, hide_userinfo, hide_signatures, displayed_name ) VALUES ( NULL, '".$_POST['user']."', '".$_POST['email']."', '".md5($_POST['passwd1'])."', ".time().", ".$level.", ".$active.", '".md5($active_key)."', '".$functions->get_config('template')."', '".$functions->get_config('language')."', '".$functions->get_config('date_format')."', ".$functions->get_config('enable_quickreply').", ".$functions->get_config('return_to_topic_after_posting').", ".$functions->get_config('target_blank').", ".$functions->get_config('hide_avatars').", ".$functions->get_config('hide_userinfo').", ".$functions->get_config('hide_signatures').", '".$_POST['user']."' )");
	
	if ( $functions->get_config('users_must_activate') ) {
		
		//
		// Send the activation e-mail if necessary
		//
		$functions->usebb_mail($lang['RegistrationActivationEmailSubject'], $lang['RegistrationActivationEmailBody'], array(
			'account_name' => stripslashes($_POST['user']),
			'activate_link' => $functions->get_config('board_url').$functions->make_url('panel.php', array('act' => 'activate', 'id' => $db->last_id(), 'key' => $active_key), false),
			'password' => $_POST['passwd1']
		), $functions->get_config('board_name'), $functions->get_config('admin_email'), $_POST['email']);
		
	} elseif ( !$functions->get_config('disable_info_emails') ) {
		
		$functions->usebb_mail($lang['RegistrationEmailSubject'], $lang['RegistrationEmailBody'], array(
			'account_name' => stripslashes($_POST['user']),
			'password' => $_POST['passwd1']
		), $functions->get_config('board_name'), $functions->get_config('admin_email'), $_POST['email']);
		
	}
	
	//
	// Update the statistics
	//
	$result = $db->query("UPDATE ".TABLE_PREFIX."stats SET content = content+1 WHERE name = 'members'");
	
	//
	// Registration was succesful!
	//
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Register'],
		'content' => ( $functions->get_config('users_must_activate') ) ? sprintf($lang['RegisteredNotActivated'], '<em>'.unhtml(stripslashes($_POST['user'])).'</em>', $_POST['email']) : sprintf($lang['RegisteredActivated'], '<em>'.$_POST['user'].'</em>', $_POST['email'])
	));
	
} elseif ( !empty($_POST['acceptedterms']) ) {
	
	//
	// The user agreed to the terms of use, show the registration form
	//
	
	if ( !empty($_POST['sentregform']) ) {
		
		//
		// The form has been submitted but there are missing fields
		//
		
		if ( $username_taken ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['UserAlreadyExists'], '<em>'.unhtml(stripslashes($_POST['user'])).'</em>')
			));
			
		} elseif ( $username_banned ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['BannedUsername'], '<em>'.unhtml(stripslashes($_POST['user'])).'</em>')
			));
			
		}
		
		if ( $email_banned ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['BannedEmail'], $_POST['email'])
			));
			
		}
		
		//
		// Define missing fields
		//
		$errors = array();
		if ( empty($_POST['user']) || !preg_match(USER_PREG, $_POST['user']) || strlen($_POST['user']) > $functions->get_config('username_max_length') )
			$errors[] = $lang['Username'];
		if ( empty($_POST['email']) || !preg_match(EMAIL_PREG, $_POST['email']) )
			$errors[] = $lang['Email'];
		if ( empty($_POST['passwd1']) || empty($_POST['passwd2']) || strlen($_POST['passwd1']) < $functions->get_config('passwd_min_length') || !preg_match(PWD_PREG, $_POST['passwd1']) || $_POST['passwd1'] != $_POST['passwd2'] )
			$errors[] = $lang['Password'];
		
		//
		// Show an error message
		//
		if ( count($errors) ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['MissingFields'], join(', ', $errors))
			));
			
		}
		
	}
	
	//
	// Show the registration form
	//
	$_POST['user'] = ( !empty($_POST['user']) && preg_match(USER_PREG, $_POST['user']) ) ? $_POST['user'] : '';
	$_POST['email'] = ( !empty($_POST['email']) && preg_match(EMAIL_PREG, $_POST['email']) ) ? $_POST['email'] : '';
	$template->parse('register_form', 'various', array(
		'form_begin'          => '<form action="'.$functions->make_url('panel.php', array('act' => 'register')).'" method="post">',
		'user_input'          => '<input type="text" name="user" size="25" maxlength="'.$functions->get_config('username_max_length').'" value="'.unhtml(stripslashes($_POST['user'])).'" />',
		'email_input'         => '<input type="text" name="email" size="25" maxlength="255" value="'.$_POST['email'].'" />',
		'passwd1_input'       => '<input type="password" name="passwd1" size="25" maxlength="255" />',
		'passwd_info'         => sprintf($lang['PasswdInfo'], $functions->get_config('passwd_min_length')),
		'passwd2_input'       => '<input type="password" name="passwd2" size="25" maxlength="255" />',
		'submit_button'       => '<input type="submit" name="sentregform" value="'.$lang['Register'].'" /><input type="hidden" name="acceptedterms" value="true" /><input type="hidden" name="saltcode" value="'.$_POST['saltcode'].'" />',
		'reset_button'        => '<input type="reset" value="'.$lang['Reset'].'" />',
		'form_end'            => '</form>'
	));
	
} elseif ( !empty($_POST['notaccepted']) ) {
	
	//
	// The user did not accept to the terms of use
	//
	$refere_to = $_SESSION['refere_to'];
	unset($_SESSION['refere_to']);
	header('Location: '.$refere_to);
	
} else {
	
	//
	// The user did not agree yet to the terms of use
	//
	if ( !$session->sess_info['user_id'] ) {
		
		$_SESSION['refere_to'] = ( !empty($_SERVER['HTTP_REFERER']) && preg_match('#^'.preg_quote($functions->get_config('board_url'), '#').'#', $_SERVER['HTTP_REFERER']) && !preg_match('#(login|logout|register|activate|sendpwd)#', $_SERVER['HTTP_REFERER']) ) ? $functions->attach_sid($_SERVER['HTTP_REFERER']) : $functions->get_config('board_url').$functions->make_url('index.php', array(), false);
		$_SESSION['saltcode'] = $saltcode = $functions->random_key();
		
		$template->parse('confirm_form', 'global', array(
			'form_begin' => '<form action="'.$functions->make_url('panel.php', array('act' => 'register')).'" method="post">',
			'title' => $lang['TermsOfUse'],
			'content' => nl2br(unhtml($lang['TermsOfUseContent'])),
			'submit_button'       => '<input type="submit" name="acceptedterms" value="'.$lang['IAccept'].'" /><input type="hidden" name="saltcode" value="'.$saltcode.'" />',
			'cancel_button'       => '<input type="submit" name="notaccepted" value="'.$lang['IDontAccept'].'" />',
			'form_end' => '</form>'
		));
		
	} else {
		
		//
		// If he/she is logged in, return to index
		//
		header('Location: '.$functions->get_config('board_url').$functions->make_url('index.php', array(), false));
		
	}
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
