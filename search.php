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
$session->update('search');

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

$template->set_page_title($lang['Search']);

//
// Get a list of forums the user is allowed to view
//
$result = $db->query("SELECT id, name, auth FROM ".TABLE_PREFIX."forums");

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
// Sanatize the forums array
//
if ( !empty($_POST['forums']) && is_array($_POST['forums']) && count($_POST['forums']) ) {
	
	$sanatized_forums = array();
	foreach ( $_POST['forums'] as $forum ) {
		
		if ( $forum === 'all' || ( valid_int($forum) && in_array($forum, $forum_ids) ) )
			$sanatized_forums[] = $forum;
		
	}
	$_POST['forums'] = $sanatized_forums;
	
} else {
	
	$_POST['forums'] = array();
	
}

if ( ( !empty($_POST['keywords']) || !empty($_POST['author']) ) && ( !empty($_POST['mode']) && ( $_POST['mode'] === 'and' || $_POST['mode'] === 'or' ) ) && count($_POST['forums']) ) {
	
	$query_where_parts = array();
	
	if ( !empty($_POST['keywords']) ) {
		
		$keywords = preg_split('#\s+#', $_POST['keywords']);
		foreach ( $keywords as $key => $val )
			$keywords[$key] = "p.content LIKE '%".$val."%'";
		$query_where_parts[] = join(' '.strtoupper($_POST['mode']).' ', $keywords);
		
	}
	
	if ( !empty($_POST['author']) ) {
		
		$author = preg_replace('#\s+#', ' ', $_POST['author']);
		$query_where_parts[] = "( m.displayed_name LIKE '%".$author."%' OR p.poster_guest LIKE '%".$author."%' )";
		
	}
	
	if ( in_array('all', $_POST['forums']) )
		$query_where_parts[] = "f.id IN(".join(', ', $forum_ids).")";
	else
		$query_where_parts[] = "f.id IN(".join(', ', $_POST['forums']).")";
	
	$query = "SELECT DISTINCT t.id FROM ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members m ON p.poster_id = m.id, ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = p.topic_id AND f.id = t.forum_id AND ".join(' AND ', $query_where_parts);
	/*$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Note'],
		'content' => unhtml($query)
	));*/
	$result = $db->query($query);
	$topic_ids = array();
	while ( $searchdata = $db->fetch_result($result) )
		$topic_ids[] = $searchdata['id'];
	
	if ( count($topic_ids) ) {
		
		$result_data = addslashes(serialize($topic_ids));
		$result = $db->query("SELECT sess_id FROM ".TABLE_PREFIX."searches WHERE sess_id = '".session_id()."'");
		if ( $db->num_rows($result) )
			$db->query("UPDATE ".TABLE_PREFIX."searches SET time = ".time().", results = '".$result_data."' WHERE sess_id = '".session_id()."'");
		else
			$db->query("INSERT INTO ".TABLE_PREFIX."searches VALUES ('".session_id()."', ".time().", '".$result_data."')");
		
		header('Location: '.$functions->get_config('board_url').$functions->make_url('search.php', array('act' => 'results'), false));
		
	} else {
		
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Note'],
			'content' => $lang['NoSearchResults']
		));
		
	}
	
} else {
	
	if ( !empty($_GET['act']) && $_GET['act'] == 'results' ) {
		
		$result = $db->query("SELECT results FROM ".TABLE_PREFIX."searches WHERE sess_id = '".session_id()."'");
		if ( $db->num_rows($result) ) {
			
			$search_results = $db->fetch_result($result);
			$search_results = unserialize(stripslashes($search_results['results']));
			
			//
			// Get page number
			//
			$numpages = ceil(intval(count($search_results)) / $functions->get_config('topics_per_page'));
			$page = ( !empty($_GET['page']) && valid_int($_GET['page']) && intval($_GET['page']) <= $numpages ) ? intval($_GET['page']) : 1;
			$limit_start = ( $page - 1 ) * $functions->get_config('topics_per_page');
			$limit_end = $functions->get_config('topics_per_page');
			$page_links = $functions->make_page_links($numpages, $page, count($search_results), $functions->get_config('topics_per_page'), 'search.php', NULL, TRUE, array('act' => 'results'));
			
			$template->parse('results_header', 'search', array(
				'page_links' => $page_links
			));
			
			$result = $db->query("SELECT t.id, t.forum_id, t.topic_title, t.last_post_id, t.count_replies, t.count_views, t.status_locked, t.status_sticky, p.poster_guest, p2.poster_guest AS last_poster_guest, p2.post_time AS last_post_time, u.id AS poster_id, u.displayed_name AS poster_name, u.level AS poster_level, u2.id AS last_poster_id, u2.displayed_name AS last_poster_name, u2.level AS last_poster_level FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id, ".TABLE_PREFIX."posts p2 LEFT JOIN ".TABLE_PREFIX."members u2 ON p2.poster_id = u2.id WHERE t.id IN(".join(', ', $search_results).") AND p.id = t.first_post_id AND p2.id = t.last_post_id ORDER BY t.status_sticky DESC, p2.post_time DESC LIMIT ".$limit_start.", ".$limit_end);
			
			while ( $topicdata = $db->fetch_result($result) ) {
				
				//
				// Loop through the topics, generating output...
				//
				$topic_name = '<a href="'.$functions->make_url('topic.php', array('id' => $topicdata['id'])).'">'.unhtml($functions->replace_badwords(stripslashes($topicdata['topic_title']))).'</a>';
				if ( $topicdata['status_sticky'] )
					$topic_name = $lang['Sticky'].': '.$topic_name;
				$last_post_author = ( $topicdata['last_poster_id'] > 0 ) ? $functions->make_profile_link($topicdata['last_poster_id'], $topicdata['last_poster_name'], $topicdata['last_poster_level']) : unhtml(stripslashes($topicdata['last_poster_guest']));
				
				list($topic_icon, $topic_status) = $functions->topic_icon($topicdata['id'], $topicdata['status_locked'], $topicdata['last_post_time']);
				
				if ( $topic_status == $lang['NewPosts'] || $topic_status == $lang['LockedNewPosts'] ) {
					
					$topic_name = sprintf($template->get_config('newpost_link_format'), $functions->make_url('topic.php', array('id' => $topicdata['id'], 'act' => 'getnewpost')).'#newpost', 'templates/'.$functions->get_config('template').'/gfx/'.$template->get_config('newpost_link_icon'), $topic_status) . $topic_name;
					
				}
				
				//
				// Parse the topic template
				//
				$template->parse('topic', 'activetopics', array(
					'topic_icon' => $topic_icon,
					'topic_status' => $topic_status,
					'topic_name' => $topic_name,
					'topic_page_links' => ( $topicdata['count_replies']+1 > $functions->get_config('posts_per_page') ) ? $functions->make_page_links(ceil(intval($topicdata['count_replies']+1) / $functions->get_config('posts_per_page')), '0', $topicdata['count_replies']+1, $functions->get_config('posts_per_page'), 'topic.php', $topicdata['id'], FALSE) : '',
					'forum' => '<a href="'.$functions->make_url('forum.php', array('id' => $topicdata['forum_id'])).'">'.unhtml(stripslashes($forum_names[$topicdata['forum_id']])).'</a>',
					'author' => ( $topicdata['poster_id'] > 0 ) ? $functions->make_profile_link($topicdata['poster_id'], $topicdata['poster_name'], $topicdata['poster_level']) : unhtml(stripslashes($topicdata['poster_guest'])),
					'replies' => $topicdata['count_replies'],
					'views' => $topicdata['count_views'],
					'author_date' => sprintf($lang['AuthorDate'], $last_post_author, $functions->make_date($topicdata['last_post_time'])),
					'by_author' => sprintf($lang['ByAuthor'], $last_post_author),
					'on_date' => sprintf($lang['OnDate'], $functions->make_date($topicdata['last_post_time'])),
					'last_post_url' => $functions->make_url('topic.php', array('post' => $topicdata['last_post_id'])).'#post'.$topicdata['last_post_id']
				));
				
			}
			
			$template->parse('results_footer', 'search', array(
				'page_links' => $page_links
			));
			
		} else {
			
			header('Location: '.$functions->get_config('board_url').$functions->make_url('search.php', array(), false));
			
		}
		
	} else {
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			$keywords = ( !empty($_POST['keywords']) ) ? unhtml(stripslashes($_POST['keywords'])) : '';
			$author = ( !empty($_POST['author']) ) ? unhtml(stripslashes($_POST['author'])) : '';
			$mode = ( !empty($_POST['mode']) ) ? $_POST['mode'] : 'and';
			$mode_and_checked = ( $_POST['mode'] == 'and' ) ? ' checked="checked"' : '';
			$mode_or_checked = ( $_POST['mode'] == 'or' ) ? ' checked="checked"' : '';
			
			$forums_all_selected = ( in_array('all', $_POST['forums']) ) ? ' selected="selected"' : '';
			
			$errors = array();
			if ( empty($_POST['keywords']) && empty($_POST['author']) )
				$errors[] = $lang['SearchKeywords'];
			if ( empty($_POST['mode']) || ( $_POST['mode'] != 'and' && $_POST['mode'] != 'or' ) )
				$errors[] = $lang['SearchMode'];
			if ( !count($_POST['forums']) )
				$errors[] = $lang['SearchForums'];
			
			if ( count($errors) ) {
				
				$template->parse('msgbox', 'global', array(
					'box_title' => $lang['Error'],
					'content' => sprintf($lang['MissingFields'], join(', ', $errors))
				));
				
			}
			
		} else {
			
			$keywords = '';
			$author = '';
			$mode_and_checked = ' checked="checked"';
			$mode_or_checked = '';
			$forums_all_selected = ' selected="selected"';
			
		}
		
		$forums_input = '<select name="forums[]" size="10" multiple="multiple"><option value="all"'.$forums_all_selected.'>'.$lang['AllForums'].'</option>';
		$seen_cats = array();
		$result = $db->query("SELECT c.id AS cat_id, c.name AS cat_name, f.id FROM ".TABLE_PREFIX."cats c, ".TABLE_PREFIX."forums f WHERE c.id = f.cat_id AND f.id IN( ".join(', ', $forum_ids)." ) ORDER BY c.sort_id ASC, c.id ASC, f.sort_id ASC, f.id ASC");
		while ( $forumdata = $db->fetch_result($result) ) {
			
			if ( !in_array($forumdata['cat_id'], $seen_cats) ) {
				
				$forums_input .= ( !count($seen_cats) ) ? '' : '</optgroup>';
				$forums_input .= '<optgroup label="'.$forumdata['cat_name'].'">';
				$seen_cats[] = $forumdata['cat_id'];
				
			}
			
			$selected = ( empty($forums_all_selected) && in_array($forumdata['id'], $_POST['forums']) ) ? ' selected="selected"' : '';
			$forums_input .= '<option value="'.$forumdata['id'].'"'.$selected.'>'.unhtml(stripslashes($forum_names[$forumdata['id']])).'</option>';
			
		}
		$forums_input .= '</optgroup></select>';
		
		$template->parse('search_form', 'search', array(
			'form_begin' => '<form action="'.$functions->make_url('search.php').'" method="post">',
			'keywords_input' => '<input type="text" name="keywords" size="35" value="'.$keywords.'" />',
			'mode_input' => '<input type="radio" name="mode" id="mode_and" value="and"'.$mode_and_checked.' /><label for="mode_and"> '.$lang['AND'].'</label> <input type="radio" name="mode" id="mode_or" value="or"'.$mode_or_checked.' /><label for="mode_or"> '.$lang['OR'].'</label>',
			'author_input' => '<input type="text" name="author" size="35" value="'.$author.'" />',
			'forums_input' => $forums_input,
			'submit_button' => '<input type="submit" value="'.$lang['Search'].'" />',
			'reset_button' => '<input type="reset" value="'.$lang['Reset'].'" />',
			'form_end' => '</form>'
		));
		
	}
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
