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

/**
 * Member and staff list
 *
 * Either gives a list of all members or a categorized staff list
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2005 UseBB Team
 * @package	UseBB
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
		
		$functions->redirect('index.php');
		
	} elseif ( $functions->get_user_level() < $functions->get_config('view_memberlist_min_level') ) {
		
		$functions->redir_to_login();
		
	} else {
		
		$template->set_page_title($lang['MemberList']);
		
		$_GET['sortby'] = ( !empty($_GET['sortby']) ) ? $_GET['sortby'] : 'regdate';
		
		//
		// Get page number
		//
		$numpages = ceil(intval($functions->get_stats('members')) / $functions->get_config('members_per_page'));
		$page = ( !empty($_GET['page']) && valid_int($_GET['page']) && intval($_GET['page']) <= $numpages ) ? intval($_GET['page']) : 1;
		$limit_start = ( $page - 1 ) * $functions->get_config('members_per_page');
		$limit_end = $functions->get_config('members_per_page');
		$page_links = $functions->make_page_links($numpages, $page, $functions->get_stats('members'), $functions->get_config('members_per_page'), 'members.php', NULL, true, array('sortby' => $_GET['sortby']));
		
		$template->parse('header', 'memberlist', array(
			'page_links' => $page_links
		));
		
		//
		// Get members information
		//
		switch ( $_GET['sortby'] ) {
			
			case 'username':
				$sort_by_sql_part = 'displayed_name ASC';
				break;
			case 'level':
				$sort_by_sql_part = 'level DESC, displayed_name ASC';
				break;
			case 'regdate':
				$sort_by_sql_part = 'regdate ASC';
				break;
			case 'posts':
				$sort_by_sql_part = 'posts DESC, displayed_name ASC';
				break;
			
		}
		
		$result = $db->query("SELECT id, displayed_name, real_name, email, email_show, level, rank, regdate, posts FROM ".TABLE_PREFIX."members ORDER BY ".$sort_by_sql_part." LIMIT ".$limit_start.", ".$limit_end);
		
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
			
			$template->parse('user', 'memberlist', array(
				'username' => $functions->make_profile_link($userdata['id'], $userdata['displayed_name'], $userdata['level']),
				'real_name' => unhtml(stripslashes($userdata['real_name'])),
				'level' => $level,
				'rank' => stripslashes($userdata['rank']),
				'registered' => $functions->make_date($userdata['regdate']),
				'posts' => $userdata['posts'],
				'email' => $functions->show_email($userdata),
			));
			
		}
		
		$sort_by_links = array(
			'<a href="'.$functions->make_url('members.php', array('sortby' => 'username', 'page' => $page)).'">' . ( ( $_GET['sortby'] != 'username' ) ? $lang['Username'] : '<strong>'.$lang['Username'].'</strong>' ) . '</a>',
			'<a href="'.$functions->make_url('members.php', array('sortby' => 'level', 'page' => $page)).'">' . ( ( $_GET['sortby'] != 'level' ) ? $lang['Level'] : '<strong>'.$lang['Level'].'</strong>' ) . '</a>',
			'<a href="'.$functions->make_url('members.php', array('sortby' => 'regdate', 'page' => $page)).'">' . ( ( $_GET['sortby'] != 'regdate' ) ? $lang['Registered'] : '<strong>'.$lang['Registered'].'</strong>' ) . '</a>',
			'<a href="'.$functions->make_url('members.php', array('sortby' => 'posts', 'page' => $page)).'">' . ( ( $_GET['sortby'] != 'posts' ) ? $lang['Posts'] : '<strong>'.$lang['Posts'].'</strong>' ) . '</a>',
		);
		
		$template->parse('footer', 'memberlist', array(
			'page_links' => $page_links,
			'sort_by_links' => sprintf($lang['SortBy'], join(', ', $sort_by_links))
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
		
		$functions->redirect('index.php');
		
	} elseif ( $functions->get_user_level() < $functions->get_config('view_stafflist_min_level') ) {
		
		$functions->redir_to_login();
		
	} else {
		
		$template->set_page_title($lang['StaffList']);
		
		//
		// Get members information
		//
		$result = $db->query("SELECT id, displayed_name, real_name, email, email_show, level, rank, regdate, posts FROM ".TABLE_PREFIX."members WHERE level > 1 ORDER BY level DESC, rank ASC");
		
		$admins = $mods = array();
		while ( $staffinfo = $db->fetch_result($result) ) {
			
			if ( $staffinfo['level'] == LEVEL_ADMIN )
				$admins[] = $staffinfo;
			else
				$mods[] = $staffinfo;
			
		}
		$template->parse('header', 'stafflist');
		
		if ( count($admins) ) {
			
			$template->parse('cat_header', 'stafflist', array(
				'level' => $lang['Administrators']
			));
			
			foreach ( $admins as $userdata ) {
				
				$template->parse('user', 'stafflist', array(
					'username' => $functions->make_profile_link($userdata['id'], $userdata['displayed_name'], $userdata['level']),
					'real_name' => unhtml(stripslashes($userdata['real_name'])),
					'rank' => stripslashes($userdata['rank']),
					'registered' => $functions->make_date($userdata['regdate']),
					'posts' => $userdata['posts'],
					'email' => $functions->show_email($userdata),
				));
				
			}
			
			$template->parse('cat_footer', 'stafflist');
			
		}
		
		if ( count($mods) ) {
			
			$template->parse('cat_header', 'stafflist', array(
				'level' => $lang['Moderators']
			));
			
			foreach ( $mods as $userdata ) {
				
				$template->parse('user', 'stafflist', array(
					'username' => $functions->make_profile_link($userdata['id'], $userdata['displayed_name'], $userdata['level']),
					'real_name' => unhtml(stripslashes($userdata['real_name'])),
					'rank' => stripslashes($userdata['rank']),
					'registered' => $functions->make_date($userdata['regdate']),
					'posts' => $userdata['posts'],
					'email' => $functions->show_email($userdata),
				));
				
			}
			
			$template->parse('cat_footer', 'stafflist');
			
		}
		
		$template->parse('footer', 'stafflist');
		
	}
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
} else {
	
	$functions->redirect('index.php');
	
}

?>
