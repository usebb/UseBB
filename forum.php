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
	
	if ( !($result = $db->query("SELECT f.name, f.auth, f.topics, c.id AS catid, c.name AS catname FROM ".TABLE_PREFIX."forums f, ".TABLE_PREFIX."cats c WHERE f.id = ".$_GET['id']." AND f.cat_id = c.id")) )
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
		
		if ( $functions->auth($forumdata['auth'], 'view') ) {
			
			$template->set_page_title(stripslashes($forumdata['name']));
			$template->parse('location_bar', array(
				'location_bar' => '<a href="'.$functions->make_url('index.php').'">'.$lang['Home'].'</a> '.$template->get_config('location_arrow').' <a href="'.$functions->make_url('index.php', array('cat' => $forumdata['catid'])).'">'.htmlentities(stripslashes($forumdata['catname'])).'</a> '.$template->get_config('location_arrow').' '.htmlentities(stripslashes($forumdata['name']))
			));
			
			if ( !$forumdata['topics'] ) {
				
				$template->parse('msgbox', array(
					'box_title' => $lang['Note'],
					'content' => $lang['NoTopics']
				));
				
			} else {
				
				if ( !($result = $db->query("SELECT t.id, t.topic_title, t.last_post_id, t.count_replies, t.count_views, t.status_locked, t.status_sticky, p.poster_guest, p2.poster_guest AS last_poster_guest, p.post_time, u.id AS poster_id, u.name AS poster_name, u2.id AS last_poster_id, u2.name AS last_poster_name FROM ".TABLE_PREFIX."topics t, ( ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."users u ON p.poster_id = u.id ), ( ".TABLE_PREFIX."posts p2 LEFT JOIN ".TABLE_PREFIX."users u2 ON p2.poster_id = u2.id ) WHERE p.id = t.first_post_id AND p2.id = t.last_post_id ORDER BY p2.post_time DESC")) )
					$functions->usebb_die('SQL', 'Unable to get topic list!', __FILE__, __LINE__);
				
				//
				// Output the topic list
				//
				$template->parse('topiclist_header', array(
					'topic' => $lang['Topic'],
					'author' => $lang['Author'],
					'replies' => $lang['Replies'],
					'views' => $lang['Views'],
					'latest_post' => $lang['LatestPost']
				));
				
				while ( $topicdata = $db->fetch_result($result) ) {
					
					print_r($topicdata);
					$template->parse('topiclist_topic');
					
				}
				
				$template->parse('topiclist_footer');
				
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
	header('Location: '.$functions->make_url('index.php', array(), false));
	
}

?>
