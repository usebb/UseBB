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
	
	header('Location: '.$functions->get_config('board_url').$functions->make_url('index.php', array(), false));
	
} else {
	
	//
	// Include the page header
	//
	require(ROOT_PATH.'sources/page_head.php');
	
	if ( !empty($_POST['submitted']) ) {
		
		if ( !empty($_POST['logout']) )
			$session->update(NULL, 0);
		header('Location: '.$_SESSION['refere_to']);
		
	} else {
		
		$_SESSION['refere_to'] = ( !empty($_SERVER['HTTP_REFERER']) ) ? $functions->attach_sid($_SERVER['HTTP_REFERER']) : $functions->make_url('index.php', array(), false);
		
		$template->set_page_title(sprintf($lang['LogOut'], $session->sess_info['user_info']['name']));
		$template->parse('confirm_form', 'global', array(
			'form_begin' => '<form action="'.$functions->make_url('panel.php', array('act' => 'logout')).'" method="post">',
			'title' => sprintf($lang['LogOut'], $session->sess_info['user_info']['name']),
			'content' => $lang['LogOutConfirm'],
			'submit_button' => '<input type="submit" name="logout" value="'.$lang['Yes'].'" />',
			'cancel_button' => '<input type="submit" value="'.$lang['Cancel'].'" />',
			'form_end' => '<input type="hidden" name="submitted" value="true" /></form>'
		));
		
	}
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
}

?>
