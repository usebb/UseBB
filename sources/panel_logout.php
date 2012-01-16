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
 * Panel logout
 *
 * Gives an interface to logout out of user accounts.
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

//
// User wants to logout
//
$session->update('logout');

if ( !$session->sess_info['user_id'] ) {
	
	$functions->redirect('index.php');
	
} else {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		if ( !empty($_POST['logout']) && $functions->verify_form(false) ) {
			
			$refere_to = $functions->get_config('board_url').$functions->make_url('index.php', array(), false);
			$functions->unset_al();
			$session->destroy();
			
		} else {
			
			//
			// Get us back to the previous page
			//
			$refere_to = ( !empty($_SESSION['refere_to']) ) ? $functions->attach_sid($_SESSION['refere_to']) : $functions->get_config('board_url').$functions->make_url('index.php', array(), false);
			unset($_SESSION['refere_to']);
			
		}
		
		$functions->raw_redirect($refere_to);
		
	} else {
	
		$_SERVER['HTTP_REFERER'] = ( !empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $functions->get_config('board_url')) === 0 && !preg_match('#(?:login|logout|register|activate|sendpwd|install)#', $_SERVER['HTTP_REFERER']) ) ? $_SERVER['HTTP_REFERER'] : '';
		$_SESSION['refere_to'] = ( !empty($_SESSION['referer']) ) ? $_SESSION['referer'] : $_SERVER['HTTP_REFERER'];
		unset($_SESSION['referer']);
		
		//
		// Include the page header
		//
		require(ROOT_PATH.'sources/page_head.php');
		
		$template->add_breadcrumb(sprintf($lang['LogOut'], unhtml(stripslashes($session->sess_info['user_info']['name']))));
		$template->parse('confirm_form', 'global', array(
			'form_begin' => '<form action="'.$functions->make_url('panel.php', array('act' => 'logout')).'" method="post">',
			'title' => sprintf($lang['LogOut'], unhtml(stripslashes($session->sess_info['user_info']['name']))),
			'content' => $lang['LogOutConfirm'],
			'submit_button' => '<input type="submit" name="logout" value="'.$lang['Yes'].'" />',
			'cancel_button' => '<input type="submit" value="'.$lang['Cancel'].'" />',
			'form_end' => '</form>'
		), false, true);
		
		//
		// Include the page footer
		//
		require(ROOT_PATH.'sources/page_foot.php');
		
	}
	
}

?>
