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

if ( !($result = $db->query("SELECT t.topic_title, t.status_locked, t.forum_id, t.count_replies, f.id AS forum_id, f.name AS forum_name, f.status AS forum_status, f.auth, f.auto_lock, f.increase_post_count FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = ".$_GET['topic']." AND f.id = t.forum_id")) )
	$functions->usebb_die('SQL', 'Unable to get topic information!', __FILE__, __LINE__);

if ( !$db->num_rows($result) ) {
	
	//
	// This topic does not exist, show an error
	//
	$template->set_page_title($lang['Error']);
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Error'],
		'content' => sprintf($lang['NoSuchTopic'], 'ID '.$_GET['topic'])
	));
	
} else {
	
	$topicdata = $db->fetch_result($result);
	
	if ( $topicdata['status_locked'] && !$functions->auth($topicdata['auth'], 'lock', $topicdata['forum_id']) ) {
		
		$template->set_page_title($lang['TopicIsLocked']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['TopicIsLocked'],
			'content' => $lang['TopicIsLockedExplain']
		));
		
	} elseif ( !$topicdata['forum_status'] && $functions->get_user_level() != 3 ) {
		
		$template->set_page_title($lang['ForumIsLocked']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['ForumIsLocked'],
			'content' => $lang['ForumIsLockedExplain']
		));
		
	} elseif ( $functions->auth($topicdata['auth'], 'reply', $topicdata['forum_id']) ) {
		
		$_POST['user'] = ( !empty($_POST['user']) ) ? $_POST['user'] : '';
		$_POST['user'] = preg_replace('/ +/', ' ', $_POST['user']);
		
		if ( $session->sess_info['user_id'] ) {
			
			if ( !($result = $db->query("SELECT topic_id FROM ".TABLE_PREFIX."subscriptions WHERE topic_id = ".$_GET['topic']." AND user_id = ".$session->sess_info['user_id'])) )
				$functions->usebb_die('SQL', 'Unable to get subscription information!', __FILE__, __LINE__);
			
			$subscribed = ( !$db->num_rows($result) ) ? false : true;
			
		}
		
		if ( ( $session->sess_info['user_id'] || ( !empty($_POST['user']) && preg_match(USER_PREG, $_POST['user']) && strlen($_POST['user']) <= $functions->get_config('username_max_length') ) ) && !empty($_POST['content']) && empty($_POST['preview']) ) {
			
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
			
			if ( !($result = $db->query("INSERT INTO ".TABLE_PREFIX."posts VALUES(NULL, ".$_GET['topic'].", ".$poster_id.", '".$poster_guest."', '".$session->sess_info['ip_addr']."', '".$_POST['content']."', ".time().", 0, 0, ".$_POST['enable_bbcode'].", ".$_POST['enable_smilies'].", ".$_POST['enable_sig'].", ".$_POST['enable_html'].")")) )
				$functions->usebb_die('SQL', 'Unable to insert new post!', __FILE__, __LINE__);
			
			$inserted_post_id = $db->last_id();
			$update_topic_status = ( ( $functions->auth($topicdata['auth'], 'lock', $topicdata['forum_id']) && !empty($_POST['lock_topic']) ) || ( $topicdata['auto_lock'] && $topicdata['count_replies']+1 >= $topicdata['auto_lock'] ) ) ? ', status_locked = 1' : '';
			
			if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."topics SET last_post_id = ".$inserted_post_id.", count_replies = count_replies+1".$update_topic_status." WHERE id = ".$_GET['topic'])) )
				$functions->usebb_die('SQL', 'Unable to update topic!', __FILE__, __LINE__);
			
			if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."forums SET posts = posts+1, last_topic_id = ".$_GET['topic']." WHERE id = ".$topicdata['forum_id'])) )
				$functions->usebb_die('SQL', 'Unable to update forum!', __FILE__, __LINE__);
			
			if ( $session->sess_info['user_id'] && $topicdata['increase_post_count'] ) {
				
				if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."members SET posts = posts+1 WHERE id = ".$session->sess_info['user_id'])) )
					$functions->usebb_die('SQL', 'Unable to update user!', __FILE__, __LINE__);
				
			}
			
			if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."stats SET content = content+1 WHERE name = 'posts'")) )
				$functions->usebb_die('SQL', 'Unable to update stats!', __FILE__, __LINE__);
			
			//
			// E-mail subscribed users
			//
			if ( !($result = $db->query("SELECT s.user_id, u.email FROM ".TABLE_PREFIX."subscriptions s, ".TABLE_PREFIX."members u WHERE s.topic_id = ".$_GET['topic']." AND u.id = s.user_id")) )
				$functions->usebb_die('SQL', 'Unable to get subscribed users!', __FILE__, __LINE__);			
			if ( $db->num_rows($result) ) {
				
				while ( $subscribed_users = $db->fetch_result($result) ) {
					
					$functions->usebb_mail(sprintf($lang['NewReplyEmailSubject'], stripslashes($topicdata['topic_title'])), $lang['NewReplyEmailBody'], array(
						'poster_name' => ( $session->sess_info['user_id'] ) ? stripslashes($session->sess_info['user_info']['name']) : stripslashes($poster_guest),
						'topic_title' => stripslashes($topicdata['topic_title']),
						'topic_link' => $functions->get_config('board_url').$functions->make_url('topic.php', array('post' => $inserted_post_id), false).'#post'.$inserted_post_id,
						'unsubscribe_link' => $functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'], 'act' => 'unsubscribe'), false)
					), $functions->get_config('board_name'), $functions->get_config('admin_email'), $subscribed_users['email']);
					
				}
				
			}
			
			//
			// Subscribe user to topic
			//
			if ( $session->sess_info['user_id'] && !$subscribed && !empty($_POST['subscribe_topic']) ) {
				
				if ( !($result = $db->query("INSERT INTO ".TABLE_PREFIX."subscriptions VALUES(".$_GET['topic'].", ".$session->sess_info['user_id'].")")) )
					$functions->usebb_die('SQL', 'Unable to subscribe user to topic!', __FILE__, __LINE__);		
				
			}
			
			if ( $functions->get_config('return_to_topic_after_posting') )
				header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('post' => $inserted_post_id), false).'#post'.$inserted_post_id);
			else
				header('Location: '.$functions->get_config('board_url').$functions->make_url('forum.php', array('id' => $topicdata['forum_id']), false));
			
		} else {
			
			$template->set_page_title(sprintf($lang['ReplyTo'], htmlspecialchars(stripslashes($topicdata['topic_title']))));
			
			$location_bar = '<a href="'.$functions->make_url('index.php').'">'.htmlspecialchars($functions->get_config('board_name')).'</a> '.$template->get_config('locationbar_item_delimiter').' <a href="'.$functions->make_url('forum.php', array('id' => $topicdata['forum_id'])).'">'.htmlspecialchars(stripslashes($topicdata['forum_name'])).'</a> '.$template->get_config('locationbar_item_delimiter').' <a href="'.$functions->make_url('topic.php', array('id' => $_GET['topic'])).'">'.htmlspecialchars(stripslashes($topicdata['topic_title'])).'</a> '.$template->get_config('locationbar_item_delimiter').' '.$lang['PostReply'];
			$template->parse('location_bar', 'global', array(
				'location_bar' => $location_bar
			));
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
					
					$template->parse('msgbox', 'global', array(
						'box_title' => $lang['Preview'],
						'content' => $functions->markup(stripslashes($_POST['content']), $enable_bbcode_checked, $enable_smilies_checked, $enable_html_checked)
					));
					
				}
				
			} else {
				
				//
				// Get session saved guest's username if there is one
				//
				$_POST['user'] = ( !$session->sess_info['user_id'] && !empty($_SESSION['user']) ) ? $_SESSION['user'] : '';
				
				if ( !empty($_GET['quotepost']) && is_numeric($_GET['quotepost']) ) {
					
					if ( !($result = $db->query("SELECT p.content, p.poster_guest, u.name FROM ( ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id ) WHERE p.id = ".$_GET['quotepost'])) )
						$functions->usebb_die('SQL', 'Unable to get quoted post!', __FILE__, __LINE__);
					
					if ( $db->num_rows($result) ) {
						
						$quoteddata = $db->fetch_result($result);
						
						$quoteduser = ( !empty($quoteddata['name']) ) ? $quoteddata['name'] : $quoteddata['poster_guest'];
						$quotedpost = stripslashes($quoteddata['content']);
						
						$_POST['content'] = '[quote='.$quoteduser.']'.$quotedpost.'[/quote]';
						
					}
					
				}
				
				$enable_bbcode_checked = ' checked="checked"';
				$enable_smilies_checked = ' checked="checked"';
				$enable_sig_checked = ' checked="checked"';
				$enable_html_checked = '';
				$lock_topic_checked = '';
				$subscribe_topic_checked = '';
				
			}
			
			$_POST['user'] = ( !empty($_POST['user']) && preg_match(USER_PREG, $_POST['user']) ) ? $_POST['user'] : '';
			$_POST['content'] = ( !empty($_POST['content']) ) ? htmlspecialchars(stripslashes($_POST['content'])) : '';
			
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
				'username' => $lang['Username'],
				'username_input' => ( $session->sess_info['user_id'] ) ? '<a href="'.$functions->make_url('profile.php', array('id' => $session->sess_info['user_info']['id'])).'">'.htmlspecialchars(stripslashes($session->sess_info['user_info']['name'])).'</a>' : '<input type="text" size="25" maxlength="'.$functions->get_config('username_max_length').'" name="user" value="'.htmlspecialchars(stripslashes($_POST['user'])).'" />',
				'subject' => $lang['Subject'],
				'subject_input' => '<a href="'.$functions->make_url('topic.php', array('id' => $_GET['topic'])).'">'.htmlspecialchars(stripslashes($topicdata['topic_title'])).'</a>',
				'content' => $lang['Content'],
				'content_input' => '<textarea rows="'.$template->get_config('textarea_rows').'" cols="'.$template->get_config('textarea_cols').'" name="content">'.$_POST['content'].'</textarea>',
				'options' => $lang['Options'],
				'options_input' => $options_input,
				'submit_button' => '<input type="submit" name="submit" value="'.$lang['OK'].'" />',
				'preview_button' => '<input type="submit" name="preview" value="'.$lang['Preview'].'" />',
				'reset_button' => '<input type="reset" value="'.$lang['Reset'].'" />',
				'form_end' => '</form>'
			));
			
			$template->parse('location_bar', 'global', array(
				'location_bar' => $location_bar
			));
			
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
