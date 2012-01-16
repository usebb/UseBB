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
 * Member and staff list
 *
 * Either gives a list of all members or a categorized staff list
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2012 UseBB Team
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
		
		$template->add_breadcrumb($lang['MemberList']);
		
		//
		// Sort options
		//
		$sort_items = array('displayed_name', 'real_name', 'level', 'rank', 'regdate', 'posts');
		$sort_orders = array('asc', 'desc');
		$_GET['search'] = ( !empty($_GET['search']) ) ? $_GET['search'] : '';
		$_GET['sort_by'] = ( !empty($_GET['sort_by']) && in_array($_GET['sort_by'], $sort_items) ) ? $_GET['sort_by'] : 'regdate';
		$_GET['order'] = ( !empty($_GET['order']) && in_array($_GET['order'], $sort_orders) ) ? $_GET['order'] : 'asc';
		
		//
		// Construct sort form
		//
		$sort_by_links = '<form action="'.$functions->make_url('members.php', NULL, true, true, true).'" method="get"><div>';
			$sort_by_links .= $lang['Search'].': <input type="text" name="search" value="'.unhtml(stripslashes($_GET['search'])).'" size="10" maxlength="255" /> ';
			$sort_by_links .= $lang['SortBy'].': <select name="sort_by">';
			foreach ( $sort_items as $sort_item ) {
				
				$selected = ( $_GET['sort_by'] == $sort_item ) ? ' selected="selected"' : '';
				$sort_by_links .= '<option value="'.$sort_item.'"'.$selected.'>'.$lang['SortBy-'.$sort_item].'</option>';
				
			}
			$sort_by_links .= '</select> ';
			$sort_by_links .= '<select name="order">';
			foreach ( $sort_orders as $sort_order ) {
				
				$selected = ( $_GET['order'] == $sort_order ) ? ' selected="selected"' : '';
				$sort_by_links .= '<option value="'.$sort_order.'"'.$selected.'>'.$lang['SortOrder-'.$sort_order].'</option>';
				
			}
			$sort_by_links .= '</select> ';
		$sort_by_links .= '<input type="submit" value="'.$lang['Sort'].'" /></div></form>';
		
		//
		// Sort query part
		// Additionally, keep second sort for displayed_name
		//
		$sort_part_sql = $_GET['sort_by']." ".strtoupper($_GET['order']);
		if ( $_GET['sort_by'] != 'displayed_name' )
			$sort_part_sql .= ", displayed_name ASC";
		
		$never_activated_sql = ( $functions->get_config('show_never_activated_members') ) ? "" : " AND ( active <> 0 OR last_login <> 0 )";
		
		//
		// Get page number
		//
		$result = $db->query("SELECT COUNT(*) as count FROM ".TABLE_PREFIX."members WHERE displayed_name LIKE '%".str_replace(array('%', '_'), array('\%', '\_'), $_GET['search'])."%'".$never_activated_sql." ORDER BY ".$sort_part_sql);
		$out = $db->fetch_result($result);
		$num_members = $out['count'];
		
		$numpages = ceil($num_members / $functions->get_config('members_per_page'));
		$page = ( !empty($_GET['page']) && valid_int($_GET['page']) && intval($_GET['page']) > 0 && intval($_GET['page']) <= $numpages ) ? intval($_GET['page']) : 1;
		$limit_start = ( $page - 1 ) * $functions->get_config('members_per_page');
		$limit_end = $functions->get_config('members_per_page');
		$page_links = $functions->make_page_links($numpages, $page, $num_members, $functions->get_config('members_per_page'), 'members.php', NULL, true, $_GET, true);
		
		$template->parse('header', 'memberlist', array(
			'page_links' => $page_links,
			'sort_by_links' => $sort_by_links
		));
		
		if ( !$num_members ) {
			
			//
			// No members found
			//
			$template->parse('no_users_found', 'memberlist');
			
		} else {
			
			//
			// Get members information
			//
			
			$result = $db->query("SELECT id, displayed_name, real_name, email, email_show, level, rank, regdate, posts FROM ".TABLE_PREFIX."members WHERE displayed_name LIKE '%".str_replace(array('%', '_'), array('\%', '\_'), $_GET['search'])."%'".$never_activated_sql." ORDER BY ".$sort_part_sql." LIMIT ".$limit_start.", ".$limit_end);
			
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
			
		}
		
		$template->parse('footer', 'memberlist', array(
			'page_links' => $page_links,
			'sort_by_links' => $sort_by_links
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
		
		$template->add_breadcrumb($lang['StaffList']);
		
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
