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
 * Panel password retrieval
 *
 * Gives an interface to create and retrieve new passwords via e-mail.
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

//
// User wants a new password
//
$session->update('sendpwd');

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

$template->set_page_title($lang['SendPassword']);

$_POST['user'] = ( !empty($_POST['user']) ) ? preg_replace('#\s+#', ' ', $_POST['user']) : '';

if ( !empty($_POST['user']) && !empty($_POST['email']) && preg_match(USER_PREG, $_POST['user']) && preg_match(EMAIL_PREG, $_POST['email']) ) {
	
	//
	// Check if this username already exists
	//
	$result = $db->query("SELECT id, email, banned, banned_reason FROM ".TABLE_PREFIX."members WHERE name = '".$_POST['user']."'");
	$userdata = $db->fetch_result($result);
	
	if ( !$userdata['id'] ) {
		
		//
		// This user does not exist, show an error
		//
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchMember'], '<em>'.htmlspecialchars(stripslashes($_POST['user'])).'</em>')
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
			
			$new_password = $functions->random_key();
			
			//
			// Update the row in the user table
			//
			$result = $db->query("UPDATE ".TABLE_PREFIX."members SET passwd = '".md5($new_password)."' WHERE id = ".$userdata['id']);
			
			//
			// E-mail new password
			//
			$functions->usebb_mail($lang['SendpwdEmailSubject'], $lang['SendpwdEmailBody'], array(
				'account_name' => stripslashes($_POST['user']),
				'password' => $new_password
			), $functions->get_config('board_name'), $functions->get_config('admin_email'), $_POST['email']);
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['SendPassword'],
				'content' => sprintf($lang['SendpwdActivated'], '<em>'.unhtml(stripslashes($_POST['user'])).'</em>', $_POST['email'])
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
		if ( empty($_POST['user']) || !preg_match(USER_PREG, $_POST['user']) )
			$errors[] = $lang['Username'];
		if ( empty($_POST['email']) || !preg_match(EMAIL_PREG, $_POST['email']) )
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
	$_POST['user'] = ( !empty($_POST['user']) && preg_match(USER_PREG, $_POST['user']) ) ? $_POST['user'] : '';
	$_POST['email'] = ( !empty($_POST['email']) && preg_match(EMAIL_PREG, $_POST['email']) ) ? $_POST['email'] : '';
	$template->parse('sendpwd_form', 'various', array(
		'form_begin'          => '<form action="'.$functions->make_url('panel.php', array('act' => 'sendpwd')).'" method="post">',
		'user_input'          => '<input type="text" name="user" id="user" size="25" maxlength="255" value="'.$_POST['user'].'" />',
		'email_input'         => '<input type="text" name="email" size="25" maxlength="255" value="'.$_POST['email'].'" />',
		'submit_button'       => '<input type="submit" value="'.$lang['SendPassword'].'" />',
		'reset_button'        => '<input type="reset" value="'.$lang['Reset'].'" />',
		'form_end'            => '</form>'
	));
	$template->set_js_onload("set_focus('user')");
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
