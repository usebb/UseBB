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
 * Topic view
 *
 * Parses a topic and posts.
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
// If an ID has been passed
//
if ( ( !empty($_GET['id']) && valid_int($_GET['id']) ) || ( !empty($_GET['post']) && valid_int($_GET['post']) ) ) {
	
	//
	// Look up the topic ID for post ID's
	//
	if ( !empty($_GET['post']) && valid_int($_GET['post']) ) {
		
		$result = $db->query("SELECT p1.topic_id, COUNT(p2.id) AS post_in_topic FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p1, ".TABLE_PREFIX."posts p2 WHERE p1.id = ".$_GET['post']." AND t.id = p1.topic_id AND p2.topic_id = p1.topic_id AND p2.id <= ".$_GET['post']." GROUP BY p1.topic_id");
		$out = $db->fetch_result($result);
		
		if ( $out['topic_id'] ) {
			
			$requested_topic = $out['topic_id'];
			$post_in_topic = $out['post_in_topic'];
			
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
			header(HEADER_404);
			$template->add_breadcrumb($lang['Error']);
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['NoSuchPost'], 'ID '.$_GET['post'])
			));
			
			//
			// Include the page header
			//
			require(ROOT_PATH.'sources/page_foot.php');
			
			exit();
			
		}
		
	} elseif ( !empty($_GET['act']) && $_GET['act'] == 'getnewpost' ) {
		
		$previous_view = ( array_key_exists('t'.$_GET['id'], $_SESSION['viewed_topics']) ) ? $_SESSION['viewed_topics']['t'.$_GET['id']] : $_SESSION['previous_visit'];
		
		$result = $db->query("SELECT COUNT(p.id) AS post_in_topic FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p WHERE t.id = ".$_GET['id']." AND t.id = p.topic_id AND p.post_time <= ".$previous_view." GROUP BY p.topic_id");
		$out = $db->fetch_result($result);
		
		if ( $out['post_in_topic'] )
			$post_in_topic = $out['post_in_topic']+1;
		
		$requested_topic = $_GET['id'];
		
	} else {
		
		$requested_topic = $_GET['id'];
		
	}
	
	//
	// Update and get the session information
	//
	$session->update('topic:'.$requested_topic);
	
	//
	// Include the page header
	//
	require(ROOT_PATH.'sources/page_head.php');
	
	$result = $db->query("SELECT t.id, t.topic_title, t.status_locked, t.status_sticky, t.count_replies, t.forum_id, t.last_post_id, f.id AS forum_id, f.name AS forum_name, f.status AS forum_status, f.auth, f.hide_mods_list FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = ".$requested_topic." AND f.id = t.forum_id");
	$topicdata = $db->fetch_result($result);
	
	if ( !$topicdata['id'] ) {
		
		//
		// This topic does not exist, show an error
		//
		header(HEADER_404);
		$template->add_breadcrumb($lang['Error']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchTopic'], 'ID '.$requested_topic)
		));
		
	} else {
		
		if ( $functions->auth($topicdata['auth'], 'read', $topicdata['forum_id']) ) {
			
			//
			// The user may view this topic
			//
			
			$topic_title = unhtml($functions->replace_badwords(stripslashes($topicdata['topic_title'])));
			
			$template->add_breadcrumb(
				unhtml(stripslashes($topicdata['forum_name'])),
				array('forum.php', array('id' => $topicdata['forum_id']))
			);
			$template->add_breadcrumb($topic_title);
			
			//
			// Update views count (once per session)
			//
			if ( !array_key_exists('t'.$requested_topic, $_SESSION['viewed_topics']) )
				$result = $db->query("UPDATE ".TABLE_PREFIX."topics SET count_views = count_views+1 WHERE id = ".$requested_topic);
			
			//
			// Eventually (un)subscribe user to topic
			//
			if ( !empty($_SESSION['subscribe_msg']) && in_array($_SESSION['subscribe_msg'], array('subscribed', 'unsubscribed')) ) {
	
				$template->parse('msgbox', 'global', array(
					'box_title' => $lang['Note'],
					'content' => ( $_SESSION['subscribe_msg'] == 'subscribed' ) ? $lang['SubscribedTopic'] : $lang['UnsubscribedTopic']
				));
				unset($_SESSION['subscribe_msg']);

			}
			if ( $session->sess_info['user_id'] ) {
				
				$result = $db->query("SELECT COUNT(*) as subscribed FROM ".TABLE_PREFIX."subscriptions WHERE topic_id = ".$requested_topic." AND user_id = ".$session->sess_info['user_id']);
				$subscribed = $db->fetch_result($result);
				$subscribed = ( !$subscribed['subscribed'] ) ? false : true;

			}
			if ( !empty($_GET['act']) && in_array($_GET['act'], array('subscribe', 'unsubscribe')) && $functions->verify_url() ) {
				
				if ( !$session->sess_info['user_id'] ) {
					
					$functions->redir_to_login();
					
				} else {
					
					if ( !$subscribed && $_GET['act'] == 'subscribe' ) {
						
						$result = $db->query("INSERT INTO ".TABLE_PREFIX."subscriptions VALUES(".$requested_topic.", ".$session->sess_info['user_id'].")");
						$_SESSION['subscribe_msg'] = 'subscribed';
						$functions->redirect('topic.php', array('id' => $requested_topic));
						
					} elseif ( $subscribed && $_GET['act'] == 'unsubscribe' ) {
						
						$result = $db->query("DELETE FROM ".TABLE_PREFIX."subscriptions WHERE topic_id = ".$requested_topic." AND user_id = ".$session->sess_info['user_id']);
						$_SESSION['subscribe_msg'] = 'unsubscribed';
						$functions->redirect('topic.php', array('id' => $requested_topic));
						
					}
					
				}
				
			}
			
			//
			// Get all the posts in one query
			//
			
			$forum_moderators = $functions->get_mods_list($topicdata['forum_id']);
			
			$new_topic_link = (
				(
					$functions->auth($topicdata['auth'], 'post', $topicdata['forum_id'])
					// True if is guest but members can post. Will redirect to login.
					|| ( $functions->get_config('show_posting_links_to_guests') && !$session->sess_info['user_id'] && $functions->auth($topicdata['auth'], 'post', $topicdata['forum_id'], FALSE, array('id' => -1, 'level' => LEVEL_MEMBER)) )
				)
				&& ( $topicdata['forum_status'] || $functions->get_user_level() == LEVEL_ADMIN )
			) ? '<a href="'.$functions->make_url('post.php', array('forum' => $topicdata['forum_id'])).'" rel="nofollow">'.$lang['PostNewTopic'].'</a>' : '';
			
			$can_post_reply = (
				( !$topicdata['status_locked'] || $functions->auth($topicdata['auth'], 'lock', $topicdata['forum_id']) )
				&& ( $topicdata['forum_status'] || $functions->get_user_level() == LEVEL_ADMIN )
				&& ( 
					$functions->auth($topicdata['auth'], 'reply', $topicdata['forum_id'])
					// True if is guest but members can post. Will redirect to login.
					|| ( $functions->get_config('show_posting_links_to_guests') && !$session->sess_info['user_id'] && $functions->auth($topicdata['auth'], 'reply', $topicdata['forum_id'], FALSE, array('id' => -1, 'level' => LEVEL_MEMBER)) )
				)
			);
			$reply_link = ( $can_post_reply ) ? '<a href="'.$functions->make_url('post.php', array('topic' => $requested_topic)).'" rel="nofollow">'.$lang['PostReply'].'</a>' : ( $topicdata['status_locked'] ? '<span class="locked-msg">'.$lang['Locked'].'</span>' : '' );
			
			//
			// Get page number
			//
			$numpages = ceil(intval($topicdata['count_replies']+1) / $functions->get_config('posts_per_page'));
			if ( empty($post_in_topic) )
				$page = ( !empty($_GET['page']) && valid_int($_GET['page']) && intval($_GET['page']) > 0 && intval($_GET['page']) <= $numpages ) ? intval($_GET['page']) : 1;
			else
				$page = ceil(intval($post_in_topic) / $functions->get_config('posts_per_page'));
			$limit_start = ( $page - 1 ) * $functions->get_config('posts_per_page');
			$limit_end = $functions->get_config('posts_per_page');
			$page_links = $functions->make_page_links($numpages, $page, $topicdata['count_replies']+1, $functions->get_config('posts_per_page'), 'topic.php', $requested_topic);
			
			//
			// Avatar helper variables
			//
			$hide_avatars = (bool)$functions->get_config('hide_avatars');
			$avatars_force_width = (int)$functions->get_config('avatars_force_width');
			$avatars_force_height = (int)$functions->get_config('avatars_force_height');
			$avatars_found = false;
			
			//
			// Output the posts
			//
			$template->parse('header', 'topic', array(
				'topic_name' => '<a href="'.$functions->make_url('topic.php', array('id' => $requested_topic)).'">'.$topic_title.'</a>',
				'forum_moderators' => ( !$topicdata['hide_mods_list'] && $forum_moderators != $lang['Nobody'] ) ? sprintf($lang['ModeratorList'], $forum_moderators) : '',
				'new_topic_link' => $new_topic_link,
				'reply_link' => $reply_link,
				'page_links' => $page_links
			));
			
			$avatars_query_part = ( !$hide_avatars ) ? ', u.avatar_type, u.avatar_remote' : '';
			$userinfo_query_part = ( !$functions->get_config('hide_userinfo') ) ? ', u.posts, u.regdate, u.location' : '';
			$signatures_query_part1 = ( !$functions->get_config('hide_signatures') ) ? ', p.enable_sig' : '';
			$signatures_query_part2 = ( !$functions->get_config('hide_signatures') ) ? ', u.signature' : '';
			
			$result = $db->query("SELECT p.id, p.poster_id, p.poster_guest, p.poster_ip_addr, p.content, p.post_time, p.enable_bbcode, p.enable_smilies".$signatures_query_part1.", p.enable_html, p.post_edit_time, p.post_edit_by, u.displayed_name AS poster_name, u.level AS poster_level, u.rank, u.active".$avatars_query_part.$userinfo_query_part.$signatures_query_part2." FROM ( ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id ) WHERE p.topic_id = ".$requested_topic." ORDER BY p.post_time ASC LIMIT ".$limit_start.", ".$limit_end);
			
			$i = (( $page - 1 ) * $functions->get_config('posts_per_page'));
			$new_post_anchor_set = false;
			$post_editors = array();
			
			while ( $postsdata = $db->fetch_result($result) ) {
				
				//
				// Loop through the posts
				//
				
				//
				// Post count
				//
				$i++;
				
				//
				// Used for switching colors in template
				//
				$colornum = ( $i % 2 ) ? 1 : 2;
				
				if ( $session->sess_info['user_id'] ) {
					
					$previous_view = ( array_key_exists('t'.$requested_topic, $_SESSION['viewed_topics']) ) ? $_SESSION['viewed_topics']['t'.$requested_topic] : $_SESSION['previous_visit'];
					
					if ( !$new_post_anchor_set && $previous_view < $postsdata['post_time'] ) {
						
						$new_post_anchor = '<a name="newpost"></a>';
						$new_post_anchor_set = true;
						
					} else {
						
						$new_post_anchor = '';
						
					}
					
				} else {
					
					$new_post_anchor = '';
					
				}
				
				//
				// This poster was logged in
				//
				if ( !empty($postsdata['poster_id']) ) {
					
					//
					// Its name and profile link
					//
					$poster_name = $functions->make_profile_link($postsdata['poster_id'], $postsdata['poster_name'], $postsdata['poster_level']);
					
					//
					// Level
					//
					switch ( $postsdata['poster_level'] ) {
						
						case LEVEL_ADMIN:
							$poster_level = $lang['Administrator'];
							break;
						case LEVEL_MOD:
							$poster_level = $lang['Moderator'];
							break;
						case LEVEL_MEMBER:
							$poster_level = $lang['Member'];
							
					}
					
					//
					// Its rank
					// If he has a custom rank, use it, otherwise use the level information
					//
					$poster_rank = ( !empty($postsdata['rank']) ) ? '<abbr title="'.$poster_level.'">'.stripslashes($postsdata['rank']).'</abbr>' : $poster_level;
					
					//
					// User's avatar
					//
					if ( $hide_avatars || empty($postsdata['avatar_type']) || !$postsdata['avatar_type'] ) {
						
						$avatar = '';
						
					} else {
						
						$avatar = '<img src="'.unhtml(stripslashes($postsdata['avatar_remote'])).'" class="usebb-avatar" alt="" />';
						$avatars_found = true;
						
					}
					
				} else {
					
					//
					// The poster was a guest
					//
					$poster_name = unhtml(stripslashes($postsdata['poster_guest']));
					$poster_rank = $lang['Guest'];
					$avatar = '';
					
				}
				
				
				$post_topic_title = ( ( $i > 1 ) ? $lang['Re'].' ' : '' ) . $topic_title;
				
				//
				// Links used to control posts: quote, edit, delete...
				//
				$post_links = array();
				
				if ( ( ( $session->sess_info['user_id'] && $postsdata['poster_id'] == $session->sess_info['user_id'] && ( time() - $functions->get_config('edit_post_timeout') ) <= $postsdata['post_time'] ) || $functions->auth($topicdata['auth'], 'edit', $topicdata['forum_id']) ) && $postsdata['poster_level'] <= $session->sess_info['user_info']['level'] )
					$post_links[] = '<a href="'.$functions->make_url('edit.php', array('post' => $postsdata['id'])).'">'.$lang['Edit'].'</a>';
				
				if ( ( ( $session->sess_info['user_id'] && $postsdata['poster_id'] == $session->sess_info['user_id'] && $topicdata['last_post_id'] == $postsdata['id'] && ( time() - $functions->get_config('edit_post_timeout') ) <= $postsdata['post_time'] ) || $functions->auth($topicdata['auth'], 'delete', $topicdata['forum_id']) ) && $postsdata['poster_level'] <= $session->sess_info['user_info']['level'] )
					$post_links[] = '<a href="'.$functions->make_url('edit.php', array('post' => $postsdata['id'], 'act' => 'delete')).'">'.$lang['Delete'].'</a>';
				
				if ( $can_post_reply )
					$post_links[] = '<a href="'.$functions->make_url('post.php', array('topic' => $requested_topic, 'quotepost' => $postsdata['id'])).'" rel="nofollow">'.$lang['Quote'].'</a>';
				
				if ( count($post_links) )
					$post_links = join($template->get_config('postlinks_item_delimiter'), $post_links);
				else
					$post_links = '';
				
				if ( $postsdata['post_edit_time'] && ( $postsdata['post_edit_time'] > ( $postsdata['post_time'] + intval($functions->get_config('show_edited_message_timeout')) ) ) ) {
					
					//
					// Show the post editor
					//
					if ( $postsdata['post_edit_by'] === $postsdata['poster_id'] ) {
						
						//
						// Current poster
						//
						$editer_info = $postsdata;
						
					} elseif ( $postsdata['post_edit_by'] === $session->sess_info['user_id'] ) {
						
						//
						// Yourself
						//
						$editer_info = array(
							'poster_name' => $session->sess_info['user_info']['displayed_name'],
							'poster_level' => $session->sess_info['user_info']['level']
						);
						
					} else {
						
						if ( !array_key_exists($postsdata['post_edit_by'], $post_editors) ) {
							
							//
							// Store editors in an array
							//
							$result2 = $db->query("SELECT displayed_name AS poster_name, level AS poster_level FROM ".TABLE_PREFIX."members WHERE id = ".$postsdata['post_edit_by']);
							$post_editors[$postsdata['post_edit_by']] = $db->fetch_result($result2);
							
						}
						
						$editer_info = $post_editors[$postsdata['post_edit_by']];
						
					}
					
					$post_editby = ( $postsdata['post_edit_by'] ) ? $functions->make_profile_link($postsdata['post_edit_by'], $editer_info['poster_name'], $editer_info['poster_level']) : $lang['Unknown'];
					$post_editinfo = sprintf($template->get_config('post_editinfo_format'), sprintf($lang['PostEditInfo'], $post_editby, $functions->make_date($postsdata['post_edit_time'])));
					
				} else {
					
					$post_editinfo = '';
					
				}

				$can_add_profile_links = $functions->antispam_can_add_profile_links($postsdata);
				$can_post_links = $functions->antispam_can_post_links($postsdata);
				
				//
				// Output the post
				//
				$template->parse('post', 'topic', array(
					'poster_name' => $poster_name,
					'poster_rank' => $poster_rank,
					'poster_avatar' => $avatar,
					'posts' => ( !empty($postsdata['poster_id']) && !$functions->get_config('hide_userinfo') ) ? $lang['Posts'].': '.$postsdata['posts'] : '',
					'registered' => ( !empty($postsdata['poster_id']) && !$functions->get_config('hide_userinfo') ) ? $lang['Registered'].': '.$functions->make_date($postsdata['regdate'], 'M Y') : '',
					'location' => ( !empty($postsdata['poster_id']) && !empty($postsdata['location']) && !$functions->get_config('hide_userinfo') ) ? $lang['Location'].': '.unhtml(stripslashes($postsdata['location'])) : '',
					'topic_title' => $post_topic_title,
					'post_anchor' => '<a href="'.$functions->make_url('topic.php', array('post' => $postsdata['id'])).'#post'.$postsdata['id'].'" name="post'.$postsdata['id'].'" rel="nofollow">#'.$i.'</a>'.$new_post_anchor,
					'post_date' => $functions->make_date($postsdata['post_time']),
					'post_links' => $post_links,
					'post_content' => $functions->markup($functions->replace_badwords(stripslashes($postsdata['content'])), $postsdata['enable_bbcode'], $postsdata['enable_smilies'], $postsdata['enable_html'], NULL, $can_post_links),
					'poster_sig' => ( !$functions->get_config('hide_signatures') && !empty($postsdata['signature']) && $postsdata['enable_sig'] ) ? sprintf($template->get_config('sig_format'), $functions->markup($functions->replace_badwords(stripslashes($postsdata['signature'])), $functions->get_config('sig_allow_bbcode'), $functions->get_config('sig_allow_smilies'), NULL, NULL, $can_add_profile_links)) : '',
					'post_editinfo' => $post_editinfo,
					'poster_ip_addr' => ( !empty($postsdata['poster_ip_addr']) && $functions->get_user_level() == LEVEL_ADMIN ) ? sprintf($template->get_config('poster_ip_addr_format'), sprintf($lang['ViewingIP'], '<a href="'.$functions->make_url('admin.php', array('act' => 'iplookup', 'ip' => $postsdata['poster_ip_addr'])).'">'.$postsdata['poster_ip_addr'].'</a>')) : '',
					'colornum' => $colornum
				));
				
			}
			
			//
			// Links for controlling topics: delete, move, lock, sticky...
			//
			$action_links = array();
			
			if ( $session->sess_info['user_id'] ) {
				
				if ( !$subscribed )
					$action_links[] = '<a href="'.$functions->make_url('topic.php', array('id' => $requested_topic, 'act' => 'subscribe'), true, true, false, true).'">'.$lang['SubscribeTopic'].'</a>';
				else
					$action_links[] = '<a href="'.$functions->make_url('topic.php', array('id' => $requested_topic, 'act' => 'unsubscribe'), true, true, false, true).'">'.$lang['UnsubscribeTopic'].'</a>';
				
			}
			
			if ( $functions->auth($topicdata['auth'], 'delete', $topicdata['forum_id']) )
				$action_links[] = '<a href="'.$functions->make_url('edit.php', array('topic' => $requested_topic, 'act' => 'delete')).'">'.$lang['DeleteTopic'].'</a>';
			
			if ( $functions->auth($topicdata['auth'], 'move', $topicdata['forum_id']) && intval($functions->get_stats('viewable_forums')) > 1 )
				$action_links[] = '<a href="'.$functions->make_url('edit.php', array('topic' => $requested_topic, 'act' => 'move')).'">'.$lang['MoveTopic'].'</a>';
			
			if ( $functions->auth($topicdata['auth'], 'lock', $topicdata['forum_id']) ) {
				
				if ( $topicdata['status_locked'] )
					$action_links[] = '<a href="'.$functions->make_url('edit.php', array('topic' => $requested_topic, 'act' => 'unlock'), true, true, false, true).'">'.$lang['UnlockTopic'].'</a>';
				else
					$action_links[] = '<a href="'.$functions->make_url('edit.php', array('topic' => $requested_topic, 'act' => 'lock'), true, true, false, true).'">'.$lang['LockTopic'].'</a>';
				
			}
			
			if ( $functions->auth($topicdata['auth'], 'sticky', $topicdata['forum_id']) ) {
				
				if ( $topicdata['status_sticky'] )
					$action_links[] = '<a href="'.$functions->make_url('edit.php', array('topic' => $requested_topic, 'act' => 'unsticky'), true, true, false, true).'">'.$lang['MakeNormalTopic'].'</a>';
				else
					$action_links[] = '<a href="'.$functions->make_url('edit.php', array('topic' => $requested_topic, 'act' => 'sticky'), true, true, false, true).'">'.$lang['MakeSticky'].'</a>';
				
			}
			
			$action_links = join($template->get_config('item_delimiter'), $action_links);
			
			$template->parse('footer', 'topic', array(
				'topic_name' => '<a href="'.$functions->make_url('topic.php', array('id' => $requested_topic)).'">'.$topic_title.'</a>',
				'forum_moderators' => ( !$topicdata['hide_mods_list'] && $forum_moderators != $lang['Nobody'] ) ? sprintf($lang['ModeratorList'], $forum_moderators) : '',
				'new_topic_link' => $new_topic_link,
				'reply_link' => $reply_link,
				'page_links' => $page_links,
				'action_links' => $action_links
			));
			
			//
			// Neat feature: the quick reply
			// Only shown if enabled, if user can reply and if user can post in locked forum..
			// If the spam check must be performed first, don't enable the quick reply form.
			//
			if ( $functions->get_config('enable_quickreply') && ( !$topicdata['status_locked'] || $functions->auth($topicdata['auth'], 'lock', $topicdata['forum_id']) ) && ( $topicdata['forum_status'] || $functions->get_user_level() == LEVEL_ADMIN ) && $functions->auth($topicdata['auth'], 'reply', $topicdata['forum_id']) && ( !$functions->get_config('antispam_question_mode') || $session->sess_info['user_id'] || $_SESSION['antispam_question_posed'] ) ) {
				
				//
				// Get session saved guest's username if there is one
				//
				$username = ( !$session->sess_info['user_id'] && !empty($_SESSION['user']) ) ? unhtml(stripslashes($_SESSION['user'])) : '';
				
				$subscribe_topic = ( $session->sess_info['user_id'] && $session->sess_info['user_info']['auto_subscribe_reply'] ) ? 1 : 0;
				
				$template->parse('quick_reply', 'topic', array(
					'form_begin' => '<form action="'.$functions->make_url('post.php', array('topic' => $requested_topic)).'" method="post">',
					'username_input' => ( $session->sess_info['user_id'] ) ? '<a href="'.$functions->make_url('profile.php', array('id' => $session->sess_info['user_info']['id'])).'">'.unhtml(stripslashes($session->sess_info['user_info']['displayed_name'])).'</a>' : '<input type="text" size="25" maxlength="255" name="user" value="'.$username.'" />',
					'content_input' => '<textarea rows="'.$template->get_config('quick_reply_textarea_rows').'" cols="'.$template->get_config('quick_reply_textarea_cols').'" name="content" accesskey="q"></textarea>',
					'submit_button' => '<input type="submit" name="submit" value="'.$lang['OK'].'" accesskey="s" /><input type="hidden" name="enable_bbcode" value="1" /><input type="hidden" name="enable_smilies" value="1" /><input type="hidden" name="enable_sig" value="1" /><input type="hidden" name="subscribe_topic" value="'.$subscribe_topic.'" />',
					'preview_button' => '<input type="submit" name="preview" value="'.$lang['Preview'].'" />',
					'form_end' => '</form>'
				), false, true);
				
			}
			
			//
			// Avatar helper Javascript function
			//
			if ( $avatars_found && ( $avatars_force_width > 0 || $avatars_force_height > 0 ) )
				$template->set_js_onload('resize_avatars('.$avatars_force_width.','.$avatars_force_height.')');
			
			$_SESSION['viewed_topics']['t'.$requested_topic] = time();
			
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
	$functions->redirect('index.php');
	
}

?>
