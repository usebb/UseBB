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
// Edit posts
//
if ( !isset($_GET['act']) ) {
	
	$session->update('editpost:'.$_GET['post']);
	
	//
	// Include the page header
	//
	require(ROOT_PATH.'sources/page_head.php');
	
	//
	// Get info about the post
	//
	if ( !($result = $db->query("SELECT p.id, p.poster_id, p.poster_guest, p.content, p.enable_bbcode, p.enable_smilies, p.enable_sig, p.enable_html, u.name AS poster_name, u.signature, f.auth, f.id AS forum_id, f.name AS forum_name, t.id AS topic_id, t.topic_title, t.first_post_id FROM ( ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id ), ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = p.topic_id AND f.id = t.forum_id AND p.id = ".$_GET['post'])) )
		$functions->usebb_die('SQL', 'Unable to get post information!', __FILE__, __LINE__);
	
	if ( !$db->num_rows($result) ) {
		
		//
		// This post does not exist
		//
		
		$template->set_page_title($lang['Error']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchPost'], 'ID '.$_GET['post'])
		));
		
	} else {
		
		$postdata = $db->fetch_result($result);
		
		//
		// Only if the user can edit posts
		//
		if ( $session->sess_info['user_id'] && $postdata['poster_id'] == $session->sess_info['user_id'] || $functions->auth($postdata['auth'], 'edit', $postdata['forum_id']) ) {
			
			if ( ( $postdata['poster_id'] || ( !empty($_POST['poster_guest']) && preg_match(USER_PREG, $_POST['poster_guest']) && strlen($_POST['poster_guest']) <= $functions->get_config('username_max_length') ) ) && ( $postdata['first_post_id'] != $_GET['post'] || !empty($_POST['topic_title']) ) && !empty($_POST['content']) && empty($_POST['preview']) ) {
				
				$update_poster_guest = ( !$postdata['poster_id'] ) ? ", poster_guest = '".$_POST['poster_guest']."'" : '';
				$enable_bbcode = ( !empty($_POST['enable_bbcode']) ) ? 1 : 0;
				$enable_smilies = ( !empty($_POST['enable_smilies']) ) ? 1 : 0;
				$enable_sig = ( $postdata['poster_id'] && !empty($postdata['signature']) && !empty($_POST['enable_sig']) ) ? 1 : 0;
				$enable_html = ( $functions->auth($postdata['auth'], 'html', $postdata['forum_id']) && !empty($_POST['enable_html']) ) ? 1 : 0;
				
				if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."posts SET content = '".$_POST['content']."'".$update_poster_guest.", enable_bbcode = ".$enable_bbcode.", enable_smilies = ".$enable_smilies.", enable_sig = ".$enable_sig.", enable_html = ".$enable_html.", post_edit_time = ".time().", post_edit_by = ".$session->sess_info['user_id']." WHERE id = ".$_GET['post'])) )
					$functions->usebb_die('SQL', 'Unable to edit post!', __FILE__, __LINE__);
				
				if ( $postdata['first_post_id'] == $_GET['post'] ) {
					
					if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."topics SET topic_title = '".$_POST['topic_title']."' WHERE id = ".$postdata['topic_id'])) )
						$functions->usebb_die('SQL', 'Unable to adjust topic title!', __FILE__, __LINE__);
					
				}
				
				header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('post' => $_GET['post']), false).'#post'.$_GET['post']);
				
			} else {
				
				$template->set_page_title($lang['EditPost']);
				$location_bar = '<a href="'.$functions->make_url('index.php').'">'.htmlentities($functions->get_config('board_name')).'</a> '.$template->get_config('locationbar_item_delimiter').' <a href="'.$functions->make_url('forum.php', array('id' => $postdata['forum_id'])).'">'.htmlentities(stripslashes($postdata['forum_name'])).'</a> '.$template->get_config('locationbar_item_delimiter').' <a href="'.$functions->make_url('topic.php', array('id' => $postdata['topic_id'])).'">'.htmlentities(stripslashes($postdata['topic_title'])).'</a> '.$template->get_config('locationbar_item_delimiter').' '.$lang['EditPost'];
				$template->parse('location_bar', 'global', array(
					'location_bar' => $location_bar
				));
				
				if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
					
					$poster_guest = ( !empty($_POST['poster_guest']) && preg_match(USER_PREG, $_POST['poster_guest']) ) ? $_POST['poster_guest'] : '';
					$topic_title = ( !empty($_POST['topic_title']) ) ? htmlentities(stripslashes($_POST['topic_title'])) : '';
					$content = ( !empty($_POST['content']) ) ? htmlentities(stripslashes($_POST['content'])) : '';
					$enable_bbcode_checked = ( !empty($_POST['enable_bbcode']) ) ? ' checked="checked"' : '';
					$enable_smilies_checked = ( !empty($_POST['enable_smilies']) ) ? ' checked="checked"' : '';
					$enable_sig_checked = ( !empty($_POST['enable_sig']) ) ? ' checked="checked"' : '';
					$enable_html_checked = ( !empty($_POST['enable_html']) ) ? ' checked="checked"' : '';
					
					$errors = array();
					if ( ( !$postdata['poster_id'] ) && ( empty($_POST['poster_guest']) || !preg_match(USER_PREG, $_POST['poster_guest']) || strlen($_POST['poster_guest']) > $functions->get_config('username_max_length') ) )
						$errors[] = $lang['Username'];
					if ( $postdata['first_post_id'] == $_GET['post'] && empty($_POST['topic_title']) )
						$errors[] = $lang['Subject'];
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
					
					$poster_guest = $postdata['poster_guest'];
					$topic_title = htmlentities(stripslashes($postdata['topic_title']));
					$content = htmlentities(stripslashes($postdata['content']));
					$enable_bbcode_checked = ( $postdata['enable_bbcode'] ) ? ' checked="checked"' : '';
					$enable_smilies_checked = ( $postdata['enable_smilies'] ) ? ' checked="checked"' : '';
					$enable_sig_checked = ( $postdata['enable_sig'] ) ? ' checked="checked"' : '';
					$enable_html_checked = ( $postdata['enable_html'] ) ? ' checked="checked"' : '';
					
				}
				
				$options_input = array();
				$options_input[] = '<input type="checkbox" name="enable_bbcode" id="enable_bbcode" value="1"'.$enable_bbcode_checked.' /><label for="enable_bbcode"> '.$lang['EnableBBCode'].'</label>';
				$options_input[] = '<input type="checkbox" name="enable_smilies" id="enable_smilies" value="1"'.$enable_smilies_checked.' /><label for="enable_smilies"> '.$lang['EnableSmilies'].'</label>';
				if ( $postdata['poster_id'] && !empty($postdata['signature']) )
					$options_input[] = '<input type="checkbox" name="enable_sig" id="enable_sig" value="1"'.$enable_sig_checked.' /><label for="enable_sig"> '.$lang['EnableSig'].'</label>';
				if ( $functions->auth($postdata['auth'], 'html', $postdata['forum_id']) )
					$options_input[] = '<input type="checkbox" name="enable_html" id="enable_html" value="1"'.$enable_html_checked.' /><label for="enable_html"> '.$lang['EnableHTML'].'</label>';
				$options_input = join('<br />', $options_input);
				
				$template->parse('post_form', 'various', array(
					'form_begin' => '<form action="'.$functions->make_url('edit.php', array('post' => $_GET['post'])).'" method="post">',
					'post_title' => $lang['EditPost'],
					'username' => $lang['Username'],
					'username_input' => ( $postdata['poster_id'] ) ? '<a href="'.$functions->make_url('profile.php', array('id' => $postdata['poster_id'])).'">'.$postdata['poster_name'].'</a>' : '<input type="text" size="25" maxlength="'.$functions->get_config('username_max_length').'" name="poster_guest" value="'.$poster_guest.'" />',
					'subject' => $lang['Subject'],
					'subject_input' => ( $postdata['first_post_id'] != $_GET['post'] ) ? '<a href="'.$functions->make_url('topic.php', array('id' => $postdata['topic_id'])).'">'.htmlentities(stripslashes($postdata['topic_title'])).'</a>' : '<input type="text" name="topic_title" size="50" value="'.$topic_title.'" />',
					'content' => $lang['Content'],
					'content_input' => '<textarea rows="'.$template->get_config('textarea_rows').'" cols="'.$template->get_config('textarea_cols').'" name="content">'.$content.'</textarea>',
					'options' => $lang['Options'],
					'options_input' => $options_input,
					'submit_button' => '<input type="submit" name="submit" value="'.$lang['EditPost'].'" />',
					'preview_button' => '<input type="submit" name="preview" value="'.$lang['Preview'].'" />',
					'reset_button' => '<input type="reset" value="'.$lang['Reset'].'" />',
					'form_end' => '</form>'
				));
				$template->parse('location_bar', 'global', array(
					'location_bar' => $location_bar
				));
				
			}
			
		} else {
			
			$functions->redir_to_login();
			
		}
		
	}
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
} elseif ( $_GET['act'] == 'delete' ) {
	
	$session->update('deletepost:'.$_GET['post']);
	
	//
	// Include the page header
	//
	require(ROOT_PATH.'sources/page_head.php');
	
	//
	// Get info about the post
	//
	if ( !($result = $db->query("SELECT p.id, p.poster_id, f.id AS forum_id, f.auth, f.last_topic_id, t.id AS topic_id, t.count_replies, t.topic_title, t.first_post_id, t.last_post_id FROM ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."forums f, ".TABLE_PREFIX."topics t WHERE t.id = p.topic_id AND f.id = t.forum_id AND p.id = ".$_GET['post'])) )
		$functions->usebb_die('SQL', 'Unable to get post information!', __FILE__, __LINE__);
	
	if ( !$db->num_rows($result) ) {
		
		//
		// This post does not exist
		//
		
		$template->set_page_title($lang['Error']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchPost'], 'ID '.$_GET['post'])
		));
		
	} else {
		
		$postdata = $db->fetch_result($result);
		
		//
		// Only if the user can delete posts
		//
		if ( $functions->auth($postdata['auth'], 'delete', $postdata['forum_id']) ) {
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				
				if ( !empty($_POST['delete']) ) {
					
					//
					// 1. Delete the post entry (and eventually the topic entry)
					//
					if ( !($result = $db->query("DELETE FROM ".TABLE_PREFIX."posts WHERE id = ".$_GET['post'])) )
						$functions->usebb_die('SQL', 'Unable to delete post!', __FILE__, __LINE__);
					if ( $postdata['count_replies'] < 1 ) {
						
						if ( !($result = $db->query("DELETE FROM ".TABLE_PREFIX."topics WHERE id = ".$postdata['topic_id'])) )
							$functions->usebb_die('SQL', 'Unable to delete topic!', __FILE__, __LINE__);
						
						$topic_deleted = TRUE;
						$update_topic_count = ', topics = topics-1';
						
					} else {
						
						$update_topic_count = '';
						
					}
					
					//
					// 2. Adjust the topic's first and last post id if needed
					//
					if ( !isset($topic_deleted) ) {
						
						if ( $postdata['first_post_id'] == $_GET['post'] ) {
							
							if ( !($result = $db->query("SELECT p.id FROM ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."topics t WHERE p.topic_id = t.id AND t.id = ".$postdata['topic_id']." ORDER BY p.post_time ASC LIMIT 1")) )
								$functions->usebb_die('SQL', 'Unable to get first post in topic!', __FILE__, __LINE__);
							$first_post_data = $db->fetch_result($result);
							$update_first_post_id = ', first_post_id = '.$first_post_data['id'];
							
						} else {
							
							$update_first_post_id = '';
							
						}
						
						if ( $postdata['last_post_id'] == $_GET['post'] ) {
							
							if ( !($result = $db->query("SELECT p.id FROM ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."topics t WHERE p.topic_id = t.id AND t.id = ".$postdata['topic_id']." ORDER BY p.post_time DESC LIMIT 1")) )
								$functions->usebb_die('SQL', 'Unable to get last post in topic!', __FILE__, __LINE__);
							$last_post_data = $db->fetch_result($result);
							$update_last_post_id = ', last_post_id = '.$last_post_data['id'];
							
						} else {
							
							$update_last_post_id = '';
							
						}
						
					}
					
					//
					// 3. Adjust the topic's replies count if needed
					//
					if ( !isset($topic_deleted) ) {
						
						if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."topics SET count_replies = count_replies-1".$update_first_post_id.$update_last_post_id." WHERE id = ".$postdata['topic_id'])) )
							$functions->usebb_die('SQL', 'Unable to adjust topic\'s replies count!', __FILE__, __LINE__);
						
					}
					
					//
					// 4. Adjust latest updated topic of forum if needed
					//
					if ( $postdata['last_topic_id'] == $postdata['topic_id'] ) {
						
						if ( !($result = $db->query("SELECT p.topic_id FROM ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."topics t WHERE p.topic_id = t.id AND t.forum_id = ".$postdata['forum_id']." ORDER BY p.post_time DESC LIMIT 1")) )
							$functions->usebb_die('SQL', 'Unable to get last updated topic in forum!', __FILE__, __LINE__);
						if ( !$db->num_rows($result) ) {
							
							if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."forums SET topics = 0, posts = 0, last_topic_id = 0 WHERE id = ".$postdata['forum_id'])) )
								$functions->usebb_die('SQL', 'Unable to adjust forum\'s last updated topic ID!', __FILE__, __LINE__);
							
							$forum_counts_updated = TRUE;
							
						} else {
							
							$lasttopicdata = $db->fetch_result($result);
							$update_last_topic_id = ', last_topic_id = '.$lasttopicdata['topic_id'];
							
						}
						
					} else {
						
						$update_last_topic_id = '';
						
					}
					
					//
					// 5. Update the forum's counters
					//
					if ( !isset($forum_counts_updated) ) {
						
						if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."forums SET posts = posts-1".$update_topic_count.$update_last_topic_id." WHERE id = ".$postdata['forum_id'])) )
								$functions->usebb_die('SQL', 'Unable to adjust forum\'s counts!', __FILE__, __LINE__);
						
					}
					
					//
					// 6. Adjust user's posts level
					//
					if ( $postdata['poster_id'] > 0 ) {
						
						if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."members SET posts = posts-1 WHERE id = ".$postdata['poster_id'])) )
							$functions->usebb_die('SQL', 'Unable to adjust member\'s post count!', __FILE__, __LINE__);
						
					}
					
					//
					// 7. Adjust stats
					//
					if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."stats SET content = content-1 WHERE name = 'posts'")) )
						$functions->usebb_die('SQL', 'Unable to update stats (posts)!', __FILE__, __LINE__);
					
					if ( isset($topic_deleted) ) {
						
						if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."stats SET content = content-1 WHERE name = 'topics'")) )
							$functions->usebb_die('SQL', 'Unable to update stats (topics)!', __FILE__, __LINE__);
						
						header('Location: '.$functions->get_config('board_url').$functions->make_url('forum.php', array('id' => $postdata['forum_id'])));
						
					} else {
						
						header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $postdata['topic_id'])));
						
					}
					
				} else {
					
					header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('post' => $_GET['post'])).'#post'.$_GET['post']);
					
				}
				
			} else {
				
				$template->set_page_title($lang['DeletePost']);
				$template->parse('confirm_form', 'global', array(
					'form_begin' => '<form action="'.$functions->make_url('edit.php', array('post' => $_GET['post'], 'act' => 'delete')).'" method="post">',
					'title' => $lang['DeletePost'],
					'content' => sprintf($lang['ConfirmDeletePost'], '<i>'.htmlentities(stripslashes($postdata['topic_title'])).'</i>'),
					'submit_button' => '<input type="submit" name="delete" value="'.$lang['Yes'].'" />',
					'cancel_button' => '<input type="submit" value="'.$lang['Cancel'].'" />',
					'form_end' => '</form>'
				));
				
			}
			
		} else {
			
			$functions->redir_to_login();
			
		}
		
	}
	
	//
	// Include the page footer
	//
	require(ROOT_PATH.'sources/page_foot.php');
	
}

?>
