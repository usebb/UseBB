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
 * Search engine
 *
 * Shows the search form, takes a search query and shows appropriate results.
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
// Update and get the session information
//
$session->update('search');

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

function search_query_order_part($sort_items, $sort_by, $order, $show_mode) {
	
	//
	// For posts, use a different field
	//
	if ( $show_mode == 'posts' )
		$sort_items['latest_post'] = 'p.post_time';
		
	//
	// Build the sort part
	// Additional sorting on topic title
	//
	$query_sort_part = $sort_items[$sort_by]." ".strtoupper($order);
	if ( $sort_by != 'topic_title' )
		$query_sort_part .= ", ".$sort_items['topic_title']." ASC";
	
	return $query_sort_part;
	
}

if ( $functions->get_user_level() < $functions->get_config('view_search_min_level') ) {
	
	$functions->redir_to_login();
	
} else {
	
	$template->add_breadcrumb($lang['Search'], array('search.php'));
	
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
	// Sanatize the keywords, removing any too short words
	//
	if ( !empty($_REQUEST['keywords']) ) {
		
		$keywords = preg_split('#\s+#', $_REQUEST['keywords']);
		$sanatized_keywords = array();
		foreach ( $keywords as $keyword ) {
			
			if ( strlen($keyword) >= $functions->get_config('search_nonindex_words_min_length') )
				$sanatized_keywords[] = $keyword;
			
		}
		$_REQUEST['keywords'] = join(' ', $sanatized_keywords);
		
	} else {
		
		$_REQUEST['keywords'] = '';
		
	}
	
	//
	// Search modes
	//
	$search_modes = array('and', 'or');
	$_REQUEST['mode'] = ( !empty($_REQUEST['mode']) && in_array($_REQUEST['mode'], $search_modes) ) ? $_REQUEST['mode'] : 'and';
	
	$_REQUEST['author'] = ( !empty($_REQUEST['author']) ) ? $_REQUEST['author'] : '';
	
	//
	// Sanatize the forums array
	//
	if ( !empty($_REQUEST['forums']) && is_array($_REQUEST['forums']) && count($_REQUEST['forums']) ) {
		
		$sanatized_forums = array();
		foreach ( $_REQUEST['forums'] as $forum ) {
			
			if ( $forum === 'all' || ( valid_int($forum) && in_array($forum, $forum_ids) ) ) {
				
				$sanatized_forums[] = $forum;
				
				if ( $forum === 'all' )
					break;
				
			}
			
		}
		$_REQUEST['forums'] = ( count($sanatized_forums) ) ? $sanatized_forums : $forum_ids;
		
	} else {
		
		$_REQUEST['forums'] = $forum_ids;
		
	}
	
	//
	// Sort options
	//
	$sort_items = array(
		'latest_post' => 'p2.post_time',
		'topic_title' => 't.topic_title',
		'forum' => 'f.name',
		'author' => 'u.displayed_name',
		'replies' => 't.count_replies',
		'views' => 't.count_views'
	);
	$sort_orders = array('asc', 'desc');
	
	$_REQUEST['sort_by'] = ( !empty($_REQUEST['sort_by']) && array_key_exists($_REQUEST['sort_by'], $sort_items) ) ? $_REQUEST['sort_by'] : 'latest_post';
	$_REQUEST['order'] = ( !empty($_REQUEST['order']) && in_array($_REQUEST['order'], $sort_orders) ) ? $_REQUEST['order'] : 'desc';
	
	//
	// Show modes
	//
	$show_modes = array('topics', 'posts');
	if ( empty($_REQUEST['show_mode']) || !in_array($_REQUEST['show_mode'], $show_modes) ) {
		
		//
		// When searching via GET for a user, view as posts
		//
		if ( $_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_REQUEST['author']) )
			$_REQUEST['show_mode'] = 'posts';
		else
			$_REQUEST['show_mode'] = 'topics';
		
	}
	
	//
	// Use exact matching and no guests when searching for author name over GET
	//
	if ( $_SERVER['REQUEST_METHOD'] == 'GET' && empty($_REQUEST['keywords']) && !empty($_REQUEST['author']) ) {
		
		$_REQUEST['exact_match'] = true;
		$_REQUEST['include_guests'] = false;
		
	}
	
	if ( !count($forum_ids) ) {
		
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Note'],
			'content' => $lang['NoViewableForums']
		));
		
	} elseif ( !empty($_REQUEST['keywords']) || !empty($_REQUEST['author']) ) {
		
		$query_select = ( $_REQUEST['show_mode'] == 'topics' ) ? 'DISTINCT t.id' : 'p.id';
		
		$query_where_parts = array();
		
		if ( !empty($_REQUEST['keywords']) ) {
			
			$keywords = explode(' ', $_REQUEST['keywords']);
			foreach ( $keywords as $key => $val )
				$keywords[$key] = "( p.content LIKE '%".str_replace(array('%', '_'), array('\%', '\_'), $val)."%' OR t.topic_title LIKE '%".str_replace(array('%', '_'), array('\%', '\_'), $val)."%' )";
			$query_where_parts[] = ' ( '.join(' '.strtoupper($_REQUEST['mode']).' ', $keywords).' ) ';
			
		}
		
		if ( !empty($_REQUEST['author']) ) {
			
			if ( !empty($_REQUEST['exact_match']) ) {
				
				$author = preg_replace('#\s+#', ' ', $_REQUEST['author']);
				$guest_search = ( !empty($_REQUEST['include_guests']) ) ? " OR p.poster_guest = '".$author."'" : '';
				$query_where_parts[] = "( u.displayed_name = '".$author."'".$guest_search." )";
				
			} else {
				
				$author = preg_replace(array('#%#', '#_#', '#\s+#'), array('\%', '\_', ' '), $_REQUEST['author']);
				$guest_search = ( !empty($_REQUEST['include_guests']) ) ? " OR p.poster_guest LIKE '%".$author."%'" : '';
				$query_where_parts[] = "( u.displayed_name LIKE '%".$author."%'".$guest_search." )";
				
			}
			
		}
		
		if ( in_array('all', $_REQUEST['forums']) )
			$query_where_parts[] = "f.id IN(".join(', ', $forum_ids).")";
		else
			$query_where_parts[] = "f.id IN(".join(', ', $_REQUEST['forums']).")";
		
		$query_sort_part = search_query_order_part($sort_items, $_REQUEST['sort_by'], $_REQUEST['order'], $_REQUEST['show_mode']);
		
		$result = $db->query("SELECT ".$query_select." FROM ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id, ".TABLE_PREFIX."posts p2, ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE p2.id = t.last_post_id AND t.id = p.topic_id AND f.id = t.forum_id AND ".join(' AND ', $query_where_parts)." ORDER BY ".$query_sort_part." LIMIT ".$functions->get_config('search_limit_results'));
		$result_ids = array();
		while ( $searchdata = $db->fetch_result($result) )
			$result_ids[] = $searchdata['id'];
		
		if ( count($result_ids) ) {
			
			$result_data = array(
				'keywords' => $_REQUEST['keywords'],
				'mode' => $_REQUEST['mode'],
				'author' => $_REQUEST['author'],
				'sort_by' => $_REQUEST['sort_by'],
				'order' => $_REQUEST['order'],
				'show_mode' => $_REQUEST['show_mode'],
				'results' => $result_ids
			);
			$result_data = addslashes(serialize($result_data));
			$result = $db->query("SELECT COUNT(*) as exist FROM ".TABLE_PREFIX."searches WHERE sess_id = '".session_id()."'");
			$searchdata = $db->fetch_result($result);
			if ( $searchdata['exist'] )
				$db->query("UPDATE ".TABLE_PREFIX."searches SET time = ".time().", results = '".$result_data."' WHERE sess_id = '".session_id()."'");
			else
				$db->query("INSERT INTO ".TABLE_PREFIX."searches VALUES ('".session_id()."', ".time().", '".$result_data."')");
			
			$functions->redirect('search.php', array('act' => 'results'));
			
		} else {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Note'],
				'content' => $lang['NoSearchResults']
			));
			
		}
		
	} else {
		
		if ( !empty($_GET['act']) && $_GET['act'] == 'results' ) {
			
			$result = $db->query("SELECT results FROM ".TABLE_PREFIX."searches WHERE sess_id = '".session_id()."'");
			$search_results = $db->fetch_result($result);
			
			if ( !empty($search_results['results']) ) {

				$template->add_breadcrumb($lang['SearchResults']);
				
				$search_results = unserialize(stripslashes($search_results['results']));
				
				//
				// Get page number
				//
				$per_page = $functions->get_config($search_results['show_mode'].'_per_page');
				$numpages = ceil(intval(count($search_results['results'])) / $per_page);
				$page = ( !empty($_GET['page']) && valid_int($_GET['page']) && intval($_GET['page']) > 0 && intval($_GET['page']) <= $numpages ) ? intval($_GET['page']) : 1;
				$limit_start = ( $page - 1 ) * $per_page;
				$limit_end = $per_page;
				$page_links = $functions->make_page_links($numpages, $page, count($search_results['results']), $per_page, 'search.php', NULL, true, array('act' => 'results'));
				
				$query_sort_part = search_query_order_part($sort_items, $search_results['sort_by'], $search_results['order'], $search_results['show_mode']);
				
				if ( $search_results['show_mode'] == 'topics' ) {
					
					//
					// Show results as topics
					//
					
					$template->parse('results_header', 'search', array(
						'page_links' => $page_links,
						'keywords' => unhtml(stripslashes($search_results['keywords'])),
						'mode' => $lang['Mode-'.$search_results['mode']],
						'author' => unhtml(stripslashes($search_results['author'])),
					));
					
					$result = $db->query("SELECT t.id, t.forum_id, t.topic_title, t.last_post_id, t.count_replies, t.count_views, t.status_locked, t.status_sticky, p.poster_guest, p2.poster_guest AS last_poster_guest, p2.post_time AS last_post_time, u.id AS poster_id, u.displayed_name AS poster_name, u.level AS poster_level, u2.id AS last_poster_id, u2.displayed_name AS last_poster_name, u2.level AS last_poster_level FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id, ".TABLE_PREFIX."posts p2 LEFT JOIN ".TABLE_PREFIX."members u2 ON p2.poster_id = u2.id, ".TABLE_PREFIX."forums f WHERE t.id IN(".join(', ', $search_results['results']).") AND f.id = t.forum_id AND f.id IN(".join(', ', $forum_ids).") AND p.id = t.first_post_id AND p2.id = t.last_post_id ORDER BY ".$query_sort_part." LIMIT ".$limit_start.", ".$limit_end);
					
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
						$template->parse('results_topic', 'search', array(
							'topic_icon' => $topic_icon,
							'topic_status' => $topic_status,
							'topic_name' => $topic_name,
							'topic_page_links' => ( $topicdata['count_replies']+1 > $functions->get_config('posts_per_page') ) ? $functions->make_page_links(ceil(intval($topicdata['count_replies']+1) / $functions->get_config('posts_per_page')), '0', $topicdata['count_replies']+1, $functions->get_config('posts_per_page'), 'topic.php', $topicdata['id'], false) : '',
							'forum' => '<a href="'.$functions->make_url('forum.php', array('id' => $topicdata['forum_id'])).'">'.unhtml(stripslashes($forum_names[$topicdata['forum_id']])).'</a>',
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
					
					$template->parse('results_footer', 'search', array(
						'page_links' => $page_links,
						'keywords' => unhtml(stripslashes($search_results['keywords'])),
						'mode' => $lang['Mode-'.$search_results['mode']],
						'author' => unhtml(stripslashes($search_results['author'])),
					));
					
				} else {
					
					//
					// Show results as posts
					//
					
					$template->parse('results_posts_header', 'search', array(
						'page_links' => $page_links,
						'keywords' => unhtml(stripslashes($search_results['keywords'])),
						'mode' => $lang['Mode-'.$search_results['mode']],
						'author' => unhtml(stripslashes($search_results['author'])),
					));
					
					$result = $db->query("SELECT p.id, p.topic_id, p.content, p.post_time, p.poster_guest, p.poster_id, u.displayed_name AS poster_name, u.level AS poster_level, t.topic_title, t.status_sticky, t.forum_id FROM ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id, ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE p.id IN(".join(', ', $search_results['results']).") AND f.id = t.forum_id AND f.id IN(".join(', ', $forum_ids).") AND t.id = p.topic_id ORDER BY ".$query_sort_part." LIMIT ".$limit_start.", ".$limit_end);
					
					while ( $postdata = $db->fetch_result($result) ) {
											
						$topic_title = '<a href="'.$functions->make_url('topic.php', array('post' => $postdata['id'])).'#post'.$postdata['id'].'">'.unhtml($functions->replace_badwords(stripslashes($postdata['topic_title']))).'</a>';
						if ( $postdata['status_sticky'] )
							$topic_title = $lang['Sticky'].': '.$topic_title;
						
						$template->parse('results_posts_post', 'search', array(
							'poster_name' => ( $postdata['poster_id'] > LEVEL_GUEST ) ? $functions->make_profile_link($postdata['poster_id'], $postdata['poster_name'], $postdata['poster_level']) : unhtml(stripslashes($postdata['poster_guest'])),
							'post_date' => $functions->make_date($postdata['post_time']),
							'topic_title' => $topic_title,
							'forum' => '<a href="'.$functions->make_url('forum.php', array('id' => $postdata['forum_id'])).'">'.unhtml(stripslashes($forum_names[$postdata['forum_id']])).'</a>',
							'post_content' => unhtml($functions->bbcode_clear(stripslashes($postdata['content']))),
						));
						
					}
					
					$template->parse('results_posts_footer', 'search', array(
						'page_links' => $page_links,
						'keywords' => unhtml(stripslashes($search_results['keywords'])),
						'mode' => $lang['Mode-'.$search_results['mode']],
						'author' => unhtml(stripslashes($search_results['author']))
					));
					
				}
				
			} else {
				
				$functions->redirect('search.php');
				
			}
			
		} else {
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				
				$_REQUEST['keywords'] = unhtml(stripslashes($_REQUEST['keywords']));
				$_REQUEST['author'] = unhtml(stripslashes($_REQUEST['author']));
				
				$forums_all_selected = ( in_array('all', $_REQUEST['forums']) ) ? ' selected="selected"' : '';
				
				$errors = array();
				if ( empty($_REQUEST['keywords']) && empty($_REQUEST['author']) )
					$errors[] = $lang['SearchKeywords'];
				
				if ( count($errors) ) {
					
					$template->parse('msgbox', 'global', array(
						'box_title' => $lang['Error'],
						'content' => sprintf($lang['MissingFields'], join(', ', $errors))
					));
					
				}
				
				$exact_match_checked = ( !empty($_REQUEST['exact_match']) ) ? ' checked="checked"' : '';
				$include_guests_checked = ( !empty($_REQUEST['include_guests']) ) ? ' checked="checked"' : '';
				
			} else {
				
				$forums_all_selected = ' selected="selected"';
				
				$exact_match_checked = '';
				$include_guests_checked = ' checked="checked"';
				
			}
			
			$mode_input = '';
			foreach ( $search_modes as $mode ) {
				
				$selected = ( $_REQUEST['mode'] == $mode ) ? ' checked="checked"' : '';
				$mode_input .= ' <label><input type="radio" name="mode" value="'.$mode.'"'.$selected.' /> '.$lang['Mode-'.$mode].'</label>';
				
			}
			
			if ( count($forum_ids) === 1 ) {
				
				$forums_input = '<input type="hidden" name="forums[]" value="'.$forum_ids[0].'" /><em>'.unhtml(stripslashes($forum_names[$forum_ids[0]])).'</em> ('.$lang['AllForums'].')';
				
			} else {
				
				$forums_input = '';
				$seen_cats = array();
				$items = 1;
				$result = $db->query("SELECT c.id AS cat_id, c.name AS cat_name, f.id FROM ".TABLE_PREFIX."cats c, ".TABLE_PREFIX."forums f WHERE c.id = f.cat_id AND f.id IN( ".join(', ', $forum_ids)." ) ORDER BY c.sort_id ASC, c.name ASC, f.sort_id ASC, f.name ASC");
				while ( $forumdata = $db->fetch_result($result) ) {
					
					if ( !in_array($forumdata['cat_id'], $seen_cats) ) {
						
						$forums_input .= ( !count($seen_cats) ) ? '' : '</optgroup>';
						$forums_input .= '<optgroup label="'.unhtml(stripslashes($forumdata['cat_name'])).'">';
						$seen_cats[] = $forumdata['cat_id'];
						$items++;
						
					}
					
					$selected = ( empty($forums_all_selected) && in_array($forumdata['id'], $_REQUEST['forums']) ) ? ' selected="selected"' : '';
					$forums_input .= '<option value="'.$forumdata['id'].'"'.$selected.'>'.unhtml(stripslashes($forum_names[$forumdata['id']])).'</option>';
					$items++;
					
				}
				$input_size = min($items, max(15, ceil($items/2)));
				$forums_input = '<select name="forums[]" size="'.$input_size.'" multiple="multiple"><option value="all"'.$forums_all_selected.'>'.$lang['AllForums'].'</option>'.$forums_input.'</optgroup></select>';
				
			}
			
			$sort_input = '<select name="sort_by">';
			foreach ( $sort_items as $sort_item => $null ) {
				
				$selected = ( $_REQUEST['sort_by'] == $sort_item ) ? ' selected="selected"' : '';
				$sort_input .= '<option value="'.$sort_item.'"'.$selected.'>'.$lang['SortBy-'.$sort_item].'</option>';
				
			}
			$sort_input .= '</select>';
			foreach ( $sort_orders as $sort_order ) {
				
				$selected = ( $_REQUEST['order'] == $sort_order ) ? ' checked="checked"' : '';
				$sort_input .= ' <label><input type="radio" name="order" value="'.$sort_order.'"'.$selected.' /> '.$lang['SortOrder-'.$sort_order].'</label>';
				
			}
			
			$show_mode_input = '';
			foreach ( $show_modes as $show_mode ) {
				
				$selected = ( $_REQUEST['show_mode'] == $show_mode ) ? ' checked="checked"' : '';
				$show_mode_input .= ' <label><input type="radio" name="show_mode" value="'.$show_mode.'"'.$selected.' /> '.$lang['ShowMode-'.$show_mode].'</label>';
				
			}
			
			$template->parse('search_form', 'search', array(
				'form_begin' => '<form action="'.$functions->make_url('search.php').'" method="post">',
				'keywords_input' => '<input type="text" name="keywords" id="keywords" size="45" value="'.$_REQUEST['keywords'].'" />',
				'keywords_explain' => sprintf($lang['KeywordsExplain'], $functions->get_config('search_nonindex_words_min_length')),
				'mode_input' => $mode_input,
				'author_input' => '<input type="text" name="author" size="25" value="'.$_REQUEST['author'].'" />',
				'exact_match_input' => '<label><input type="checkbox" name="exact_match" value="1"'.$exact_match_checked.' /> '.$lang['ExactMatch'].'</label>',
				'include_guests_input' => '<label><input type="checkbox" name="include_guests" value="1"'.$include_guests_checked.' /> '.$lang['IncludeGuests'].'</label>',
				'forums_input' => $forums_input,
				'sort_input' => $sort_input,
				'show_mode_input' => $show_mode_input,
				'submit_button' => '<input type="submit" value="'.$lang['Search'].'" />',
				'form_end' => '</form>'
			));
			$template->set_js_onload("set_focus('keywords')");
			
		}
		
	}
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
