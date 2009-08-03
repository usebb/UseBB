<?php
 
/*
	Copyright (C) 2003-2009 UseBB Team
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
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * RSS feed
 *
 * Parses an RSS 2.0 feed for the entire forum.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2009 UseBB Team
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
// Update and get the session information
//
$session->update('rss');

//
// Error page for the RSS feed
// Don't use the templated pages for RSS readers
//
function rss_error($num) {
	
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

//
// Make a Forbidden header when the RSS feed cannot be requested
//
if ( $session->sess_info['ip_banned'] || $functions->get_config('board_closed') || ( !$functions->get_config('guests_can_access_board') && $functions->get_user_level() == LEVEL_GUEST ) )
	rss_error(403);

$pubDate = $functions->make_date(time(), 'D, d M Y H:i:s', true, false).' GMT';

//
// Figure out what feed to show
//
if ( !empty($_GET['forum']) && valid_int($_GET['forum']) ) {
	
	//
	// Show a feed for a forum
	//
	
	if ( !$functions->get_config('enable_rss_per_forum') )
		rss_error(404);
	
	//
	// Get information about the forum
	//
	$result = $db->query("SELECT id, name, descr, auth FROM ".TABLE_PREFIX."forums WHERE id = ".$_GET['forum']);
	$forumdata = $db->fetch_result($result);
	
	if ( !$forumdata['id'] )
		rss_error(404);
	
	if ( !$functions->auth($forumdata['auth'], 'view', $_GET['forum']) )
		rss_error(403);
	
	//
	// Generate the items
	//
	
	$forum_name = unhtml(stripslashes($functions->get_config('board_name').': '.$forumdata['name']), true);
	$forum_link = $functions->get_config('board_url').$functions->make_url('forum.php', array('id' => $_GET['forum']), true, false);
	
	$header_vars = array(
		
		'board_name' => $forum_name,
		'board_descr' => strip_tags(stripslashes($forumdata['descr'])),
		'board_url' => $forum_link,
		'pubDate' => $pubDate,
		'link_rss' => $functions->get_config('board_url').$functions->make_url('rss.php', array('forum' => $_GET['forum']), true, false),
		
	);
	
	$template->parse('header', 'rss', $header_vars, true);
	
	$result = $db->query("SELECT t.id, t.topic_title, p.poster_id, p.poster_guest, p.content, p.post_time, p.enable_bbcode, p.enable_smilies, p.enable_html, m.displayed_name FROM ".TABLE_PREFIX."topics t LEFT JOIN ".TABLE_PREFIX."posts p ON t.first_post_id = p.id LEFT JOIN ".TABLE_PREFIX."members m ON p.poster_id = m.id WHERE t.forum_id = ".$_GET['forum']." ORDER BY p.post_time DESC LIMIT ".$functions->get_config('rss_items_count'));
	
	while ( $topicdata = $db->fetch_result($result) ) {
		
		$link = $functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $topicdata['id']), true, false);
		
		$template->parse('topic', 'rss', array(
			'title' => $topicdata['topic_title'],
			'description' => unhtml($functions->markup($functions->replace_badwords(stripslashes($topicdata['content'])), $topicdata['enable_bbcode'], $topicdata['enable_smilies'], $topicdata['enable_html'], true)),
			// <author> was renamed to <dc:creator> in the default template to keep validity.
			'author' => unhtml(stripslashes( ( !empty($topicdata['poster_id']) ) ? $topicdata['displayed_name'] : $topicdata['poster_guest']), true),
			'link' => $link,
			// <comments> was removed from the default template because it was used incorrectly (not for posting comments).
			'comments' => $functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $topicdata['id']), true, false),
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
	
	if ( !$functions->get_config('enable_rss_per_topic') )
		rss_error(404);
	
} else {
	
	//
	// Show a regular active topics feed
	//
	
	if ( !$functions->get_config('enable_rss') )
		rss_error(404);
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');
	
?>
