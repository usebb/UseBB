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

$session->update('onlinelist');
	
//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

if ( $functions->get_config('enable_detailed_online_list') && $functions->get_user_level() >= $functions->get_config('view_detailed_online_list_min_level') ) {
	
	$min_updated = time() - ( $functions->get_config('online_min_updated') * 60 );
	
	if ( !($result = $db->query("SELECT s.user_id, s.ip_addr, s.updated, s.location, u.name, u.level, u.hide_from_online_list FROM ".TABLE_PREFIX."sessions s LEFT JOIN ".TABLE_PREFIX."members u ON u.id = s.user_id WHERE s.updated > ".$min_updated." ORDER BY s.updated DESC")) )
		$functions->usebb_die('SQL', 'Unable to get sessions information!', __FILE__, __LINE__);
	
	$ids = $names = array(
		'forums' => array(),
		'topics' => array(),
		'posts' => array(),
		'users' => array()
	);
	$sessions = array();
	while ( $sessiondata = $db->fetch_result($result) ) {
		
		$element = '';
		
		if ( preg_match('#^(forum|posttopic):#', $sessiondata['location']) )
			$element = 'forums';
		elseif ( preg_match('#^(topic|reply|movetopic|deletetopic):#', $sessiondata['location']) )
			$element = 'topics';
		elseif ( preg_match('#^(editpost|deletepost):#', $sessiondata['location']) )
			$element = 'posts';
		elseif ( preg_match('#^(profile|sendemail):#', $sessiondata['location']) )
			$element = 'users';
		
		if ( !empty($element) )
			$ids[$element][] = intval(substr(strstr($sessiondata['location'], ':'), 1));
		
		$sessions[] = $sessiondata;
		
	}
	
	if ( count($ids['forums']) ) {
		
		if ( !($result = $db->query("SELECT id, name, auth FROM ".TABLE_PREFIX."forums WHERE id IN(".join(', ', $ids['forums']).")")) )
			$functions->usebb_die('SQL', 'Unable to get forums information!', __FILE__, __LINE__);
		while ( $forumdata = $db->fetch_result($result) ) {
			
			if ( $functions->auth($forumdata['auth'], 'view', $forumdata['id']) )
				$names['forums'][$forumdata['id']] = $forumdata['name'];
			
		}
		
	}
	
	if ( count($ids['topics']) ) {
		
		if ( !($result = $db->query("SELECT t.id, t.topic_title, t.forum_id, f.auth FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id IN(".join(', ', $ids['topics']).") AND f.id = t.forum_id")) )
			$functions->usebb_die('SQL', 'Unable to get topics information!', __FILE__, __LINE__);
		while ( $topicdata = $db->fetch_result($result) ) {
			
			if ( $functions->auth($topicdata['auth'], 'view', $topicdata['forum_id']) )
				$names['topics'][$topicdata['id']] = $topicdata['topic_title'];
			
		}
		
	}
		
	if ( count($ids['posts']) ) {
		
		if ( !($result = $db->query("SELECT p.id, t.topic_title, t.forum_id, f.auth FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."forums f WHERE p.id IN(".join(', ', $ids['posts']).") AND t.id = p.topic_id AND f.id = t.forum_id")) )
			$functions->usebb_die('SQL', 'Unable to get posts information!', __FILE__, __LINE__);
		while ( $topicdata = $db->fetch_result($result) ) {
			
			if ( $functions->auth($topicdata['auth'], 'view', $topicdata['forum_id']) )
				$names['posts'][$topicdata['id']] = $topicdata['topic_title'];
			
		}
		
	}
	
	if ( count($ids['users']) ) {
		
		if ( !($result = $db->query("SELECT id, name FROM ".TABLE_PREFIX."members WHERE id IN(".join(', ', $ids['users']).")")) )
			$functions->usebb_die('SQL', 'Unable to get members information!', __FILE__, __LINE__);
		while ( $userdata = $db->fetch_result($result) )
			$names['users'][$userdata['id']] = $userdata['name'];
		
	}
	
	$template->set_page_title($lang['DetailedOnlineList']);
	
	$template->parse('onlinelist_header', 'onlinelist', array(
		'username' => $lang['Username'],
		'location' => $lang['Location'],
		'last_update' => $lang['LastUpdate'],
	));
	
	$seen_members = $seen_ips = array();
	
	foreach ( $sessions as $sessiondata ) {
		
		if ( !$sessiondata['hide_from_online_list'] || $functions->get_user_level() == 3 )
			continue;
		
		if ( $sessiondata['user_id'] && in_array($sessiondata['user_id'], $seen_members) )
			continue;
		
		if ( !$sessiondata['user_id'] && in_array($sessiondata['ip_addr'], $seen_ips) )
			continue;
		
		$username = ( $sessiondata['user_id'] ) ? $functions->make_profile_link($sessiondata['user_id'], $sessiondata['name'], $sessiondata['level']) : $lang['Guest'];
		
		if ( $functions->get_user_level() == 3 )
			$username .= ' (<em>'.$sessiondata['ip_addr'].'</em>)';
		
		switch ( $sessiondata['location'] ) {
			
			case 'index':
				$location = '<a href="'.$functions->make_url('index.php').'">'.$lang['ForumIndex'].'</a>';
				break;
			case 'panel_home':
				$location = '<a href="'.$functions->make_url('panel.php').'">'.$lang['PanelHome'].'</a>';
				break;
			case 'editprofile':
				$location = '<a href="'.$functions->make_url('panel.php', array('act' => 'editprofile')).'">'.$lang['EditProfile'].'</a>';
				break;
			case 'editoptions':
				$location = '<a href="'.$functions->make_url('panel.php', array('act' => 'editoptions')).'">'.$lang['EditOptions'].'</a>';
				break;
			case 'editpwd':
				$location = '<a href="'.$functions->make_url('panel.php', array('act' => 'editpwd')).'">'.$lang['EditPasswd'].'</a>';
				break;
			case 'subscriptions':
				$location = '<a href="'.$functions->make_url('panel.php', array('act' => 'subscriptions')).'">'.$lang['Subscriptions'].'</a>';
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
				$location = $lang['LogOut'];
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
				$location = '<a href="'.$functions->make_url('online.php').'">'.$lang['DetailedOnlineList'].'</a>';
				break;
			
		}
		
		if ( empty($location) ) {
			
			if ( preg_match('#^forum:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['forums']) )
				$location = '<a href="'.$functions->make_url('forum.php', array('id' => $matches[1])).'">'.htmlspecialchars(stripslashes($names['forums'][$matches[1]])).'</a>';
			elseif ( preg_match('#^posttopic:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['forums']) )
				$location = sprintf($lang['PostingTopic'], '<a href="'.$functions->make_url('forum.php', array('id' => $matches[1])).'">'.htmlspecialchars(stripslashes($names['forums'][$matches[1]])).'</a>');
			elseif ( preg_match('#^topic:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['topics']) )
				$location = '<a href="'.$functions->make_url('topic.php', array('id' => $matches[1])).'">'.htmlspecialchars(stripslashes($names['topics'][$matches[1]])).'</a>';
			elseif ( preg_match('#^reply:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['topics']) )
				$location = sprintf($lang['PostingReply'], '<a href="'.$functions->make_url('topic.php', array('id' => $matches[1])).'">'.htmlspecialchars(stripslashes($names['topics'][$matches[1]])).'</a>');
			elseif ( preg_match('#^movetopic:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['topics']) )
				$location = sprintf($lang['MovingTopic'], '<a href="'.$functions->make_url('topic.php', array('id' => $matches[1])).'">'.htmlspecialchars(stripslashes($names['topics'][$matches[1]])).'</a>');
			elseif ( preg_match('#^deletetopic:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['topics']) )
				$location = sprintf($lang['DeletingTopic'], '<a href="'.$functions->make_url('topic.php', array('id' => $matches[1])).'">'.htmlspecialchars(stripslashes($names['topics'][$matches[1]])).'</a>');
			elseif ( preg_match('#^editpost:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['posts']) )
				$location = sprintf($lang['EditingPost'], '<a href="'.$functions->make_url('topic.php', array('post' => $matches[1])).'#post'.$matches[1].'">'.htmlspecialchars(stripslashes($names['posts'][$matches[1]])).'</a>');
			elseif ( preg_match('#^deletepost:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['posts']) )
				$location = sprintf($lang['DeletingPost'], '<a href="'.$functions->make_url('topic.php', array('post' => $matches[1])).'#post'.$matches[1].'">'.htmlspecialchars(stripslashes($names['posts'][$matches[1]])).'</a>');
			elseif ( preg_match('#^profile:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['users']) )
				$location = sprintf($lang['Profile'], '<a href="'.$functions->make_url('profile.php', array('id' => $matches[1])).'">'.htmlspecialchars(stripslashes($names['users'][$matches[1]])).'</a>');
			elseif ( preg_match('#^sendemail:([0-9]+)$#', $sessiondata['location'], $matches) && array_key_exists($matches[1], $names['users']) )
				$location = sprintf($lang['SendEmail'], '<a href="'.$functions->make_url('profile.php', array('id' => $matches[1])).'">'.htmlspecialchars(stripslashes($names['users'][$matches[1]])).'</a>');
			else
				$location = $lang['Unknown'];
			
		}
		
		$template->parse('onlinelist_user', 'onlinelist', array(
			'username' => $username,
			'location' => $location,
			'last_update' => $functions->make_date($sessiondata['updated']),
		));
		
		if ( $sessiondata['user_id'] )
			$seen_members[] = $sessiondata['user_id'];
		else
			$seen_ips[] = $sessiondata['ip_addr'];
		unset($location);
		
	}
	
	$template->parse('onlinelist_footer', 'onlinelist');
	
} else {
	
	$functions->redir_to_login();
	
}
	
//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
