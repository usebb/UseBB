<?php

/*
	Copyright (C) 2003-2004 UseBB Team
	http://usebb.sourceforge.net
	
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

//
// Define wich category we should show
//
$view_cat = ( !empty($_GET['cat']) && is_numeric($_GET['cat']) ) ? $_GET['cat'] : 0;

//
// Get the forums and categories out of the database
//
if ( !($result = $db->query("SELECT f.id, f.name, f.descr, f.status, f.topics, f.posts, f.auth, c.id AS cat_id, c.name AS cat_name, t.topic_title, t.last_post_id, t.count_replies, p.poster_id, p.poster_guest, p.post_time, u.name AS poster_name FROM ( ( ( ".TABLE_PREFIX."forums f LEFT JOIN ".TABLE_PREFIX."topics t ON f.last_topic_id = t.id ) LEFT JOIN ".TABLE_PREFIX."posts p ON t.last_post_id = p.id ) LEFT JOIN ".TABLE_PREFIX."users u ON p.poster_id = u.id ), ".TABLE_PREFIX."cats c WHERE f.cat_id = c.id ORDER BY c.sort_id ASC, c.id ASC, f.sort_id ASC, f.id ASC")) )
	$functions->usebb_die('SQL', 'Unable to get forums and categories!', __FILE__, __LINE__);

//
// Set empty stats
//
$stats = array('topics' => 0, 'posts' => 0);

if ( $db->num_rows($result) > 0 ) {
	
	//
	// There are forums, so parse them...
	//
	
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
		
		if ( $functions->auth($forumdata['auth'], 'view') ) {
			
			//
			// If this user can view this forum
			//
			if ( !isset($headershown) ) {
				
				//
				// A forumlist heading needs to be parsed because there is at least
				// one viewable forum. Parse it if this hasn't been done yet.
				//
				$template->parse('forumlist_header', array(
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
				$template->parse('forumlist_cat', array(
					'forum' => $lang['Forum'],
					'topics' => $lang['Topics'],
					'posts' => $lang['Posts'],
					'latest_post' => $lang['LatestPost'],
					'cat_name' => '<a href="'.$functions->make_url('index.php', array('cat' => $forumdata['cat_id'])).'">'.htmlentities(stripslashes($forumdata['cat_name'])).'</a>'
				));
				//
				// *Now* we've seen this category... :)
				//
				$seen_cats[$forumdata['cat_id']] = TRUE;
				
			}
			
			if ( $view_cat == 0 || $forumdata['cat_id'] == $view_cat ) {
				
				//
				// Output this forum if no category or the category
				// this forum is in has been chosen
				//
				
				if ( $forumdata['topics'] == 0 ) {
					
					$latest_post = $lang['NoPosts'];
					$author_date = '-';
					
				} else {
					
					$last_topic_title  = ( $forumdata['count_replies'] > 0 ) ? $lang['Re'].' ' : '';
					$last_topic_title .= htmlentities(stripslashes($forumdata['topic_title']));
					$author = ( $forumdata['poster_id'] > 0 ) ? '<a href="'.$functions->make_url('profile.php', array('id' => $forumdata['poster_id'])).'">'.$forumdata['poster_name'].'</a>' : $forumdata['poster_guest'];
					
					$latest_post = '<a href="'.$functions->make_url('topic.php', array('p' => $forumdata['last_post_id'])).'#'.$forumdata['last_post_id'].'">'.$last_topic_title.'</a>';
					$author_date = sprintf($lang['AuthorDate'], $author, $functions->make_date($profiledata['post_time']));
					
				}
				
				$template->parse('forumlist_forum', array(
					'forum_icon' => ( $forumdata['status'] ) ? 'nonewposts.gif' : 'locked.gif',
					'forum_status' => ( $forumdata['status'] ) ? $lang['NoNewPosts'] : $lang['Locked'],
					'forum_name' => '<a href="'.$functions->make_url('forum.php', array('id' => $forumdata['id'])).'">'.htmlentities(stripslashes($forumdata['name'])).'</a>',
					'forum_descr' => stripslashes($forumdata['descr']),
					'total_topics' => $forumdata['topics'],
					'total_posts' => $forumdata['posts'],
					'latest_post' => $latest_post,
					'author_date' => $author_date
				));
				
			}
			
			//
			// There is at least one viewable forum, so set this TRUE.
			//
			$viewableforums = TRUE;
			
		}
		
		//
		// Total topics and posts count
		//
		$stats['topics'] = $stats['topics']+$forumdata['topics'];
		$stats['posts'] = $stats['posts']+$forumdata['posts'];
		
	}
	
	if ( !$viewableforums ) {
		
		//
		// There were no viewable forums.
		// Show this notice
		//
		$template->parse('msgbox', array(
			'box_title' => $lang['Note'],
			'content' => $lang['NoViewableForums']
		));
		
	} else {
		
		//
		// There were viewable forums,
		// so parse the forumlist footer
		//
		$template->parse('forumlist_footer');
		
	}
	
} else {
	
	//
	// No forums have been found. Output this notice.
	//
	$template->parse('msgbox', array(
		'box_title' => $lang['Note'],
		'content' => $lang['NoForums']
	));
	
}

//
// Get the user count
//
if ( !($result = $db->query("SELECT id, name FROM ".TABLE_PREFIX."users ORDER BY regdate DESC")) )
	$functions->usebb_die('SQL', 'Unable to get member count and latest member information!', __FILE__, __LINE__);
$stats['users'] = $db->num_rows($result);

//
// Get the last user
//
$stats['lastuser'] = $db->fetch_result($result);

//
// Small statistics
//
$lastuser = ( $stats['users'] == 0 ) ? '' : ' '.sprintf($lang['IndexLastUser'], '<a href="'.$functions->make_url('profile.php', array('id' => $stats['lastuser']['id'])).'">'.$stats['lastuser']['name'].'</a>');

//
// Online users
//

//
// Timestamp for defining last updated sessions
//
$min_updated = gmmktime() - ( $functions->get_config('online_min_updated') * 60 );

//
// Get the session and user information
//
if ( !($result = $db->query("SELECT u.name, u.level, s.user_id AS id, s.ip_addr FROM ( ".TABLE_PREFIX."sessions s LEFT JOIN ".TABLE_PREFIX."users u ON s.user_id = u.id ) WHERE s.updated > ".$min_updated." ORDER BY s.updated DESC")) )
	$functions->usebb_die('SQL', 'Unable to get online members information!', __FILE__, __LINE__);

//
// Arrays for holding a list of online guests and members.
//
$online_guests = array();
$online_members = array();

while ( $onlinedata = $db->fetch_result($result) ) {
	
	if ( $onlinedata['id'] == 0 ) {
		
		//
		// This is a guest
		// Guests will only be counted per IP address
		//
		
		if ( !isset($online_guests[$onlinedata['ip_addr']]) )
			$online_guests[$onlinedata['ip_addr']] = TRUE;
		
	} else {
		
		//
		// This is a member
		//
		
		//
		// CSS classes to be put into the <a>-tag.
		// Needed to mark out admins and mods.
		//
		switch ( $onlinedata['level'] ) {
			
			case 3:
				$levelclass = ' class="administrator"';
				break;
			case 2:
				$levelclass = ' class="moderator"';
				break;
			case 1:
				$levelclass = '';
				break;
			
		}
		
		if ( !isset($online_members[$onlinedata['id']]) )
			$online_members[$onlinedata['id']] = '<a href="'.$functions->make_url('profile.php', array('id' => $onlinedata['id'])).'"'.$levelclass.'>'.$onlinedata['name'].'</a>';
		
	}
	
}

//
// Online list
//
if ( !$functions->get_config('enable_online_list') || ( !$functions->get_config('guests_can_view_online_list') && $session->sess_info['user_id'] == 0 ) )
	$online_list_link = '';
else
	$online_list_link = ' <a href="'.$functions->make_url('online.php').'">'.$lang['DetailedOnlineList'].'</a>';

//
// Parse the online box
//
$template->parse('forumlist_stats', array(
	'stats_title' => $lang['VariousInfo'],
	'small_stats' => sprintf($lang['IndexStats'], $stats['posts'], $stats['topics'], $stats['users']).$lastuser,
	'users_online' => sprintf($lang['OnlineUsers'], count($online_members), count($online_guests), $functions->get_config('online_min_updated')).$online_list_link,
	'members' => ( count($online_members) > 0 ) ? join(', ', $online_members) : ''
));

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>