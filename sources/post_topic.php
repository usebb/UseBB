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
$session->update('posttopic:'.$_GET['forum']);

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

if ( !($result = $db->query("SELECT name, status, auth FROM ".TABLE_PREFIX."forums WHERE id = ".$_GET['forum'])) )
	$functions->usebb_die('SQL', 'Unable to get forum information!', __FILE__, __LINE__);

if ( !$db->num_rows($result) ) {
	
	//
	// This forum does not exist, show an error
	//
	$template->set_page_title($lang['Error']);
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Error'],
		'content' => sprintf($lang['NoSuchForum'], 'ID '.$_GET['forum'])
	));
	
} else {
	
	$forumdata = $db->fetch_result($result);
	
	if ( !$forumdata['status'] && $functions->get_user_level() != 3 ) {
		
		$template->set_page_title($lang['ForumIsLocked']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['ForumIsLocked'],
			'content' => $lang['ForumIsLockedExplain']
		));
		
	} elseif ( $functions->auth($forumdata['auth'], 'post', $_GET['forum']) ) {
		
		$_POST['user'] = ( !empty($_POST['user']) ) ? $_POST['user'] : '';
		$_POST['user'] = preg_replace('/ +/', ' ', $_POST['user']);
		
		if ( ( $session->sess_info['user_id'] || ( !empty($_POST['user']) && preg_match(USER_PREG, $_POST['user']) && strlen($_POST['user']) <= $functions->get_config('username_max_length') ) ) && !empty($_POST['subject']) && !empty($_POST['content']) ) {
			
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
			
			if ( !($result = $db->query("INSERT INTO ".TABLE_PREFIX."posts VALUES(NULL, 0, ".$poster_id.", '".$poster_guest."', '".$session->sess_info['ip_addr']."', '".$_POST['content']."', ".gmmktime().", 0, 0, ".$_POST['enable_bbcode'].", ".$_POST['enable_smilies'].", ".$_POST['enable_sig'].", ".$_POST['enable_html'].")")) )
				$functions->usebb_die('SQL', 'Unable to insert new post!', __FILE__, __LINE__);
			
			$inserted_post_id = $db->last_id();
			$status_locked = ( $functions->auth($forumdata['auth'], 'lock', $_GET['forum']) && !empty($_POST['lock_topic']) ) ? 1 : 0;
			$status_sticky = ( $functions->auth($forumdata['auth'], 'sticky', $_GET['forum']) && !empty($_POST['sticky_topic']) ) ? 1 : 0;
			
			if ( !($result = $db->query("INSERT INTO ".TABLE_PREFIX."topics VALUES(NULL, ".$_GET['forum'].", '".$_POST['subject']."', ".$inserted_post_id.", ".$inserted_post_id.", 0, 0, ".$status_locked.", ".$status_sticky.")")) )
				$functions->usebb_die('SQL', 'Unable to insert new topic!', __FILE__, __LINE__);
			
			$inserted_topic_id = $db->last_id();
			
			if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."posts SET topic_id = ".$inserted_topic_id." WHERE id = ".$inserted_post_id)) )
				$functions->usebb_die('SQL', 'Unable to update post\'s forum ID!', __FILE__, __LINE__);
			
			if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."forums SET topics = topics+1, posts = posts+1, last_topic_id = ".$inserted_topic_id." WHERE id = ".$_GET['forum'])) )
				$functions->usebb_die('SQL', 'Unable to update forum!', __FILE__, __LINE__);
			
			if ( $session->sess_info['user_id'] ) {
				
				if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."members SET posts = posts+1 WHERE id = ".$session->sess_info['user_id'])) )
					$functions->usebb_die('SQL', 'Unable to update user!', __FILE__, __LINE__);
				
			}
			
			if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."stats SET content = content+1 WHERE name = 'topics'")) )
				$functions->usebb_die('SQL', 'Unable to update stats (topics)!', __FILE__, __LINE__);
			
			if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."stats SET content = content+1 WHERE name = 'posts'")) )
				$functions->usebb_die('SQL', 'Unable to update stats (posts)!', __FILE__, __LINE__);
			
			if ( $functions->get_config('return_to_topic_after_posting') )
				header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $inserted_topic_id), false));
			else
				header('Location: '.$functions->get_config('board_url').$functions->make_url('forum.php', array('id' => $_GET['forum']), false));
			
		} else {
			
			$template->set_page_title($lang['PostNewTopic']);
			
			$location_bar = '<a href="'.$functions->make_url('index.php').'">'.$functions->get_config('board_name').'</a> '.$template->get_config('locationbar_item_delimiter').' <a href="'.$functions->make_url('forum.php', array('id' => $_GET['forum'])).'">'.htmlentities(stripslashes($forumdata['name'])).'</a> '.$template->get_config('locationbar_item_delimiter').' '.$lang['PostNewTopic'];
			$template->parse('location_bar', 'global', array(
				'location_bar' => $location_bar
			));
			
			$_POST['user'] = ( !empty($_POST['user']) && preg_match(USER_PREG, $_POST['user']) ) ? $_POST['user'] : '';
			$_POST['subject'] = ( !empty($_POST['subject']) ) ? htmlentities(stripslashes($_POST['subject'])) : '';
			$_POST['content'] = ( !empty($_POST['content']) ) ? htmlentities(stripslashes($_POST['content'])) : '';
			if ( empty($_POST['submitted']) ) {
				
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
				
			} else {
				
				$errors = array();
				if ( !$session->sess_info['user_id'] && ( empty($_POST['user']) || !preg_match(USER_PREG, $_POST['user']) ) )
					$errors[] = $lang['Username'];
				if ( empty($_POST['subject']) )
					$errors[] = $lang['Subject'];
				if ( empty($_POST['content']) )
					$errors[] = $lang['Content'];
				
				if ( count($errors) ) {
					
					$template->parse('msgbox', 'global', array(
						'box_title' => $lang['Error'],
						'content' => sprintf($lang['MissingFields'], join(', ', $errors))
					));
					
				}
				
				$enable_bbcode_checked = ( !empty($_POST['enable_bbcode']) ) ? ' checked="checked"' : '';
				$enable_smilies_checked = ( !empty($_POST['enable_smilies']) ) ? ' checked="checked"' : '';
				$enable_sig_checked = ( !empty($_POST['enable_sig']) ) ? ' checked="checked"' : '';
				$enable_html_checked = ( !empty($_POST['enable_html']) ) ? ' checked="checked"' : '';
				$lock_topic_checked = ( !empty($_POST['lock_topic']) ) ? ' checked="checked"' : '';
				$sticky_topic_checked = ( !empty($_POST['sticky_topic']) ) ? ' checked="checked"' : '';
				
			}
			
			$options_input = array();
			$options_input[] = '<input type="checkbox" name="enable_bbcode" id="enable_bbcode" value="1"'.$enable_bbcode_checked.' /><label for="enable_bbcode"> '.$lang['EnableBBCode'].'</label>';
			$options_input[] = '<input type="checkbox" name="enable_smilies" id="enable_smilies" value="1"'.$enable_smilies_checked.' /><label for="enable_smilies"> '.$lang['EnableSmilies'].'</label>';
			if ( $session->sess_info['user_id'] && !empty($session->sess_info['user_info']['signature']) )
				$options_input[] = '<input type="checkbox" name="enable_sig" id="enable_sig" value="1"'.$enable_sig_checked.' /><label for="enable_sig"> '.$lang['EnableSig'].'</label>';
			if ( $functions->auth($forumdata['auth'], 'html', $_GET['forum']) )
				$options_input[] = '<input type="checkbox" name="enable_html" id="enable_html" value="1"'.$enable_html_checked.' /><label for="enable_html"> '.$lang['EnableHTML'].'</label>';
			if ( $functions->auth($forumdata['auth'], 'lock', $_GET['forum']) )
				$options_input[] = '<input type="checkbox" name="lock_topic" id="lock_topic" value="1"'.$lock_topic_checked.' /><label for="lock_topic"> '.$lang['LockTopicAfterPost'].'</label>';
			if ( $functions->auth($forumdata['auth'], 'sticky', $_GET['forum']) )
				$options_input[] = '<input type="checkbox" name="sticky_topic" id="sticky_topic" value="1"'.$sticky_topic_checked.' /><label for="sticky_topic"> '.$lang['MakeTopicSticky'].'</label>';
			$options_input = join('<br />', $options_input);
			
			$template->parse('post_form', 'various', array(
				'form_begin' => '<form action="'.$functions->make_url('post.php', array('forum' => $_GET['forum'])).'" method="post">',
				'post_title' => $lang['PostNewTopic'],
				'username' => $lang['Username'],
				'username_input' => ( $session->sess_info['user_id'] ) ? '<a href="'.$functions->make_url('profile.php', array('id' => $session->sess_info['user_info']['id'])).'">'.$session->sess_info['user_info']['name'].'</a>' : '<input type="text" size="25" maxlength="'.$functions->get_config('username_max_length').'" name="user" value="'.$_POST['user'].'" />',
				'subject' => $lang['Subject'],
				'subject_input' => '<input type="text" name="subject" size="50" value="'.$_POST['subject'].'" />',
				'content' => $lang['Content'],
				'content_input' => '<textarea rows="'.$template->get_config('textarea_rows').'" cols="'.$template->get_config('textarea_cols').'" name="content">'.$_POST['content'].'</textarea>',
				'options' => $lang['Options'],
				'options_input' => $options_input,
				'submit_button' => '<input type="submit" name="submit" value="'.$lang['PostNewTopic'].'" />',
				'reset_button' => '<input type="reset" value="'.$lang['Reset'].'" />',
				'form_end' => '<input type="hidden" name="submitted" value="true" /></form>'
			));
			
			$template->parse('location_bar', 'global', array(
				'location_bar' => $location_bar
			));
			
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
