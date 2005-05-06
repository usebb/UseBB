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
 
//
// Update and get the session information
//
$session->update('stats');
 
//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');
 
if ( $functions->get_config('enable_stats') ) {
		
	$template->set_page_title($lang['Statistics']);
	
	//
	// Show general statistics
	//
	
	$days_since_start = ( ( time() - $functions->get_stats('started') ) / 86400 );
	$latest_member = $functions->get_stats('latest_member');
	
	$template->parse('general_stats', 'stats', array(
		'count_posts' => $functions->get_stats('posts'),
		'count_topics' => $functions->get_stats('topics'),
		'count_members' => $functions->get_stats('members'),
		'posts_per_day' => round($functions->get_stats('posts') / $days_since_start, 2),
		'topics_per_day' => round($functions->get_stats('topics') / $days_since_start, 2),
		'members_per_day' => round($functions->get_stats('members') / $days_since_start, 2),
		'board_started' => $functions->make_date($functions->get_stats('started')),
		'board_days' => round($days_since_start),
		'regdate_newest_member' => ( !$functions->get_stats('members') ) ? '' : $functions->make_date($latest_member['regdate']),
		'newest_member' => ( !$functions->get_stats('members') ) ? '' : '<a href="'.$functions->make_url('profile.php', array('id' => $latest_member['id'])).'">'.unhtml(stripslashes($latest_member['displayed_name'])).'</a>',
		'posts_per_member' => round($functions->get_stats('posts') / $functions->get_stats('members'), 2),
		'posts_per_forum' => round($functions->get_stats('posts') / $functions->get_stats('forums'), 2),
	));
	
	//
	// Most active and viewed topics
	//
	
	if ( $functions->get_stats('topics') ) {
		
		//
		// Excluded forums
		//
		$exclude_forums = $functions->get_config('exclude_forums_stats');
		$exclude_forums_query_part = ( is_array($exclude_forums) && count($exclude_forums) ) ? " AND id NOT IN (".join(', ', $exclude_forums).")" : '';
		
		//
		// Get a list of forums
		//
		$result = $db->query("SELECT id, name, auth FROM ".TABLE_PREFIX."forums WHERE topics > 0".$exclude_forums_query_part);
		
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
		
		if ( count($forum_ids) ) {
			
			$template->parse('most_active_topics_header', 'stats');
			
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
			
			$template->parse('most_active_topics_footer', 'stats');
			
			$template->parse('most_viewed_topics_header', 'stats');
			
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
			
			$template->parse('most_viewed_topics_footer', 'stats');
			
		}
		
	}
	
} else {
	
	
	
}
 
//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');
 
?>