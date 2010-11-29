<?php

/*
	Copyright (C) 2003-2009 UseBB Team
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
 * Header file
 *
 * Does some stuff at the beginning of the processing.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2009 UseBB Team
 * @package	UseBB
 * @subpackage Core
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

//
// Fetch the language array
//
$lang = $functions->fetch_language();

//
// Init external window Javascript
//
if ( $functions->get_config('target_blank') )
	$template->set_js_onload('init_external()');

//
// RSS feed
//
// Note: the feed may not always be available, depending on the user's permissions.
// This code does not incorporate forum permissions and may therefore
// generate the link while the feed is actually inaccessible.
//
$rss_enabled = false;
$rss_link = '';

if ( in_array($session->sess_info['location'], array('index', 'activetopics')) && $functions->get_config('enable_rss') ) {
	
	$rss_enabled = true;
	$rss_link = $functions->make_url('rss.php');
	
} elseif ( preg_match('#^(forum|topic):([0-9]+)$#', $session->sess_info['location'], $matches) ) {
	
	if ( ( $matches[1] == 'forum' && $functions->get_config('enable_rss_per_forum') ) || ( $matches[1] == 'topic' && $functions->get_config('enable_rss_per_topic') ) ) {
		
		$rss_enabled = true;
		$rss_link = $functions->make_url('rss.php', array($matches[1] => $matches[2]));
		
	}
	
}

if ( empty($rss_link) )
	$rss_link = $functions->make_url('rss.php');

$link_bar = array();

//
// ACP
//
if ( $functions->get_user_level() == LEVEL_ADMIN )
	$link_bar[] = '<a href="'.$functions->make_url('admin.php').'" id="usebb_acp_link">'.$lang['ACP'].'</a>';
	
//
// Don't show these if they cannot be accessed after all
//
if ( ( !$session->sess_info['ip_banned'] && !$functions->get_config('board_closed') && ( $functions->get_config('guests_can_access_board') || $functions->get_user_level() != LEVEL_GUEST ) ) || $functions->get_user_level() == LEVEL_ADMIN  ) {
	
	//
	// Member list
	//
	if ( $functions->get_config('enable_memberlist') && $functions->get_user_level() >= $functions->get_config('view_memberlist_min_level') )
		$link_bar[] = '<a href="'.$functions->make_url('members.php').'">'.$lang['MemberList'].'</a>';
	
	//
	// Staff list
	//
	if ( $functions->get_config('enable_stafflist') && $functions->get_user_level() >= $functions->get_config('view_stafflist_min_level') )
		$link_bar[] = '<a href="'.$functions->make_url('members.php', array('act' => 'staff')).'">'.$lang['StaffList'].'</a>';
	
	//
	// Statistics
	//
	if ( $functions->get_config('enable_stats') && $functions->get_user_level() >= $functions->get_config('view_stats_min_level') )
		$link_bar[] = '<a href="'.$functions->make_url('stats.php').'">'.$lang['Statistics'].'</a>';
	
	//
	// RSS feed
	//	
	if ( $rss_enabled ) {
		
		$html_rss_link = '<a href="'.$rss_link.'">'.$lang['RSSFeed'].'</a>';
		$rss_feed_icon = $template->get_config('rss_feed_icon');
		
		if ( !empty($rss_feed_icon) )
			$html_rss_link = '<a href="'.$rss_link.'" id="rss-feed-icon"><img src="templates/'.$functions->get_config('template').'/gfx/'.$rss_feed_icon.'" alt="'.$lang['RSSFeed'].'" /></a>'.$html_rss_link;
		
		$link_bar[] = $html_rss_link;
		
	}
	
}
	
//
// Contact admin
//
if ( $functions->get_config('enable_contactadmin') && $functions->get_user_level() >= $functions->get_config('view_contactadmin_min_level') )
	$link_bar[] = '<a href="mailto:'.$functions->get_config('admin_email').'">'.$lang['ContactAdmin'].'</a>';

$template->add_global_vars(array(
	
	//
	// board settings
	//
	'board_name' => unhtml($functions->get_config('board_name')),
	'board_descr' => unhtml($functions->get_config('board_descr')),
	'board_keywords' => unhtml($functions->get_config('board_keywords')),
	'board_url' => $functions->get_config('board_url'),
	'admin_email' => $functions->get_config('admin_email'),
	
	//
	// menu links
	//
	'link_home' => $functions->make_url('index.php'),
	'link_reg_panel' => ( $session->sess_info['user_id'] ) ? $functions->make_url('panel.php') : $functions->make_url('panel.php', array('act' => 'register')),
	'reg_panel' => ( $session->sess_info['user_id'] ) ? $lang['YourPanel'] : $lang['Register'],
	'link_faq' => $functions->make_url('faq.php'),
	'link_search' => $functions->make_url('search.php'),
	'link_active' => $functions->make_url('active.php'),
	'link_log_inout' => ( $session->sess_info['user_id'] ) ? $functions->make_url('panel.php', array('act' => 'logout')) : $functions->make_url('panel.php', array('act' => 'login')),
	'log_inout' => ( $session->sess_info['user_id'] ) ? sprintf($lang['LogOut'], '<em>'.unhtml(stripslashes($session->sess_info['user_info']['name'])).'</em>') : $lang['LogIn'],
	
	//
	// link bar (list of additional enabled features)
	//
	'link_bar' => ( count($link_bar) ) ? join($template->get_config('item_delimiter'), $link_bar) : '',
	
	//
	// additional links to features (might end up in error when feature is disabled)
	// use 'em when you want to have more links in the menu or somewhere else
	//
	'link_memberlist' => $functions->make_url('members.php'),
	'link_stafflist' => $functions->make_url('members.php', array('act' => 'staff')),
	'link_rss' => $rss_link,
	'link_stats' => $functions->make_url('stats.php'),
	
	'rss_head_link' => ( $rss_enabled ) ? '<link rel="alternate" type="application/rss+xml" title="'.$lang['RSSFeed'].'" href="'.$rss_link.'" />' : '',
	'usebb_copyright' => sprintf($lang['PoweredBy'], unhtml($functions->get_config('board_name')), '<a href="http://www.usebb.net">UseBB 1 '.$lang['ForumSoftware'].'</a>'),
	
	'reset_button' => '<input type="reset" value="'.$lang['Reset'].'" />',
	
));

//
// Page header
//
$template->parse('normal_header', 'global');

//
// Banned IP addresses catch this message
//
if ( $session->sess_info['ip_banned'] ) {
	
	header(HEADER_403);
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
	if ( $functions->get_user_level() < LEVEL_ADMIN ) {
		
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
if ( !$functions->get_config('guests_can_access_board') && $functions->get_user_level() == LEVEL_GUEST && !in_array($session->sess_info['location'], array('login', 'register', 'activate', 'sendpwd')) ) {
	
	$functions->redir_to_login();
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
	exit();
	
}

?>
