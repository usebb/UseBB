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
 * Mail form
 *
 * Gives a form that can be used to e-mail members.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2012 UseBB Team
 * @package	UseBB
 */

define('INCLUDED', true);
define('ROOT_PATH', './');

//
// Include usebb engine
//
require(ROOT_PATH.'sources/common.php');

$mail_user = ( intval($functions->get_config('email_view_level')) === 1 && !empty($_GET['id']) && valid_int($_GET['id']) );

if ( $mail_user || ( $functions->get_config('enable_contactadmin') && $functions->get_config('enable_contactadmin_form') && !empty($_GET['act']) && $_GET['act'] == 'admin' ) ) {
	
	//
	// Update and get the session information
	//
	$session->update('sendemail:'. ( $mail_user ? $_GET['id'] : 'admin' ));
	
	//
	// Include the page header
	//
	require(ROOT_PATH.'sources/page_head.php');

	$is_guest = ( $session->sess_info['user_id'] == LEVEL_GUEST );
	
	if ( $mail_user ) {
		
		//
		// Get the user information
		//
		if ( $_GET['id'] == $session->sess_info['user_id'] ) {
			
			//
			// This user wants to send an email to himself, so we don't need a new query
			//
			$own_mailpage = true;
			$user_to_mail = $session->sess_info['user_info'];
			
		} else {
			
			//
			// This user is not emailing himself, so we need a new query
			//
			$own_mailpage = false;
			$result = $db->query("SELECT id, displayed_name, email, email_show, language FROM ".TABLE_PREFIX."members WHERE id = ".$_GET['id']);
			$user_to_mail = $db->fetch_result($result);
			
		}

	} else {
		
		//
		// Send to board admin
		//
		$own_mailpage = false;
		$user_to_mail = array(
			'displayed_name' => addslashes($lang['Administrator']),
			'language' => $functions->get_config('language', true),
			'email' => $functions->get_config('admin_email'),
		);

	}
	
	if ( !$mail_user || $own_mailpage || $user_to_mail['id'] ) {
		
		if ( $mail_user && !$user_to_mail['email_show'] && $functions->get_user_level() < $functions->get_config('view_hidden_email_addresses_min_level') && !$own_mailpage ) {
			
			//
			// You can't e-mail this user if he/she chose not to receive e-mails
			// unless you are an admin or your are trying to e-mail yourself :p
			//
			$template->add_breadcrumb($lang['Error']);
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => $lang['NoMails']
			));
			
		} else {
			
			//
			// Pose the antispam question
			//
			$functions->pose_antispam_question();
			
			$template->add_breadcrumb(sprintf($lang['SendEmail'], unhtml(stripslashes($user_to_mail['displayed_name']))));
			
			$_POST['name'] = ( !empty($_POST['name']) ) ? stripslashes($_POST['name']) : '';
			$_POST['email'] = ( !empty($_POST['email']) ) ? stripslashes($_POST['email']) : '';
			$_POST['subject'] = ( !empty($_POST['subject']) ) ? stripslashes($_POST['subject']) : '';
			$_POST['body'] = ( !empty($_POST['body']) ) ? stripslashes($_POST['body']) : '';

			if ( !empty($_POST['subject']) && !empty($_POST['body'])
				&& ( !$is_guest || ( !empty($_POST['name']) && !empty($_POST['email']) && $functions->validate_email($_POST['email']) ) )
				&& $functions->verify_form() ) {
				
				//
				// All information is passed, now send the mail
				//
				$lang_email = $functions->fetch_language($user_to_mail['language']);

				if ( $is_guest ) {
					
					$username = sprintf($lang['GuestName'], $_POST['name']);
					$from_email = $_POST['email'];
					$bcc_email = ( !empty($_POST['bcc']) ) ? $_POST['email'] : '';

				} else {
					
					$username = stripslashes($session->sess_info['user_info']['displayed_name']);
					$from_email = $session->sess_info['user_info']['email'];
					$bcc_email = ( !empty($_POST['bcc']) && !$own_mailpage ) ? $session->sess_info['user_info']['email'] : '';

				}

				$functions->usebb_mail($_POST['subject'], $lang_email['UserEmailBody'], array(
					'username' => $username,
					'body' => $_POST['body']
				), $username, $from_email, $user_to_mail['email'], $bcc_email);
				
				$template->parse('msgbox', 'global', array(
					'box_title' => $lang['Note'],
					'content' => sprintf($lang['EmailSent'], '<em>'.unhtml(stripslashes($user_to_mail['displayed_name'])).'</em>')
				));
				
			} else {
				
				if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
					
					//
					// Some fields have not been filled in,
					//
					$errors = array();
					if ( $is_guest ) {

						if ( empty($_POST['name']) )
							$errors[] = $lang['Name'];
						if ( empty($_POST['email']) || !$functions->validate_email($_POST['email']) )
							$errors[] = $lang['Email'];

					}
					if ( empty($_POST['subject']) )
						$errors[] = $lang['Subject'];
					if ( empty($_POST['body']) )
						$errors[] = $lang['Body'];
					
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
				// Show the mail form
				//
				$to_v = unhtml(stripslashes($user_to_mail['displayed_name']));
				if ( $mail_user )
					$to_v = '<a href="'.$functions->make_url('profile.php', array('id' => $_GET['id'])).'">'.$to_v.'</a>';
				if ( $functions->get_user_level() >= $functions->get_config('view_hidden_email_addresses_min_level') )
					$to_v .= ' &lt;<a href="mailto:'.$user_to_mail['email'].'">'.$user_to_mail['email'].'</a>&gt;';

				$_POST['name'] = ( !empty($_POST['name']) ) ? unhtml($_POST['name']) : '';
				$_POST['email'] = ( !empty($_POST['email']) ) ? unhtml($_POST['email']) : '';
				$_POST['subject'] = ( !empty($_POST['subject']) ) ? unhtml($_POST['subject']) : '';
				$_POST['body'] = ( !empty($_POST['body']) ) ? unhtml($_POST['body']) : '';

				$bcc_checked = ( !empty($_POST['bcc']) && !$own_mailpage ) ? ' checked="checked"' : '';
				$bcc_disabled = ( $own_mailpage ) ? ' disabled="disabled"' : '';

				$params = array(
					'form_begin' => '<form action="'.$functions->make_url('mail.php', $_GET).'" method="post">',
					'sendemail' => sprintf($lang['SendEmail'], unhtml(stripslashes($user_to_mail['displayed_name']))),
					'to_v' => $to_v,
					'subject_input' => '<input type="text" name="subject" id="subject" size="50" value="'.$_POST['subject'].'" />',
					'body_input' => '<textarea rows="'.$template->get_config('textarea_rows').'" cols="'.$template->get_config('textarea_cols').'" name="body">'.$_POST['body'].'</textarea>',
					'bcc_input' => '<label><input type="checkbox" name="bcc" value="1"'.$bcc_checked.$bcc_disabled.' /> '.$lang['BCCMyself'].'</label>',
					'submit_button' => '<input type="submit" name="submit" value="'.$lang['Send'].'" />',
					'form_end' => '</form>'
				);

				if ( $is_guest ) {
					
					$params['name_v'] = '<input type="text" name="name" id="name" size="30" value="'.$_POST['name'].'" />';
					$params['email_v'] = '<input type="text" name="email" id="email" size="30" value="'.$_POST['email'].'" />';

				} else {
					
					$params['from_v'] = '<a href="'.$functions->make_url('profile.php', array('id' => $session->sess_info['user_info']['id'])).'">'.unhtml(stripslashes($session->sess_info['user_info']['displayed_name'])).'</a> &lt;<a href="mailto:'.$session->sess_info['user_info']['email'].'">'.$session->sess_info['user_info']['email'].'</a>&gt;';

				}
				
				$template->parse($is_guest ? 'mail_form_guest' : 'mail_form', 'various', $params, false, true);
				$template->set_js_onload($is_guest ? "set_focus('name')" : "set_focus('subject')");
				
			}
			
		}
		
	} else {
		
		//
		// This user does not exist, show an error
		//
		header(HEADER_404);
		$template->add_breadcrumb($lang['Error']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchMember'], 'ID '.$_GET['id'])
		));
		
	}
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
} else {
	
	//
	// There's no user ID or the mail form has not been enabled!
	// Get us back to the index...
	//
	$functions->redirect('index.php');
	
}

?>
