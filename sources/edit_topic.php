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
// Delete topics
//
if ( $_GET['act'] == 'delete' ) {
	
	$session->update('deletetopic:'.$_GET['topic']);
	
	//
	// Get info about the topic
	//
	if ( !($result = $db->query("SELECT t.forum_id, t.topic_title, t.count_replies, f.name AS forum_name, f.auth, f.last_topic_id FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.forum_id = f.id AND t.id = ".$_GET['topic'])) )
		$functions->usebb_die('SQL', 'Unable to get topic information!', __FILE__, __LINE__);
	
	if ( !$db->num_rows($result) ) {
		
		//
		// This topic does not exist
		//
		
		//
		// Include the page header
		//
		require(ROOT_PATH.'sources/page_head.php');
		
		$template->set_page_title($lang['Error']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchTopic'], 'ID '.$_GET['topic'])
		));
		
		//
		// Include the page footer
		//
		require(ROOT_PATH.'sources/page_foot.php');
		
	} else {
		
		$topicdata = $db->fetch_result($result);
		
		//
		// Only if the user can delete topics
		//
		if ( $functions->auth($topicdata['auth'], 'delete', $topicdata['forum_id']) ) {
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				
				if ( !empty($_POST['delete']) ) {
					
					//
					// 1. Delete the topic entry
					//
					if ( !($result = $db->query("DELETE FROM ".TABLE_PREFIX."topics WHERE id = ".$_GET['topic'])) )
						$functions->usebb_die('SQL', 'Unable to delete topic!', __FILE__, __LINE__);
					
					//
					// 2. Adjust latest updated topic of forum if needed
					//
					if ( $topicdata['last_topic_id'] == $_GET['topic'] ) {
						
						if ( !($result = $db->query("SELECT p.topic_id FROM ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."topics t WHERE p.topic_id = t.id AND t.forum_id = ".$topicdata['forum_id']." ORDER BY p.post_time DESC LIMIT 1")) )
							$functions->usebb_die('SQL', 'Unable to get last updated topic in forum!', __FILE__, __LINE__);
						if ( !$db->num_rows($result) ) {
							
							if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."forums SET topics = 0, posts = 0, last_topic_id = 0 WHERE id = ".$topicdata['forum_id'])) )
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
					// 3. Update the forum's counters
					//
					if ( !isset($forum_counts_updated) ) {
						
						if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."forums SET topics = topics-1, posts = posts-". ( $topicdata['count_replies']+1 ) .$update_last_topic_id." WHERE id = ".$topicdata['forum_id'])) )
								$functions->usebb_die('SQL', 'Unable to adjust forum\'s last updated topic ID!', __FILE__, __LINE__);
						
					}
					
					//
					// 4. Adjust users' posts levels by defining which users posted and how many posts made
					//
					if ( !($result = $db->query("SELECT poster_id FROM ".TABLE_PREFIX."posts WHERE topic_id = ".$_GET['topic'])) )
						$functions->usebb_die('SQL', 'Unable to get posts information!', __FILE__, __LINE__);
					
					$users_posted = array();
					while ( $postsdata = $db->fetch_result($result) ) {
						
						if ( !isset($users_posted[$postsdata['poster_id']]) )
							$users_posted[$postsdata['poster_id']] = 1;
						else
							$users_posted[$postsdata['poster_id']]++;
						
					}
					
					foreach ( $users_posted as $userid => $postcount ) {
						
						//
						// Adjust the count for every user that posted
						//
						if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."members SET posts = posts-".$postcount." WHERE id = ".$userid)) )
							$functions->usebb_die('SQL', 'Unable to adjust member\'s post count!', __FILE__, __LINE__);
						
					}
					
					//
					// 5. Delete posts within the deleted topic
					//
					if ( !($result = $db->query("DELETE FROM ".TABLE_PREFIX."posts WHERE topic_id = ".$_GET['topic'])) )
						$functions->usebb_die('SQL', 'Unable to delete posts!', __FILE__, __LINE__);
					
					//
					// 6. Adjust stats
					//
					if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."stats SET content = content-1 WHERE name = 'topics'")) )
						$functions->usebb_die('SQL', 'Unable to update stats (topics)!', __FILE__, __LINE__);
					
					if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."stats SET content = content-". ( $topicdata['count_replies']+1 ) ." WHERE name = 'posts'")) )
						$functions->usebb_die('SQL', 'Unable to update stats (posts)!', __FILE__, __LINE__);
					
					header('Location: '.$functions->get_config('board_url').$functions->make_url('forum.php', array('id' => $topicdata['forum_id'])));
					
				} else {
					
					header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
					
				}
				
			} else {
				
				//
				// Include the page header
				//
				require(ROOT_PATH.'sources/page_head.php');
				
				$template->set_page_title($lang['DeleteTopic']);
				$template->parse('confirm_form', 'global', array(
					'form_begin' => '<form action="'.$functions->make_url('edit.php', array('topic' => $_GET['topic'], 'act' => 'delete')).'" method="post">',
					'title' => $lang['DeleteTopic'],
					'content' => sprintf($lang['ConfirmDeleteTopic'], '<i>'.htmlentities(stripslashes($topicdata['topic_title'])).'</i>', '<i>'.htmlentities(stripslashes($topicdata['forum_name'])).'</i>'),
					'submit_button' => '<input type="submit" name="delete" value="'.$lang['Yes'].'" />',
					'cancel_button' => '<input type="submit" value="'.$lang['Cancel'].'" />',
					'form_end' => '</form>'
				));
				
				//
				// Include the page footer
				//
				require(ROOT_PATH.'sources/page_foot.php');
				
			}
			
		} else {
			
			$functions->redir_to_login();
			
		}
		
	}
	
} elseif ( $_GET['act'] == 'move' ) {
	
	//
	// Move topics
	//
	
	$session->update('movetopic:'.$_GET['topic']);
	
	//
	// Get topic information
	//
	if ( !($result = $db->query("SELECT t.topic_title, t.forum_id, t.count_replies, p.post_time, f.name AS forum_name, f.auth, f.last_topic_id FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."forums f WHERE t.forum_id = f.id AND p.id = t.last_post_id AND t.id = ".$_GET['topic'])) )
		$functions->usebb_die('SQL', 'Unable to get topic information!', __FILE__, __LINE__);
	
	if ( !$db->num_rows($result) ) {
		
		//
		// I didn't see that topic!?
		//
		
		//
		// Include the page header
		//
		require(ROOT_PATH.'sources/page_head.php');
		
		$template->set_page_title($lang['Error']);
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Error'],
			'content' => sprintf($lang['NoSuchTopic'], 'ID '.$_GET['topic'])
		));
		
		//
		// Include the page footer
		//
		require(ROOT_PATH.'sources/page_foot.php');
		
	} else {
		
		$topicdata = $db->fetch_result($result);
		
		//
		// If the user is granted to move topics
		//
		if ( $functions->auth($topicdata['auth'], 'move', $topicdata['forum_id']) ) {
			
			if ( !empty($_POST['new_forum_id']) && is_numeric($_POST['new_forum_id']) ) {
				
				if ( !empty($_POST['move']) ) {
					
					//
					// Get information about the new forum
					//
					if ( !($result = $db->query("SELECT f.last_topic_id, f.auth, p.post_time FROM ( ( ".TABLE_PREFIX."forums f LEFT JOIN ".TABLE_PREFIX."topics t ON t.id = f.last_topic_id ) LEFT JOIN ".TABLE_PREFIX."posts p ON p.id = t.last_post_id ) WHERE f.id = ".$_POST['new_forum_id'])) )
						$functions->usebb_die('SQL', 'Unable to get forum information!', __FILE__, __LINE__);
					if ( !$db->num_rows($result) ) {
						
						header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
						
					} else {
						
						$forumdata = $db->fetch_result($result);
						if ( !$functions->auth($forumdata['auth'], 'view', $_POST['new_forum_id']) ) {
							
							header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
							
						} else {
							
							//
							// Move the topic
							//
							if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."topics SET forum_id = ".$_POST['new_forum_id']." WHERE id = ".$_GET['topic'])) )
								$functions->usebb_die('SQL', 'Unable to update topic information!', __FILE__, __LINE__);
							
							if ( $topicdata['last_topic_id'] == $_GET['topic'] ) {
								
								//
								// Adjust the last updated topic
								//
								if ( !($result = $db->query("SELECT p.topic_id FROM ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."topics t WHERE p.topic_id = t.id AND t.forum_id = ".$topicdata['forum_id']." ORDER BY p.post_time DESC LIMIT 1")) )
									$functions->usebb_die('SQL', 'Unable to get last updated topic in forum!', __FILE__, __LINE__);
								if ( !$db->num_rows($result) ) {
									
									if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."forums SET topics = 0, posts = 0, last_topic_id = 0 WHERE id = ".$topicdata['forum_id'])) )
										$functions->usebb_die('SQL', 'Unable to adjust forum\'s last updated topic ID!', __FILE__, __LINE__);
									
									$old_forum_counts_updated = TRUE;
									
								} else {
									
									$lasttopicdata = $db->fetch_result($result);
									$update_old_last_topic_id = ', last_topic_id = '.$lasttopicdata['topic_id'];
									
								}
								
							} else {
								
								$update_old_last_topic_id = '';
								
							}
							
							$update_new_last_topic_id = ( $topicdata['post_time'] > $forumdata['post_time'] ) ? ', last_topic_id = '.$_GET['topic'] : '';
							
							//
							// Adjust forum counts
							//
							if ( !isset($old_forum_counts_updated) ) {
								
								if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."forums SET topics = topics-1, posts = posts-". ( $topicdata['count_replies']+1 ) .$update_old_last_topic_id." WHERE id = ".$topicdata['forum_id'])) )
									$functions->usebb_die('SQL', 'Unable to update old forum counts!', __FILE__, __LINE__);
								
							}
							
							if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."forums SET topics = topics+1, posts = posts+". ( $topicdata['count_replies']+1 ) .$update_new_last_topic_id." WHERE id = ".$_POST['new_forum_id'])) )
								$functions->usebb_die('SQL', 'Unable to update new forum counts!', __FILE__, __LINE__);
							
							header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
							
						}
						
					}
				
				} else {
					
					header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
					
				}
				
			} else {
				
				//
				// Include the page header
				//
				require(ROOT_PATH.'sources/page_head.php');
				
				$template->set_page_title($lang['MoveTopic']);
				
				if ( !($result = $db->query("SELECT c.id AS cat_id, c.name AS cat_name, f.id, f.name, f.auth FROM ".TABLE_PREFIX."cats c, ".TABLE_PREFIX."forums f WHERE c.id = f.cat_id AND f.id <> ".$topicdata['forum_id']." ORDER BY c.sort_id ASC, c.id ASC, f.sort_id ASC, f.id ASC")) )
					$functions->usebb_die('SQL', 'Unable to get available forums!', __FILE__, __LINE__);
				
				if ( $db->num_rows($result) === 1 ) {
					
					$forumdata = $db->fetch_result($result);
					$new_forum_input = '<a href="'.$functions->make_url('forum.php', array('id' => $forumdata['id'])).'">'.htmlentities(stripslashes($forumdata['name'])).'</a><input type="hidden" name="new_forum_id" value="'.$forumdata['id'].'" />';
					
				} else {
					
					//
					// Get a list of available forums to move to
					//
					$new_forum_input = '<select name="new_forum_id">';
					$seen_cats = array();
					while ( $forumdata = $db->fetch_result($result) ) {
						
						if ( $functions->auth($forumdata['auth'], 'view', $forumdata['id']) ) {
							
							if ( !in_array($forumdata['cat_id'], $seen_cats) ) {
								
								$new_forum_input .= ( !count($seen_cats) ) ? '' : '</optgroup>';
								$new_forum_input .= '<optgroup label="'.$forumdata['cat_name'].'">';
								$seen_cats[] = $forumdata['cat_id'];
								
							}
							
							$new_forum_input .= '<option value="'.$forumdata['id'].'">'.htmlentities(stripslashes($forumdata['name'])).'</option>';
							
						}
						
					}
					$new_forum_input .= '</optgroup></select>';
					
				}
				
				$template->parse('move_topic_form', 'various', array(
					'form_begin' => '<form action="'.$functions->make_url('edit.php', array('topic' => $_GET['topic'], 'act' => 'move')).'" method="post">',
					'move_topic' => $lang['MoveTopic'],
					'topic' => $lang['Topic'],
					'topic_v' => '<a href="'.$functions->make_url('topic.php', array('id' => $_GET['topic'])).'">'.htmlentities(stripslashes($topicdata['topic_title'])).'</a>',
					'old_forum' => $lang['OldForum'],
					'old_forum_v' => '<a href="'.$functions->make_url('forum.php', array('id' => $topicdata['forum_id'])).'">'.htmlentities(stripslashes($topicdata['forum_name'])).'</a>',
					'new_forum' => $lang['NewForum'],
					'new_forum_input' => $new_forum_input,
					'submit_button' => '<input type="submit" name="move" value="'.$lang['MoveTopic'].'" />',
					'cancel_button' => '<input type="submit" value="'.$lang['Cancel'].'" />',
					'form_end' => '</form>'
				));
				
				//
				// Include the page footer
				//
				require(ROOT_PATH.'sources/page_foot.php');
				
			}
			
		} else {
			
			$functions->redir_to_login();
			
		}
		
	}
	
} elseif ( $_GET['act'] == 'lock' ) {
	
	//
	// Lock topics
	//
	$session->update('locktopic:'.$_GET['topic']);
	
	if ( !($result = $db->query("SELECT t.status_locked, f.id, f.auth FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = ".$_GET['topic']." AND f.id = t.forum_id")) )
		$functions->usebb_die('SQL', 'Unable to get topic information!', __FILE__, __LINE__);
	
	if ( !$db->num_rows($result) ) {
		
		header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
		
	} else {
		
		$topicdata = $db->fetch_result($result);
		if ( !$functions->auth($topicdata['auth'], 'lock', $topicdata['id']) ) {
			
			$functions->redir_to_login();
			
		} else {
			
			if ( $topicdata['status_locked'] ) {
				
				header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
				
			} else {
				
				if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."topics SET status_locked = 1 WHERE id = ".$_GET['topic'])) )
					$functions->usebb_die('SQL', 'Unable to update topic lock status!', __FILE__, __LINE__);
				
				header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
				
			}
			
		}
		
	}
	
} elseif ( $_GET['act'] == 'unlock' ) {
	
	//
	// Unlock topics
	//
	$session->update('unlocktopic:'.$_GET['topic']);
	
	if ( !($result = $db->query("SELECT t.status_locked, f.id, f.auth FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = ".$_GET['topic']." AND f.id = t.forum_id")) )
		$functions->usebb_die('SQL', 'Unable to get topic information!', __FILE__, __LINE__);
	
	if ( !$db->num_rows($result) ) {
		
		header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
		
	} else {
		
		$topicdata = $db->fetch_result($result);
		if ( !$functions->auth($topicdata['auth'], 'lock', $topicdata['id']) ) {
			
			$functions->redir_to_login();
			
		} else {
			
			if ( !$topicdata['status_locked'] ) {
				
				header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
				
			} else {
				
				if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."topics SET status_locked = 0 WHERE id = ".$_GET['topic'])) )
					$functions->usebb_die('SQL', 'Unable to update topic lock status!', __FILE__, __LINE__);
				
				header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
				
			}
			
		}
		
	}
	
} elseif ( $_GET['act'] == 'sticky' ) {
	
	//
	// Sticky topics
	//
	$session->update('stickytopic:'.$_GET['topic']);
	
	if ( !($result = $db->query("SELECT t.status_sticky, f.id, f.auth FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = ".$_GET['topic']." AND f.id = t.forum_id")) )
		$functions->usebb_die('SQL', 'Unable to get topic information!', __FILE__, __LINE__);
	
	if ( !$db->num_rows($result) ) {
		
		header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
		
	} else {
		
		$topicdata = $db->fetch_result($result);
		if ( !$functions->auth($topicdata['auth'], 'sticky', $topicdata['id']) ) {
			
			$functions->redir_to_login();
			
		} else {
			
			if ( $topicdata['status_sticky'] ) {
				
				header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
				
			} else {
				
				if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."topics SET status_sticky = 1 WHERE id = ".$_GET['topic'])) )
					$functions->usebb_die('SQL', 'Unable to update topic sticky status!', __FILE__, __LINE__);
				
				header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
				
			}
			
		}
		
	}
	
} elseif ( $_GET['act'] == 'unsticky' ) {
	
	//
	// "Unsticky" topics
	// -does that word exist?-
	//
	$session->update('unstickytopic:'.$_GET['topic']);
	
	if ( !($result = $db->query("SELECT t.status_sticky, f.id, f.auth FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = ".$_GET['topic']." AND f.id = t.forum_id")) )
		$functions->usebb_die('SQL', 'Unable to get topic information!', __FILE__, __LINE__);
	
	if ( !$db->num_rows($result) ) {
		
		header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
		
	} else {
		
		$topicdata = $db->fetch_result($result);
		if ( !$functions->auth($topicdata['auth'], 'sticky', $topicdata['id']) ) {
			
			$functions->redir_to_login();
			
		} else {
			
			if ( !$topicdata['status_sticky'] ) {
				
				header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
				
			} else {
				
				if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."topics SET status_sticky = 0 WHERE id = ".$_GET['topic'])) )
					$functions->usebb_die('SQL', 'Unable to update topic sticky status!', __FILE__, __LINE__);
				
				header('Location: '.$functions->get_config('board_url').$functions->make_url('topic.php', array('id' => $_GET['topic'])));
				
			}
			
		}
		
	}
	
}

?>
