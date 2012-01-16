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
 * Post reply
 *
 * Gives an interface to post replies.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2012 UseBB Team
 * @package	UseBB
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
	header(HEADER_404);
	$template->add_breadcrumb($lang['Error']);
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Error'],
		'content' => sprintf($lang['NoSuchTopic'], 'ID '.$_GET['topic'])
	));
	
} else {
	
	$topic_title = unhtml($functions->replace_badwords(stripslashes($topicdata['topic_title'])));
	
	$template->add_breadcrumb(
		unhtml(stripslashes($topicdata['forum_name'])), 
		array('forum.php', array('id' => $topicdata['forum_id']))
	);
	$template->add_breadcrumb(
		$topic_title, 
		array('topic.php', array('id' => $_GET['topic'])) 
	);
	$template->add_breadcrumb($lang['PostReply']);
	
	if ( $topicdata['status_locked'] && !$functions->auth($topicdata['auth'], 'lock', $topicdata['forum_id']) ) {
		
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['TopicIsLocked'],
			'content' => $lang['TopicIsLockedExplain']
		));
		
	} elseif ( !$topicdata['forum_status'] && $functions->get_user_level() != LEVEL_ADMIN ) {
		
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['ForumIsLocked'],
			'content' => $lang['ForumIsLockedExplain']
		));
		
	} elseif ( $functions->auth($topicdata['auth'], 'reply', $topicdata['forum_id']) ) {
		
		//
		// Pose the antispam question
		//
		$functions->pose_antispam_question();
		
		$_POST['user'] = ( !empty($_POST['user']) ) ? preg_replace('#\s+#', ' ', $_POST['user']) : '';
		
		if ( $session->sess_info['user_id'] ) {
			
			$result = $db->query("SELECT COUNT(*) as subscribed FROM ".TABLE_PREFIX."subscriptions WHERE topic_id = ".$_GET['topic']." AND user_id = ".$session->sess_info['user_id']);
			$subscribed = $db->fetch_result($result);
			$subscribed = ( !$subscribed['subscribed'] ) ? false : true;
			
		}
		
		$flood_protect_wait_sec = ( $functions->get_user_level() <= LEVEL_MEMBER ) ? ( $functions->get_config('flood_interval') - ( time() - $_SESSION['latest_post'] ) ) : 0;
		
		if ( ( $session->sess_info['user_id'] || ( !empty($_POST['user']) && entities_strlen($_POST['user']) >= $functions->get_config('username_min_length') && entities_strlen($_POST['user']) <= $functions->get_config('username_max_length') ) ) && !$functions->post_empty($_POST['content']) && empty($_POST['preview']) && $flood_protect_wait_sec <= 0 && $functions->verify_form() ) {
			
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
				
				$result = $db->query("UPDATE ".TABLE_PREFIX."members SET posts = posts+1, active = ".$functions->user_active_value($session->sess_info['user_info'], TRUE)." WHERE id = ".$session->sess_info['user_id']);
				
			}
			
			$functions->set_stats('posts', 1, true);
			
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
					), $functions->get_config('board_name'), $functions->get_config('admin_email'), $subscribed_user['email'], null, $subscribed_user['language'], $user_lang['character_encoding']);
					
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
			$_SESSION['viewed_topics']['t'.$_GET['topic']] = time();
			$_SESSION['latest_post'] = time();
			
			if ( $functions->get_config('return_to_topic_after_posting') )
				$functions->redirect('topic.php', array('post' => $inserted_post_id), 'post'.$inserted_post_id);
			else
				$functions->redirect('forum.php', array('id' => $topicdata['forum_id']));
			
		} else {
			
			$can_post_links = $functions->antispam_can_post_links($session->sess_info['user_info'], TRUE);
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				
				$enable_bbcode_checked = ( !empty($_POST['enable_bbcode']) ) ? ' checked="checked"' : '';
				$enable_smilies_checked = ( !empty($_POST['enable_smilies']) ) ? ' checked="checked"' : '';
				$enable_sig_checked = ( !empty($_POST['enable_sig']) ) ? ' checked="checked"' : '';
				$enable_html_checked = ( !empty($_POST['enable_html']) ) ? ' checked="checked"' : '';
				$lock_topic_checked = ( !empty($_POST['lock_topic']) ) ? ' checked="checked"' : '';
				$subscribe_topic_checked = ( !empty($_POST['subscribe_topic']) ) ? ' checked="checked"' : '';
				
				$errors = array();
				if ( !$session->sess_info['user_id'] && empty($_POST['user']) )
					$errors[] = $lang['Username'];
				if ( $functions->post_empty($_POST['content']) )
					$errors[] = $lang['Content'];
				
				if ( count($errors) ) {
					
					$template->parse('msgbox', 'global', array(
						'box_title' => $lang['Error'],
						'content' => sprintf($lang['MissingFields'], join(', ', $errors))
					));
					
				}
				
				if ( !$session->sess_info['user_id'] && !empty($_POST['user']) && entities_strlen($_POST['user']) < $functions->get_config('username_min_length') ) {
					
					$template->parse('msgbox', 'global', array(
						'box_title' => $lang['Error'],
						'content' => sprintf($lang['StringTooShort'], $lang['Username'], $functions->get_config('username_min_length'))
					));
					
				}
				
				if ( !$session->sess_info['user_id'] && !empty($_POST['user']) && entities_strlen($_POST['user']) > $functions->get_config('username_max_length') ) {
					
					$template->parse('msgbox', 'global', array(
						'box_title' => $lang['Error'],
						'content' => sprintf($lang['StringTooLong'], $lang['Username'], $functions->get_config('username_max_length'))
					));
					
				}
				
				if ( !empty($_POST['preview']) && !$functions->post_empty($_POST['content']) ) {
					
					$template->parse('preview', 'various', array(
						'post_content' => $functions->markup(stripslashes($_POST['content']), $enable_bbcode_checked, $enable_smilies_checked, $enable_html_checked, NULL, $can_post_links)
					));
					
				} elseif ( $flood_protect_wait_sec > 0 ) {
					
					$template->parse('msgbox', 'global', array(
						'box_title' => $lang['Note'],
						'content' => sprintf($lang['FloodIntervalWarning'], $functions->get_config('flood_interval'), $flood_protect_wait_sec)
					));
					
				}
				
			} else {
				
				//
				// Get session saved guest's username if there is one
				//
				$_POST['user'] = ( !$session->sess_info['user_id'] && !empty($_SESSION['user']) ) ? $_SESSION['user'] : '';
				
				if ( !empty($_GET['quotepost']) && valid_int($_GET['quotepost']) ) {
					
					$result = $db->query("SELECT p.id, p.content, p.poster_guest, u.displayed_name FROM ( ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id ) WHERE p.id = ".$_GET['quotepost']." AND p.topic_id = ".$_GET['topic']);
					$quoteddata = $db->fetch_result($result);
					
					if ( !empty($quoteddata['id']) ) {
						
						$quoteduser = ( !empty($quoteddata['displayed_name']) ) ? $quoteddata['displayed_name'] : $quoteddata['poster_guest'];
						$quotedpost = $functions->replace_badwords(stripslashes($quoteddata['content']));
						$_POST['content'] = '[quote='.str_replace(array('[', ']'), '', $quoteduser).']'."\n".$quotedpost."\n".'[/quote]'."\n";
						
					}
					
				}
				
				$enable_bbcode_checked = ' checked="checked"';
				$enable_smilies_checked = ' checked="checked"';
				$enable_sig_checked = ' checked="checked"';
				$enable_html_checked = '';
				$lock_topic_checked = '';
				$subscribe_topic_checked = ( $session->sess_info['user_id'] && $session->sess_info['user_info']['auto_subscribe_reply'] ) ? ' checked="checked"' : '';
				
				if ( !$session->sess_info['user_id'] && empty($_POST['user']) )
					$template->set_js_onload("set_focus('user')");
				else
					$template->set_js_onload("set_focus('tags-txtarea')");
				
			}
			
			$_POST['user'] = ( !empty($_POST['user']) ) ? $_POST['user'] : '';
			$_POST['subject'] = ( !empty($_POST['subject']) ) ? $_POST['subject'] : '';
			$_POST['content'] = ( !$functions->post_empty($_POST['content']) ) ? $_POST['content'] : '';
			
			$options_input = array();
			$options_input[] = '<label><input type="checkbox" name="enable_bbcode" value="1"'.$enable_bbcode_checked.' /> '.$lang['EnableBBCode'].'</label>';
			$options_input[] = '<label><input type="checkbox" name="enable_smilies" value="1"'.$enable_smilies_checked.' /> '.$lang['EnableSmilies'].'</label>';
			if ( $session->sess_info['user_id'] && !empty($session->sess_info['user_info']['signature']) )
				$options_input[] = '<label><input type="checkbox" name="enable_sig" value="1"'.$enable_sig_checked.' /> '.$lang['EnableSig'].'</label>';
			if ( $functions->auth($topicdata['auth'], 'html', $topicdata['forum_id']) )
				$options_input[] = '<label><input type="checkbox" name="enable_html" value="1"'.$enable_html_checked.' /> '.$lang['EnableHTML'].'</label>';
			if ( !$topicdata['status_locked'] && $functions->auth($topicdata['auth'], 'lock', $topicdata['forum_id']) )
				$options_input[] = '<label><input type="checkbox" name="lock_topic" value="1"'.$lock_topic_checked.' /> '.$lang['LockTopicAfterPost'].'</label>';
			if ( $session->sess_info['user_id'] && !$subscribed )
				$options_input[] = '<label><input type="checkbox" name="subscribe_topic" value="1"'.$subscribe_topic_checked.' /> '.$lang['SubscribeToThisTopic'].'</label>';
			$options_input = '<div>'.join('</div><div>', $options_input).'</div>';
			
			$template->parse('post_form', 'various', array(
				'form_begin' => '<form action="'.$functions->make_url('post.php', array('topic' => $_GET['topic'])).'" method="post">',
				'post_title' => $lang['PostReply'],
				'username_input' => ( $session->sess_info['user_id'] ) ? '<a href="'.$functions->make_url('profile.php', array('id' => $session->sess_info['user_info']['id'])).'">'.unhtml(stripslashes($session->sess_info['user_info']['displayed_name'])).'</a>' : '<input type="text" size="25" maxlength="'.$functions->get_config('username_max_length').'" name="user" id="user" value="'.unhtml(stripslashes($_POST['user'])).'" tabindex="1" />',
				'subject_input' => '<a href="'.$functions->make_url('topic.php', array('id' => $_GET['topic'])).'">'.$topic_title.'</a>',
				'content_input' => '<textarea rows="'.$template->get_config('textarea_rows').'" cols="'.$template->get_config('textarea_cols').'" name="content" id="tags-txtarea" tabindex="2">'.unhtml(stripslashes($_POST['content'])).'</textarea>',
				'potential_spammer_notice' => $can_post_links ? '' : '<div class="potential-spammer-notice">'.$lang['PotentialSpammerNoPostLinks'].'</div>',
				'bbcode_controls' => $functions->get_bbcode_controls($can_post_links),
				'smiley_controls' => $functions->get_smiley_controls(),
				'options_input' => $options_input,
				'submit_button' => '<input type="submit" name="submit" value="'.$lang['OK'].'" tabindex="4" accesskey="s" />',
				'preview_button' => '<input type="submit" name="preview" value="'.$lang['Preview'].'" tabindex="3" />',
				'form_end' => '</form>'
			), false, true);
			
			if ( $functions->get_config('topicreview_posts') ) {
				
				//
				// Topic review feature
				//
				$result = $db->query("SELECT p.poster_id, u.displayed_name, u.level, u.active, p.poster_guest, p.post_time, p.content, p.enable_bbcode, p.enable_smilies, p.enable_sig, p.enable_html FROM ( ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id ), ".TABLE_PREFIX."topics t WHERE t.id = ".$_GET['topic']." AND p.topic_id = t.id ORDER BY p.post_time DESC LIMIT ".$functions->get_config('topicreview_posts'));
				
				$view_more_posts = ( $topicdata['count_replies']+1 > $functions->get_config('topicreview_posts') ) ? '<a href="'.$functions->make_url('topic.php', array('id' => $_GET['topic'])).'" target="topicreview">'.$lang['ViewMorePosts'].'</a>' : '';
				$template->parse('header', 'topicreview', array(
					'view_more_posts' => $view_more_posts
				));
				
				$colornum = 1;				
				while ( $postsdata = $db->fetch_result($result) ) {
					
					$template->parse('post', 'topicreview', array(
						'poster_name' => ( !empty($postsdata['poster_id']) ) ? unhtml(stripslashes($postsdata['displayed_name'])) : unhtml(stripslashes($postsdata['poster_guest'])),
						'post_date' => $functions->make_date($postsdata['post_time']),
						'post_content' => $functions->markup($functions->replace_badwords(stripslashes($postsdata['content'])), $postsdata['enable_bbcode'], $postsdata['enable_smilies'], $postsdata['enable_html'], NULL, $functions->antispam_can_post_links($postsdata)),
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
