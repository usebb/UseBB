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
	$view_cat = ( !empty($_GET['cat']) && is_numeric($_GET['cat']) ) ? $_GET['cat'] : 0;
	
	//
	// Get the forums and categories out of the database
	//
	if ( !($result = $db->query("SELECT f.id, f.name, f.descr, f.status, f.topics, f.posts, f.auth, c.id AS cat_id, c.name AS cat_name, t.topic_title, t.last_post_id, t.count_replies, p.poster_id, p.poster_guest, p.post_time, u.name AS poster_name, u.level AS poster_level FROM ( ( ( ".TABLE_PREFIX."forums f LEFT JOIN ".TABLE_PREFIX."topics t ON f.last_topic_id = t.id ) LEFT JOIN ".TABLE_PREFIX."posts p ON t.last_post_id = p.id ) LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id ), ".TABLE_PREFIX."cats c WHERE f.cat_id = c.id ORDER BY c.sort_id ASC, c.id ASC, f.sort_id ASC, f.id ASC")) )
		$functions->usebb_die('SQL', 'Unable to get forums and categories!', __FILE__, __LINE__);
	
	//
	// This array holds a list of categories that have been parsed
	//
	$seen_cats = array();
	
	//
	// This will set TRUE when viewable forums have been found
	// Until now, it remains FALSE
	//
	$viewableforums = FALSE;
	
	//
	// Loop through the forums
	//
	while ( $forumdata = $db->fetch_result($result) ) {
		//
		// If this user can view this forum
		//
		if ( $functions->auth($forumdata['auth'], 'view', $forumdata['id']) ) {
			
			//
			// If this is the only (viewable) forum and the forum has been set up
			// to kick the user to this only (viewable) forum ...
			//
			if ( $functions->get_config('kick_user_to_only_viewable_forum') && intval($functions->get_stats('forums')) === 1 ) {
				
				header('Location: '.$functions->get_config('board_url').$functions->make_url('forum.php', array('id' => $forumdata['id']), false));
				exit();
				
			}
			
			if ( !isset($headershown) ) {
				
				$location_bar = '<a href="'.$functions->make_url('index.php').'">'.htmlentities($functions->get_config('board_name')).'</a>';
				$template->parse('location_bar', 'global', array(
					'location_bar' => $location_bar
				));
				
				//
				// A forumlist heading needs to be parsed because there is at least
				// one viewable forum. Parse it if this hasn't been done yet.
				//
				$template->parse('forumlist_header', 'forumlist', array(
					'forum' => $lang['Forum'],
					'topics' => $lang['Topics'],
					'posts' => $lang['Posts'],
					'latest_post' => $lang['LatestPost']
				));
				
				//
				// We have parsed the header. This doesn't need to be done again.
				//
				$headershown = TRUE;
				
			}
			
			if ( empty($seen_cats[$forumdata['cat_id']]) ) {
				
				//
				// If we didn't parse this category yet, than do it now
				//
				$template->parse('forumlist_cat_header', 'forumlist', array(
					'forum' => $lang['Forum'],
					'topics' => $lang['Topics'],
					'posts' => $lang['Posts'],
					'latest_post' => $lang['LatestPost'],
					'cat_name' => '<a href="'.$functions->make_url('index.php', array('cat' => $forumdata['cat_id'])).'#cat'.$forumdata['cat_id'].'" name="cat'.$forumdata['cat_id'].'">'.htmlentities(stripslashes($forumdata['cat_name'])).'</a>'
				));
				
			}
			
			if ( $view_cat == 0 || $forumdata['cat_id'] == $view_cat ) {
				
				//
				// Output this forum if no category or the category
				// this forum is in has been chosen
				//
				
				//
				// Get a list of forum moderators
				//
				if ( !($result2 = $db->query("SELECT u.id, u.name, u.level FROM ".TABLE_PREFIX."members u, ".TABLE_PREFIX."moderators m WHERE m.forum_id = ".$forumdata['id']." AND m.user_id = u.id ORDER BY u.name")) )
					$functions->usebb_die('SQL', 'Unable to get forum moderators list!', __FILE__, __LINE__);
				if ( !$db->num_rows($result2) ) {
					
					$forum_moderators = $lang['Nobody'];
					
				} else {
					
					$forum_moderators = array();
					
					while ( $modsdata = $db->fetch_result($result2) ) {
						
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
				
				if ( $forumdata['topics'] == 0 ) {
					
					$latest_post = $lang['NoPosts'];
					$author_date = '-';
					$by_author = '-';
					$on_date = '-';
					
				} else {
					
					$last_topic_title = ( $forumdata['count_replies'] ) ? $lang['Re'].' ' : '';
					#$last_topic_title .= htmlentities(stripslashes($forumdata['topic_title']));
					if ( strlen(stripslashes($forumdata['topic_title'])) > $template->get_config('forumlist_topic_rtrim_length') )
						$last_topic_title .= htmlentities(stripslashes(substr_replace($forumdata['topic_title'], $template->get_config('forumlist_topic_rtrim_completion'), $template->get_config('forumlist_topic_rtrim_length'))));
					else
						$last_topic_title .= htmlentities(stripslashes($forumdata['topic_title']));
					$author = ( $forumdata['poster_id'] ) ? $functions->make_profile_link($forumdata['poster_id'], $forumdata['poster_name'], $forumdata['poster_level']) : $forumdata['poster_guest'];
					
					$latest_post = '<a href="'.$functions->make_url('topic.php', array('post' => $forumdata['last_post_id'])).'#post'.$forumdata['last_post_id'].'">'.$last_topic_title.'</a>';
					$author_date = sprintf($lang['AuthorDate'], $author, $functions->make_date($forumdata['post_time']));
					$by_author = sprintf($lang['ByAuthor'], $author);
					$on_date = sprintf($lang['OnDate'], $functions->make_date($forumdata['post_time']));
					
				}
				
				$template->parse('forumlist_forum', 'forumlist', array(
					'forum_icon' => ( $forumdata['status'] ) ? $template->get_config('open_nonewposts_icon') : $template->get_config('closed_nonewposts_icon'),
					'forum_status' => ( $forumdata['status'] ) ? $lang['NoNewPosts'] : $lang['Locked'],
					'forum_name' => '<a href="'.$functions->make_url('forum.php', array('id' => $forumdata['id'])).'">'.htmlentities(stripslashes($forumdata['name'])).'</a>',
					'forum_descr' => stripslashes($forumdata['descr']),
					'forum_mods' => sprintf($lang['ModeratorsInThisForumSmall'], $forum_moderators),
					'total_topics' => $forumdata['topics'],
					'total_posts' => $forumdata['posts'],
					'latest_post' => $latest_post,
					'author_date' => $author_date,
					'by_author' => $by_author,
					'on_date' => $on_date
				));
				
			}
			
			if ( empty($seen_cats[$forumdata['cat_id']]) ) {
				
				//
				// If we didn't parse this category yet, than do it now
				//
				$template->parse('forumlist_cat_footer', 'forumlist', array(
					'forum' => $lang['Forum'],
					'topics' => $lang['Topics'],
					'posts' => $lang['Posts'],
					'latest_post' => $lang['LatestPost'],
					'cat_name' => '<a href="'.$functions->make_url('index.php', array('cat' => $forumdata['cat_id'])).'#cat'.$forumdata['cat_id'].'" name="cat'.$forumdata['cat_id'].'">'.htmlentities(stripslashes($forumdata['cat_name'])).'</a>'
				));
				
				//
				// *Now* we've seen this category... :)
				//
				$seen_cats[$forumdata['cat_id']] = TRUE;
				
			}
			
			//
			// There is at least one viewable forum, so set this TRUE.
			//
			$viewableforums = TRUE;
			
		}
		
	}
	
	if ( !$viewableforums ) {
		
		//
		// There were no viewable forums.
		// Show this notice
		//
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Note'],
			'content' => $lang['NoViewableForums']
		));
		
	} else {
		
		//
		// There were viewable forums,
		// so parse the forumlist footer
		//
		$template->parse('forumlist_footer', 'forumlist');
		
		$template->parse('location_bar', 'global', array(
			'location_bar' => $location_bar
		));
		
	}
	
}

$functions->forum_stats_box();

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
