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
 * Edit topic interface
 *
 * Interface to editing topics.
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
// Delete topics
//
if ( $_GET['act'] == 'delete' ) {
	
	$session->update('deletetopic:'.$_GET['topic']);
	
	//
	// Include the page header
	//
	require(ROOT_PATH.'sources/page_head.php');
	
	//
	// Get info about the topic
	//
	$result = $db->query("SELECT t.id, t.forum_id, t.topic_title, t.count_replies, f.name AS forum_name, f.auth, f.last_topic_id, f.increase_post_count FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.forum_id = f.id AND t.id = ".$_GET['topic']);
	$topicdata = $db->fetch_result($result);
	
	if ( !$topicdata['id'] ) {
		
		//
		// This topic does not exist
		//
		header(HEADER_404);
		$template->add_breadcrumb($lang['Error']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchTopic'], 'ID '.$_GET['topic'])
		));
		
	} else {
		
		//
		// Only if the user can delete topics
		//
		if ( $functions->auth($topicdata['auth'], 'delete', $topicdata['forum_id']) ) {
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				
				if ( !empty($_POST['delete']) && $functions->verify_form(false) ) {
					
					$forum_counts_updated = false;
					
					//
					// 1. Delete the topic entry
					//
					$result = $db->query("DELETE FROM ".TABLE_PREFIX."topics WHERE id = ".$_GET['topic']);
					
					//
					// 2. Adjust latest updated topic of forum if needed
					//
					if ( $topicdata['last_topic_id'] == $_GET['topic'] ) {
						
						$result = $db->query("SELECT p.topic_id FROM ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."topics t WHERE p.topic_id = t.id AND t.forum_id = ".$topicdata['forum_id']." ORDER BY p.post_time DESC LIMIT 1");
						$lasttopicdata = $db->fetch_result($result);
						
						if ( !$lasttopicdata['topic_id'] ) {
							
							$result = $db->query("UPDATE ".TABLE_PREFIX."forums SET topics = 0, posts = 0, last_topic_id = 0 WHERE id = ".$topicdata['forum_id']);
							
							$forum_counts_updated = true;
							
						} else {
							
							$update_last_topic_id = ', last_topic_id = '.$lasttopicdata['topic_id'];
							
						}
						
					} else {
						
						$update_last_topic_id = '';
						
					}
					
					//
					// 3. Update the forum's counters
					//
					if ( !$forum_counts_updated ) {
						
						$result = $db->query("UPDATE ".TABLE_PREFIX."forums SET topics = topics-1, posts = posts-". ( $topicdata['count_replies']+1 ) .$update_last_topic_id." WHERE id = ".$topicdata['forum_id']);
						
					}
					
					//
					// 4. Adjust users' posts levels by defining which users posted and how many posts made
					//
					if ( $topicdata['increase_post_count'] ) {
						
						$result = $db->query("SELECT poster_id FROM ".TABLE_PREFIX."posts WHERE topic_id = ".$_GET['topic']);
						
						$users_posted = array();
						while ( $postsdata = $db->fetch_result($result) ) {
							
							if ( !array_key_exists($postsdata['poster_id'], $users_posted) )
								$users_posted[$postsdata['poster_id']] = 1;
							else
								$users_posted[$postsdata['poster_id']]++;
							
						}
						
						//
						// Adjust the count for every user that posted
						//
						foreach ( $users_posted as $userid => $postcount )
							$result = $db->query("UPDATE ".TABLE_PREFIX."members SET posts = posts-".$postcount." WHERE id = ".$userid);
						
					}
					
					//
					// 5. Delete posts within the deleted topic
					//
					$result = $db->query("DELETE FROM ".TABLE_PREFIX."posts WHERE topic_id = ".$_GET['topic']);
					
					//
					// 6. Adjust stats
					//
					$functions->set_stats('topics', -1, true);
					$functions->set_stats('posts', - ( $topicdata['count_replies']+1 ), true);
					
					$functions->redirect('forum.php', array('id' => $topicdata['forum_id']));
					
				} else {
					
					$functions->redirect('topic.php', array('id' => $_GET['topic']));
					
				}
				
			} else {
				
				$template->add_breadcrumb(
					unhtml(stripslashes($topicdata['forum_name'])), 
					array('forum.php', array('id' => $topicdata['forum_id']))
				);
				$template->add_breadcrumb(
					unhtml($functions->replace_badwords(stripslashes($topicdata['topic_title']))), 
					array('topic.php', array('id' => $_GET['topic'])) 
				);
				$template->add_breadcrumb($lang['DeleteTopic']);
				
				$template->parse('confirm_form', 'global', array(
					'form_begin' => '<form action="'.$functions->make_url('edit.php', array('topic' => $_GET['topic'], 'act' => 'delete')).'" method="post">',
					'title' => $lang['DeleteTopic'],
					'content' => sprintf($lang['ConfirmDeleteTopic'], '<em>'.unhtml(stripslashes($topicdata['topic_title'])).'</em>', '<em>'.unhtml(stripslashes($topicdata['forum_name'])).'</em>'),
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
	
} elseif ( $_GET['act'] == 'move' ) {
	
	//
	// Move topics
	//
	$session->update($_GET['act'].'topic:'.$_GET['topic']);
		
	//
	// Include the page header
	//
	require(ROOT_PATH.'sources/page_head.php');
	
	//
	// Get topic information
	//
	$result = $db->query("SELECT t.id, t.topic_title, t.forum_id, t.count_replies, p.post_time, f.name AS forum_name, f.auth, f.last_topic_id FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."forums f WHERE t.forum_id = f.id AND p.id = t.last_post_id AND t.id = ".$_GET['topic']);
	$topicdata = $db->fetch_result($result);
	
	if ( !$topicdata['id'] ) {
		
		//
		// I didn't see that topic!?
		//
		header(HEADER_404);
		$template->add_breadcrumb($lang['Error']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchTopic'], 'ID '.$_GET['topic'])
		));
		
	} else {
		
		//
		// If the user is granted to move topics
		//
		if ( $functions->auth($topicdata['auth'], 'move', $topicdata['forum_id']) ) {
			
			if ( !empty($_POST['new_forum_id']) && valid_int($_POST['new_forum_id']) ) {
					
				if ( !empty($_POST['move']) && $functions->verify_form(false) ) {
					
					//
					// Get information about the new forum
					//
					$result = $db->query("SELECT f.id, f.last_topic_id, f.auth, p.post_time FROM ( ( ".TABLE_PREFIX."forums f LEFT JOIN ".TABLE_PREFIX."topics t ON t.id = f.last_topic_id ) LEFT JOIN ".TABLE_PREFIX."posts p ON p.id = t.last_post_id ) WHERE f.id = ".$_POST['new_forum_id']);
					$forumdata = $db->fetch_result($result);
					
					if ( !$forumdata['id'] ) {
						
						$functions->redirect('topic.php', array('id' => $_GET['topic']));
						
					} else {
						
						if ( !$functions->auth($forumdata['auth'], 'view', $_POST['new_forum_id']) ) {
							
							$functions->redirect('topic.php', array('id' => $_GET['topic']));
							
						} else {
							
							//
							// Move the topic
							//
							$result = $db->query("UPDATE ".TABLE_PREFIX."topics SET forum_id = ".$_POST['new_forum_id']." WHERE id = ".$_GET['topic']);
							$old_forum_counts_updated = false;
							
							if ( $topicdata['last_topic_id'] == $_GET['topic'] ) {
								
								//
								// Adjust the last updated topic
								//
								$result = $db->query("SELECT p.topic_id FROM ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."topics t WHERE p.topic_id = t.id AND t.forum_id = ".$topicdata['forum_id']." ORDER BY p.post_time DESC LIMIT 1");
								$lasttopicdata = $db->fetch_result($result);
								
								if ( !$lasttopicdata['topic_id'] ) {
									
									$result = $db->query("UPDATE ".TABLE_PREFIX."forums SET topics = 0, posts = 0, last_topic_id = 0 WHERE id = ".$topicdata['forum_id']);
									
									$old_forum_counts_updated = true;
									
								} else {
									
									$update_old_last_topic_id = ', last_topic_id = '.$lasttopicdata['topic_id'];
									
								}
								
							} else {
								
								$update_old_last_topic_id = '';
								
							}
							
							$update_new_last_topic_id = ( $topicdata['post_time'] > $forumdata['post_time'] ) ? ', last_topic_id = '.$_GET['topic'] : '';
							
							//
							// Adjust forum counts
							//
							if ( !$old_forum_counts_updated ) {
								
								$result = $db->query("UPDATE ".TABLE_PREFIX."forums SET topics = topics-1, posts = posts-". ( $topicdata['count_replies']+1 ) .$update_old_last_topic_id." WHERE id = ".$topicdata['forum_id']);
								
							}
							
							$result = $db->query("UPDATE ".TABLE_PREFIX."forums SET topics = topics+1, posts = posts+". ( $topicdata['count_replies']+1 ) .$update_new_last_topic_id." WHERE id = ".$_POST['new_forum_id']);
							
							$functions->redirect('topic.php', array('id' => $_GET['topic']));
							
						}
						
					}
				
				} else {
					
					$functions->redirect('topic.php', array('id' => $_GET['topic']));
					
				}
				
			} else {
				
				$template->add_breadcrumb(
					unhtml(stripslashes($topicdata['forum_name'])), 
					array('forum.php', array('id' => $topicdata['forum_id']))
				);
				$template->add_breadcrumb(
					unhtml($functions->replace_badwords(stripslashes($topicdata['topic_title']))), 
					array('topic.php', array('id' => $_GET['topic'])) 
				);
				$template->add_breadcrumb($lang['MoveTopic']);
				
				$result = $db->query("SELECT c.id AS cat_id, c.name AS cat_name, f.id, f.name, f.auth FROM ".TABLE_PREFIX."cats c, ".TABLE_PREFIX."forums f WHERE c.id = f.cat_id AND f.id <> ".$topicdata['forum_id']." ORDER BY c.sort_id ASC, c.name ASC, f.sort_id ASC, f.name ASC");
				$forums = array();
				while ( $forumdata = $db->fetch_result($result) ) {
					
					if ( $functions->auth($forumdata['auth'], 'view', $forumdata['id']) )
						$forums[] = $forumdata;
					
				}
				if ( !count($forums) ) {
					
					$functions->redirect('topic.php', array('id' => $_GET['topic']));
					
				} elseif ( count($forums) === 1 ) {
					
					$forumdata = $forums[0];
					$new_forum_input = '<a href="'.$functions->make_url('forum.php', array('id' => $forumdata['id'])).'">'.unhtml(stripslashes($forumdata['name'])).'</a><input type="hidden" name="new_forum_id" value="'.$forumdata['id'].'" />';
					
				} else {
					
					//
					// Get a list of available forums to move to
					//
					$new_forum_input = '<select name="new_forum_id">';
					$seen_cats = array();
					foreach ( $forums as $forumdata ) {
						
						if ( !in_array($forumdata['cat_id'], $seen_cats) ) {
							
							$new_forum_input .= ( !count($seen_cats) ) ? '' : '</optgroup>';
							$new_forum_input .= '<optgroup label="'.unhtml(stripslashes($forumdata['cat_name'])).'">';
							$seen_cats[] = $forumdata['cat_id'];
							
						}
						
						$new_forum_input .= '<option value="'.$forumdata['id'].'">'.unhtml(stripslashes($forumdata['name'])).'</option>';
						
					}
					$new_forum_input .= '</optgroup></select>';
					
				}
				
				$template->parse('move_topic_form', 'various', array(
					'form_begin' => '<form action="'.$functions->make_url('edit.php', array('topic' => $_GET['topic'], 'act' => 'move')).'" method="post">',
					'topic_v' => '<a href="'.$functions->make_url('topic.php', array('id' => $_GET['topic'])).'">'.unhtml(stripslashes($topicdata['topic_title'])).'</a>',
					'old_forum_v' => '<a href="'.$functions->make_url('forum.php', array('id' => $topicdata['forum_id'])).'">'.unhtml(stripslashes($topicdata['forum_name'])).'</a>',
					'new_forum_input' => $new_forum_input,
					'submit_button' => '<input type="submit" name="move" value="'.$lang['OK'].'" />',
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
	
} elseif ( $_GET['act'] == 'lock' && $functions->verify_url(false) ) {
	
	//
	// Lock topics
	//
	$session->update();
	
	$result = $db->query("SELECT t.id, t.status_locked, f.id, f.auth FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = ".$_GET['topic']." AND f.id = t.forum_id");
	$topicdata = $db->fetch_result($result);
	
	if ( !$topicdata['id'] ) {
		
		$functions->redirect('topic.php', array('id' => $_GET['topic']));
		
	} else {
		
		if ( !$functions->auth($topicdata['auth'], 'lock', $topicdata['id']) ) {
			
			//
			// Include the page header
			//
			require(ROOT_PATH.'sources/page_head.php');
			
			$functions->redir_to_login();
			
			//
			// Include the page footer
			//
			require(ROOT_PATH.'sources/page_foot.php');
			
		} else {
			
			if ( $topicdata['status_locked'] ) {
				
				$functions->redirect('topic.php', array('id' => $_GET['topic']));
				
			} else {
				
				$result = $db->query("UPDATE ".TABLE_PREFIX."topics SET status_locked = 1 WHERE id = ".$_GET['topic']);
				
				$functions->redirect('topic.php', array('id' => $_GET['topic']));
				
			}
			
		}
		
	}
	
} elseif ( $_GET['act'] == 'unlock' && $functions->verify_url(false) ) {
	
	//
	// Unlock topics
	//
	$session->update();
	
	$result = $db->query("SELECT t.id, t.status_locked, f.id, f.auth FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = ".$_GET['topic']." AND f.id = t.forum_id");
	$topicdata = $db->fetch_result($result);
	
	if ( !$topicdata['id'] ) {
		
		$functions->redirect('topic.php', array('id' => $_GET['topic']));
		
	} else {
		
		if ( !$functions->auth($topicdata['auth'], 'lock', $topicdata['id']) ) {
			
			//
			// Include the page header
			//
			require(ROOT_PATH.'sources/page_head.php');
			
			$functions->redir_to_login();
			
			//
			// Include the page footer
			//
			require(ROOT_PATH.'sources/page_foot.php');
			
		} else {
			
			if ( !$topicdata['status_locked'] ) {
				
				$functions->redirect('topic.php', array('id' => $_GET['topic']));
				
			} else {
				
				$result = $db->query("UPDATE ".TABLE_PREFIX."topics SET status_locked = 0 WHERE id = ".$_GET['topic']);
				
				$functions->redirect('topic.php', array('id' => $_GET['topic']));
				
			}
			
		}
		
	}
	
} elseif ( $_GET['act'] == 'sticky' && $functions->verify_url(false) ) {
	
	//
	// Sticky topics
	//
	$session->update();
	
	$result = $db->query("SELECT t.id, t.status_sticky, f.id, f.auth FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = ".$_GET['topic']." AND f.id = t.forum_id");
	$topicdata = $db->fetch_result($result);
	
	if ( !$topicdata['id'] ) {
		
		$functions->redirect('topic.php', array('id' => $_GET['topic']));
		
	} else {
		
		if ( !$functions->auth($topicdata['auth'], 'sticky', $topicdata['id']) ) {
			
			//
			// Include the page header
			//
			require(ROOT_PATH.'sources/page_head.php');
			
			$functions->redir_to_login();
			
			//
			// Include the page footer
			//
			require(ROOT_PATH.'sources/page_foot.php');
			
		} else {
			
			if ( $topicdata['status_sticky'] ) {
				
				$functions->redirect('topic.php', array('id' => $_GET['topic']));
				
			} else {
				
				$result = $db->query("UPDATE ".TABLE_PREFIX."topics SET status_sticky = 1 WHERE id = ".$_GET['topic']);
				
				$functions->redirect('topic.php', array('id' => $_GET['topic']));
				
			}
			
		}
		
	}
	
} elseif ( $_GET['act'] == 'unsticky' && $functions->verify_url(false) ) {
	
	//
	// "Unsticky" topics
	// -does that word exist?-
	//
	$session->update();
	
	$result = $db->query("SELECT t.id, t.status_sticky, f.id, f.auth FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = ".$_GET['topic']." AND f.id = t.forum_id");
	$topicdata = $db->fetch_result($result);
	
	if ( !$topicdata['id'] ) {
		
		$functions->redirect('topic.php', array('id' => $_GET['topic']));
		
	} else {
		
		if ( !$functions->auth($topicdata['auth'], 'sticky', $topicdata['id']) ) {
			
			//
			// Include the page header
			//
			require(ROOT_PATH.'sources/page_head.php');
			
			$functions->redir_to_login();
			
			//
			// Include the page footer
			//
			require(ROOT_PATH.'sources/page_foot.php');
			
		} else {
			
			if ( !$topicdata['status_sticky'] ) {
				
				$functions->redirect('topic.php', array('id' => $_GET['topic']));
				
			} else {
				
				$result = $db->query("UPDATE ".TABLE_PREFIX."topics SET status_sticky = 0 WHERE id = ".$_GET['topic']);
				
				$functions->redirect('topic.php', array('id' => $_GET['topic']));
				
			}
			
		}
		
	}
	
} else {

	$functions->redirect('topic.php', array('id' => $_GET['topic']));

}

?>
