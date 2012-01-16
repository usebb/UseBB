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
 * Panel edit password
 *
 * Gives an interface to edit account passwords.
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

$valid_password = ( !empty($_POST['new_passwd1']) && $functions->validate_password(stripslashes($_POST['new_passwd1']), true) );
if ( !empty($_POST['current_passwd']) && !empty($_POST['new_passwd2']) && md5(stripslashes($_POST['current_passwd'])) == $session->sess_info['user_info']['passwd'] && $valid_password && strlen(stripslashes($_POST['new_passwd1'])) >= $functions->get_config('passwd_min_length') && $_POST['new_passwd1'] === $_POST['new_passwd2'] && $functions->verify_form() ) {
	
	$new_passwd = md5(stripslashes($_POST['new_passwd1']));

	//
	// Update the password
	//
	$result = $db->query("UPDATE ".TABLE_PREFIX."members SET passwd = '".$new_passwd."' WHERE id = ".$session->sess_info['user_id']);
	
	if ( $functions->isset_al() ) {
		
		//
		// Renew AL cookie
		//
		$functions->set_al($session->sess_info['user_id'], $new_passwd);
		
	}
	
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Note'],
		'content' => $lang['PasswordEdited']
	));
	
} else {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		//
		// Define missing fields
		//
		$errors = array();
		if ( empty($_POST['current_passwd']) || md5(stripslashes($_POST['current_passwd'])) != $session->sess_info['user_info']['passwd'] )
			$errors[] = $lang['CurrentPassword'];
		if ( empty($_POST['new_passwd1']) || empty($_POST['new_passwd2']) || $_POST['new_passwd1'] !== $_POST['new_passwd2'] )
			$errors[] = $lang['NewPassword'];
		
		//
		// Show an error message
		//
		if ( count($errors) ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['MissingFields'], join(', ', $errors))
			));
			
		}

		if ( !empty($_POST['new_passwd1']) && !$valid_password ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['PasswdInfoNew'], $functions->get_config('passwd_min_length'))
			));

		} elseif ( !empty($_POST['new_passwd1']) && strlen(stripslashes($_POST['new_passwd1'])) < $functions->get_config('passwd_min_length') ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['StringTooShort'], $lang['NewPassword'], $functions->get_config('passwd_min_length'))
			));
			
		}
		
	}
	
	$template->parse('editpwd_form', 'panel', array(
		'form_begin'           => '<form action="'.$functions->make_url('panel.php', array('act' => 'editpwd')).'" method="post">',
		'current_passwd_input' => '<input type="password" name="current_passwd" id="current_passwd" size="25" maxlength="255" />',
		'passwd_info'         => sprintf($lang['PasswdInfoNew'], $functions->get_config('passwd_min_length')),
		'new_passwd1_input'    => '<input type="password" name="new_passwd1" size="25" maxlength="255" />',
		'new_passwd2_input'    => '<input type="password" name="new_passwd2" size="25" maxlength="255" />',
		'submit_button'        => '<input type="submit" name="submit" value="'.$lang['OK'].'" />',
		'form_end'             => '</form>'
	), false, true);
	$template->set_js_onload("set_focus('current_passwd')");
	
}

?>
