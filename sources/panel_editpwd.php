<?php

/*
	Copyright (C) 2003-2004 UseBB Team
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
$template->set_page_title($lang['EditPasswd']);

$_POST['current_passwd'] = ( !empty($_POST['current_passwd']) ) ? $_POST['current_passwd'] : '';
$_POST['new_passwd1'] = ( !empty($_POST['new_passwd1']) ) ? $_POST['new_passwd1'] : '';
$_POST['new_passwd2'] = ( !empty($_POST['new_passwd2']) ) ? $_POST['new_passwd2'] : '';

if ( md5($_POST['current_passwd']) == $session->sess_info['user_info']['passwd'] && strlen($_POST['new_passwd1']) >= 5 && preg_match(PWD_PREG, $_POST['new_passwd1']) && $_POST['new_passwd1'] == $_POST['new_passwd2'] ) {
	
	//
	// Update the password
	//
	if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."users SET passwd = '".md5($_POST['new_passwd1'])."' WHERE id = ".$session->sess_info['user_id'])) )
		$functions->usebb_die('SQL', 'Unable to update user information!', __FILE__, __LINE__);
	
	if ( $functions->isset_al() ) {
		
		//
		// Renew AL cookie
		//
		$functions->set_al($session->sess_info['user_id'], md5($_POST['new_passwd1']));
		
	}
	
	$template->parse('msgbox', array(
		'box_title' => $lang['Note'],
		'content' => $lang['PasswordEdited']
	));
	
} else {
	
	if ( !empty($_POST['submitted']) ) {
		
		//
		// Define missing fields
		//
		$errors = array();
		if ( md5($_POST['current_passwd']) != $session->sess_info['user_info']['passwd'] )
			$errors[] = strtolower($lang['CurrentPassword']);
		if ( strlen($_POST['new_passwd1']) < 5 || !preg_match(PWD_PREG, $_POST['new_passwd1']) || $_POST['new_passwd1'] != $_POST['new_passwd2'] )
			$errors[] = strtolower($lang['NewPassword']);
		
		//
		// Show an error message
		//
		if ( count($errors) ) {
			
			$template->parse('msgbox', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['MissingFields'], join(', ', $errors))
			));
			
		}
		
	}
	
	$template->parse('editpwd_form', array(
		'form_begin'           => '<form action="'.$functions->make_url('panel.php', array('act' => 'editpwd')).'" method="post">',
		'edit_pwd'             => $lang['EditPasswd'],
		'current_passwd'       => $lang['CurrentPassword'],
		'current_passwd_input' => '<input type="password" name="current_passwd" size="25" maxlength="255" />',
		'new_passwd'           => $lang['NewPassword'],
		'new_passwd1_input'    => '<input type="password" name="new_passwd1" size="25" maxlength="255" />',
		'new_passwd_again'     => $lang['NewPasswordAgain'],
		'new_passwd2_input'    => '<input type="password" name="new_passwd2" size="25" maxlength="255" />',
		'everything_required'  => $lang['EverythingRequired'],
		'submit_button'        => '<input type="submit" name="submit" value="'.$lang['EditPasswd'].'" />',
		'reset_button'         => '<input type="reset" value="'.$lang['Reset'].'" />',
		'form_end'             => '<input type="hidden" name="submitted" value="true" /></form>'
	));
	
}

?>
