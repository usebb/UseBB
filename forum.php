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
	
	//
	// Get information about the forum
	//
	if ( !($result = $db->query("SELECT f.name, f.auth, f.topics, f.status, c.id AS cat_id, c.name AS cat_name FROM ".TABLE_PREFIX."forums f, ".TABLE_PREFIX."cats c WHERE f.id = ".$_GET['id']." AND f.cat_id = c.id")) )
		$functions->usebb_die('SQL', 'Unable to get forum information!', __FILE__, __LINE__);
	
	if ( !$db->num_rows($result) ) {
		
		//
		// This forum does not exist, show an error
		//
		$template->set_page_title($lang['Error']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchForum'], 'ID '.$_GET['id'])
		));
		
	} else {	
		
		$forumdata = $db->fetch_result($result);
		
		if ( $functions->auth($forumdata['auth'], 'view', $_GET['id']) ) {
			
			//
			// User is allowed to view this forum
			//
			
			$template->set_page_title(stripslashes($forumdata['name']));
			
			$location_bar = '<a href="'.$functions->make_url('index.php').'">'.htmlentities($functions->get_config('board_name')).'</a> '.$template->get_config('locationbar_item_delimiter').' '.htmlentities(stripslashes($forumdata['name']));
			$template->parse('location_bar', 'global', array(
				'location_bar' => $location_bar
			));
			
			//
			// Get a list of forum moderators
			//
			if ( !($result = $db->query("SELECT u.id, u.name, u.level FROM ".TABLE_PREFIX."members u, ".TABLE_PREFIX."moderators m WHERE m.forum_id = ".$_GET['id']." AND m.user_id = u.id ORDER BY u.name")) )
				$functions->usebb_die('SQL', 'Unable to get forum moderators list!', __FILE__, __LINE__);
			if ( !$db->num_rows($result) ) {
				
				$forum_moderators = $lang['Nobody'];
				
			} else {
				
				$forum_moderators = array();
				
				while ( $modsdata = $db->fetch_result($result) ) {
					
					//
					// Array containing links to moderators
					//
					$forum_moderators[] = $functions->make_profile_link($modsdata['id'], $modsdata['name'], $modsdata['level']);
					
				}
				
				//
				// Join all values in the array
				//
				$forum_moderators = join(', ', $forum_moderators);
				
			}
			
			$new_topic_link = ( $functions->auth($forumdata['auth'], 'post', $_GET['id']) && ( $forumdata['status'] || $functions->get_user_level() == 3 ) ) ? '<a href="'.$functions->make_url('post.php', array('forum' => $_GET['id'])).'"><img src="templates/'.$functions->get_config('template').'/gfx/'.$functions->get_config('language').'/'.$template->get_config('new_topic_button').'" alt="'.$lang['PostNewTopic'].'" /></a>' : '';
			
			//
			// Get page number
			//
			$numpages = ceil(intval($forumdata['topics']) / $functions->get_config('topics_per_page'));
			$page = ( !empty($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) <= $numpages ) ? intval($_GET['page']) : 1;
			$limit_start = ( $page - 1 ) * $functions->get_config('topics_per_page');
			$limit_end = $functions->get_config('topics_per_page');
			
			if ( intval($forumdata['topics']) > $functions->get_config('topics_per_page') ) {
				
				$page_links = array();
					
				if ( $page > 1 )
					$page_links[] = '<a href="'.$functions->make_url('forum.php', array('id' => $_GET['id'], 'page' => $page-1)).'">&laquo;</a>';
				
				for ( $i = 1; $i <= $numpages; $i++ ) {
					
					if ( $page != $i )
						$page_links[] = '<a href="'.$functions->make_url('forum.php', array('id' => $_GET['id'], 'page' => $i)).'">'.$i.'</a>';
					else
						$page_links[] = $i;
					
				}
					
				if ( $page < $numpages )
					$page_links[] = '<a href="'.$functions->make_url('forum.php', array('id' => $_GET['id'], 'page' => $page+1)).'">&raquo;</a>';
				
				$page_links = sprintf($lang['PageLinks'], join(', ',$page_links));
				
			} else {
				
				$page_links = sprintf($lang['PageLinks'], '1');
				
			}
			
			//
			// Output the topic list
			//
			$template->parse('topiclist_header', 'topiclist', array(
				'forum_name' => '<a href="'.$functions->make_url('forum.php', array('id' => $_GET['id'])).'">'.htmlentities(stripslashes($forumdata['name'])).'</a>',
				'forum_moderators' => sprintf($lang['ModeratorsInThisForumLarge'], $forum_moderators),
				'new_topic_link' => $new_topic_link,
				'page_links' => $page_links,
				'topic' => $lang['Topic'],
				'author' => $lang['Author'],
				'replies' => $lang['Replies'],
				'views' => $lang['Views'],
				'latest_post' => $lang['LatestPost']
			));
			
			if ( $forumdata['topics'] ) {
				
				//
				// Get the topic list information in one query
				//
				if ( !($result = $db->query("SELECT t.id, t.topic_title, t.last_post_id, t.count_replies, t.count_views, t.status_locked, t.status_sticky, p.poster_guest, p2.poster_guest AS last_poster_guest, p2.post_time AS last_post_time, u.id AS poster_id, u.name AS poster_name, u.level AS poster_level, u2.id AS last_poster_id, u2.name AS last_poster_name, u2.level AS last_poster_level FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id, ".TABLE_PREFIX."posts p2 LEFT JOIN ".TABLE_PREFIX."members u2 ON p2.poster_id = u2.id WHERE t.forum_id = ".$_GET['id']." AND p.id = t.first_post_id AND p2.id = t.last_post_id ORDER BY t.status_sticky DESC, p2.post_time DESC LIMIT ".$limit_start.", ".$limit_end)) )
					$functions->usebb_die('SQL', 'Unable to get topic list!', __FILE__, __LINE__);
				
				while ( $topicdata = $db->fetch_result($result) ) {
					
					//
					// Loop through the topics, generating output...
					//
					$topic_name = '<a href="'.$functions->make_url('topic.php', array('id' => $topicdata['id'])).'">'.htmlentities(stripslashes($topicdata['topic_title'])).'</a>';
					if ( $topicdata['status_sticky'] )
						$topic_name = $lang['Sticky'].': '.$topic_name;
					$last_post_author = ( $topicdata['last_poster_id'] > 0 ) ? $functions->make_profile_link($topicdata['last_poster_id'], $topicdata['last_poster_name'], $topicdata['last_poster_level']) : $topicdata['last_poster_guest'];
					
					//
					// Parse the topic template
					//
					$template->parse('topiclist_topic', 'topiclist', array(
						'topic_icon' => ( !$topicdata['status_locked'] ) ? $template->get_config('open_nonewposts_icon') : $template->get_config('closed_nonewposts_icon'),
						'topic_status' => ( !$topicdata['status_locked'] ) ? $lang['NoNewPosts'] : $lang['Locked'],
						'topic_name' => $topic_name,
						'author' => ( $topicdata['poster_id'] > 0 ) ? $functions->make_profile_link($topicdata['poster_id'], $topicdata['poster_name'], $topicdata['poster_level']) : $topicdata['poster_guest'],
						'replies' => $topicdata['count_replies'],
						'views' => $topicdata['count_views'],
						'author_date' => sprintf($lang['AuthorDate'], $last_post_author, $functions->make_date($topicdata['last_post_time'])),
						'by_author' => sprintf($lang['ByAuthor'], $last_post_author),
						'on_date' => sprintf($lang['OnDate'], $functions->make_date($topicdata['last_post_time'])),
						'last_post_url' => $functions->make_url('topic.php', array('post' => $topicdata['last_post_id'])).'#post'.$topicdata['last_post_id']
					));
					
				}
				
			} else {
				
				//
				// There are no topics yet...
				//
				$template->parse('topiclist_notopics', 'topiclist', array(
					'notopics' => $lang['NoTopics']
				));
				
			}
			
			//
			// Topiclist footer and location bar
			//
			
			$template->parse('topiclist_footer', 'topiclist', array(
				'new_topic_link' => $new_topic_link,
				'page_links' => $page_links,
				'forum_moderators' => sprintf($lang['ModeratorsInThisForumLarge'], $forum_moderators)
			));
				
			$template->parse('location_bar', 'global', array(
				'location_bar' => $location_bar
			));
			
			//
			// If this is the only (viewable) forum and the forum has been set up
			// to kick the user to this only (viewable) forum ...
			//
			if ( $functions->get_config('kick_user_to_only_viewable_forum') && intval($functions->get_stats('forums')) === 1 ) {
				
				$functions->forum_stats_box();
				
			}
			
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
	header('Location: '.$functions->get_config('board_url').$functions->make_url('index.php', array(), false));
	
}

?>
