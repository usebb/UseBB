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
 * Panel user registration
 *
 * Gives an interface to register user accounts.
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

//
// User wants to register
//
$session->update('register');

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

$template->add_breadcrumb($lang['Register']);

if ( $functions->get_config('disable_registrations') ) {
	
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['RegistrationsDisabled'],
		'content' => $functions->get_config('disable_registrations_reason')
	));
	
} else {
	
	//
	// Pose the antispam question
	//
	$functions->pose_antispam_question();

	$_POST['user'] = ( !empty($_POST['user']) ) ? preg_replace('#\s+#', ' ', $_POST['user']) : '';
	
	$username_taken = $username_banned = $email_taken = $email_banned = false;
	if ( ( !empty($_POST['user']) && preg_match(USER_PREG, $_POST['user']) ) || ( !empty($_POST['email']) && $functions->validate_email($_POST['email']) ) ) {
		
		//
		// Get banned usernames and e-mail addresses
		//
		$result = $db->query("SELECT name, email FROM ".TABLE_PREFIX."bans WHERE name <> '' OR email <> ''");
		$banned = array('usernames' => array(), 'emails' => array());
		while ( $out = $db->fetch_result($result) ) {
			
			//
			// Store all the usernames and e-mail addresses in an array
			//
			if ( !empty($out['name']) )
				$banned['usernames'][] = $out['name'];
			if ( !empty($out['email']) )
				$banned['emails'][] = $out['email'];
			
		}
		
		if ( !empty($_POST['user']) && preg_match(USER_PREG, $_POST['user']) ) {
			
			//
			// Check if this username already exists
			//
			$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."members WHERE name = '".$_POST['user']."' OR displayed_name = '".$_POST['user']."'");
			$out = $db->fetch_result($result);
			if ( $out['count'] )
				$username_taken = true;
			
			foreach ( $banned['usernames'] as $banned_username ) {
				
				if ( preg_match('#^'.str_replace(array('\*', '\?'), array('.*', '.'), preg_quote(stripslashes($banned_username), '#')).'$#i', $_POST['user']) )
					$username_banned = true;
				
			}
			
		}
		
		if ( !empty($_POST['email']) && $functions->validate_email($_POST['email']) ) {
			
			//
			// Check if this email already exists
			//
			if ( !$functions->get_config('allow_duplicate_emails') ) {
				
				$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."members WHERE email = '".$_POST['email']."'");
				$out = $db->fetch_result($result);
				if ( $out['count'] )
					$email_taken = true;
				
			}
			
			foreach ( $banned['emails'] as $banned_email ) {
				
				if ( preg_match('#^'.str_replace(array('\*', '\?'), array('.*', '.'), preg_quote($banned_email, '#')).'$#i', $_POST['email']) )
					$email_banned = true;
				
			}

			//
			// Check Stop Forum Spam API for e-mail address
			//
			$email_banned = ( $email_banned || $functions->sfs_email_banned($_POST['email']) );
			
		}
		
	}
	
	//
	// If all necessary information has been posted and the user accepted the terms
	//
	$valid_password = ( !empty($_POST['passwd1']) && $functions->validate_password(stripslashes($_POST['passwd1']), true) );
	if ( !empty($_POST['user']) && strlen($_POST['user']) >= $functions->get_config('username_min_length') && strlen($_POST['user']) <= $functions->get_config('username_max_length') && !$username_taken && !$username_banned && !empty($_POST['email']) && !$email_taken && !$email_banned && !empty($_POST['passwd2']) && preg_match(USER_PREG, $_POST['user']) && $functions->validate_email($_POST['email']) && $valid_password && strlen(stripslashes($_POST['passwd1'])) >= $functions->get_config('passwd_min_length') && $_POST['passwd1'] == $_POST['passwd2'] && !empty($_POST['acceptedterms']) ) {
		
		//
		// Registration log file
		//
		$log_file = $functions->get_config('registration_log_file');
		if ( $functions->get_config('enable_registration_log') && !empty($log_file) ) {
			
			if ( preg_match('#^[^/\.]#', $log_file) )
				$log_file = ROOT_PATH.$log_file;
			
			if ( file_exists($log_file) && is_writable($log_file) ) {
				
				$log_entry = $functions->make_date(time(), 'D, d M Y H:i:s', true, false)." @ ".$functions->get_config('board_name')."\n";
				
				$entry_data = array(
					'Username'		=> $_POST['user'],
					'Email address'		=> $_POST['email'],
					'IP address'		=> $session->sess_info['ip_addr'],
					'Host name'		=> gethostbyaddr($session->sess_info['ip_addr']),
					'Browser'		=> $_SERVER['HTTP_USER_AGENT'],
					'Session started'	=> $functions->make_date($session->sess_info['started'], 'D, d M Y H:i:s', true, false),
					'Pages'			=> $session->sess_info['pages']
				);
				
				foreach ( $entry_data as $key => $val )
					$log_entry .= "\t".$key.":\t".$val."\n";
				
				$fh = fopen($log_file, 'a');
				fwrite($fh, $log_entry);
				fclose($fh);
				
			}
			
		}
		
		$level = ( intval($functions->get_stats('members')) > 0 ) ? LEVEL_MEMBER : LEVEL_ADMIN;
		
		//
		// Generate the activation key if necessary
		//
		if ( $level == LEVEL_ADMIN ) {
			
			//
			// The first user does not need activation
			//
			$active = USER_ACTIVE;
			$active_key_md5 = '';
			$msgbox_content = sprintf($lang['RegisteredActivated'], '<em>'.unhtml(stripslashes($_POST['user'])).'</em>', $_POST['email']);
			
		} else {
			
			switch ( intval($functions->get_config('activation_mode')) ) {
				
				// No activation
				case 0:
					$active = $functions->user_active_value();
					$active_key_md5 = '';
					$msgbox_content = sprintf($lang['RegisteredActivated'], '<em>'.unhtml(stripslashes($_POST['user'])).'</em>', $_POST['email']);
					break;
				
				// E-mail activation
				case 1:
					$active = USER_INACTIVE;
					$active_key = $functions->random_key(); # used in the email url
					$active_key_md5 = md5($active_key);
					$msgbox_content = sprintf($lang['RegisteredNotActivated'], '<em>'.unhtml(stripslashes($_POST['user'])).'</em>', $_POST['email']);
					break;
				
				// Admin activation
				case 2:
					$active = USER_INACTIVE;
					$active_key_md5 = '';
					$msgbox_content = sprintf($lang['RegisteredNotActivatedByAdmin'], '<em>'.unhtml(stripslashes($_POST['user'])).'</em>', $_POST['email']);
				
			}
			
		}
		
		//
		// Create a new row in the user table
		//
		$result = $db->query("INSERT INTO ".TABLE_PREFIX."members ( id, name, email, passwd, regdate, level, active, active_key, template, language, date_format, timezone, dst, enable_quickreply, return_to_topic_after_posting, target_blank, hide_avatars, hide_userinfo, hide_signatures, displayed_name, banned_reason, signature ) VALUES ( NULL, '".$_POST['user']."', '".$_POST['email']."', '".md5(stripslashes($_POST['passwd1']))."', ".time().", ".$level.", ".$active.", '".$active_key_md5."', '".$functions->get_config('template')."', '".$functions->get_config('language')."', '".$functions->get_config('date_format')."', ".$functions->get_config('timezone').", ".$functions->get_config('dst').", ".$functions->get_config('enable_quickreply').", ".$functions->get_config('return_to_topic_after_posting').", ".$functions->get_config('target_blank').", ".$functions->get_config('hide_avatars').", ".$functions->get_config('hide_userinfo').", ".$functions->get_config('hide_signatures').", '".$_POST['user']."', '', '' )");
		$inserted_user_id = $db->last_id();
		
		//
		// Update the statistics
		//
		$functions->set_stats('members', 1, true);
		
		if ( intval($functions->get_config('activation_mode')) === 1 && $level < LEVEL_ADMIN ) {
			
			//
			// Send the activation e-mail if necessary
			//
			$functions->usebb_mail($lang['RegistrationActivationEmailSubject'], $lang['RegistrationActivationEmailBody'], array(
				'account_name' => stripslashes($_POST['user']),
				'activate_link' => $functions->get_config('board_url').$functions->make_url('panel.php', array('act' => 'activate', 'id' => $inserted_user_id, 'key' => $active_key), false),
				'password' => stripslashes($_POST['passwd1'])
			), $functions->get_config('board_name'), $functions->get_config('admin_email'), $_POST['email']);
			
		} else {
			
			if ( intval($functions->get_config('activation_mode')) === 2 && $level < LEVEL_ADMIN ) {
				
				$functions->usebb_mail($lang['AdminActivationEmailSubject'], $lang['AdminActivationEmailBody'], array(
					'account_name' => stripslashes($_POST['user']),
					'password' => stripslashes($_POST['passwd1'])
				), $functions->get_config('board_name'), $functions->get_config('admin_email'), $_POST['email']);
				
			} else {
				
				$functions->usebb_mail($lang['RegistrationEmailSubject'], $lang['RegistrationEmailBody'], array(
					'account_name' => stripslashes($_POST['user']),
					'password' => stripslashes($_POST['passwd1'])
				), $functions->get_config('board_name'), $functions->get_config('admin_email'), $_POST['email']);
				
			}
			
		}
		
		//
		// Registration was succesful!
		//
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Register'],
			'content' => $msgbox_content
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
					'box_title' => $lang['Note'],
					'content' => sprintf($lang['DisplayedNameTaken'], '<em>'.unhtml(stripslashes($_POST['user'])).'</em>')
				));
				
			} elseif ( $username_banned ) {
				
				$template->parse('msgbox', 'global', array(
					'box_title' => $lang['Note'],
					'content' => sprintf($lang['BannedUsername'], '<em>'.unhtml(stripslashes($_POST['user'])).'</em>')
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
			
			//
			// Define missing fields
			//
			$errors = array();
			if ( empty($_POST['user']) || !preg_match(USER_PREG, $_POST['user']) )
				$errors[] = $lang['Username'];
			if ( empty($_POST['email']) || !$functions->validate_email($_POST['email']) )
				$errors[] = $lang['Email'];
			if ( empty($_POST['passwd1']) || empty($_POST['passwd2']) || $_POST['passwd1'] != $_POST['passwd2'] )
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
			
			if ( !empty($_POST['user']) && strlen($_POST['user']) < $functions->get_config('username_min_length') ) {
				
				$template->parse('msgbox', 'global', array(
					'box_title' => $lang['Error'],
					'content' => sprintf($lang['StringTooShort'], $lang['Username'], $functions->get_config('username_min_length'))
				));
				
			}
			
			if ( !empty($_POST['user']) && strlen($_POST['user']) > $functions->get_config('username_max_length') ) {
				
				$template->parse('msgbox', 'global', array(
					'box_title' => $lang['Error'],
					'content' => sprintf($lang['StringTooLong'], $lang['Username'], $functions->get_config('username_max_length'))
				));
				
			}
			
			if ( !empty($_POST['passwd1']) && !$valid_password ) {
				
				$template->parse('msgbox', 'global', array(
					'box_title' => $lang['Error'],
					'content' => sprintf($lang['PasswdInfoNew'], $functions->get_config('passwd_min_length'))
				));
	
			} elseif ( !empty($_POST['passwd1']) && strlen(stripslashes($_POST['passwd1'])) < $functions->get_config('passwd_min_length') ) {
				
				$template->parse('msgbox', 'global', array(
					'box_title' => $lang['Error'],
					'content' => sprintf($lang['StringTooShort'], $lang['Password'], $functions->get_config('passwd_min_length'))
				));
				
			}
			
		}
		
		//
		// Show the registration form
		//
		$_POST['user'] = ( !empty($_POST['user']) && preg_match(USER_PREG, $_POST['user']) ) ? $_POST['user'] : '';
		$_POST['email'] = ( !empty($_POST['email']) && $functions->validate_email($_POST['email']) ) ? $_POST['email'] : '';
		$template->parse('register_form', 'various', array(
			'form_begin'          => '<form action="'.$functions->make_url('panel.php', array('act' => 'register')).'" method="post">',
			'user_input'          => '<input type="text" name="user" id="user" size="25" maxlength="'.$functions->get_config('username_max_length').'" value="'.unhtml(stripslashes($_POST['user'])).'" />',
			'email_input'         => '<input type="text" name="email" size="25" maxlength="255" value="'.$_POST['email'].'" />',
			'passwd1_input'       => '<input type="password" name="passwd1" size="25" maxlength="255" />',
			'passwd_info'         => sprintf($lang['PasswdInfoNew'], $functions->get_config('passwd_min_length')),
			'passwd2_input'       => '<input type="password" name="passwd2" size="25" maxlength="255" />',
			'submit_button'       => '<input type="submit" name="sentregform" value="'.$lang['Register'].'" /><input type="hidden" name="acceptedterms" value="true" />',
			'form_end'            => '</form>'
		));
		$template->set_js_onload("set_focus('user')");
		
	} elseif ( !empty($_POST['notaccepted']) ) {
		
		//
		// The user did not accept to the terms of use
		//
		$refere_to = ( !empty($_SESSION['refere_to']) ) ? $functions->attach_sid($_SESSION['refere_to']) : $functions->get_config('board_url').$functions->make_url('index.php', array(), false);
		unset($_SESSION['refere_to']);
		$functions->raw_redirect($refere_to);
		
	} else {
		
		//
		// The user did not agree yet to the terms of use
		//
		if ( !$session->sess_info['user_id'] ) {
			
			$_SESSION['refere_to'] = ( !empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $functions->get_config('board_url')) === 0 && !preg_match('#(?:login|logout|register|activate|sendpwd|install)#', $_SERVER['HTTP_REFERER']) ) ? $_SERVER['HTTP_REFERER'] : '';
			
			$template->parse('confirm_form', 'global', array(
				'more_css_classes' => ' termsofuse',
				'form_begin' => '<form action="'.$functions->make_url('panel.php', array('act' => 'register')).'" method="post">',
				'title' => $lang['TermsOfUse'],
				'content' => nl2br($lang['TermsOfUseContent']),
				'submit_button' => '<input type="submit" name="acceptedterms" value="'.$lang['IAccept'].'" />',
				'cancel_button' => '<input type="submit" name="notaccepted" value="'.$lang['IDontAccept'].'" />',
				'form_end' => '</form>'
			));
			
		} else {
			
			//
			// If he/she is logged in, return to index
			//
			$functions->redirect('index.php');
			
		}
		
	}
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
