<?php

/*
    Copyright (C) 2003-2004 UseBB Team
	http://usebb.sourceforge.net
	
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

if ( !empty($_POST['submitted']) ) {
	
	if ( strlen($_POST['passwd1']) >= 5 && preg_match(PWD_PREG, $_POST['passwd1']) && strlen($_POST['passwd2']) >= 5 && preg_match(PWD_PREG, $_POST['passwd2']) && $_POST['passwd1'] == $_POST['passwd2'] ) {
		
		//
		// Update the password
		//
		if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."users SET passwd = '".md5($_POST['passwd1'])."' WHERE id = ".$sess_info['user_id'])) )
			usebb_die('SQL', 'Unable to update user information!', __FILE__, __LINE__);
		
		if ( isset($_COOKIE[$config['session_name'].'_al']) ) {
			
			//
			// Renew AL cookie
			//
			usebb_set_al($sess_info['user_id'].':'.md5($_POST['passwd1']));
			
		}
		header('Location: index.php');
		exit();
		
	} else {
		
		//
		// The passwords aren't correct
		//
		$template->parse('msgbox', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['MissingFields'], strtolower($lang['Password']))
		));
		
	}
	
}

$template->parse('editpwd_form', array(
	'form_begin'          => '<form action="'.usebb_make_url('panel.php', array('a' => 'editpwd')).'" method="post">',
	'edit_pwd'            => $lang['EditPasswd'],
	'passwd'              => $lang['Password'],
	'passwd1_input'       => '<input type="password" name="passwd1" size="25" maxlength="255" />',
	'passwd_again'        => $lang['PasswordAgain'],
	'passwd2_input'       => '<input type="password" name="passwd2" size="25" maxlength="255" />',
	'everything_required' => $lang['EverythingRequired'],
	'submit_button'       => '<input type="submit" name="submit" value="'.$lang['EditPasswd'].'" />',
	'reset_button'        => '<input type="reset" value="'.$lang['Reset'].'" />',
	'form_end'            => '<input type="hidden" name="submitted" value="true" /></form>'
));

?>