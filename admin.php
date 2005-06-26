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

define('INCLUDED', true);
define('ROOT_PATH', './');

//
// Include usebb engine
//
require(ROOT_PATH.'sources/common.php');

//
// Update and get the session information
//
$session->update('admin');

if ( $functions->get_user_level() == LEVEL_ADMIN ) {
	
	//
	// Include the page header
	//
	require(ROOT_PATH.'sources/page_head.php');
	
	//
	// Get Admin variables
	//
	$admin_file = ROOT_PATH.'languages/admin_'.$functions->get_config('language').'.php';
	if ( !file_exists($admin_file) || !is_readable($admin_file) )
		trigger_error('Unable to get "'.$functions->get_config('language').'" Admin!');
	else
		require($admin_file);
	
	if ( !empty($_POST['passwd']) )
		$_SESSION['admin_pwd'] = md5($_POST['passwd']);
	
	if ( !empty($_SESSION['admin_pwd']) && $_SESSION['admin_pwd'] === $session->sess_info['user_info']['passwd'] ) {
		
		require(ROOT_PATH.'sources/functions_admin.php');
		$admin_functions = new admin_functions;
		
		//
		// Include page
		//
		$pages = array('index', 'version', 'iplookup', 'sqltoolbox');
		$_GET['act'] = ( !empty($_GET['act']) && in_array($_GET['act'], $pages) ) ? $_GET['act'] : 'index';
		require(ROOT_PATH.'sources/admin_'.$_GET['act'].'.php');
		
	} else {
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			if ( empty($_POST['passwd']) ) {
							
				$template->parse('msgbox', 'global', array(
					'box_title' => $lang['Error'],
					'content' => sprintf($lang['MissingFields'], $lang['Password'])
				));
				
			} else {
				
				$template->parse('msgbox', 'global', array(
					'box_title' => $lang['Error'],
					'content' => $lang['WrongPassword']
				));
				
			}
			
		}
		
		$template->set_page_title($lang['AdminLogin']);
		$template->parse('login_form', 'admin', array(
			'form_begin' => '<form action="'.$functions->make_url('admin.php', $_GET).'" method="post">',
			'form_end' => '</form>',
			'username' => $session->sess_info['user_info']['name'],
			'password_input' => '<input type="password" name="passwd" id="passwd" size="25" maxlength="255" />',
			'submit_button'  => '<input type="submit" value="'.$lang['LogIn'].'" />',
		));
		$template->set_js_onload("set_focus('passwd')");
		
	}
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
} else {
	
	$functions->redir_to_login();
	
}

?>
