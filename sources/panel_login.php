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
// User wants to login
//
$session->update('login');

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

$template->set_page_title($lang['LogIn']);

$_POST['user'] = ( !empty($_POST['user']) ) ? $_POST['user'] : '';
$_POST['user'] = preg_replace('/ +/', ' ', $_POST['user']);
$_POST['passwd'] = ( !empty($_POST['user']) ) ? $_POST['passwd'] : '';

if ( preg_match(USER_PREG, $_POST['user']) && preg_match(PWD_PREG, $_POST['passwd']) && strlen($_POST['passwd']) >= 5 ) {
	
	//
	// The user already passed a username and password
	//
	
	//
	// Get information about this username
	//
	if ( !($result = $db->query("SELECT id, passwd, active, banned, banned_reason, level FROM ".TABLE_PREFIX."users WHERE name = '".$_POST['user']."'")) )
		$functions->usebb_die('SQL', 'Unable to get user entry!', __FILE__, __LINE__);
	$userdata = $db->fetch_result($result);
	
	//
	// If this user does not exist...
	//
	if ( $db->num_rows($result) == 0 ) {
		
		//
		// ...show a warning
		//
		$template->parse('msgbox', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchUser'], '<i>'.$_POST['user'].'</i>').' '.$lang['RegisterIt']
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
		
	} elseif ( !$userdata['active'] ) {
		
		//
		// It does exist, but it hasn't been activated yet
		// thus, show another warning...
		//
		$template->parse('msgbox', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NotActivated'], '<i>'.$_POST['user'].'</i>')
		));
		
	} elseif ( $functions->get_config('board_closed') && $userdata['level'] != 3 ) {
		
		//
		// Only admins can log in when the forum is closed.
		// Show a warning to users...
		//
		$template->parse('msgbox', array(
			'box_title' => $lang['Error'],
			'content' => $lang['BoardClosedOnlyAdmins']
		));
		
	} elseif ( md5($_POST['passwd']) == $userdata['passwd'] ) {
		
		//
		// The password is correct,
		// we will now log in the user
		//
		$session->update(NULL, $userdata['id']);
		
		//
		// Set a remember cookie if the user chose to
		//
		if ( !empty($_POST['remember']) && $_POST['remember'] == 'yes' )
			$functions->set_al($userdata['id'], $userdata['passwd']);
		
		//
		// Get us back to the previous page
		//
		header('Location: '.$_SESSION['referer']);
		unset($_SESSION['referer']);
		
	} else {
		
		//
		// The password was not correct
		// another warning
		//
		$template->parse('msgbox', array(
			'box_title' => $lang['Error'],
			'content' => $lang['WrongPassword']
		));
		
	}
	
} else {
	
	//
	// Show the login form, if the user is not logged in
	//
	if ( $session->sess_info['user_id'] == 0 ) {
		
		$_SERVER['HTTP_REFERER'] = ( !empty($_SERVER['HTTP_REFERER']) ) ? $_SERVER['HTTP_REFERER'] : 'index.php';
		$_SESSION['referer'] = ( !empty($_SESSION['referer']) && !preg_match("/act=activate/", $_SESSION['referer']) ) ? $_SESSION['referer'] : $_SERVER['HTTP_REFERER'];
		
		$_POST['user'] = ( preg_match(USER_PREG, $_POST['user']) ) ? $_POST['user'] : '';
		$template->parse('login_form', array(
			'form_begin'     => '<form action="'.$functions->make_url('panel.php', array('act' => 'login')).'" method="post">',
			'login'          => $lang['LogIn'],
			'user'           => $lang['Username'],
			'user_input'     => '<input type="text" name="user" size="25" maxlength="'.$functions->get_config('username_max_length').'" value="'.$_POST['user'].'" />',
			'password'       => $lang['Password'],
			'password_input' => '<input type="password" name="passwd" size="25" maxlength="255" />',
			'remember'       => $lang['RememberMe'],
			'remember_input' => '<input type="checkbox" name="remember" id="remember" value="yes" /> <label for="remember">'.$lang['Yes'].'</label>',
			'submit_button'  => '<input type="submit" value="'.$lang['LogIn'].'" />',
			'reset_button'   => '<input type="reset" value="'.$lang['Reset'].'" />',
			'link_reg'       => '<a href="'.$functions->make_url('panel.php', array('act' => 'register')).'">'.$lang['RegisterNewAccount'].'</a>',
			'link_sendpwd'   => '<a href="'.$functions->make_url('panel.php', array('act' => 'sendpwd')).'">'.$lang['SendPassword'].'</a>',
			'form_end'       => '</form>'
		));
		
	} else {
		
		//
		// If he/she is logged in, return to index
		//
		header('Location: index.php');
		
	}
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>