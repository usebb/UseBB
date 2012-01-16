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
 * Edit post interface
 *
 * Interface to editing posts.
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
	$result = $db->query("SELECT p.id, p.poster_id, p.post_time, p.poster_guest, p.content, p.enable_bbcode, p.enable_smilies, p.enable_sig, p.enable_html, u.displayed_name AS poster_name, u.level AS poster_level, u.signature, f.auth, f.id AS forum_id, f.name AS forum_name, t.id AS topic_id, t.topic_title, t.first_post_id FROM ( ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id ), ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = p.topic_id AND f.id = t.forum_id AND p.id = ".$_GET['post']);
	$postdata = $db->fetch_result($result);
	
	if ( !$postdata['id'] ) {
		
		//
		// This post does not exist
		//
		header(HEADER_404);
		$template->add_breadcrumb($lang['Error']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchPost'], 'ID '.$_GET['post'])
		));
		
	} else {
		
		//
		// Only if the user can edit posts
		//
		if ( ( ( $session->sess_info['user_id'] && $postdata['poster_id'] == $session->sess_info['user_id'] && ( time() - $functions->get_config('edit_post_timeout') ) <= $postdata['post_time'] ) || $functions->auth($postdata['auth'], 'edit', $postdata['forum_id']) ) && $postdata['poster_level'] <= $session->sess_info['user_info']['level'] ) {
			
			$_POST['poster_guest'] = ( !empty($_POST['poster_guest']) ) ? preg_replace('#\s+#', ' ', $_POST['poster_guest']) : '';
			if ( ( $postdata['poster_id'] || ( !empty($_POST['poster_guest']) && preg_match(USER_PREG, $_POST['poster_guest']) ) ) && ( $postdata['first_post_id'] != $_GET['post'] || !empty($_POST['topic_title']) ) && !$functions->post_empty($_POST['content']) && empty($_POST['preview']) && $functions->verify_form() ) {
				
				$update_poster_guest = ( !$postdata['poster_id'] ) ? ", poster_guest = '".$_POST['poster_guest']."'" : '';
				$enable_bbcode = ( !empty($_POST['enable_bbcode']) ) ? 1 : 0;
				$enable_smilies = ( !empty($_POST['enable_smilies']) ) ? 1 : 0;
				$enable_sig = ( $postdata['poster_id'] && !empty($postdata['signature']) && !empty($_POST['enable_sig']) ) ? 1 : 0;
				$enable_html = ( $functions->auth($postdata['auth'], 'html', $postdata['forum_id']) && !empty($_POST['enable_html']) ) ? 1 : 0;
				
				$result = $db->query("UPDATE ".TABLE_PREFIX."posts SET content = '".$_POST['content']."'".$update_poster_guest.", enable_bbcode = ".$enable_bbcode.", enable_smilies = ".$enable_smilies.", enable_sig = ".$enable_sig.", enable_html = ".$enable_html.", post_edit_time = ".time().", post_edit_by = ".$session->sess_info['user_id']." WHERE id = ".$_GET['post']);
				
				if ( $postdata['first_post_id'] == $_GET['post'] ) {
					
					$result = $db->query("UPDATE ".TABLE_PREFIX."topics SET topic_title = '".$_POST['topic_title']."' WHERE id = ".$postdata['topic_id']);
					
				}
				
				$functions->redirect('topic.php', array('post' => $_GET['post']), 'post'.$_GET['post']);
				
			} else {
				
				$can_post_links = $functions->antispam_can_post_links($session->sess_info['user_info']);
				
				$template->add_breadcrumb(
					unhtml(stripslashes($postdata['forum_name'])), 
					array('forum.php', array('id' => $postdata['forum_id']))
				);
				$template->add_breadcrumb(
					unhtml($functions->replace_badwords(stripslashes($postdata['topic_title']))), 
					array('topic.php', array('post' => $_GET['post'])), 
					'post'.$_GET['post']
				);
				$template->add_breadcrumb($lang['EditPost']);
				
				if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
					
					$poster_guest = ( !empty($_POST['poster_guest']) && preg_match(USER_PREG, $_POST['poster_guest']) ) ? $_POST['poster_guest'] : '';
					$topic_title = ( !empty($_POST['topic_title']) ) ? unhtml(stripslashes($_POST['topic_title'])) : '';
					$content = ( !$functions->post_empty($_POST['content']) ) ? unhtml(stripslashes($_POST['content'])) : '';
					$enable_bbcode_checked = ( !empty($_POST['enable_bbcode']) ) ? ' checked="checked"' : '';
					$enable_smilies_checked = ( !empty($_POST['enable_smilies']) ) ? ' checked="checked"' : '';
					$enable_sig_checked = ( !empty($_POST['enable_sig']) ) ? ' checked="checked"' : '';
					$enable_html_checked = ( !empty($_POST['enable_html']) ) ? ' checked="checked"' : '';
					
					$errors = array();
					if ( ( !$postdata['poster_id'] ) && ( empty($_POST['poster_guest']) || !preg_match(USER_PREG, $_POST['poster_guest']) ) )
						$errors[] = $lang['Username'];
					if ( $postdata['first_post_id'] == $_GET['post'] && empty($_POST['topic_title']) )
						$errors[] = $lang['Subject'];
					if ( $functions->post_empty($_POST['content']) )
						$errors[] = $lang['Content'];
					
					if ( count($errors) ) {
						
						$template->parse('msgbox', 'global', array(
							'box_title' => $lang['Error'],
							'content' => sprintf($lang['MissingFields'], join(', ', $errors))
						));
						
					} elseif ( !empty($_POST['preview']) ) {
						
						$template->parse('preview', 'various', array(
							'post_content' => $functions->markup(stripslashes($_POST['content']), $enable_bbcode_checked, $enable_smilies_checked, $enable_html_checked, NULL, $can_post_links)
						));
						
					}
					
				} else {
					
					$poster_guest = $postdata['poster_guest'];
					$topic_title = unhtml(stripslashes($postdata['topic_title']));
					$content = unhtml(stripslashes($postdata['content']));
					$enable_bbcode_checked = ( $postdata['enable_bbcode'] ) ? ' checked="checked"' : '';
					$enable_smilies_checked = ( $postdata['enable_smilies'] ) ? ' checked="checked"' : '';
					$enable_sig_checked = ( $postdata['enable_sig'] ) ? ' checked="checked"' : '';
					$enable_html_checked = ( $postdata['enable_html'] ) ? ' checked="checked"' : '';
					
					if ( !$postdata['poster_id'] )
						$template->set_js_onload("set_focus('poster_guest')");
					elseif ( $postdata['first_post_id'] == $_GET['post'] )
						$template->set_js_onload("set_focus('topic_title')");
					else
						$template->set_js_onload("set_focus('tags-txtarea')");
					
				}
				
				$options_input = array();
				$options_input[] = '<label><input type="checkbox" name="enable_bbcode" value="1"'.$enable_bbcode_checked.' /> '.$lang['EnableBBCode'].'</label>';
				$options_input[] = '<label><input type="checkbox" name="enable_smilies" value="1"'.$enable_smilies_checked.' /> '.$lang['EnableSmilies'].'</label>';
				if ( $postdata['poster_id'] && !empty($postdata['signature']) )
					$options_input[] = '<label><input type="checkbox" name="enable_sig" value="1"'.$enable_sig_checked.' /> '.$lang['EnableSig'].'</label>';
				if ( $functions->auth($postdata['auth'], 'html', $postdata['forum_id']) )
					$options_input[] = '<label><input type="checkbox" name="enable_html" value="1"'.$enable_html_checked.' /> '.$lang['EnableHTML'].'</label>';
				$options_input = '<div>'.join('</div><div>', $options_input).'</div>';
				
				$template->parse('post_form', 'various', array(
					'form_begin' => '<form action="'.$functions->make_url('edit.php', array('post' => $_GET['post'])).'" method="post">',
					'post_title' => $lang['EditPost'],
					'username_input' => ( $postdata['poster_id'] ) ? '<a href="'.$functions->make_url('profile.php', array('id' => $postdata['poster_id'])).'">'.unhtml(stripslashes($postdata['poster_name'])).'</a>' : '<input type="text" size="25" maxlength="255" name="poster_guest" id="poster_guest" value="'.unhtml(stripslashes($poster_guest)).'" tabindex="1" />',
					'subject_input' => ( $postdata['first_post_id'] != $_GET['post'] ) ? '<a href="'.$functions->make_url('topic.php', array('id' => $postdata['topic_id'])).'">'.unhtml(stripslashes($postdata['topic_title'])).'</a>' : '<input type="text" name="topic_title" id="topic_title" size="50" value="'.$topic_title.'" tabindex="2" />',
					'content_input' => '<textarea rows="'.$template->get_config('textarea_rows').'" cols="'.$template->get_config('textarea_cols').'" name="content" id="tags-txtarea" tabindex="3">'.$content.'</textarea>',
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
	$result = $db->query("SELECT p.id, p.poster_id, p.post_time, u.level AS poster_level, f.id AS forum_id, f.auth, f.last_topic_id, f.increase_post_count, t.id AS topic_id, t.count_replies, t.topic_title, t.first_post_id, t.last_post_id FROM ( ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."members u ON p.poster_id = u.id ), ".TABLE_PREFIX."forums f, ".TABLE_PREFIX."topics t WHERE t.id = p.topic_id AND f.id = t.forum_id AND p.id = ".$_GET['post']);
	$postdata = $db->fetch_result($result);
	
	if ( !$postdata['id'] ) {
		
		//
		// This post does not exist
		//
		header(HEADER_404);
		$template->add_breadcrumb($lang['Error']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchPost'], 'ID '.$_GET['post'])
		));
		
	} else {
		
		//
		// Only if the user can delete posts
		//
		if ( ( ( $session->sess_info['user_id'] && $postdata['poster_id'] == $session->sess_info['user_id'] && $postdata['last_post_id'] == $_GET['post'] && ( time() - $functions->get_config('edit_post_timeout') ) <= $postdata['post_time'] ) || $functions->auth($postdata['auth'], 'delete', $postdata['forum_id']) ) && $postdata['poster_level'] <= $session->sess_info['user_info']['level'] ) {
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				
				if ( !empty($_POST['delete']) && $functions->verify_form(false) ) {
					
					//
					// 1. Delete the post entry (and eventually the topic entry)
					//
					$topic_deleted = false;
					$result = $db->query("DELETE FROM ".TABLE_PREFIX."posts WHERE id = ".$_GET['post']);
					if ( $postdata['count_replies'] < 1 ) {
						
						$result = $db->query("DELETE FROM ".TABLE_PREFIX."topics WHERE id = ".$postdata['topic_id']);
						
						$topic_deleted = true;
						$update_topic_count = ', topics = topics-1';
						
					} else {
						
						$update_topic_count = '';
						
					}
					
					//
					// 2. Adjust the topic's first and last post id if needed
					//
					if ( !$topic_deleted ) {
						
						if ( $postdata['first_post_id'] == $_GET['post'] ) {
							
							$result = $db->query("SELECT p.id FROM ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."topics t WHERE p.topic_id = t.id AND t.id = ".$postdata['topic_id']." ORDER BY p.post_time ASC LIMIT 1");
							$first_post_data = $db->fetch_result($result);
							$update_first_post_id = ', first_post_id = '.$first_post_data['id'];
							
						} else {
							
							$update_first_post_id = '';
							
						}
						
						if ( $postdata['last_post_id'] == $_GET['post'] ) {
							
							$result = $db->query("SELECT p.id FROM ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."topics t WHERE p.topic_id = t.id AND t.id = ".$postdata['topic_id']." ORDER BY p.post_time DESC LIMIT 1");
							$last_post_data = $db->fetch_result($result);
							$update_last_post_id = ', last_post_id = '.$last_post_data['id'];
							
						} else {
							
							$update_last_post_id = '';
							
						}
						
					}
					
					//
					// 3. Adjust the topic's replies count if needed
					//
					if ( !$topic_deleted ) {
						
						$result = $db->query("UPDATE ".TABLE_PREFIX."topics SET count_replies = count_replies-1".$update_first_post_id.$update_last_post_id." WHERE id = ".$postdata['topic_id']);
						
					}
					
					//
					// 4. Adjust latest updated topic of forum if needed
					//
					if ( $postdata['last_topic_id'] == $postdata['topic_id'] ) {
						
						$result = $db->query("SELECT p.topic_id FROM ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."topics t WHERE p.topic_id = t.id AND t.forum_id = ".$postdata['forum_id']." ORDER BY p.post_time DESC LIMIT 1");
						$lasttopicdata = $db->fetch_result($result);
						
						if ( !$lasttopicdata['topic_id'] ) {
							
							$result = $db->query("UPDATE ".TABLE_PREFIX."forums SET topics = 0, posts = 0, last_topic_id = 0 WHERE id = ".$postdata['forum_id']);
							
							$forum_counts_updated = true;
							
						} else {
							
							$update_last_topic_id = ', last_topic_id = '.$lasttopicdata['topic_id'];
							
						}
						
					} else {
						
						$update_last_topic_id = '';
						
					}
					
					//
					// 5. Update the forum's counters
					//
					if ( !isset($forum_counts_updated) ) {
						
						$result = $db->query("UPDATE ".TABLE_PREFIX."forums SET posts = posts-1".$update_topic_count.$update_last_topic_id." WHERE id = ".$postdata['forum_id']);
						
					}
					
					//
					// 6. Adjust user's posts level
					//
					if ( $postdata['poster_id'] > LEVEL_GUEST && $postdata['increase_post_count'] ) {
						
						$result = $db->query("UPDATE ".TABLE_PREFIX."members SET posts = posts-1 WHERE id = ".$postdata['poster_id']);
						
					}
					
					//
					// 7. Adjust stats
					//
					$functions->set_stats('posts', -1, true);
					
					if ( $topic_deleted ) {
						
						$functions->set_stats('topics', -1, true);
						$functions->redirect('forum.php', array('id' => $postdata['forum_id']));
						
					} else {
						
						$functions->redirect('topic.php', array('id' => $postdata['topic_id']));
						
					}
					
				} else {
					
					$functions->redirect('topic.php', array('post' => $_GET['post']), 'post'.$_GET['post']);
					
				}
				
			} else {
				
				$template->add_breadcrumb($lang['DeletePost']);
				$template->parse('confirm_form', 'global', array(
					'form_begin' => '<form action="'.$functions->make_url('edit.php', array('post' => $_GET['post'], 'act' => 'delete')).'" method="post">',
					'title' => $lang['DeletePost'],
					'content' => sprintf($lang['ConfirmDeletePost'], '<em>'.unhtml(stripslashes($postdata['topic_title'])).'</em>'),
					'submit_button' => '<input type="submit" name="delete" value="'.$lang['Yes'].'" />',
					'cancel_button' => '<input type="submit" value="'.$lang['Cancel'].'" />',
					'form_end' => '</form>'
				), false, true);
				
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
