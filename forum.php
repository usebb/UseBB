<?php

/*
	Copyright (C) 2003-2004 UseBB Team
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
// If an ID has been passed
//
if ( !empty($_GET['id']) && is_numeric($_GET['id']) ) {	
	
	//
	// Update and get the session information
	//
	$session->update('forum:'.$_GET['id']);
	
	//
	// Include the page header
	//
	require(ROOT_PATH.'sources/page_head.php');
	
	if ( !($result = $db->query("SELECT f.name, f.auth, f.topics, f.status, c.id AS cat_id, c.name AS cat_name FROM ".TABLE_PREFIX."forums f, ".TABLE_PREFIX."cats c WHERE f.id = ".$_GET['id']." AND f.cat_id = c.id")) )
		$functions->usebb_die('SQL', 'Unable to get forum information!', __FILE__, __LINE__);
	
	if ( !$db->num_rows($result) ) {
		
		//
		// This forum does not exist, show an error
		//
		$template->set_page_title($lang['Error']);
		$template->parse('msgbox', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchForum'], 'ID '.$_GET['id'])
		));
		
	} else {	
		
		$forumdata = $db->fetch_result($result);
		
		if ( $functions->auth($forumdata['auth'], 'view', $_GET['id']) ) {
			
			$template->set_page_title(stripslashes($forumdata['name']));
			
			$location_bar = '<a href="'.$functions->make_url('index.php').'">'.$functions->get_config('board_name').'</a> '.$template->get_config('location_arrow').' '.htmlentities(stripslashes($forumdata['name']));
			$template->parse('location_bar', array(
				'location_bar' => $location_bar
			));
			
			if ( !$forumdata['topics'] ) {
				
				$template->parse('msgbox', array(
					'box_title' => $lang['Note'],
					'content' => $lang['NoTopics']
				));
				
			}
			
			$forum_links = ( $forumdata['status'] ) ? '<a href="'.$functions->make_url('post.php', array('forum' => $_GET['id'])).'">'.$lang['PostNewTopic'].'</a>' : '<a href="'.$functions->make_url('post.php', array('forum' => $_GET['id'])).'">'.$lang['ForumIsLocked'].'</a>';
			
			//
			// Output the topic list
			//
			$template->parse('topiclist_header', array(
				'topic' => $lang['Topic'],
				'author' => $lang['Author'],
				'replies' => $lang['Replies'],
				'views' => $lang['Views'],
				'latest_post' => $lang['LatestPost'],
				'forum_links' => $forum_links
			));
			
			if ( $forumdata['topics'] ) {
				
				if ( !($result = $db->query("SELECT t.id, t.topic_title, t.last_post_id, t.count_replies, t.count_views, t.status_locked, t.status_sticky, p.poster_guest, p2.poster_guest AS last_poster_guest, p.post_time AS last_post_time, u.id AS poster_id, u.name AS poster_name, u2.id AS last_poster_id, u2.name AS last_poster_name FROM ".TABLE_PREFIX."topics t, ( ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."users u ON p.poster_id = u.id ), ( ".TABLE_PREFIX."posts p2 LEFT JOIN ".TABLE_PREFIX."users u2 ON p2.poster_id = u2.id ) WHERE t.forum_id = ".$_GET['id']." AND p.id = t.first_post_id AND p2.id = t.last_post_id ORDER BY p2.post_time DESC")) )
					$functions->usebb_die('SQL', 'Unable to get topic list!', __FILE__, __LINE__);
				
				while ( $topicdata = $db->fetch_result($result) ) {
					
					$topic_name = '<a href="'.$functions->make_url('topic.php', array('id' => $topicdata['id'])).'">'.$topicdata['topic_title'].'</a>';
					if ( $topicdata['status_sticky'] )
						$topic_name = $lang['Sticky'].': '.$topic_name;
					$last_post_author = ( $topicdata['last_poster_id'] > 0 ) ? '<a href="'.$functions->make_url('profile.php', array('id' => $topicdata['last_poster_id'])).'">'.$topicdata['last_poster_name'].'</a>' : $topicdata['last_poster_guest'];
					
					$template->parse('topiclist_topic', array(
						'topic_icon' => ( !$topicdata['status_locked'] ) ? $template->get_config('open_nonewposts_icon') : $template->get_config('closed_nonewposts_icon'),
						'topic_status' => ( !$topicdata['status_locked'] ) ? $lang['NoNewPosts'] : $lang['Locked'],
						'topic_name' => $topic_name,
						'author' => ( $topicdata['poster_id'] > 0 ) ? '<a href="'.$functions->make_url('profile.php', array('id' => $topicdata['poster_id'])).'">'.$topicdata['poster_name'].'</a>' : $topicdata['poster_guest'],
						'replies' => $topicdata['count_replies'],
						'views' => $topicdata['count_views'],
						'author_date' => sprintf($lang['AuthorDate'], $last_post_author, '<a href="'.$functions->make_url('topic.php', array('post' => $topicdata['last_post_id'])).'#post'.$topicdata['last_post_id'].'">'.$functions->make_date($topicdata['last_post_time']).'</a>'),
					));
					
				}
				
			}
			
			$template->parse('topiclist_footer', array(
				'forum_links' => $forum_links
			));
			
			$template->parse('location_bar', array(
				'location_bar' => $location_bar
			));
			
		} else {
			
			//
			// The user is not granted to view this forum
			//
			$functions->redir_to_login();
			
		}
		
	}
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
} else {
	
	//
	// There's no forum ID! Get us back to the index...
	//
	header('Location: '.$functions->make_url('index.php', array(), false));
	
}

?>
