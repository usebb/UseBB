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

$config['date_format'] = ( !empty($sess_info['user_info']['date_format']) ) ? $sess_info['user_info']['date_format'] : $config['date_format'];

//
// Get language variables
//
if ( !($result = $db->query("SELECT name, content FROM ".TABLE_PREFIX."language WHERE language = '".$config['language']."'")) )
	$functions->usebb_die('SQL', 'Unable to get translation for "'.$config['language'].'"!', __FILE__, __LINE__);
while ( $langvars = $db->fetch_result($result) )
	$lang[$langvars['name']] = stripslashes($langvars['content']);

//
// Page header
//
$template->parse('normal_header', array(
	'board_name' => $config['board_name'],
	'board_descr' => $config['board_descr'],
	'css_url' => $functions->make_url('css.php'),
	'link_home' => $functions->make_url('index.php'),
	'home' => $lang['Home'],
	'link_reg_panel' => ( $sess_info['user_id'] ) ? $functions->make_url('panel.php') : $functions->make_url('panel.php', array('a' => 'register')),
	'reg_panel' => ( $sess_info['user_id'] ) ? $lang['YourPanel'] : $lang['Register'],
	'link_faq' => $functions->make_url('faq.php'),
	'faq' => $lang['FAQ'],
	'link_search' => $functions->make_url('search.php'),
	'search' => $lang['Search'],
	'link_active' => $functions->make_url('active.php'),
	'active' => $lang['ActiveTopics'],
	'link_log_inout' => ( $sess_info['user_id'] ) ? $functions->make_url('panel.php', array('a' => 'logout')) : $functions->make_url('panel.php', array('a' => 'login')),
	'log_inout' => ( $sess_info['user_id'] ) ? sprintf($lang['LogOut'], $sess_info['user_info']['name']) : $lang['LogIn']
));

//
// Banned IP addresses catch this message
//
if ( $sess_info['ip_banned'] ) {
	
	$template->set_page_title($lang['Note']);
	
	$template->parse('msgbox', array(
		'box_title' => $lang['Note'],
		'content' => sprintf($lang['BannedIP'], $sess_info['ip_addr'])
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
if ( $config['board_closed'] && $sess_info['location'] != 'login' ) {
	
	$template->set_page_title($lang['BoardClosed']);
	
	//
	// Show this annoying board closed message on all pages but the login page.
	//
	$template->parse('msgbox', array(
		'box_title' => $lang['BoardClosed'],
		'content' => $config['board_closed_reason']
	));
	
	//
	// Admins can still enter the board
	//
	if ( !isset($sess_info['user_info']) || $sess_info['user_info']['level'] != 3 ) {
		
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
if ( !$config['guests_can_access_board'] && $sess_info['user_id'] == 0 && !in_array($sess_info['location'], array('login', 'register', 'activate', 'sendpwd')) ) {
	
	$template->set_page_title($lang['Note']);
	
	$template->parse('msgbox', array(
		'box_title' => $lang['Note'],
		'content' => $lang['NeedToBeLoggedInGlobal']
	));
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
	exit();
	
}

?>