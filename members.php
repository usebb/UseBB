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

if ( empty($_GET['act']) ) {
	
	$session->update('memberlist');
	
	//
	// Include the page header
	//
	require(ROOT_PATH.'sources/page_head.php');
	
	if ( !$functions->get_config('enable_memberlist') ) {
		
		header('Location: '.$functions->get_config('board_url').$functions->make_url('index.php', array(), false));
		
	} elseif ( $functions->get_user_level() < $functions->get_config('view_memberlist_min_level') ) {
		
		$functions->redir_to_login();
		
	} else {
		
		$template->set_page_title($lang['MemberList']);
		
		//
		// Get page number
		//
		$numpages = ceil(intval($functions->get_stats('members')) / $functions->get_config('members_per_page'));
		$page = ( !empty($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) <= $numpages ) ? intval($_GET['page']) : 1;
		$limit_start = ( $page - 1 ) * $functions->get_config('members_per_page');
		$limit_end = $functions->get_config('members_per_page');
		$page_links = $functions->make_page_links($numpages, $page, $functions->get_stats('members'), $functions->get_config('members_per_page'), 'members.php');
		
		$template->parse('memberlist_header', 'memberlist', array(
			'page_links' => $page_links
		));
		
		//
		// Get members information
		//
		if ( !($result = $db->query("SELECT id, name, real_name, level, rank, regdate, posts FROM ".TABLE_PREFIX."members ORDER BY id ASC LIMIT ".$limit_start.", ".$limit_end)) )
			$functions->usebb_die('SQL', 'Unable to get members information!', __FILE__, __LINE__);
		
		while ( $userdata = $db->fetch_result($result) ) {
			
			switch ( $userdata['level'] ) {
				
				case 3:
					$level = $lang['Administrator'];
					break;
				case 2:
					$level = $lang['Moderator'];
					break;
				case 1:
					$level = $lang['Member'];
					break;
				
			}
			
			$template->parse('memberlist_user', 'memberlist', array(
				'username' => $functions->make_profile_link($userdata['id'], $userdata['name'], $userdata['level']),
				'real_name' => htmlspecialchars(stripslashes($userdata['real_name'])),
				'level' => $level,
				'rank' => htmlspecialchars(stripslashes($userdata['rank'])),
				'registered' => $functions->make_date($userdata['regdate']),
				'posts' => $userdata['posts'],
			));
			
		}
		
		$template->parse('memberlist_footer', 'memberlist', array(
			'page_links' => $page_links
		));
		
	}
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
} elseif ( $_GET['act'] == 'staff' ) {
	
	$session->update('stafflist');
	
	//
	// Include the page header
	//
	require(ROOT_PATH.'sources/page_head.php');
	
	if ( !$functions->get_config('enable_stafflist') ) {
		
		header('Location: '.$functions->get_config('board_url').$functions->make_url('index.php', array(), false));
		
	} elseif ( $functions->get_user_level() < $functions->get_config('view_stafflist_min_level') ) {
		
		$functions->redir_to_login();
		
	} else {
		
		$template->set_page_title($lang['StaffList']);
		
	}
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
} else {
	
	header('Location: '.$functions->get_config('board_url').$functions->make_url('index.php', array(), false));
	
}

?>
