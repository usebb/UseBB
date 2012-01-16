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
 * RSS feed
 *
 * Parses RSS 2.0 feeds
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
// Don't use gzip for the feed
//
define('NO_GZIP', true);

//
// Include usebb engine
//
require(ROOT_PATH.'sources/common.php');

//
// Fetch the language array
//
$lang = $functions->fetch_language();

//
// Set the xml content type and only parse the xml templates
//
$template->content_type = 'application/rss+xml';
$template->parse_special_templates_only = true;

//
// Error page for the RSS feed
// Don't use the templated pages for RSS readers
//
function usebb_rss_error($num) {
	
	switch ( $num ) {
		case 403:
			header(HEADER_403);
			die('<h1>403 Forbidden</h1>');
			break;
		case 404:
			header(HEADER_404);
			die('<h1>404 Not Found</h1>');
	}
	
}

function usebb_check_rss_access() {
	
	global $session, $functions;

	//
	// Make a Forbidden header when the RSS feed cannot be requested
	//
	if ( $session->sess_info['ip_banned'] || $functions->get_config('board_closed') || ( !$functions->get_config('guests_can_access_board') && $functions->get_user_level() == LEVEL_GUEST ) )
		usebb_rss_error(403);

}

$pubDate = $functions->make_date(time(), 'D, d M Y H:i:s', true, false).' GMT';

//
// Absolute template gfx directory
//
$template->add_global_vars(array('img_dir' => $functions->get_config('board_url').str_replace('./', '', ROOT_PATH).'templates/'.$functions->get_config('template').'/gfx/'));

//
// Figure out what feed to show
//
if ( !empty($_GET['forum']) && valid_int($_GET['forum']) ) {
	
	//
	// Show a feed for a forum
	//
	$session->update('rss-forum:'.$_GET['forum']);

	usebb_check_rss_access();
	
	if ( !$functions->get_config('enable_rss_per_forum') )
		usebb_rss_error(404);
	
	//
	// Get information about the forum
	//
	$result = $db->query("SELECT id, name, descr, auth FROM ".TABLE_PREFIX."forums WHERE id = ".$_GET['forum']);
	$forumdata = $db->fetch_result($result);
	
	//
	// Forum does not exist
	//
	if ( !$forumdata['id'] )
		usebb_rss_error(404);
	
	//
	// Forum is not accessible
	//
	if ( !$functions->auth($forumdata['auth'], 'view', $_GET['forum']) )
		usebb_rss_error(403);
	
	$forum_name = unhtml(stripslashes($forumdata['name']), true);
	$forum_link = $functions->get_config('board_url').$functions->make_url('forum.php', array('id' => $_GET['forum']), true, false);
	
	$header_vars = array(
		
		'board_name' => unhtml($functions->get_config('board_name').': '.stripslashes($forumdata['name']), true),
		// Stripping tags, Firefox doesn't show the description when it has tags.
		'board_descr' => named_entities_to_numeric(strip_tags(stripslashes($forumdata['descr']))),
		'board_url' => $forum_link,
		'pubDate' => $pubDate,
		'link_rss' => $functions->get_config('board_url').$functions->make_url('rss.php', array('forum' => $_GET['forum']), true, false),
		
	);
	
	$template->parse('header', 'rss', $header_vars, true);
	
	//
	// Get the topics
	//

	if ( $functions->auth($forumdata['auth'], 'read', $_GET['forum']) ) {
		
		$can_read = true;
		$add_to_query = array("p.content", "p.enable_bbcode", "p.enable_smilies", "p.enable_html", "m.level AS poster_level", "m.active");
		
	} else {
		
		$can_read = false;
		$add_to_query = array();
		
	}

	$add_to_query = count($add_to_query) ? ', '.implode(', ', $add_to_query) : '';
	
	$result = $db->query("SELECT t.id, t.topic_title, p.poster_id, p.poster_guest, p.post_time, m.displayed_name".$add_to_query." FROM ".TABLE_PREFIX."topics t LEFT JOIN ".TABLE_PREFIX."posts p ON t.first_post_id = p.id LEFT JOIN ".TABLE_PREFIX."members m ON p.poster_id = m.id WHERE t.forum_id = ".$_GET['forum']." ORDER BY p.post_time DESC LIMIT ".$functions->get_config('rss_items_count'));
	
	while ( $topicdata = $db->fetch_result($result) ) {
		
		$link = $functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $topicdata['id']), true, false);
		$can_post_links = $can_read && $functions->antispam_can_post_links($topicdata);
		
		$template->parse('topic', 'rss', array(
			'title' => unhtml($functions->replace_badwords(stripslashes($topicdata['topic_title'])), true),
			'description' => $can_read ? $functions->markup($functions->replace_badwords(stripslashes($topicdata['content'])), $topicdata['enable_bbcode'], $topicdata['enable_smilies'], $topicdata['enable_html'], true, $can_post_links) : '',
			// <author> was renamed to <dc:creator> in the default template to keep validity.
			'author' => unhtml(stripslashes( ( !empty($topicdata['poster_id']) ) ? $topicdata['displayed_name'] : $topicdata['poster_guest']), true),
			'link' => $link,
			// <comments> was removed from the default template because it was used incorrectly (not for posting comments).
			'comments' => $link,
			'category' => $forum_name,
			'category_domain' => $forum_link,
			'pubDate' => $functions->make_date($topicdata['post_time'], 'D, d M Y H:i:s', true, false).' GMT',
			'guid' => $link
		), true);
		
	}
	
	$template->parse('footer', 'rss', $header_vars, true);
	
} elseif ( !empty($_GET['topic']) && valid_int($_GET['topic']) ) {
	
	//
	// Show a feed for a topic
	//
	$session->update('rss-topic:'.$_GET['topic']);

	usebb_check_rss_access();
	
	if ( !$functions->get_config('enable_rss_per_topic') )
		usebb_rss_error(404);
	
	//
	// Get information about the topic and forum
	//
	$result = $db->query("SELECT t.id, t.forum_id, t.topic_title, t.first_post_id, f.name AS forum_name, f.auth FROM ".TABLE_PREFIX."forums f, ".TABLE_PREFIX."topics t WHERE f.id = t.forum_id AND t.id = ".$_GET['topic']);
	$topicdata = $db->fetch_result($result);
	
	//
	// Topic does not exist
	//
	if ( !$topicdata['id'] )
		usebb_rss_error(404);
	
	//
	// Topic is not accessible
	//
	if ( !$functions->auth($topicdata['auth'], 'read', $topicdata['forum_id']) )
		usebb_rss_error(403);
	
	$topic_name = unhtml(stripslashes($topicdata['topic_title']), true);
	$topic_link = $functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic']), true, false);
	
	$header_vars = array(
		
		'board_name' => unhtml($functions->get_config('board_name'), true).': '.unhtml(stripslashes($topicdata['topic_title']), true),
		'board_descr' => '',
		'board_url' => $topic_link,
		'pubDate' => $pubDate,
		'link_rss' => $functions->get_config('board_url').$functions->make_url('rss.php', array('topic' => $_GET['topic']), true, false),
		
	);
	
	$template->parse('header', 'rss', $header_vars, true);
	
	//
	// Get the posts
	//
	
	$result = $db->query("SELECT p.id, p.poster_id, p.poster_guest, p.content, p.post_time, p.enable_bbcode, p.enable_smilies, p.enable_html, m.displayed_name, m.level AS poster_level, m.active FROM ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members m ON p.poster_id = m.id WHERE p.topic_id = ".$_GET['topic']." ORDER BY p.post_time DESC LIMIT ".$functions->get_config('rss_items_count'));
	
	while ( $postdata = $db->fetch_result($result) ) {
		
		$title = unhtml($functions->replace_badwords(stripslashes($topicdata['topic_title'])), true);
		$title = ( ( $postdata['id'] != $topicdata['first_post_id'] ) ? $lang['Re'].' ' : '' ) . $title;

		$link = $functions->get_config('board_url').$functions->make_url('topic.php', array('post' => $postdata['id']), true, false).'#post'.$postdata['id'];
		$can_post_links = $functions->antispam_can_post_links($postdata);
		
		$template->parse('topic', 'rss', array(
			'title' => $title,
			'description' => $functions->markup($functions->replace_badwords(stripslashes($postdata['content'])), $postdata['enable_bbcode'], $postdata['enable_smilies'], $postdata['enable_html'], true, $can_post_links),
			// <author> was renamed to <dc:creator> in the default template to keep validity.
			'author' => unhtml(stripslashes( ( !empty($postdata['poster_id']) ) ? $postdata['displayed_name'] : $postdata['poster_guest']), true),
			'link' => $link,
			// <comments> was removed from the default template because it was used incorrectly (not for posting comments).
			'comments' => $link,
			'category' => $topic_name,
			'category_domain' => $topic_link,
			'pubDate' => $functions->make_date($postdata['post_time'], 'D, d M Y H:i:s', true, false).' GMT',
			'guid' => $link
		), true);

	}
	
	$template->parse('footer', 'rss', $header_vars, true);
	
} else {
	
	//
	// Show a regular active topics feed
	//
	$session->update('rss');

	usebb_check_rss_access();
	
	if ( !$functions->get_config('enable_rss') )
		usebb_rss_error(404);
	
	//
	// Excluded forums
	//
	$exclude_forums = $functions->get_config('exclude_forums_rss');
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
	// No viewable forums
	//
	if ( !count($forum_ids) )
		usebb_rss_error(403);
	
	$header_vars = array(
		
		'board_name' => unhtml($functions->get_config('board_name'), true),
		'board_descr' => unhtml($functions->get_config('board_descr'), true),
		'board_url' => $functions->get_config('board_url'),
		'pubDate' => $pubDate,
		'link_rss' => $functions->get_config('board_url').$functions->make_url('rss.php', null, true, false),
		
	);
	
	$template->parse('header', 'rss', $header_vars, true);
	
	$result = $db->query("SELECT p.id AS post_id, p.topic_id, t.forum_id, t.topic_title, t.count_replies, p.content, p.enable_bbcode, p.enable_smilies, p.enable_html, p.poster_id, m.displayed_name AS last_poster_name, m.level AS poster_level, m.active, p.poster_guest AS last_poster_guest, p.post_time FROM ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members m ON p.poster_id = m.id, ".TABLE_PREFIX."topics t WHERE t.forum_id IN(".join(', ', $forum_ids).") AND t.id = p.topic_id ORDER BY p.post_time DESC LIMIT ".$functions->get_config('rss_items_count'));
			
	$reply_counts = array();
	
	while ( $topicdata = $db->fetch_result($result) ) {
					
		if ( !array_key_exists($topicdata['topic_id'], $reply_counts) )
			$reply_counts[$topicdata['topic_id']] = $topicdata['count_replies'];
		else
			$reply_counts[$topicdata['topic_id']]--;
		
		$title = unhtml($functions->replace_badwords(stripslashes($topicdata['topic_title'])), true);
		if ( $reply_counts[$topicdata['topic_id']] )
			$title = $lang['Re'].' '.$title;
		
		$link = $functions->get_config('board_url').$functions->make_url('topic.php', array('post' => $topicdata['post_id']), true, false).'#post'.$topicdata['post_id'];
		$can_post_links = $functions->antispam_can_post_links($topicdata);
		
		//
		// Parse the topic template
		//
		$template->parse('topic', 'rss', array(
			'title' => $title,
			'description' => $functions->markup($functions->replace_badwords(stripslashes($topicdata['content'])), $topicdata['enable_bbcode'], $topicdata['enable_smilies'], $topicdata['enable_html'], true, $can_post_links),
			// <author> was renamed to <dc:creator> in the default template to keep validity.
			'author' => unhtml(stripslashes( ( !empty($topicdata['poster_id']) ) ? $topicdata['last_poster_name'] : $topicdata['last_poster_guest']), true),
			'link' => $link,
			// <comments> was removed from the default template because it was used incorrectly (not for posting comments).
			'comments' => $functions->get_config('board_url').$functions->make_url('post.php', array('topic' => $topicdata['topic_id'], 'quotepost' => $topicdata['post_id']), true, false),
			'category' => unhtml(stripslashes($forum_names[$topicdata['forum_id']]), true),
			'category_domain' => $functions->get_config('board_url').$functions->make_url('forum.php', array('id' => $topicdata['forum_id']), true, false),
			'pubDate' => $functions->make_date($topicdata['post_time'], 'D, d M Y H:i:s', true, false).' GMT',
			'guid' => $link
		), true);
		
	}
	
	$template->parse('footer', 'rss', $header_vars, true);
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');
	
?>
