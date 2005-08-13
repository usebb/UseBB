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

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

//
// Update and get the session information
//
$session->update('reply:'.$_GET['topic']);

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

$result = $db->query("SELECT t.id, t.topic_title, t.status_locked, t.forum_id, t.count_replies, f.id AS forum_id, f.name AS forum_name, f.status AS forum_status, f.auth, f.auto_lock, f.increase_post_count FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = ".$_GET['topic']." AND f.id = t.forum_id");
$topicdata = $db->fetch_result($result);

if ( !$topicdata['id'] ) {
	
	//
	// This topic does not exist, show an error
	//
	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
	$template->set_page_title($lang['Error']);
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Error'],
		'content' => sprintf($lang['NoSuchTopic'], 'ID '.$_GET['topic'])
	));
	
} else {
	
	if ( $topicdata['status_locked'] && !$functions->auth($topicdata['auth'], 'lock', $topicdata['forum_id']) ) {
		
		$template->set_page_title($lang['TopicIsLocked']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['TopicIsLocked'],
			'content' => $lang['TopicIsLockedExplain']
		));
		
	} elseif ( !$topicdata['forum_status'] && $functions->get_user_level() != LEVEL_ADMIN ) {
		
		$template->set_page_title($lang['ForumIsLocked']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['ForumIsLocked'],
			'content' => $lang['ForumIsLockedExplain']
		));
		
	} elseif ( $functions->auth($topicdata['auth'], 'reply', $topicdata['forum_id']) ) {
		
		$_POST['user'] = ( !empty($_POST['user']) ) ? preg_replace('#\s+#', '_', $_POST['user']) : '';
		
		if ( $session->sess_info['user_id'] ) {
			
			$result = $db->query("SELECT COUNT(*) as subscribed FROM ".TABLE_PREFIX."subscriptions WHERE topic_id = ".$_GET['topic']." AND user_id = ".$session->sess_info['user_id']);
			$subscribed = $db->fetch_result($result);
			$subscribed = ( !$subscribed['subscribed'] ) ? false : true;
			
		}
		
		if ( ( $session->sess_info['user_id'] || ( !empty($_POST['user']) && preg_match(USER_PREG, $_POST['user']) && strlen($_POST['user']) <= $functions->get_config('username_max_length') ) ) && !empty($_POST['content']) && empty($_POST['preview']) && ( time() > $_SESSION['latest_post'] + $functions->get_config('flood_interval') || $functions->get_user_level() > LEVEL_MEMBER ) ) {
			
			//
			// Save the guest's username in the session
			//
			if ( !$session->sess_info['user_id'] )
				$_SESSION['user'] = $_POST['user'];
			
			$poster_id = ( $session->sess_info['user_id'] ) ? $session->sess_info['user_id'] : 0;
			$poster_guest = ( !$session->sess_info['user_id'] ) ? $_POST['user'] : '';
			$_POST['enable_bbcode'] = ( !empty($_POST['enable_bbcode']) ) ? 1 : 0;
			$_POST['enable_smilies'] = ( !empty($_POST['enable_smilies']) ) ? 1 : 0;
			$_POST['enable_sig'] = ( $session->sess_info['user_id'] && !empty($session->sess_info['user_info']['signature']) && !empty($_POST['enable_sig']) ) ? 1 : 0;
			$_POST['enable_html'] = ( $functions->auth($topicdata['auth'], 'html', $topicdata['forum_id']) && !empty($_POST['enable_html']) ) ? 1 : 0;
			
			$result = $db->query("INSERT INTO ".TABLE_PREFIX."posts VALUES(NULL, ".$_GET['topic'].", ".$poster_id.", '".$poster_guest."', '".$session->sess_info['ip_addr']."', '".$_POST['content']."', ".time().", 0, 0, ".$_POST['enable_bbcode'].", ".$_POST['enable_smilies'].", ".$_POST['enable_sig'].", ".$_POST['enable_html'].")");
			
			$inserted_post_id = $db->last_id();
			$update_topic_status = ( ( $functions->auth($topicdata['auth'], 'lock', $topicdata['forum_id']) && !empty($_POST['lock_topic']) ) || ( $topicdata['auto_lock'] && $topicdata['count_replies']+1 >= $topicdata['auto_lock'] ) ) ? ', status_locked = 1' : '';
			
			$result = $db->query("UPDATE ".TABLE_PREFIX."topics SET last_post_id = ".$inserted_post_id.", count_replies = count_replies+1".$update_topic_status." WHERE id = ".$_GET['topic']);
			
			$result = $db->query("UPDATE ".TABLE_PREFIX."forums SET posts = posts+1, last_topic_id = ".$_GET['topic']." WHERE id = ".$topicdata['forum_id']);
			
			if ( $session->sess_info['user_id'] && $topicdata['increase_post_count'] ) {
				
				$result = $db->query("UPDATE ".TABLE_PREFIX."members SET posts = posts+1 WHERE id = ".$session->sess_info['user_id']);
				
			}
			
			$result = $db->query("UPDATE ".TABLE_PREFIX."stats SET content = content+1 WHERE name = 'posts'");
			
			if ( !$functions->get_config('disable_info_emails') ) {
				
				//
				// E-mail subscribed users
				//
				$result = $db->query("SELECT s.user_id AS id, u.level, u.email, u.language FROM ".TABLE_PREFIX."subscriptions s, ".TABLE_PREFIX."members u WHERE s.topic_id = ".$_GET['topic']." AND u.id = s.user_id AND s.user_id <> ".$session->sess_info['user_id']);			
				while ( $subscribed_user = $db->fetch_result($result) ) {
					
					if ( $functions->auth($topicdata['auth'], 'read', $topicdata['forum_id'], false, $subscribed_user) ) {
						
						//
						// Fetch the language of the user
						//
						$user_lang = $functions->fetch_language($subscribed_user['language']);
						
						$functions->usebb_mail(sprintf($user_lang['NewReplyEmailSubject'], stripslashes($topicdata['topic_title'])), $user_lang['NewReplyEmailBody'], array(
							'poster_name' => ( $session->sess_info['user_id'] ) ? stripslashes($session->sess_info['user_info']['displayed_name']) : stripslashes($poster_guest),
							'topic_title' => stripslashes($topicdata['topic_title']),
							'topic_link' => $functions->get_config('board_url').$functions->make_url('topic.php', array('post' => $inserted_post_id), false).'#post'.$inserted_post_id,
							'unsubscribe_link' => $functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'], 'act' => 'unsubscribe'), false)
						), $functions->get_config('board_name'), $functions->get_config('admin_email'), $subscribed_user['email'], $subscribed_user['language'], $user_lang['character_encoding']);
						
					}
					
				}
				
			}
			
			//
			// Subscribe user to topic
			//
			if ( $session->sess_info['user_id'] && !$subscribed && !empty($_POST['subscribe_topic']) ) {
				
				$result = $db->query("INSERT INTO ".TABLE_PREFIX."subscriptions VALUES(".$_GET['topic'].", ".$session->sess_info['user_id'].")");		
				
			}
			
			//
			// This topic should be viewed
			//
			$_SESSION['viewed_topics'][$_GET['topic']] = time();
			$_SESSION['latest_post'] = time();
			
			if ( $functions->get_config('return_to_topic_after_posting') )
				$functions->redirect('topic.php', array('post' => $inserted_post_id), 'post'.$inserted_post_id);
			else
				$functions->redirect('forum.php', array('id' => $topicdata['forum_id']));
			
		} else {
			
			$topic_title = unhtml($functions->replace_badwords(stripslashes($topicdata['topic_title'])));
			
			$template->set_page_title('<a href="'.$functions->make_url('forum.php', array('id' => $topicdata['forum_id'])).'">'.unhtml(stripslashes($topicdata['forum_name'])).'</a>'.$template->get_config('locationbar_item_delimiter').'<a href="'.$functions->make_url('topic.php', array('id' => $_GET['topic'])).'">'.$topic_title.'</a>'.$template->get_config('locationbar_item_delimiter').$lang['PostReply']);
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				
				$enable_bbcode_checked = ( !empty($_POST['enable_bbcode']) ) ? ' checked="checked"' : '';
				$enable_smilies_checked = ( !empty($_POST['enable_smilies']) ) ? ' checked="checked"' : '';
				$enable_sig_checked = ( !empty($_POST['enable_sig']) ) ? ' checked="checked"' : '';
				$enable_html_checked = ( !empty($_POST['enable_html']) ) ? ' checked="checked"' : '';
				$lock_topic_checked = ( !empty($_POST['lock_topic']) ) ? ' checked="checked"' : '';
				$subscribe_topic_checked = ( !empty($_POST['subscribe_topic']) ) ? ' checked="checked"' : '';
				
				$errors = array();
				if ( !$session->sess_info['user_id'] && ( empty($_POST['user']) || !preg_match(USER_PREG, $_POST['user']) ) )
					$errors[] = $lang['Username'];
				if ( empty($_POST['content']) )
					$errors[] = $lang['Content'];
				
				if ( count($errors) ) {
					
					$template->parse('msgbox', 'global', array(
						'box_title' => $lang['Error'],
						'content' => sprintf($lang['MissingFields'], join(', ', $errors))
					));
					
				} elseif ( !empty($_POST['preview']) ) {
					
					$template->parse('preview', 'various', array(
						'post_content' => $functions->markup(stripslashes($_POST['content']), $enable_bbcode_checked, $enable_smilies_checked, $enable_html_checked)
					));
					
				} elseif ( time() <= $_SESSION['latest_post'] + $functions->get_config('flood_interval') ) {
					
					$template->parse('msgbox', 'global', array(
						'box_title' => $lang['Note'],
						'content' => sprintf($lang['FloodIntervalWarning'], $functions->get_config('flood_interval'))
					));
					
				}
				
			} else {
				
				//
				// Get session saved guest's username if there is one
				//
				$_POST['user'] = ( !$session->sess_info['user_id'] && !empty($_SESSION['user']) ) ? $_SESSION['user'] : '';
				
				if ( !empty($_GET['quotepost']) && valid_int($_GET['quotepost']) ) {
					
					$result = $db->query("SELECT p.content, p.poster_guest, u.displayed_name FROM ( ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id ) WHERE p.id = ".$_GET['quotepost']." AND p.topic_id = ".$_GET['topic']);
					$quoteddata = $db->fetch_result($result);
					$quoteduser = ( !empty($quoteddata['displayed_name']) ) ? $quoteddata['displayed_name'] : $quoteddata['poster_guest'];
					$quotedpost = $functions->replace_badwords(stripslashes($quoteddata['content']));
					$_POST['content'] = '[quote='.$quoteduser.']'.$quotedpost.'[/quote]';
					
				}
				
				$enable_bbcode_checked = ' checked="checked"';
				$enable_smilies_checked = ' checked="checked"';
				$enable_sig_checked = ' checked="checked"';
				$enable_html_checked = '';
				$lock_topic_checked = '';
				$subscribe_topic_checked = ( $session->sess_info['user_id'] && $session->sess_info['user_info']['auto_subscribe_reply'] ) ? ' checked="checked"' : '';
				
				if ( $session->sess_info['user_id'] )
					$template->set_js_onload("set_focus('tags-txtarea')");
				else
					$template->set_js_onload("set_focus('user')");
				
			}
			
			$_POST['user'] = ( !empty($_POST['user']) && preg_match(USER_PREG, $_POST['user']) ) ? $_POST['user'] : '';
			$_POST['content'] = ( !empty($_POST['content']) ) ? unhtml(stripslashes($_POST['content'])) : '';
			
			$options_input = array();
			$options_input[] = '<input type="checkbox" name="enable_bbcode" id="enable_bbcode" value="1"'.$enable_bbcode_checked.' /><label for="enable_bbcode"> '.$lang['EnableBBCode'].'</label>';
			$options_input[] = '<input type="checkbox" name="enable_smilies" id="enable_smilies" value="1"'.$enable_smilies_checked.' /><label for="enable_smilies"> '.$lang['EnableSmilies'].'</label>';
			if ( $session->sess_info['user_id'] && !empty($session->sess_info['user_info']['signature']) )
				$options_input[] = '<input type="checkbox" name="enable_sig" id="enable_sig" value="1"'.$enable_sig_checked.' /><label for="enable_sig"> '.$lang['EnableSig'].'</label>';
			if ( $functions->auth($topicdata['auth'], 'html', $topicdata['forum_id']) )
				$options_input[] = '<input type="checkbox" name="enable_html" id="enable_html" value="1"'.$enable_html_checked.' /><label for="enable_html"> '.$lang['EnableHTML'].'</label>';
			if ( !$topicdata['status_locked'] && $functions->auth($topicdata['auth'], 'lock', $topicdata['forum_id']) )
				$options_input[] = '<input type="checkbox" name="lock_topic" id="lock_topic" value="1"'.$lock_topic_checked.' /><label for="lock_topic"> '.$lang['LockTopicAfterPost'].'</label>';
			if ( $session->sess_info['user_id'] && !$subscribed )
				$options_input[] = '<input type="checkbox" name="subscribe_topic" id="subscribe_topic" value="1"'.$subscribe_topic_checked.' /><label for="subscribe_topic"> '.$lang['SubscribeToThisTopic'].'</label>';
			$options_input = join('<br />', $options_input);
			
			$template->parse('post_form', 'various', array(
				'form_begin' => '<form action="'.$functions->make_url('post.php', array('topic' => $_GET['topic'])).'" method="post">',
				'post_title' => $lang['PostReply'],
				'username_input' => ( $session->sess_info['user_id'] ) ? '<a href="'.$functions->make_url('profile.php', array('id' => $session->sess_info['user_info']['id'])).'">'.unhtml(stripslashes($session->sess_info['user_info']['displayed_name'])).'</a>' : '<input type="text" size="25" maxlength="'.$functions->get_config('username_max_length').'" name="user" id="user" value="'.unhtml(stripslashes($_POST['user'])).'" tabindex="1" />',
				'subject_input' => '<a href="'.$functions->make_url('topic.php', array('id' => $_GET['topic'])).'">'.$topic_title.'</a>',
				'content_input' => '<textarea rows="'.$template->get_config('textarea_rows').'" cols="'.$template->get_config('textarea_cols').'" name="content" id="tags-txtarea" tabindex="2">'.$_POST['content'].'</textarea>',
				'bbcode_controls' => $functions->get_bbcode_controls(),
				'smiley_controls' => $functions->get_smiley_controls(),
				'options_input' => $options_input,
				'submit_button' => '<input type="submit" name="submit" value="'.$lang['OK'].'" tabindex="3" />',
				'preview_button' => '<input type="submit" name="preview" value="'.$lang['Preview'].'" />',
				'reset_button' => '<input type="reset" value="'.$lang['Reset'].'" />',
				'form_end' => '</form>'
			));
			
			if ( $functions->get_config('topicreview_posts') ) {
				
				//
				// Topic review feature
				//
				$result = $db->query("SELECT p.poster_id, u.displayed_name, p.poster_guest, p.post_time, p.content, p.enable_bbcode, p.enable_smilies, p.enable_sig, p.enable_html FROM ( ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id ), ".TABLE_PREFIX."topics t WHERE t.id = ".$_GET['topic']." AND p.topic_id = t.id ORDER BY p.post_time DESC LIMIT ".$functions->get_config('topicreview_posts'));
				
				$view_more_posts = ( $topicdata['count_replies']+1 > $functions->get_config('topicreview_posts') ) ? '<a href="'.$functions->make_url('topic.php', array('id' => $_GET['topic'])).'" target="topicreview">'.$lang['ViewMorePosts'].'</a>' : '';
				$template->parse('header', 'topicreview', array(
					'view_more_posts' => $view_more_posts
				));
				
				$colornum = 1;				
				while ( $postsdata = $db->fetch_result($result) ) {
					
					$template->parse('post', 'topicreview', array(
						'poster_name' => ( !empty($postsdata['poster_id']) ) ? unhtml(stripslashes($postsdata['displayed_name'])) : unhtml(stripslashes($postsdata['poster_guest'])),
						'post_date' => $functions->make_date($postsdata['post_time']),
						'post_content' => $functions->markup($functions->replace_badwords(stripslashes($postsdata['content'])), $postsdata['enable_bbcode'], $postsdata['enable_smilies'], $postsdata['enable_html']),
						'colornum' => $colornum
					));
					$colornum = ( $colornum !== 1 ) ? 1 : 2;
					
				}
				
				$template->parse('footer', 'topicreview', array(
					'view_more_posts' => $view_more_posts
				));
				
			}
			
		}
		
	} else {
		
		//
		// The user is not granted to post replies in this forum
		//
		$functions->redir_to_login();
		
	}
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
