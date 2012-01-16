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
 * Online users
 *
 * Gives a list of online users (guests and members), what they are doing and when.
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

$session->update('onlinelist');

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

if ( !$functions->get_config('enable_detailed_online_list') ) {
	
	$functions->redirect('index.php');
	
} elseif ( $functions->get_user_level() < $functions->get_config('view_detailed_online_list_min_level') ) {
	
	$functions->redir_to_login();
	
} else {

	$show = ( !empty($_GET['show']) && in_array($_GET['show'], array('all', 'members', 'staff', 'guests')) ) ? $_GET['show'] : 'all';
	
	if ( $show != 'all' )
		$q_user_id = ( $show == 'guests' ) ? " AND s.user_id = 0" : " AND s.user_id > 0";
	else
		$q_user_id = "";
	
	$q_level = ( $show == 'staff' ) ? " AND u.level > 1" : "";
	
	$min_updated = time() - ( $functions->get_config('online_min_updated') * 60 );
	
	$result = $db->query("SELECT s.user_id, s.ip_addr, s.updated, s.location, u.displayed_name, u.level, u.hide_from_online_list FROM ".TABLE_PREFIX."sessions s LEFT JOIN ".TABLE_PREFIX."members u ON u.id = s.user_id WHERE s.updated > ".$min_updated.$q_user_id.$q_level." ORDER BY s.updated DESC");
	
	$ids = $names = array(
		'forums' => array(),
		'topics' => array(),
		'posts' => array(),
		'users' => array()
	);
	
	$sessions = $seen_members = $seen_ips = array();
	
	while ( $sessiondata = $db->fetch_result($result) ) {
		
		if ( $sessiondata['hide_from_online_list'] && $functions->get_user_level() < LEVEL_ADMIN )
			continue;
		
		if ( $sessiondata['user_id'] && in_array($sessiondata['user_id'], $seen_members) )
			continue;
		
		if ( !$sessiondata['user_id'] && in_array($sessiondata['ip_addr'], $seen_ips) )
			continue;
		
		$sessions[] = $sessiondata;
		
		if ( $sessiondata['user_id'] )
			$seen_members[] = $sessiondata['user_id'];
		else
			$seen_ips[] = $sessiondata['ip_addr'];
		
	}
	
	//
	// Get page number
	//
	$numpages = ceil(intval(count($sessions)) / $functions->get_config('members_per_page'));
	$page = ( !empty($_GET['page']) && valid_int($_GET['page']) && intval($_GET['page']) > 0 && intval($_GET['page']) <= $numpages ) ? intval($_GET['page']) : 1;
	$limit_start = ( $page - 1 ) * $functions->get_config('members_per_page');
	$limit_end = $functions->get_config('members_per_page');
	$page_links = $functions->make_page_links($numpages, $page, count($sessions), $functions->get_config('members_per_page'), 'online.php', null, true, array('show' => $show));
	
	$i = 0;
	foreach ( $sessions as $key => $sessiondata ) {
		
		$i++;
		
		if ( $i <= $limit_start || $i > ( $limit_start+$limit_end ) ) {
			
			unset($sessions[$key]);
			continue;
			
		}
		
		$element = '';
		
		if ( preg_match('#^(?:forum|posttopic|rss-forum):([0-9]+)$#', $sessiondata['location'], $matches) )
			$element = 'forums';
		elseif ( preg_match('#^(?:topic|reply|movetopic|deletetopic|rss-topic):([0-9]+)$#', $sessiondata['location'], $matches) )
			$element = 'topics';
		elseif ( preg_match('#^(?:editpost|deletepost):([0-9]+)$#', $sessiondata['location'], $matches) )
			$element = 'posts';
		elseif ( preg_match('#^(?:profile|sendemail):([0-9]+)$#', $sessiondata['location'], $matches) )
			$element = 'users';
		
		if ( !empty($element) )
			$ids[$element][] = $matches[1];
		
	}
	
	if ( count($ids['forums']) ) {
		
		$result = $db->query("SELECT id, name, auth FROM ".TABLE_PREFIX."forums WHERE id IN(".join(', ', $ids['forums']).")");
		while ( $forumdata = $db->fetch_result($result) ) {
			
			if ( $functions->auth($forumdata['auth'], 'view', $forumdata['id']) )
				$names['forums'][$forumdata['id']] = $forumdata['name'];
			
		}
		
	}
	
	if ( count($ids['topics']) ) {
		
		$result = $db->query("SELECT t.id, t.topic_title, t.forum_id, f.auth FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id IN(".join(', ', $ids['topics']).") AND f.id = t.forum_id");
		while ( $topicdata = $db->fetch_result($result) ) {
			
			if ( $functions->auth($topicdata['auth'], 'view', $topicdata['forum_id']) )
				$names['topics'][$topicdata['id']] = $topicdata['topic_title'];
			
		}
		
	}
		
	if ( count($ids['posts']) ) {
		
		$result = $db->query("SELECT p.id, t.topic_title, t.forum_id, f.auth FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."forums f WHERE p.id IN(".join(', ', $ids['posts']).") AND t.id = p.topic_id AND f.id = t.forum_id");
		while ( $topicdata = $db->fetch_result($result) ) {
			
			if ( $functions->auth($topicdata['auth'], 'view', $topicdata['forum_id']) )
				$names['posts'][$topicdata['id']] = $topicdata['topic_title'];
			
		}
		
	}
	
	if ( count($ids['users']) ) {
		
		$result = $db->query("SELECT id, displayed_name FROM ".TABLE_PREFIX."members WHERE id IN(".join(', ', $ids['users']).")");
		while ( $userdata = $db->fetch_result($result) )
			$names['users'][$userdata['id']] = $userdata['displayed_name'];
		
	}
	
	$template->add_breadcrumb($lang['DetailedOnlineList']);
	
	function usebb_show_filter_link($new) {
		
		global $lang, $show, $functions;
		
		$lang_var = $lang[ucfirst($new)];
		
		return ( $show == $new ) ? '<strong>'.$lang_var.'</strong>' : '<a href="'.$functions->make_url('online.php', array('show' => $new)).'">'.$lang_var.'</a>';
		
	}
	
	$filter_links = array(
		usebb_show_filter_link('members'),
		usebb_show_filter_link('staff'),
		usebb_show_filter_link('guests'),
		usebb_show_filter_link('all'),
	);
	$filter_links = $lang['ShowOnly'].': '.implode(', ', $filter_links);
	
	$template->parse('header', 'onlinelist', array(
		'page_links' => $page_links,
		'filter_links' => $filter_links,
	));
	
	foreach ( $sessions as $sessiondata ) {
		
		if ( $sessiondata['user_id'] ) {
			
			if ( !$sessiondata['hide_from_online_list'] ) {
				
				$username = $functions->make_profile_link($sessiondata['user_id'], $sessiondata['displayed_name'], $sessiondata['level']);
				
			} else {
				
				$username = '<em>'.$functions->make_profile_link($sessiondata['user_id'], $sessiondata['displayed_name'], $sessiondata['level']).'</em>';
				
			}
			
		} else {
			
			$username = $lang['Guest'];
			
		}
		
		if ( $functions->get_user_level() == LEVEL_ADMIN )
			$username .= ' (<a href="'.$functions->make_url('admin.php', array('act' => 'iplookup', 'ip' => $sessiondata['ip_addr'])).'"><em>'.$sessiondata['ip_addr'].'</em></a>)';
		
		switch ( $sessiondata['location'] ) {
			
			case 'index':
				$location = '<a href="'.$functions->make_url('index.php').'">'.$lang['ForumIndex'].'</a>';
				break;
			case 'panel_home':
				$location = $lang['PanelHome'];
				break;
			case 'editprofile':
				$location = $lang['EditProfile'];
				break;
			case 'editoptions':
				$location = $lang['EditOptions'];
				break;
			case 'editpwd':
				$location = $lang['EditPasswd'];
				break;
			case 'subscriptions':
				$location = $lang['Subscriptions'];
				break;
			case 'faq':
				$location = '<a href="'.$functions->make_url('faq.php').'">'.$lang['FAQ'].'</a>';
				break;
			case 'search':
				$location = '<a href="'.$functions->make_url('search.php').'">'.$lang['Search'].'</a>';
				break;
			case 'activetopics':
				$location = '<a href="'.$functions->make_url('active.php').'">'.$lang['ActiveTopics'].'</a>';
				break;
			case 'login':
				$location = $lang['LogIn'];
				break;
			case 'logout':
				$location = sprintf($lang['LogOut'], '');
				break;
			case 'register':
				$location = $lang['Register'];
				break;
			case 'activate':
				$location = $lang['Activate'];
				break;
			case 'sendpwd':
				$location = $lang['SendPassword'];
				break;
			case 'onlinelist':
				$location = $lang['DetailedOnlineList'];
				break;
			case 'memberlist':
				$location = '<a href="'.$functions->make_url('members.php').'">'.$lang['MemberList'].'</a>';
				break;
			case 'stafflist':
				$location = '<a href="'.$functions->make_url('members.php', array('act' => 'staff')).'">'.$lang['StaffList'].'</a>';
				break;
			case 'rss':
				$location = '<a href="'.$functions->make_url('rss.php').'">'.$lang['RSSFeed'].'</a>';
				break;
			case 'stats':
				$location = '<a href="'.$functions->make_url('stats.php').'">'.$lang['Statistics'].'</a>';
				break;
			case 'sendemail:admin':
				$location = '<a href="'.$functions->make_url('mail.php', array('act' => 'admin')).'">'.$lang['ContactAdmin'].'</a>';
				break;
			
		}
		
		if ( empty($location) ) {
			
			if ( preg_match('#^forum:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['forums']) )
				$location = sprintf($lang['ViewingForum'], '<a href="'.$functions->make_url('forum.php', array('id' => $matches[1])).'">'.unhtml(stripslashes($names['forums'][$matches[1]])).'</a>');
			elseif ( preg_match('#^posttopic:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['forums']) )
				$location = sprintf($lang['PostingTopic'], '<a href="'.$functions->make_url('forum.php', array('id' => $matches[1])).'">'.unhtml(stripslashes($names['forums'][$matches[1]])).'</a>');
			elseif ( preg_match('#^topic:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['topics']) )
				$location = sprintf($lang['ViewingTopic'], '<a href="'.$functions->make_url('topic.php', array('id' => $matches[1])).'">'.unhtml($functions->replace_badwords(stripslashes($names['topics'][$matches[1]]))).'</a>');
			elseif ( preg_match('#^reply:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['topics']) )
				$location = sprintf($lang['PostingReply'], '<a href="'.$functions->make_url('topic.php', array('id' => $matches[1])).'">'.unhtml($functions->replace_badwords(stripslashes($names['topics'][$matches[1]]))).'</a>');
			elseif ( preg_match('#^movetopic:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['topics']) )
				$location = sprintf($lang['MovingTopic'], '<a href="'.$functions->make_url('topic.php', array('id' => $matches[1])).'">'.unhtml($functions->replace_badwords(stripslashes($names['topics'][$matches[1]]))).'</a>');
			elseif ( preg_match('#^deletetopic:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['topics']) )
				$location = sprintf($lang['DeletingTopic'], '<a href="'.$functions->make_url('topic.php', array('id' => $matches[1])).'">'.unhtml($functions->replace_badwords(stripslashes($names['topics'][$matches[1]]))).'</a>');
			elseif ( preg_match('#^editpost:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['posts']) )
				$location = sprintf($lang['EditingPost'], '<a href="'.$functions->make_url('topic.php', array('post' => $matches[1])).'#post'.$matches[1].'">'.unhtml($functions->replace_badwords(stripslashes($names['posts'][$matches[1]]))).'</a>');
			elseif ( preg_match('#^deletepost:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['posts']) )
				$location = sprintf($lang['DeletingPost'], '<a href="'.$functions->make_url('topic.php', array('post' => $matches[1])).'#post'.$matches[1].'">'.unhtml($functions->replace_badwords(stripslashes($names['posts'][$matches[1]]))).'</a>');
			elseif ( preg_match('#^profile:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['users']) )
				$location = sprintf($lang['Profile'], '<a href="'.$functions->make_url('profile.php', array('id' => $matches[1])).'">'.unhtml(stripslashes($names['users'][$matches[1]])).'</a>');
			elseif ( preg_match('#^sendemail:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['users']) )
				$location = sprintf($lang['SendEmail'], '<a href="'.$functions->make_url('profile.php', array('id' => $matches[1])).'">'.unhtml(stripslashes($names['users'][$matches[1]])).'</a>');
			elseif ( preg_match('#^rss-topic:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['topics']) )
				$location = sprintf($lang['RSSFeedForTopic'], '<a href="'.$functions->make_url('topic.php', array('id' => $matches[1])).'">'.unhtml($functions->replace_badwords(stripslashes($names['topics'][$matches[1]]))).'</a>');
			elseif ( preg_match('#^rss-forum:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['forums']) )
				$location = sprintf($lang['RSSFeedForForum'], '<a href="'.$functions->make_url('forum.php', array('id' => $matches[1])).'">'.unhtml(stripslashes($names['forums'][$matches[1]])).'</a>');
			else
				$location = $lang['Unknown'];
			
		}
		
		$template->parse('user', 'onlinelist', array(
			'username' => $username,
			'current_page' => $location,
			'latest_update' => $functions->make_date($sessiondata['updated'], 'h:i:s a'),
		));
		
		unset($location);
		
	}
	
	if ( !count($sessions) ) {
		
		$template->parse('user', 'onlinelist', array(
			'username' => '<em>('.$lang['Nobody'].')</em>',
			'current_page' => '&ndash;',
			'latest_update' => '&ndash;',
		));
		
	}
	
	$template->parse('footer', 'onlinelist', array(
		'page_links' => $page_links,
		'filter_links' => $filter_links,
	));
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
