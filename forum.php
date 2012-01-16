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
 * Forum view
 *
 * Parses a forum and topic list.
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
// If an ID has been passed
//
if ( !empty($_GET['id']) && valid_int($_GET['id']) ) {	
	
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
	$result = $db->query("SELECT f.id, f.name, f.auth, f.topics, f.status, f.hide_mods_list, c.id AS cat_id, c.name AS cat_name FROM ".TABLE_PREFIX."forums f, ".TABLE_PREFIX."cats c WHERE f.id = ".$_GET['id']." AND f.cat_id = c.id");
	$forumdata = $db->fetch_result($result);
	
	if ( !$forumdata['id'] ) {
		
		//
		// This forum does not exist, show an error
		//
		header(HEADER_404);
		$template->add_breadcrumb($lang['Error']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchForum'], 'ID '.$_GET['id'])
		));
		
	} else {	
		
		if ( $functions->auth($forumdata['auth'], 'view', $_GET['id']) ) {
			
			//
			// User is allowed to view this forum
			//
			
			$template->add_breadcrumb(unhtml(stripslashes($forumdata['name'])));
			
			$forum_moderators = $functions->get_mods_list($_GET['id']);
			
			$new_topic_link = (
				(
					$functions->auth($forumdata['auth'], 'post', $_GET['id'])
					// True if is guest but members can post. Will redirect to login.
					|| ( $functions->get_config('show_posting_links_to_guests') && !$session->sess_info['user_id'] && $functions->auth($forumdata['auth'], 'post', $_GET['id'], FALSE, array('id' => -1, 'level' => LEVEL_MEMBER)) )
				)
				&& ( $forumdata['status'] || $functions->get_user_level() == LEVEL_ADMIN )
			) ? '<a href="'.$functions->make_url('post.php', array('forum' => $_GET['id'])).'" rel="nofollow">'.$lang['PostNewTopic'].'</a>' : '';
			
			//
			// Get page number
			//
			$numpages = ceil(intval($forumdata['topics']) / $functions->get_config('topics_per_page'));
			$page = ( !empty($_GET['page']) && valid_int($_GET['page']) && intval($_GET['page']) > 0 && intval($_GET['page']) <= $numpages ) ? intval($_GET['page']) : 1;
			$limit_start = ( $page - 1 ) * $functions->get_config('topics_per_page');
			$limit_end = $functions->get_config('topics_per_page');
			$page_links = $functions->make_page_links($numpages, $page, $forumdata['topics'], $functions->get_config('topics_per_page'), 'forum.php', $_GET['id']);
			
			//
			// Output the topic list
			//
			$template->parse('header', 'topiclist', array(
				'forum_name' => '<a href="'.$functions->make_url('forum.php', array('id' => $_GET['id'])).'">'.unhtml(stripslashes($forumdata['name'])).'</a>',
				'forum_moderators' => ( !$forumdata['hide_mods_list'] && $forum_moderators != $lang['Nobody'] ) ? sprintf($lang['ModeratorList'], $forum_moderators) : '',
				'new_topic_link' => $new_topic_link,
				'page_links' => $page_links
			));
			
			if ( $forumdata['topics'] ) {
				
				//
				// Get the topic list information in one query
				//
				$result = $db->query("SELECT t.id, t.topic_title, t.last_post_id, t.count_replies, t.count_views, t.status_locked, t.status_sticky, p.poster_guest, p2.poster_guest AS last_poster_guest, p2.post_time AS last_post_time, u.id AS poster_id, u.displayed_name AS poster_name, u.level AS poster_level, u2.id AS last_poster_id, u2.displayed_name AS last_poster_name, u2.level AS last_poster_level FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id, ".TABLE_PREFIX."posts p2 LEFT JOIN ".TABLE_PREFIX."members u2 ON p2.poster_id = u2.id WHERE t.forum_id = ".$_GET['id']." AND p.id = t.first_post_id AND p2.id = t.last_post_id ORDER BY t.status_sticky DESC, p2.post_time DESC LIMIT ".$limit_start.", ".$limit_end);
				
				while ( $topicdata = $db->fetch_result($result) ) {
					
					//
					// Loop through the topics, generating output...
					//
					$topic_name = '<a href="'.$functions->make_url('topic.php', array('id' => $topicdata['id'])).'">'.unhtml($functions->replace_badwords(stripslashes($topicdata['topic_title']))).'</a>';
					if ( $topicdata['status_sticky'] )
						$topic_name = $lang['Sticky'].': '.$topic_name;
					$last_post_author = ( $topicdata['last_poster_id'] > LEVEL_GUEST ) ? $functions->make_profile_link($topicdata['last_poster_id'], $topicdata['last_poster_name'], $topicdata['last_poster_level']) : unhtml(stripslashes($topicdata['last_poster_guest']));
					
					list($topic_icon, $topic_status) = $functions->topic_icon($topicdata['id'], $topicdata['status_locked'], $topicdata['last_post_time']);
					
					if ( $topic_status == $lang['NewPosts'] || $topic_status == $lang['LockedNewPosts'] ) {
						
						$topic_name = sprintf($template->get_config('newpost_link_format'), $functions->make_url('topic.php', array('id' => $topicdata['id'], 'act' => 'getnewpost')).'#newpost', 'templates/'.$functions->get_config('template').'/gfx/'.$template->get_config('newpost_link_icon'), $topic_status) . $topic_name;
						
					}
					
					//
					// Parse the topic template
					//
					$template->parse('topic', 'topiclist', array(
						'topic_icon' => $topic_icon,
						'topic_status' => $topic_status,
						'topic_name' => $topic_name,
						'topic_page_links' => ( $topicdata['count_replies']+1 > $functions->get_config('posts_per_page') ) ? $functions->make_page_links(ceil(intval($topicdata['count_replies']+1) / $functions->get_config('posts_per_page')), '0', $topicdata['count_replies']+1, $functions->get_config('posts_per_page'), 'topic.php', $topicdata['id'], false) : '',
						'author' => ( $topicdata['poster_id'] > LEVEL_GUEST ) ? $functions->make_profile_link($topicdata['poster_id'], $topicdata['poster_name'], $topicdata['poster_level']) : unhtml(stripslashes($topicdata['poster_guest'])),
						'replies' => $topicdata['count_replies'],
						'views' => $topicdata['count_views'],
						'author_date' => sprintf($lang['AuthorDate'], $last_post_author, $functions->make_date($topicdata['last_post_time'])),
						'by_author' => sprintf($lang['ByAuthor'], $last_post_author),
						'on_date' => sprintf($lang['OnDate'], $functions->make_date($topicdata['last_post_time'])),
						'lp_author' => $last_post_author,
						'lp_date' => $functions->make_date($topicdata['last_post_time']),
						'last_post_url' => $functions->make_url('topic.php', array('post' => $topicdata['last_post_id'])).'#post'.$topicdata['last_post_id']
					));
					
				}
				
			} else {
				
				//
				// There are no topics yet...
				//
				$template->parse('notopics', 'topiclist');
				
			}
			
			//
			// Topiclist footer
			//
			
			$template->parse('footer', 'topiclist', array(
				'forum_name' => '<a href="'.$functions->make_url('forum.php', array('id' => $_GET['id'])).'">'.unhtml(stripslashes($forumdata['name'])).'</a>',
				'forum_moderators' => ( !$forumdata['hide_mods_list'] && $forum_moderators != $lang['Nobody'] ) ? sprintf($lang['ModeratorList'], $forum_moderators) : '',
				'new_topic_link' => $new_topic_link,
				'page_links' => $page_links
			));
			
			//
			// If this is the only (viewable) forum and the forum has been set up
			// to kick the user to this only (viewable) forum ...
			//
			if ( $functions->get_config('single_forum_mode') && $functions->get_stats('viewable_forums') === 1 )
				$functions->forum_stats_box();
			
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
	$functions->redirect('index.php');
	
}

?>
