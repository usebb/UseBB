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

define('INCLUDED', true);
define('ROOT_PATH', './');

//
// Include usebb engine
//
require(ROOT_PATH.'sources/common.php');

$_GET['a'] = ( !empty($_GET['a']) ) ? $_GET['a'] : 'panel_home';

if ( $_GET['a'] == 'login' ) {
	
	//
	// Log In
	//
	$sess_info = $session->update($_GET['a']);
	require(ROOT_PATH.'sources/panel_login.php');
	
} elseif ( $_GET['a'] == 'logout' ) {
	
	//
	// Log Out
	//
	$session->update(NULL, 0);
	$page = ( !empty($_SERVER['HTTP_REFERER']) ) ? $_SERVER['HTTP_REFERER'] : 'index.php';
	header('Location: '.$page);
	
} elseif ( $_GET['a'] == 'register' ) {
	
	//
	// Register
	//
	$sess_info = $session->update($_GET['a']);
	require(ROOT_PATH.'sources/panel_register.php');
	
} elseif ( $_GET['a'] == 'activate' && !empty($_GET['id']) && is_numeric($_GET['id']) && !empty($_GET['key']) ) {
	
	//
	// Activate
	//
	$sess_info = $session->update($_GET['a']);
	require(ROOT_PATH.'sources/panel_activate.php');
	
} elseif ( $_GET['a'] == 'sendpwd' ) {
	
	//
	// Send Password
	//
	$sess_info = $session->update($_GET['a']);
	require(ROOT_PATH.'sources/panel_sendpwd.php');
	
} else {
	
	if ( !in_array($_GET['a'], array('panel_home', 'editprofile', 'editoptions', 'editpwd')) ) {
		
		header('Location: index.php');
		exit();
		
	}
	
	$sess_info = $session->update($_GET['a']);
	
	if ( $sess_info['user_id'] <= 0 ) {
		
		header('Location: index.php');
		exit();
		
	}
	
	//
	// Include the page header
	//
	require(ROOT_PATH.'sources/page_head.php');
	
	$template->parse('panel_menu', array(
		'panel_home' => '<a href="'.usebb_make_url('panel.php').'">'.$lang['PanelHome'].'</a>',
		'view_profile' => '<a href="'.usebb_make_url('profile.php', array('id' => $sess_info['user_info']['id'])).'">'.$lang['ViewProfile'].'</a>',
		'panel_profile' => '<a href="'.usebb_make_url('panel.php', array('a' => 'editprofile')).'">'.$lang['EditProfile'].'</a>',
		'panel_options' => '<a href="'.usebb_make_url('panel.php', array('a' => 'editoptions')).'">'.$lang['EditOptions'].'</a>',
		'panel_passwd' => '<a href="'.usebb_make_url('panel.php', array('a' => 'editpwd')).'">'.$lang['EditPasswd'].'</a>',
	));
	
	switch ( $_GET['a'] ) {
		
		case 'panel_home':
			require(ROOT_PATH.'sources/panel_home.php');
			break;
		case 'editprofile':
			require(ROOT_PATH.'sources/panel_profile.php');
			break;
		case 'editoptions':
			require(ROOT_PATH.'sources/panel_options.php');
			break;
		case 'editpwd':
			require(ROOT_PATH.'sources/panel_editpwd.php');
			break;
		
	}
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
}

?>