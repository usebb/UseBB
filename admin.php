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
 * Admin control panel
 *
 * Gives access to the ACP features, including authorizing the admin first.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2012 UseBB Team
 * @package	UseBB
 * @subpackage	ACP
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

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

if ( $functions->get_user_level() == LEVEL_ADMIN ) {
	
	//
	// Get Admin variables
	//
	$lang = $functions->fetch_language('', 'admin');

	$_GET['act'] = ( !empty($_GET['act']) ) ? str_replace(array('/', '\\'), '', $_GET['act']) : 'index';

	$_SESSION['admin_last_activity'] = ( isset($_SESSION['admin_last_activity']) ) ? (int) $_SESSION['admin_last_activity'] : 0;
	$_SESSION['admin_disable_logout'] = ( isset($_SESSION['admin_disable_logout']) ) ? (bool) $_SESSION['admin_disable_logout'] : false;
	$acp_auto_logout = (int) $functions->get_config('acp_auto_logout');
	
	if ( $_GET['act'] == 'logout' && $functions->verify_url(false) ) {
		
		//
		// Log out from ACP
		//
		$_SESSION['admin_pwd'] = '';
		$functions->redirect('index.php');

	} elseif ( !empty($_POST['passwd']) && md5(stripslashes($_POST['passwd'])) === $session->sess_info['user_info']['passwd'] ) {
		
		//
		// Password submitted and correct
		//

		$_SESSION['admin_pwd'] = md5(stripslashes($_POST['passwd']));
		$_SESSION['admin_last_activity'] = time();
		$_SESSION['admin_disable_logout'] = false;

		$functions->redirect('admin.php', $_GET);
		
	} elseif ( !empty($_SESSION['admin_pwd']) && $_SESSION['admin_pwd'] === $session->sess_info['user_info']['passwd'] && ( $_SESSION['admin_disable_logout'] || $_SESSION['admin_last_activity'] > time() - $acp_auto_logout * 60 ) ) {
		
		//
		// Password in session and recent activity
		//
		
		$_SESSION['admin_last_activity'] = time();
		$_SESSION['admin_disable_logout'] = false;

		require(ROOT_PATH.'sources/functions_admin.php');
		$admin_functions = new admin_functions;
		
		//
		// Include page/module
		//
		
		if ( preg_match('#^mod_([A-Za-z0-9\-_\.]+)$#', $_GET['act'], $module_name) && array_key_exists($module_name[1], $admin_functions->acp_modules) ) {
			
			//
			// ACP module
			//
			$admin_functions->run_module($module_name[1]);
			
		} elseif ( file_exists(ROOT_PATH.'sources/admin_'.$_GET['act'].'.php') ) {
			
			//
			// Regular page
			//
			$content = '';
			require(ROOT_PATH.'sources/admin_'.$_GET['act'].'.php');
			
		} else {
			
			//
			// Non existent
			//
			$functions->redirect('admin.php');
			
		}
		
	} else {
		
		//
		// Request password
		//

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
		
		$template->add_breadcrumb($lang['AdminLogin']);
		$template->parse('login_form', 'admin', array(
			'form_begin' => '<form action="'.$functions->make_url('admin.php', $_GET).'" method="post">',
			'form_end' => '</form>',
			'username' => $session->sess_info['user_info']['name'],
			'password_input' => '<input type="password" name="passwd" id="passwd" size="25" maxlength="255" />',
			'submit_button'  => '<input type="submit" value="'.$lang['LogIn'].'" />',
		));
		$template->set_js_onload("set_focus('passwd')");
		
	}
	
} else {
	
	$functions->redir_to_login();
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
