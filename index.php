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
$session->update('index');

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

$template->set_page_title($lang['ForumIndex']);

//
// Parse the forums
//

if ( !$functions->get_stats('forums') ) {
	
	//
	// No forums have been found. Output this notice.
	//
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Note'],
		'content' => $lang['NoForums']
	));
	
} else {
	
	//
	// Define wich category we should show
	//
	$view_cat = ( !empty($_GET['cat']) && valid_int($_GET['cat']) ) ? $_GET['cat'] : 0;
	
	//
	// Get the forums and categories out of the database
	//
	if ( !($result = $db->query("SELECT f.id, f.name, f.descr, f.status, f.topics, f.posts, f.auth, c.id AS cat_id, c.name AS cat_name, t.topic_title, t.last_post_id, t.count_replies, p.poster_id, p.poster_guest, p.post_time, u.displayed_name AS poster_name, u.level AS poster_level FROM ( ( ( ".TABLE_PREFIX."forums f LEFT JOIN ".TABLE_PREFIX."topics t ON f.last_topic_id = t.id ) LEFT JOIN ".TABLE_PREFIX."posts p ON t.last_post_id = p.id ) LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id ), ".TABLE_PREFIX."cats c WHERE f.cat_id = c.id ORDER BY c.sort_id ASC, c.id ASC, f.sort_id ASC, f.id ASC")) )
		$functions->usebb_die('SQL', 'Unable to get forums and categories!', __FILE__, __LINE__);
	
	$categories = array();
	$forums = array();
	$forum_ids = array();
	while ( $forumdata = $db->fetch_result($result) ) {
		
		if ( $functions->auth($forumdata['auth'], 'view', $forumdata['id']) ) {
			
			if ( !array_key_exists($forumdata['cat_id'], $categories) )
				$categories[$forumdata['cat_id']] = 1;
			else
				$categories[$forumdata['cat_id']]++;
			$forums[] = $forumdata;
			$forum_ids[] = $forumdata['id'];
			
		}
		
	}
	
	if ( !count($forums) ) {
		
		//
		// There were no viewable forums.
		// Show this notice
		//
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Note'],
			'content' => $lang['NoViewableForums']
		));
		
	} elseif ( $functions->get_config('kick_user_to_only_viewable_forum') && count($forums) === 1 ) {
		
		//
		// If this is the only (viewable) forum and the forum has been set up
		// to kick the user to this only (viewable) forum ...
		//
		header('Location: '.$functions->get_config('board_url').$functions->make_url('forum.php', array('id' => $forums[0]['id']), false));
		exit();
		
	} else {
		
		//
		// Get all the moderators
		//
		if ( !($result = $db->query("SELECT m.forum_id, u.id, u.displayed_name, u.level FROM ".TABLE_PREFIX."moderators m, ".TABLE_PREFIX."members u WHERE m.forum_id IN(".join(', ', $forum_ids).") AND m.user_id = u.id ORDER BY u.displayed_name")) )
			$functions->usebb_die('SQL', 'Unable to get forums moderators!', __FILE__, __LINE__);
		$all_mods = $mods_per_forum = array();
		while ( $mods_data = $db->fetch_result($result) ) {
			
			if ( !array_key_exists($mods_data['forum_id'], $mods_per_forum) )
				$mods_per_forum[$mods_data['forum_id']] = 1;
			else
				$mods_per_forum[$mods_data['forum_id']]++;
			
			$all_mods[] = $mods_data;
			
		}
		foreach ( $forum_ids as $forum_id ) {
			
			if ( !array_key_exists($forum_id, $mods_per_forum) )
				$mods_per_forum[$forum_id] = 0;
			
		}
		
		$template->parse('forumlist_header', 'forumlist');
		
		$seen_forums = array();
		
		//
		// Loop through the forums
		//
		foreach ( $forums as $forumdata ) {
		
			//
			// Which forum in the current category is this?
			//
			if ( empty($seen_forums[$forumdata['cat_id']]) )
				$seen_forums[$forumdata['cat_id']] = 1;
			else
				$seen_forums[$forumdata['cat_id']]++;
			
			if ( $seen_forums[$forumdata['cat_id']] === 1 ) {
				
				//
				// If we didn't parse this category yet, than do it now
				//
				$template->parse('forumlist_cat_header', 'forumlist', array(
					'cat_name' => unhtml(stripslashes($forumdata['cat_name'])),
					'cat_url' => $functions->make_url('index.php', array('cat' => $forumdata['cat_id'])).'#cat'.$forumdata['cat_id']
				));
				
			}
			
			if ( $view_cat == 0 || $forumdata['cat_id'] == $view_cat ) {
				
				//
				// Output this forum if no category or the category
				// this forum is in has been chosen
				//
				
				if ( $forumdata['topics'] == 0 ) {
					
					$latest_post = $lang['NoPosts'];
					$author_date = '-';
					$by_author = '-';
					$on_date = '-';
					
				} else {
					
					$last_topic_title = ( $forumdata['count_replies'] ) ? $lang['Re'].' ' : '';
					$last_topic_title .= unhtml(stripslashes($forumdata['topic_title']));
					$author = ( $forumdata['poster_id'] ) ? $functions->make_profile_link($forumdata['poster_id'], $forumdata['poster_name'], $forumdata['poster_level']) : unhtml(stripslashes($forumdata['poster_guest']));
					
					$latest_post = '<a href="'.$functions->make_url('topic.php', array('post' => $forumdata['last_post_id'])).'#post'.$forumdata['last_post_id'].'">'.$last_topic_title.'</a>';
					$author_date = sprintf($lang['AuthorDate'], $author, $functions->make_date($forumdata['post_time']));
					$by_author = sprintf($lang['ByAuthor'], $author);
					$on_date = sprintf($lang['OnDate'], $functions->make_date($forumdata['post_time']));
					
				}
				
				list($forum_icon, $forum_status) = $functions->forum_topic_icon($forumdata['status'], 1, $forumdata['post_time'], $forumdata['poster_id'], 'forum', $forumdata['id']);
				
				$template->parse('forumlist_forum', 'forumlist', array(
					'forum_icon' => $forum_icon,
					'forum_status' => $forum_status,
					'forum_name' => '<a href="'.$functions->make_url('forum.php', array('id' => $forumdata['id'])).'">'.unhtml(stripslashes($forumdata['name'])).'</a>',
					'forum_descr' => stripslashes($forumdata['descr']),
					'forum_mods' => ( $mods_per_forum[$forumdata['id']] >= 1 ) ? sprintf($lang['ModeratorList'], $functions->get_mods_list($forumdata['id'], $all_mods)) : '',
					'total_topics' => $forumdata['topics'],
					'total_posts' => $forumdata['posts'],
					'latest_post' => $latest_post,
					'author_date' => $author_date,
					'by_author' => $by_author,
					'on_date' => $on_date
				));
				
			}
			
			if ( $seen_forums[$forumdata['cat_id']] === $categories[$forumdata['cat_id']] ) {
				
				//
				// If we didn't parse this category footer yet, than do it now
				//
				$template->parse('forumlist_cat_footer', 'forumlist', array(
					'cat_name' => '<a href="'.$functions->make_url('index.php', array('cat' => $forumdata['cat_id'])).'#cat'.$forumdata['cat_id'].'" name="cat'.$forumdata['cat_id'].'">'.unhtml(stripslashes($forumdata['cat_name'])).'</a>'
				));
				
			}
			
		}
		
		//
		// Parse the forumlist footer
		//
		$template->parse('forumlist_footer', 'forumlist');
		
	}
	
}

$functions->forum_stats_box();

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
