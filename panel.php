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

$_GET['act'] = ( !empty($_GET['act']) ) ? $_GET['act'] : 'panel_home';

if ( $_GET['act'] == 'login' ) {
	
	//
	// Log In
	//
	require(ROOT_PATH.'sources/panel_login.php');
	
} elseif ( $_GET['act'] == 'logout' ) {
	
	//
	// Log Out
	//
	require(ROOT_PATH.'sources/panel_logout.php');
	
} elseif ( $_GET['act'] == 'register' ) {
	
	//
	// Register
	//
	require(ROOT_PATH.'sources/panel_register.php');
	
} elseif ( $_GET['act'] == 'activate' && !empty($_GET['id']) && is_numeric($_GET['id']) && !empty($_GET['key']) ) {
	
	//
	// Activate
	//
	require(ROOT_PATH.'sources/panel_activate.php');
	
} elseif ( $_GET['act'] == 'sendpwd' ) {
	
	//
	// Send Password
	//
	require(ROOT_PATH.'sources/panel_sendpwd.php');
	
} elseif ( in_array($_GET['act'], array('panel_home', 'editprofile', 'editoptions', 'editpwd')) ) {
	
	//
	// Update and get the session information
	//
	$session->update($_GET['act']);
	
	if ( !$session->sess_info['user_id'] ) {
		
		$functions->redir_to_login();
		
	} else {
		
		//
		// Include the page header
		//
		require(ROOT_PATH.'sources/page_head.php');
		
		$template->parse('panel_menu', array(
			'yourpanel' => $lang['YourPanel'],
			'panel_home' => ( $_GET['act'] != 'panel_home' ) ? '<a href="'.$functions->make_url('panel.php').'">'.$lang['PanelHome'].'</a>' : $lang['PanelHome'],
			'view_profile' => '<a href="'.$functions->make_url('profile.php', array('id' => $session->sess_info['user_info']['id'])).'">'.$lang['ViewProfile'].'</a>',
			'panel_profile' => ( $_GET['act'] != 'editprofile' ) ? '<a href="'.$functions->make_url('panel.php', array('act' => 'editprofile')).'">'.$lang['EditProfile'].'</a>' : $lang['EditProfile'],
			'panel_options' => ( $_GET['act'] != 'editoptions' ) ? '<a href="'.$functions->make_url('panel.php', array('act' => 'editoptions')).'">'.$lang['EditOptions'].'</a>' : $lang['EditOptions'],
			'panel_passwd' => ( $_GET['act'] != 'editpwd' ) ? '<a href="'.$functions->make_url('panel.php', array('act' => 'editpwd')).'">'.$lang['EditPasswd'].'</a>' : $lang['EditPasswd'],
		));
		
		switch ( $_GET['act'] ) {
			
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
	
} else {
	
	header('Location: '.$functions->make_url('index.php', array(), false));
	
}

?>