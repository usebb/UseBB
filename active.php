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

$session->update('activetopics');
	
//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

if ( !$functions->get_stats('topics') ) {
	
	//
	// No active topics
	//
	$template->set_page_title($lang['Note']);
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Note'],
		'content' => $lang['NoActivetopics']
	));
	
} else {
	
	//
	// Get a list of forums
	//
	if ( !($result = $db->query("SELECT id, name, auth FROM ".TABLE_PREFIX."forums WHERE topics > 0")) )
		$functions->usebb_die('SQL', 'Unable to get forums information!', __FILE__, __LINE__);
	
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
	if ( !count($forum_ids) ) {
		
		//
		// No forums the user has access to
		//
		$template->set_page_title($lang['Note']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Note'],
			'content' => $lang['NoViewableForums']
		));
		
	} else {
		
		//
		// Parse the active topics list
		//
		
		$template->set_page_title($lang['ActiveTopics']);
		
		$template->parse('topiclist_header', 'activetopics', array(
			'active_topics' => '<a href="'.$functions->make_url('active.php').'">'.$lang['ActiveTopics'].'</a>',
			'topic' => $lang['Topic'],
			'forum' => $lang['Forum'],
			'author' => $lang['Author'],
			'replies' => $lang['Replies'],
			'views' => $lang['Views'],
			'latest_post' => $lang['LatestPost']
		));
		
		if ( !($result = $db->query("SELECT t.id, t.forum_id, t.topic_title, t.last_post_id, t.count_replies, t.count_views, t.status_locked, t.status_sticky, p.poster_guest, p2.poster_guest AS last_poster_guest, p2.post_time AS last_post_time, u.id AS poster_id, u.name AS poster_name, u.level AS poster_level, u2.id AS last_poster_id, u2.name AS last_poster_name, u2.level AS last_poster_level FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id, ".TABLE_PREFIX."posts p2 LEFT JOIN ".TABLE_PREFIX."members u2 ON p2.poster_id = u2.id WHERE t.forum_id IN(".join(', ', $forum_ids).") AND p.id = t.first_post_id AND p2.id = t.last_post_id ORDER BY p2.post_time DESC LIMIT ".$functions->get_config('topics_per_page'))) )
			$functions->usebb_die('SQL', 'Unable to get topic list!', __FILE__, __LINE__);
		
		while ( $topicdata = $db->fetch_result($result) ) {
			
			//
			// Loop through the topics, generating output...
			//
			$topic_name = '<a href="'.$functions->make_url('topic.php', array('id' => $topicdata['id'])).'">'.htmlspecialchars(stripslashes($topicdata['topic_title'])).'</a>';
			if ( $topicdata['status_sticky'] )
				$topic_name = $lang['Sticky'].': '.$topic_name;
			$last_post_author = ( $topicdata['last_poster_id'] > 0 ) ? $functions->make_profile_link($topicdata['last_poster_id'], $topicdata['last_poster_name'], $topicdata['last_poster_level']) : $topicdata['last_poster_guest'];
			
			list($topic_icon, $topic_status) = $functions->forum_topic_icon($topicdata['status_locked'], 0, $topicdata['last_post_time'] , $topicdata['last_poster_id'], 'topic', $topicdata['id']);
			
			//
			// Parse the topic template
			//
			$template->parse('topiclist_topic', 'activetopics', array(
				'topic_icon' => $topic_icon,
				'topic_status' => $topic_status,
				'topic_name' => $topic_name,
				'topic_page_links' => ( $topicdata['count_replies']+1 > $functions->get_config('posts_per_page') ) ? $functions->make_page_links(ceil(intval($topicdata['count_replies']+1) / $functions->get_config('posts_per_page')), '0', $topicdata['count_replies']+1, $functions->get_config('posts_per_page'), 'topic.php', $topicdata['id'], FALSE) : '',
				'forum' => '<a href="'.$functions->make_url('forum.php', array('id' => $topicdata['forum_id'])).'">'.htmlspecialchars(stripslashes($forum_names[$topicdata['forum_id']])).'</a>',
				'author' => ( $topicdata['poster_id'] > 0 ) ? $functions->make_profile_link($topicdata['poster_id'], $topicdata['poster_name'], $topicdata['poster_level']) : $topicdata['poster_guest'],
				'replies' => $topicdata['count_replies'],
				'views' => $topicdata['count_views'],
				'author_date' => sprintf($lang['AuthorDate'], $last_post_author, $functions->make_date($topicdata['last_post_time'])),
				'by_author' => sprintf($lang['ByAuthor'], $last_post_author),
				'on_date' => sprintf($lang['OnDate'], $functions->make_date($topicdata['last_post_time'])),
				'last_post_url' => $functions->make_url('topic.php', array('post' => $topicdata['last_post_id'])).'#post'.$topicdata['last_post_id']
			));
			
		}
		
		$template->parse('topiclist_footer', 'activetopics');
		
	}
	
}
	
//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
