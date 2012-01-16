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
 * Statistics
 *
 * Shows some general statistical values and top lists.
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
 
//
// Update and get the session information
//
$session->update('stats');
 
//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');
 
if ( !$functions->get_config('enable_stats') ) {
	
	$functions->redirect('index.php');
	
} elseif ( $functions->get_user_level() < $functions->get_config('view_stats_min_level') ) {
	
	$functions->redir_to_login();
	
} else {
		
	$template->add_breadcrumb($lang['Statistics']);
	
	//
	// Show general statistics
	//
	
	$days_since_start = ( ( time() - $functions->get_stats('started') ) / 86400 );
	$latest_member = $functions->get_stats('latest_member');
	
	$template->parse('general_stats', 'stats', array(
		'count_posts' => $functions->get_stats('posts'),
		'count_topics' => $functions->get_stats('topics'),
		'count_members' => $functions->get_stats('members'),
		'count_cats' => $functions->get_stats('categories'),
		'count_forums' => $functions->get_stats('forums'),
		'posts_per_day' => ( $days_since_start > 1 ) ? round($functions->get_stats('posts') / $days_since_start, 2) : $functions->get_stats('posts'),
		'topics_per_day' => ( $days_since_start > 1 ) ? round($functions->get_stats('topics') / $days_since_start, 2) : $functions->get_stats('topics'),
		'members_per_day' => ( $days_since_start > 1 ) ? round($functions->get_stats('members') / $days_since_start, 2) : $functions->get_stats('members'),
		'board_started' => $functions->make_date($functions->get_stats('started')),
		'board_days' => floor($days_since_start),
		'regdate_newest_member' => ( !$functions->get_stats('members') ) ? '' : $functions->make_date($latest_member['regdate']),
		'newest_member' => ( !$functions->get_stats('members') ) ? '' : '<a href="'.$functions->make_url('profile.php', array('id' => $latest_member['id'])).'">'.unhtml(stripslashes($latest_member['displayed_name'])).'</a>',
		'posts_per_member' => ( $functions->get_stats('members') ) ? round($functions->get_stats('posts') / $functions->get_stats('members'), 2) : 0,
		'posts_per_forum' => ( $functions->get_stats('forums') ) ? round($functions->get_stats('posts') / $functions->get_stats('forums'), 2) : 0,
		'topics_per_member' => ( $functions->get_stats('members') ) ? round($functions->get_stats('topics') / $functions->get_stats('members'), 2) : 0,
		'topics_per_forum' => ( $functions->get_stats('forums') ) ? round($functions->get_stats('topics') / $functions->get_stats('forums'), 2) : 0,
	));
	
	//
	// Excluded forums
	//
	$exclude_forums = $functions->get_config('exclude_forums_stats');
	$exclude_forums_query_part = ( is_array($exclude_forums) && count($exclude_forums) ) ? " WHERE id NOT IN (".join(', ', $exclude_forums).")" : '';
	
	//
	// Get a list of forums
	//
	$result = $db->query("SELECT id, name, auth FROM ".TABLE_PREFIX."forums".$exclude_forums_query_part);
	
	$forum_ids = $forum_names = array();
	while ( $forumdata = $db->fetch_result($result) ) {
		
		//
		// Place permitted forums into the arrays
		//
		if ( $functions->auth($forumdata['auth'], 'read', $forumdata['id']) ) {
			
			$forum_ids[] = $forumdata['id'];
			$forum_names[$forumdata['id']] = $forumdata['name'];
			
		}
		
	}
	
	//
	// Most active members
	//
	$template->parse('most_active_members_header', 'stats');
	
	if ( $functions->get_stats('members') ) {
		
		$result = $db->query("SELECT id, displayed_name, level, posts FROM ".TABLE_PREFIX."members ORDER BY posts DESC LIMIT 10");
		
		$i = 1;
		while ( $memberdata = $db->fetch_result($result) ) {
			
			$template->parse('most_active_members_member', 'stats', array(
				'username' => $functions->make_profile_link($memberdata['id'], $memberdata['displayed_name'], $memberdata['level']),
				'posts' => $memberdata['posts'],
				'rank' => $i
			));
			$i++;
			
		}
		
	}
	
	$template->parse('most_active_members_footer', 'stats');
	
	//
	// Most active forums
	//
	$template->parse('most_active_forums_header', 'stats');
	
	if ( $functions->get_stats('topics') && count($forum_ids) ) {
		
		$result = $db->query("SELECT id, name, posts FROM ".TABLE_PREFIX."forums WHERE id IN(".join(', ', $forum_ids).") ORDER BY posts DESC LIMIT 10");
		
		$i = 1;
		while ( $forumdata = $db->fetch_result($result) ) {
			
			$template->parse('most_active_forums_forum', 'stats', array(
				'forum' => '<a href="'.$functions->make_url('forum.php', array('id' => $forumdata['id'])).'">'.unhtml(stripslashes($forumdata['name'])).'</a>',
				'posts' => $forumdata['posts'],
				'rank' => $i
			));
			$i++;
			
		}
		
	}
	
	$template->parse('most_active_forums_footer', 'stats');
	
	//
	// Most active topics
	//
	$template->parse('most_active_topics_header', 'stats');
	
	if ( $functions->get_stats('topics') && count($forum_ids) ) {
		
		$result = $db->query("SELECT id, topic_title, count_replies FROM ".TABLE_PREFIX."topics WHERE forum_id IN(".join(', ', $forum_ids).") ORDER BY count_replies DESC LIMIT 10");
		
		$i = 1;
		while ( $topicdata = $db->fetch_result($result) ) {
			
			$template->parse('most_active_topics_topic', 'stats', array(
				'title' => '<a href="'.$functions->make_url('topic.php', array('id' => $topicdata['id'])).'">'.unhtml($functions->replace_badwords(stripslashes($topicdata['topic_title']))).'</a>',
				'replies' => $topicdata['count_replies'],
				'rank' => $i
			));
			$i++;
			
		}
		
	}
	
	$template->parse('most_active_topics_footer', 'stats');
	
	//
	// Most viewed topics
	//
	$template->parse('most_viewed_topics_header', 'stats');
	
	if ( $functions->get_stats('topics') && count($forum_ids) ) {
		
		$result = $db->query("SELECT id, topic_title, count_views FROM ".TABLE_PREFIX."topics WHERE forum_id IN(".join(', ', $forum_ids).") ORDER BY count_views DESC LIMIT 10");
		
		$i = 1;
		while ( $topicdata = $db->fetch_result($result) ) {
			
			$template->parse('most_viewed_topics_topic', 'stats', array(
				'title' => '<a href="'.$functions->make_url('topic.php', array('id' => $topicdata['id'])).'">'.unhtml($functions->replace_badwords(stripslashes($topicdata['topic_title']))).'</a>',
				'views' => $topicdata['count_views'],
				'rank' => $i
			));
			$i++;
			
		}
		
	}
	
	$template->parse('most_viewed_topics_footer', 'stats');
	
}
 
//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');
 
?>
