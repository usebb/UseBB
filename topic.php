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
if ( ( !empty($_GET['id']) && is_numeric($_GET['id']) ) || ( !empty($_GET['post']) && is_numeric($_GET['post']) ) ) {
	
	//
	// Look up the topic ID for post ID's
	//
	if ( !empty($_GET['post']) && is_numeric($_GET['post']) ) {
		
		if ( !($result = $db->query("SELECT topic_id FROM ".TABLE_PREFIX."posts WHERE id = ".$_GET['post'])) )
			$functions->usebb_die('SQL', 'Unable to get parent topic!', __FILE__, __LINE__);
		
		if ( $db->num_rows($result) ) {
			
			$out = $db->fetch_result($result);
			$_GET['id'] = $out['topic_id'];
			
		} else {
			
			//
			// Update and get the session information
			//
			$session->update();
			
			//
			// Include the page header
			//
			require(ROOT_PATH.'sources/page_head.php');
			
			//
			// This post does not exist, show an error
			//
			$template->set_page_title($lang['Error']);
			$template->parse('msgbox', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['NoSuchPost'], 'ID '.$_GET['post'])
			));
			
			//
			// Include the page header
			//
			require(ROOT_PATH.'sources/page_foot.php');
			
			exit();
			
		}
		
	}
	
	//
	// Update and get the session information
	//
	$session->update('topic:'.$_GET['id']);
	
	//
	// Include the page header
	//
	require(ROOT_PATH.'sources/page_head.php');
	
	if ( !($result = $db->query("SELECT t.topic_title, t.status_locked, t.status_sticky, t.forum_id, f.id AS forum_id, f.name AS forum_name, f.status AS forum_status, f.auth FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = ".$_GET['id']." AND f.id = t.forum_id")) )
		$functions->usebb_die('SQL', 'Unable to get topic information!', __FILE__, __LINE__);
	
	if ( !$db->num_rows($result) ) {
		
		//
		// This topic does not exist, show an error
		//
		$template->set_page_title($lang['Error']);
		$template->parse('msgbox', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchTopic'], 'ID '.$_GET['id'])
		));
		
	} else {
		
		$topicdata = $db->fetch_result($result);
		
		if ( $functions->auth($topicdata['auth'], 'read', $topicdata['forum_id']) ) {
			
			//
			// The user may view this topic
			//
			
			$template->set_page_title(htmlentities(stripslashes($topicdata['topic_title'])));
			
			//
			// Make the location bar
			//
			
			$location_bar = '<a href="'.$functions->make_url('index.php').'">'.htmlentities($functions->get_config('board_name')).'</a> '.$template->get_config('location_arrow').' <a href="'.$functions->make_url('forum.php', array('id' => $topicdata['forum_id'])).'">'.htmlentities(stripslashes($topicdata['forum_name'])).'</a> '.$template->get_config('location_arrow').' '.htmlentities(stripslashes($topicdata['topic_title']));
			$template->parse('location_bar', array(
				'location_bar' => $location_bar
			));
			
			//
			// Get all the posts in one query
			//
			if ( !($result = $db->query("SELECT p.id, p.poster_id, p.poster_guest, p.poster_ip_addr, p.content, p.post_time, p.enable_bbcode, p.enable_smilies, p.enable_sig, p.enable_html, u.name AS poster_name, u.level AS poster_level, u.rank, u.avatar_type, u.avatar_remote, u.posts, u.regdate, u.location, u.signature FROM ( ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."users u ON p.poster_id = u.id ) WHERE p.topic_id = ".$_GET['id']." ORDER BY p.post_time ASC")) )
				$functions->usebb_die('SQL', 'Unable to get posts in topic!', __FILE__, __LINE__);
			
			$new_topic_link = ( $functions->auth($topicdata['auth'], 'post', $topicdata['forum_id']) && ( $topicdata['forum_status'] || $functions->get_user_level() == 3 ) ) ? '<a href="'.$functions->make_url('post.php', array('forum' => $topicdata['forum_id'])).'"><img src="gfx/'.$functions->get_config('template').'/'.$functions->get_config('language').'/'.$template->get_config('new_topic_icon').'" alt="'.$lang['PostNewTopic'].'" /></a>' : '';
			
			$reply_link = ( ( !$topicdata['status_locked'] || $functions->auth($topicdata['auth'], 'lock', $topicdata['forum_id']) ) && ( $topicdata['forum_status'] || $functions->get_user_level() == 3 ) && $functions->auth($topicdata['auth'], 'reply', $topicdata['forum_id']) ) ? '<a href="'.$functions->make_url('post.php', array('topic' => $_GET['id'])).'"><img src="gfx/'.$functions->get_config('template').'/'.$functions->get_config('language').'/'.$template->get_config('reply_icon').'" alt="'.$lang['PostReply'].'" /></a>' : '';
			
			//
			// Output the posts
			//
			$template->parse('topic_header', array(
				'author' => $lang['Author'],
				'post' => $lang['Post'],
				'new_topic_link' => $new_topic_link,
				'reply_link' => $reply_link
			));
			
			while ( $postsdata = $db->fetch_result($result) ) {
				
				//
				// Loop through the posts
				//
				
				//
				// Used for switching colors in template
				//
				$colornum = ( !isset($colornum) || $colornum !== 1 ) ? 1 : 2;
				
				//
				// Post count
				//
				$i = ( !isset($i) ) ? 1 : $i+1;
				
				//
				// This poster was logged in
				//
				if ( !empty($postsdata['poster_id']) ) {
					
					//
					// Its name and profile link
					//
					$poster_name = $functions->make_profile_link($postsdata['poster_id'], $postsdata['poster_name'], $postsdata['poster_level']);
					
					//
					// Its rank
					// If he has a custom rank, use it, otherwise use the level information
					//
					if ( !empty($postsdata['rank']) ) {
						
						$poster_rank = stripslashes($postsdata['rank']);
						
					} else {
						
						switch ( $postsdata['poster_level'] ) {
							
							case 3:
								$poster_rank = $lang['Administrator'];
								break;
							case 2:
								$poster_rank = $lang['Moderator'];
								break;
							case 1:
								$poster_rank = $lang['Member'];
								break;
							
						}
						
					}
					
					//
					// User's avatar
					//
					if ( !$postsdata['avatar_type'] )
						$avatar = '';
					elseif ( intval($postsdata['avatar_type']) === 1 )
						$avatar = '<img src="'.$postsdata['avatar_remote'].'" alt="" />';
					
				} else {
					
					//
					// The poster was a guest
					//
					$poster_name = $postsdata['poster_guest'];
					$poster_rank = $lang['Guest'];
					$avatar = '';
					
				}
				
				
				$topic_title  = ( $i > 1 ) ? $lang['Re'].' ' : '';
					$topic_title .= htmlentities(stripslashes($topicdata['topic_title']));
				
				//
				// Links used to control posts: quote, edit, delete...
				//
				$post_links = array();
				if ( $session->sess_info['user_id'] && $postsdata['poster_id'] == $session->sess_info['user_id'] || $functions->auth($topicdata['auth'], 'edit', $topicdata['forum_id']) )
					$post_links[] = '<a href="'.$functions->make_url('edit.php', array('post' => $postsdata['id'])).'"><img src="gfx/'.$functions->get_config('template').'/'.$functions->get_config('language').'/'.$template->get_config('edit_icon').'" alt="'.$lang['Edit'].'" /></a>';
				if ( $functions->auth($topicdata['auth'], 'delete', $topicdata['forum_id']) )
					$post_links[] = '<a href="'.$functions->make_url('edit.php', array('post' => $postsdata['id'], 'act' => 'delete')).'"><img src="gfx/'.$functions->get_config('template').'/'.$functions->get_config('language').'/'.$template->get_config('delete_icon').'" alt="'.$lang['Delete'].'" /></a>';
				if ( ( !$topicdata['status_locked'] || $functions->auth($topicdata['auth'], 'lock', $topicdata['forum_id']) ) && ( $topicdata['forum_status'] || $functions->get_user_level() == 3 ) && $functions->auth($topicdata['auth'], 'reply', $topicdata['forum_id']) )
					$post_links[] = '<a href="'.$functions->make_url('post.php', array('topic' => $_GET['id'], 'quotepost' => $postsdata['id'])).'"><img src="gfx/'.$functions->get_config('template').'/'.$functions->get_config('language').'/'.$template->get_config('quote_icon').'" alt="'.$lang['Quote'].'" /></a>';
				if ( count($post_links) )
					$post_links = join(' ', $post_links);
				else
					$post_links = '';
				
				//
				// Output the post
				//
				$template->parse('topic_post', array(
					'poster_name' => $poster_name,
					'poster_rank' => $poster_rank,
					'poster_avatar' => $avatar,
					'posts' => ( !empty($postsdata['poster_id']) ) ? $lang['Posts'].': '.$postsdata['posts'] : '',
					'registered' => ( !empty($postsdata['poster_id']) ) ? $lang['Registered'].': '.date('M y', $postsdata['regdate']) : '',
					'location' => ( !empty($postsdata['poster_id']) && !empty($postsdata['location']) ) ? $lang['Location'].': '.htmlentities(stripslashes($postsdata['location'])) : '',
					'topic_title' => $topic_title,
					'post_anchor' => '<a href="'.$functions->make_url('topic.php', array('post' => $postsdata['id'])).'#post'.$postsdata['id'].'" name="post'.$postsdata['id'].'">#'.$i.'</a>',
					'post_date' => $functions->make_date($postsdata['post_time']),
					'post_links' => $post_links,
					'post_content' => $functions->markup(stripslashes($postsdata['content']), $postsdata['enable_bbcode'], $postsdata['enable_smilies'], $postsdata['enable_html']),
					'poster_sig' => ( !empty($postsdata['signature']) && $postsdata['enable_sig'] ) ? sprintf($template->get_config('sig_format'), $functions->markup(stripslashes($postsdata['signature']), $functions->get_config('sig_allow_bbcode'), $functions->get_config('sig_allow_smilies'))) : '',
					'colornum' => $colornum
				));
				
			}
			
			//
			// Links for controlling topics: delete, move, lock, sticky...
			//
			$action_links = array();
			if ( $functions->auth($topicdata['auth'], 'delete', $topicdata['forum_id']) )
				$action_links[] = '<a href="'.$functions->make_url('edit.php', array('topic' => $_GET['id'], 'act' => 'delete')).'">'.$lang['DeleteTopic'].'</a>';
			if ( $functions->auth($topicdata['auth'], 'move', $topicdata['forum_id']) )
				$action_links[] = '<a href="'.$functions->make_url('edit.php', array('topic' => $_GET['id'], 'act' => 'move')).'">'.$lang['MoveTopic'].'</a>';
			if ( $functions->auth($topicdata['auth'], 'lock', $topicdata['forum_id']) ) {
				
				if ( $topicdata['status_locked'] )
					$action_links[] = '<a href="'.$functions->make_url('edit.php', array('topic' => $_GET['id'], 'act' => 'unlock')).'">'.$lang['UnlockTopic'].'</a>';
				else
					$action_links[] = '<a href="'.$functions->make_url('edit.php', array('topic' => $_GET['id'], 'act' => 'lock')).'">'.$lang['LockTopic'].'</a>';
				
			}
			if ( $functions->auth($topicdata['auth'], 'sticky', $topicdata['forum_id']) ) {
				
				if ( $topicdata['status_sticky'] )
					$action_links[] = '<a href="'.$functions->make_url('edit.php', array('topic' => $_GET['id'], 'act' => 'unsticky')).'">'.$lang['MakeNormalTopic'].'</a>';
				else
					$action_links[] = '<a href="'.$functions->make_url('edit.php', array('topic' => $_GET['id'], 'act' => 'sticky')).'">'.$lang['MakeSticky'].'</a>';
				
			}
			$action_links = join(' '.$template->get_config('item_delimiter').' ', $action_links);
			
			$template->parse('topic_footer', array(
				'new_topic_link' => $new_topic_link,
				'reply_link' => $reply_link,
				'action_links' => $action_links
			));
			
			$template->parse('location_bar', array(
				'location_bar' => $location_bar
			));
			
			//
			// Neat feature: the quick reply
			// Only shown if enabled, if user can reply and if user can post in lcoked forum...
			//
			if ( $functions->get_config('enable_quickreply') && ( !$topicdata['status_locked'] || $functions->auth($topicdata['auth'], 'lock', $topicdata['forum_id']) ) && ( $topicdata['forum_status'] || $functions->get_user_level() == 3 ) && $functions->auth($topicdata['auth'], 'reply', $topicdata['forum_id']) ) {
				
				//
				// Get session saved guest's username if there is one
				//
				$username = ( !$session->sess_info['user_id'] && !empty($_SESSION['user']) ) ? $_SESSION['user'] : '';
				
				$template->parse('quick_reply', array(
					'form_begin' => '<form action="'.$functions->make_url('post.php', array('topic' => $_GET['id'])).'" method="post">',
					'quick_reply' => $lang['QuickReply'],
					'username' => $lang['Username'],
					'username_input' => ( $session->sess_info['user_id'] ) ? '<a href="'.$functions->make_url('profile.php', array('id' => $session->sess_info['user_info']['id'])).'">'.$session->sess_info['user_info']['name'].'</a>' : '<input type="text" size="25" maxlength="'.$functions->get_config('username_max_length').'" name="user" value="'.$username.'" />',
					'content' => $lang['Content'],
					'content_input' => '<textarea rows="'.$template->get_config('quick_reply_textarea_rows').'" cols="'.$template->get_config('textarea_cols').'" name="content"></textarea>',
					'submit_button' => '<input type="submit" name="submit" value="'.$lang['PostReply'].'" />',
					'reset_button' => '<input type="reset" value="'.$lang['Reset'].'" />',
					'form_end' => '<input type="hidden" name="enable_bbcode" value="1" /><input type="hidden" name="enable_smilies" value="1" /><input type="hidden" name="enable_sig" value="1" /><input type="hidden" name="submitted" value="true" /></form>'
				));
				
			}
			
			//
			// Update views count
			//
			if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."topics SET count_views = count_views+1 WHERE id = ".$_GET['id'])) )
				$functions->usebb_die('SQL', 'Unable to update topic views count!', __FILE__, __LINE__);
			
		} else {
			
			//
			// The user is not granted to view this topic
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
	header('Location: '.$functions->get_config('board_url').$functions->make_url('index.php', array(), false));
	
}

?>
