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
 * Post topic
 *
 * Gives an interface to post topics.
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
$session->update('posttopic:'.$_GET['forum']);

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

$result = $db->query("SELECT id, name, status, auth, increase_post_count FROM ".TABLE_PREFIX."forums WHERE id = ".$_GET['forum']);
$forumdata = $db->fetch_result($result);

if ( !$forumdata['id'] ) {
	
	//
	// This forum does not exist, show an error
	//
	header(HEADER_404);
	$template->add_breadcrumb($lang['Error']);
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Error'],
		'content' => sprintf($lang['NoSuchForum'], 'ID '.$_GET['forum'])
	));
	
} else {
	
	$template->add_breadcrumb(
		unhtml(stripslashes($forumdata['name'])), 
		array('forum.php', array('id' => $_GET['forum']))
	);
	$template->add_breadcrumb($lang['PostNewTopic']);
	
	if ( !$forumdata['status'] && $functions->get_user_level() != LEVEL_ADMIN ) {
		
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['ForumIsLocked'],
			'content' => $lang['ForumIsLockedExplain']
		));
		
	} elseif ( $functions->auth($forumdata['auth'], 'post', $_GET['forum']) ) {
		
		//
		// Pose the antispam question
		//
		$functions->pose_antispam_question();
		
		$_POST['user'] = ( !empty($_POST['user']) ) ? preg_replace('#\s+#', ' ', $_POST['user']) : '';
		
		$flood_protect_wait_sec = ( $functions->get_user_level() <= LEVEL_MEMBER ) ? ( $functions->get_config('flood_interval') - ( time() - $_SESSION['latest_post'] ) ) : 0;
		
		if ( ( $session->sess_info['user_id'] || ( !empty($_POST['user']) && entities_strlen($_POST['user']) >= $functions->get_config('username_min_length') && entities_strlen($_POST['user']) <= $functions->get_config('username_max_length') ) ) && !empty($_POST['subject']) && !$functions->post_empty($_POST['content']) && empty($_POST['preview']) && $flood_protect_wait_sec <= 0 && $functions->verify_form() ) {
			
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
			$_POST['enable_html'] = ( $functions->auth($forumdata['auth'], 'html', $_GET['forum']) && !empty($_POST['enable_html']) ) ? 1 : 0;
			
			$result = $db->query("INSERT INTO ".TABLE_PREFIX."posts VALUES(NULL, 0, ".$poster_id.", '".$poster_guest."', '".$session->sess_info['ip_addr']."', '".$_POST['content']."', ".time().", 0, 0, ".$_POST['enable_bbcode'].", ".$_POST['enable_smilies'].", ".$_POST['enable_sig'].", ".$_POST['enable_html'].")");
			
			$inserted_post_id = $db->last_id();
			$status_locked = ( $functions->auth($forumdata['auth'], 'lock', $_GET['forum']) && !empty($_POST['lock_topic']) ) ? 1 : 0;
			$status_sticky = ( $functions->auth($forumdata['auth'], 'sticky', $_GET['forum']) && !empty($_POST['sticky_topic']) ) ? 1 : 0;
			
			$result = $db->query("INSERT INTO ".TABLE_PREFIX."topics VALUES(NULL, ".$_GET['forum'].", '".$_POST['subject']."', ".$inserted_post_id.", ".$inserted_post_id.", 0, 0, ".$status_locked.", ".$status_sticky.")");
			
			$inserted_topic_id = $db->last_id();
			
			$result = $db->query("UPDATE ".TABLE_PREFIX."posts SET topic_id = ".$inserted_topic_id." WHERE id = ".$inserted_post_id);
			
			$result = $db->query("UPDATE ".TABLE_PREFIX."forums SET topics = topics+1, posts = posts+1, last_topic_id = ".$inserted_topic_id." WHERE id = ".$_GET['forum']);
			
			if ( $session->sess_info['user_id'] && $forumdata['increase_post_count'] ) {
				
				$result = $db->query("UPDATE ".TABLE_PREFIX."members SET posts = posts+1, active = ".$functions->user_active_value($session->sess_info['user_info'], TRUE)." WHERE id = ".$session->sess_info['user_id']);
				
			}
			
			$functions->set_stats('topics', 1, true);
			$functions->set_stats('posts', 1, true);
			
			//
			// Subscribe user to topic
			//
			if ( $session->sess_info['user_id'] && !empty($_POST['subscribe_topic']) ) {
				
				$result = $db->query("INSERT INTO ".TABLE_PREFIX."subscriptions VALUES(".$inserted_topic_id.", ".$session->sess_info['user_id'].")");		
				
			}
			
			//
			// This topic should be viewed
			//
			$_SESSION['viewed_topics']['t'.$inserted_topic_id] = time();
			$_SESSION['latest_post'] = time();
			
			if ( $functions->get_config('return_to_topic_after_posting') )
				$functions->redirect('topic.php', array('id' => $inserted_topic_id));
			else
				$functions->redirect('forum.php', array('id' => $_GET['forum']));
			
		} else {
			
			$can_post_links = $functions->antispam_can_post_links($session->sess_info['user_info'], TRUE);
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				
				$enable_bbcode_checked = ( !empty($_POST['enable_bbcode']) ) ? ' checked="checked"' : '';
				$enable_smilies_checked = ( !empty($_POST['enable_smilies']) ) ? ' checked="checked"' : '';
				$enable_sig_checked = ( !empty($_POST['enable_sig']) ) ? ' checked="checked"' : '';
				$enable_html_checked = ( !empty($_POST['enable_html']) ) ? ' checked="checked"' : '';
				$lock_topic_checked = ( !empty($_POST['lock_topic']) ) ? ' checked="checked"' : '';
				$sticky_topic_checked = ( !empty($_POST['sticky_topic']) ) ? ' checked="checked"' : '';
				$subscribe_topic_checked = ( !empty($_POST['subscribe_topic']) ) ? ' checked="checked"' : '';
				
				$errors = array();
				if ( !$session->sess_info['user_id'] && empty($_POST['user']) )
					$errors[] = $lang['Username'];
				if ( empty($_POST['subject']) )
					$errors[] = $lang['Subject'];
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
				
				$enable_bbcode_checked = ' checked="checked"';
				$enable_smilies_checked = ' checked="checked"';
				$enable_sig_checked = ' checked="checked"';
				$enable_html_checked = '';
				$lock_topic_checked = '';
				$sticky_topic_checked = '';
				$subscribe_topic_checked = ( $session->sess_info['user_id'] && $session->sess_info['user_info']['auto_subscribe_topic'] ) ? ' checked="checked"' : '';
				
				if ( !$session->sess_info['user_id'] && empty($_POST['user']) )
					$template->set_js_onload("set_focus('user')");
				else
					$template->set_js_onload("set_focus('subject')");
				
			}
			
			$_POST['user'] = ( !empty($_POST['user']) ) ? $_POST['user'] : '';
			$_POST['subject'] = ( !empty($_POST['subject']) ) ? $_POST['subject'] : '';
			$_POST['content'] = ( !$functions->post_empty($_POST['content']) ) ? $_POST['content'] : '';
			
			$options_input = array();
			$options_input[] = '<label><input type="checkbox" name="enable_bbcode" value="1"'.$enable_bbcode_checked.' /> '.$lang['EnableBBCode'].'</label>';
			$options_input[] = '<label><input type="checkbox" name="enable_smilies" value="1"'.$enable_smilies_checked.' /> '.$lang['EnableSmilies'].'</label>';
			if ( $session->sess_info['user_id'] && !empty($session->sess_info['user_info']['signature']) )
				$options_input[] = '<label><input type="checkbox" name="enable_sig" value="1"'.$enable_sig_checked.' /> '.$lang['EnableSig'].'</label>';
			if ( $functions->auth($forumdata['auth'], 'html', $_GET['forum']) )
				$options_input[] = '<label><input type="checkbox" name="enable_html" value="1"'.$enable_html_checked.' /> '.$lang['EnableHTML'].'</label>';
			if ( $functions->auth($forumdata['auth'], 'lock', $_GET['forum']) )
				$options_input[] = '<label><input type="checkbox" name="lock_topic" value="1"'.$lock_topic_checked.' /> '.$lang['LockTopicAfterPost'].'</label>';
			if ( $functions->auth($forumdata['auth'], 'sticky', $_GET['forum']) )
				$options_input[] = '<label><input type="checkbox" name="sticky_topic" value="1"'.$sticky_topic_checked.' /> '.$lang['MakeTopicSticky'].'</label>';
			if ( $session->sess_info['user_id'] )
				$options_input[] = '<label><input type="checkbox" name="subscribe_topic" value="1"'.$subscribe_topic_checked.' /> '.$lang['SubscribeToThisTopic'].'</label>';
			$options_input = '<div>'.join('</div><div>', $options_input).'</div>';
			
			$template->parse('post_form', 'various', array(
				'form_begin' => '<form action="'.$functions->make_url('post.php', array('forum' => $_GET['forum'])).'" method="post">',
				'post_title' => $lang['PostNewTopic'],
				'username_input' => ( $session->sess_info['user_id'] ) ? '<a href="'.$functions->make_url('profile.php', array('id' => $session->sess_info['user_info']['id'])).'">'.unhtml(stripslashes($session->sess_info['user_info']['displayed_name'])).'</a>' : '<input type="text" size="25" maxlength="'.$functions->get_config('username_max_length').'" name="user" id="user" value="'.unhtml(stripslashes($_POST['user'])).'" tabindex="1" />',
				'subject_input' => '<input type="text" name="subject" id="subject" size="50" value="'.unhtml(stripslashes($_POST['subject'])).'" tabindex="2" />',
				'content_input' => '<textarea rows="'.$template->get_config('textarea_rows').'" cols="'.$template->get_config('textarea_cols').'" name="content" id="tags-txtarea" tabindex="3">'.unhtml(stripslashes($_POST['content'])).'</textarea>',
				'potential_spammer_notice' => $can_post_links ? '' : '<div class="potential-spammer-notice">'.$lang['PotentialSpammerNoPostLinks'].'</div>',
				'bbcode_controls' => $functions->get_bbcode_controls($can_post_links),
				'smiley_controls' => $functions->get_smiley_controls(),
				'options_input' => $options_input,
				'submit_button' => '<input type="submit" name="submit" value="'.$lang['OK'].'" tabindex="5" accesskey="s" />',
				'preview_button' => '<input type="submit" name="preview" value="'.$lang['Preview'].'" tabindex="4" />',
				'form_end' => '</form>'
			), false, true);
			
		}
		
	} else {
		
		//
		// The user is not granted to post new topics in this forum
		//
		$functions->redir_to_login();
		
	}
	
}

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
