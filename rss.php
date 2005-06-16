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
// Don't use gzip for the feed
//
define('NO_GZIP', true);

//
// Include usebb engine
//
require(ROOT_PATH.'sources/common.php');

//
// Set the xml content type and only parse the xml templates
//
$template->content_type = 'text/xml';
$template->parse_special_templates_only = true;

//
// Update and get the session information
//
$session->update('rss');

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

//
// RSS feed pubDate in GMT
//
$header_vars = array('pubDate' => $functions->make_date(time(), 'D, d M Y H:i:s', true, false).' GMT');

$template->parse('header', 'rss', $header_vars, true);

if ( $functions->get_config('enable_rss') && $functions->get_stats('topics') ) {
	
	//
	// RSS is enabled and the forum contains topics, so proceed...
	//
	
	//
	// Excluded forums
	//
	$exclude_forums = $functions->get_config('exclude_forums_rss');
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
		
		//
		// There are viewable forums
		//
		
		$result = $db->query("SELECT t.id, t.topic_title, t.last_post_id, t.count_replies, t.forum_id, p.content, p.enable_bbcode, p.enable_html, p.post_time AS last_post_time, p.poster_id, p.poster_guest AS last_poster_guest, u.displayed_name AS last_poster_name FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id WHERE t.forum_id IN(".join(', ', $forum_ids).") AND p.id = t.last_post_id ORDER BY p.post_time DESC LIMIT ".$functions->get_config('rss_items_count'));
		
		while ( $topicdata = $db->fetch_result($result) ) {
			
			//
			// Parse the topic template
			//
			$template->parse('topic', 'rss', array(
				'title' => ( ( $topicdata['count_replies'] ) ? $lang['Re'].' ' : '' ) . unhtml($functions->replace_badwords(stripslashes($topicdata['topic_title']))),
				'description' => $functions->markup($functions->replace_badwords(stripslashes($topicdata['content'])), $topicdata['enable_bbcode'], false, $topicdata['enable_html']),
				'author' => ( !empty($topicdata['poster_id']) ) ? $topicdata['last_poster_name'] : $topicdata['last_poster_guest'],
				'link' => $functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $topicdata['id'])),
				'comments' => $functions->get_config('board_url').$functions->make_url('post.php', array('topic' => $topicdata['id'])),
				'category' => unhtml(stripslashes($forum_names[$topicdata['forum_id']])),
				'pubDate' => $functions->make_date($topicdata['last_post_time'], 'D, d M Y H:i:s', true, false).' GMT',
				'guid' => $functions->get_config('board_url').$functions->make_url('topic.php', array('post' => $topicdata['last_post_id'])).'#post'.$topicdata['last_post_id']
			), true);
			
		}
		
	}
	
}
	
$template->parse('footer', 'rss', $header_vars, true);

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');
	
?>
