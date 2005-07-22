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
// User wants to activate
//
$session->update('activate');

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

$template->set_page_title($lang['Activate']);

//
// Check if the user exists
//
$result = $db->query("SELECT id, name, active, active_key FROM ".TABLE_PREFIX."members WHERE id = ".$_GET['id']);
$userdata = $db->fetch_result($result);
if ( $userdata['id'] ) {
	
	//
	// If this user is already active,
	// show an error message
	//
	if ( $userdata['active'] ) {
		
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['AlreadyActivated'], $_GET['id'])
		));
		
	//
	// If the user is not yet active and the key is OK, activate the user
	//
	} elseif ( md5($_GET['key']) == $userdata['active_key'] ) {
		
		$result = $db->query("UPDATE ".TABLE_PREFIX."members SET active = 1, active_key = '' WHERE id = ".$_GET['id']);
		
		//
		// Activation was succesful!
		//
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Activate'],
			'content' => sprintf($lang['Activated'], '<em>'.unhtml(stripslashes($userdata['name'])).'</em>')
		));
		
	//
	// If the user is not yet active and the key is not OK, show an error message
	//
	} else {
		
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['WrongActivationKey'], $_GET['id'])
		));
		
	}
	
//
// Show an error if the user ID does not exist
//
} else {
	
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Error'],
		'content' => sprintf($lang['NoSuchUser'], 'ID '.$_GET['id'])
	));
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
