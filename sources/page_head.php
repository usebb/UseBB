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
	trigger_error('Unable to get "'.$functions->get_config('language').'" translation!');
else
	require($lang_file);

//
// Overwrite the English language array with the translation
// so we don't get errors when the translation isn't uptodate
//
if ( isset($lang_English) )
	$lang = array_merge($lang_English, $lang);

$character_encoding = ( !empty($lang['character_encoding']) ) ? $lang['character_encoding'] : 'iso-8859-1';

//
// Header informs the browser about the encoding
//
header('Content-Type: text/html; charset='.$character_encoding);

$link_bar = array();
/*if ( $functions->get_user_level() == 3 )
	$link_bar[] = '<a href="'.$functions->make_url('admin.php').'">'.$lang['ACP'].'</a>';*/
	
if ( $functions->get_config('enable_memberlist') && $functions->get_user_level() >= $functions->get_config('view_memberlist_min_level') )
	$link_bar[] = '<a href="'.$functions->make_url('members.php').'">'.$lang['MemberList'].'</a>';
	
if ( $functions->get_config('enable_stafflist') && $functions->get_user_level() >= $functions->get_config('view_stafflist_min_level') )
	$link_bar[] = '<a href="'.$functions->make_url('members.php', array('act' => 'staff')).'">'.$lang['StaffList'].'</a>';
	
/*if ( $functions->get_config('enable_rss') && $functions->get_user_level() >= $functions->get_config('view_rss_min_level') )
	$link_bar[] = '<a href="'.$functions->make_url('rss.php').'">'.$lang['RSSFeed'].'</a>';
	
if ( $functions->get_config('enable_stats') && $functions->get_user_level() >= $functions->get_config('view_stats_min_level') )
	$link_bar[] = '<a href="'.$functions->make_url('stats.php').'">'.$lang['Statistics'].'</a>';*/
	
if ( $functions->get_config('enable_contactadmin') && $functions->get_user_level() >= $functions->get_config('view_contactadmin_min_level') )
	$link_bar[] = '<a href="mailto:'.$functions->get_config('admin_email').'">'.$lang['ContactAdmin'].'</a>';

$template->add_global_vars(array(
	// language settings
	'text_direction' => ( !empty($lang['text_direction']) ) ? $lang['text_direction'] : 'ltr',
	'character_encoding' => $character_encoding,
	'language_code' => ( !empty($lang['language_code']) ) ? $lang['language_code'] : 'en',
	// board settings
	'board_name' => $functions->get_config('board_name'),
	'board_descr' => $functions->get_config('board_descr'),
	'admin_email' => $functions->get_config('admin_email'),
	// menu links
	'css_url' => $functions->make_url('css.php'),
	'link_home' => $functions->make_url('index.php'),
	'link_reg_panel' => ( $session->sess_info['user_id'] ) ? $functions->make_url('panel.php') : $functions->make_url('panel.php', array('act' => 'register')),
	'reg_panel' => ( $session->sess_info['user_id'] ) ? $lang['YourPanel'] : $lang['Register'],
	'link_faq' => $functions->make_url('faq.php'),
	'link_search' => $functions->make_url('search.php'),
	'link_active' => $functions->make_url('active.php'),
	'link_log_inout' => ( $session->sess_info['user_id'] ) ? $functions->make_url('panel.php', array('act' => 'logout')) : $functions->make_url('panel.php', array('act' => 'login')),
	'log_inout' => ( $session->sess_info['user_id'] ) ? sprintf($lang['LogOut'], '<em>'.unhtml(stripslashes($session->sess_info['user_info']['name'])).'</em>') : $lang['LogIn'],
	// link bar (list of additional enabled features)
	'link_bar' => ( count($link_bar) ) ? join($template->get_config('item_delimiter'), $link_bar) : '',
	// additional links to features (might end up in error when feature is disabled)
	// use 'em when you want to have more links in the menu or somewhere else
	'link_memberlist' => $functions->make_url('members.php'),
	'link_stafflist' => $functions->make_url('members.php', array('act' => 'staff')),
	'link_rss' => $functions->make_url('rss.php'),
	'link_stats' => $functions->make_url('stats.php'),
	'usebb_version' => USEBB_VERSION
));

//
// Page header
//
$template->parse('normal_header', 'global');

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
