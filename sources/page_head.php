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
// Get language variables
//

//
// Read the always updated English language file (if present)
//
$lang_file = ROOT_PATH.'languages/lang_English.php';
if ( $functions->get_config('language') != 'English' && file_exists($lang_file) && is_readable($lang_file) ) {
	
	require($lang_file);
	$lang_English = $lang;
	
}

$lang_file = ROOT_PATH.'languages/lang_'.$functions->get_config('language').'.php';
if ( !file_exists($lang_file) || !is_readable($lang_file) )
	$functions->usebb_die('General', 'Unable to get "'.$functions->get_config('language').'" translation!', __FILE__, __LINE__);
else
	require($lang_file);

//
// Overwrite the English language array with the translation
// so we don't get errors when the translation isn't uptodate
//
if ( isset($lang_English) )
	$lang = array_merge($lang_English, $lang);

//
// Page header
//
$template->parse('normal_header', 'global', array(
	'board_name' => $functions->get_config('board_name'),
	'board_descr' => $functions->get_config('board_descr'),
	'css_url' => $functions->make_url('css.php'),
	'link_home' => $functions->make_url('index.php'),
	'home' => $lang['Home'],
	'link_reg_panel' => ( $session->sess_info['user_id'] ) ? $functions->make_url('panel.php') : $functions->make_url('panel.php', array('act' => 'register')),
	'reg_panel' => ( $session->sess_info['user_id'] ) ? $lang['YourPanel'] : $lang['Register'],
	'link_faq' => $functions->make_url('faq.php'),
	'faq' => $lang['FAQ'],
	'link_search' => $functions->make_url('search.php'),
	'search' => $lang['Search'],
	'link_active' => $functions->make_url('active.php'),
	'active' => $lang['ActiveTopics'],
	'link_log_inout' => ( $session->sess_info['user_id'] ) ? $functions->make_url('panel.php', array('act' => 'logout')) : $functions->make_url('panel.php', array('act' => 'login')),
	'log_inout' => ( $session->sess_info['user_id'] ) ? sprintf($lang['LogOut'], '<em>'.$session->sess_info['user_info']['name'].'</em>') : $lang['LogIn']
));

//
// Banned IP addresses catch this message
//
if ( $session->sess_info['ip_banned'] ) {
	
	$template->set_page_title($lang['Note']);
	
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Note'],
		'content' => sprintf($lang['BannedIP'], $session->sess_info['ip_addr'])
	));
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
	exit();
	
}

//
// Board Closed message
//
if ( $functions->get_config('board_closed') && $session->sess_info['location'] != 'login' ) {
	
	$template->set_page_title($lang['BoardClosed']);
	
	//
	// Show this annoying board closed message on all pages but the login page.
	//
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['BoardClosed'],
		'content' => $functions->get_config('board_closed_reason')
	));
	
	//
	// Admins can still enter the board
	//
	if ( $functions->get_user_level() < 3 ) {
		
		//
		// Include the page footer
		//
		require(ROOT_PATH.'sources/page_foot.php');
		
		exit();
		
	}
	
}

//
// Guests must log in
//
if ( !$functions->get_config('guests_can_access_board') && $session->sess_info['user_id'] == 0 && !in_array($session->sess_info['location'], array('login', 'register', 'activate', 'sendpwd')) ) {
	
	$functions->redir_to_login();
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
	exit();
	
}

?>
