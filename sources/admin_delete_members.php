<?php

/*
	Copyright (C) 2003-2010 UseBB Team
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
 * ACP member deletion
 *
 * Gives an interface to delete members.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2010 UseBB Team
 * @package	UseBB
 * @subpackage	ACP
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

if ( !empty($_GET['id']) && valid_int($_GET['id']) ) {
	
	$result = $db->query("SELECT id, name, email FROM ".TABLE_PREFIX."members WHERE id = ".$_GET['id']);
	$memberdata = $db->fetch_result($result);
	
	if ( $memberdata['id'] ) {
		
		$userid = $memberdata['id'];
		$email = $memberdata['email'];
		$email_wildcard = substr_replace($email, '*', 0, strpos($email, '@'));
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			if ( !empty($_POST['delete']) ) {
				
				//
				// Delete the member
				//

				if ( !empty($_POST['deleteposts']) ) {
					
					//
					// Delete the posts
					// This is a tiresome process since there is no ORM or API in 1.0.
					// A small one could have been added, but since this code was added
					// after the closing of 1.0 development it is faster this way.
					// It is unlikely this code will have to be edited a lot. 
					//
					
					$topics = $posts = 0;
					$topic_replies = $topic_firsts = $topic_lasts = $delete_topics = array();
					
					// 1. Delete post entries
					$result = $db->query("SELECT t.id AS topic_id, t.count_replies, t.first_post_id, t.last_post_id, p.id AS post_id FROM ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."topics t WHERE t.id = p.topic_id AND p.poster_id = ".$userid);
					while ( $data = $db->fetch_result($result) ) {
						
						// Calculate the remanining replies in the end
						if ( !array_key_exists($data['topic_id'], $topic_replies) )
							$topic_replies[$data['topic_id']] = $data['count_replies']-1;
						else
							$topic_replies[$data['topic_id']]--;
						
						// Collect topics with to reset first/last ID
						if ( $data['first_post_id'] == $data['post_id'] )
							$topic_firsts[] = $data['topic_id'];
						if ( $data['last_post_id']  == $data['post_id'] )
							$topic_lasts[]  = $data['topic_id'];

					}
					$db->query("DELETE FROM ".TABLE_PREFIX."posts WHERE poster_id = ".$userid);
					
					// 2. Delete topic entries with no replies
					foreach ( $topic_replies as $topic => $replies ) {
						
						// Collect empty topics
						if ( $replies < 0 ) # Smaller than zero, because 0 = no replies but one post
							$delete_topics[] = $topic;
						
					}
					if ( count($delete_topics) > 0 ) {
						
						$db->query("DELETE FROM ".TABLE_PREFIX."topics WHERE id IN(".implode(',', $delete_topics).")");
						$db->query("DELETE FROM ".TABLE_PREFIX."subscriptions WHERE topic_id IN(".implode(',', $delete_topics).")");
						
					}

					// 3. Adjust topic first and last post IDs
					$topic_firsts = array_diff($topic_firsts, $delete_topics);
					$topic_lasts  = array_diff($topic_lasts,  $delete_topics);
					foreach ( $topic_firsts as $topic ) {
						
						// Get the first ASC
						$result = $db->query("SELECT id FROM ".TABLE_PREFIX."posts WHERE topic_id = ".$topic." ORDER BY id ASC LIMIT 1");
						$data = $db->fetch_result($result);
						$db->query("UPDATE ".TABLE_PREFIX."topics SET first_post_id = ".$data['id']." WHERE id = ".$topic);	
					}
					foreach ( $topic_lasts as $topic ) {
						
						// Get the first DESC
						$result = $db->query("SELECT id FROM ".TABLE_PREFIX."posts WHERE topic_id = ".$topic." ORDER BY id DESC LIMIT 1");
						$data = $db->fetch_result($result);
						$db->query("UPDATE ".TABLE_PREFIX."topics SET last_post_id = ".$data['id']." WHERE id = ".$topic);	
					}
					
					// 4. Adjust topic reply counts
					foreach ( $topic_replies as $topic => $replies ) {
						
						if ( in_array($topic, $delete_topics) )
							continue;

						$db->query("UPDATE ".TABLE_PREFIX."topics SET count_replies = ".$replies." WHERE id = ".$topic);

					}
					
					// 5. Adjust forum latest updated topic and counters
					// Simply do this for all forums, or it gets too complicated above
					$result = $db->query("SELECT forum_id, COUNT(id) AS topics, SUM(count_replies) AS replies FROM ".TABLE_PREFIX."topics GROUP BY forum_id");
					while ( $forum = $db->fetch_result($result) ) {
						
						$topics += $forum['topics'];
						$posts += ($forum['topics'] + $forum['replies']); # Because replies does not include the first post

						if ( $forum['topics'] > 0 ) {
							
							// Select last updated topic in forum
							$topicresult = $db->query("SELECT id FROM ".TABLE_PREFIX."topics WHERE forum_id = ".$forum['forum_id']." ORDER BY last_post_id DESC LIMIT 1");
							$topic = $db->fetch_result($topicresult);
							$last_topic_id = $topic['id'];

						} else {
							
							$last_topic_id = 0;

						}
						$db->query("UPDATE ".TABLE_PREFIX."forums SET topics = ".$forum['topics'].", posts = ".($forum['topics'] + $forum['replies']).", last_topic_id = ".$last_topic_id." WHERE id = ".$forum['forum_id']);

					}
					
					// 6. Adjust global stats
					$db->query("UPDATE ".TABLE_PREFIX."stats SET content = ".$topics." WHERE name = 'topics'");
					$db->query("UPDATE ".TABLE_PREFIX."stats SET content = ".$posts." WHERE name = 'posts'");
					
				} else {
					
					//
					// Reassign the posts to guest
					//
					$db->query("UPDATE ".TABLE_PREFIX."posts SET poster_id = 0, poster_guest = '".$memberdata['name']."' WHERE poster_id = ".$userid);
					
				}

				//
				// Ban email address
				//
				if ( !empty($_POST['banemail']) ) {
					
					$toban = '';
					switch ( $_POST['banemail'] ) {
						
						// example@example.net
						case 'email':
							$toban = $email;
							break;
						// *@example.net
						case 'wildcard':
							$toban = $email_wildcard;
							break;

					}

					if ( !empty($toban) ) {
						
						$db->query("DELETE FROM ".TABLE_PREFIX."bans WHERE email = '".$toban."'");
						$db->query("INSERT INTO ".TABLE_PREFIX."bans VALUES(NULL, '', '".$toban."', '')");
						
					}

				}
				
				$db->query("UPDATE ".TABLE_PREFIX."posts SET post_edit_by = 0 WHERE post_edit_by = ".$userid);
				$db->query("DELETE FROM ".TABLE_PREFIX."subscriptions WHERE user_id = ".$userid);
				$db->query("DELETE FROM ".TABLE_PREFIX."moderators WHERE user_id = ".$userid);
				$db->query("DELETE FROM ".TABLE_PREFIX."members WHERE id = ".$userid);
				$db->query("DELETE FROM ".TABLE_PREFIX."sessions WHERE user_id = ".$userid);
				$db->query("UPDATE ".TABLE_PREFIX."stats SET content = content-1 WHERE name = 'members'");
				
				$content = '<p>'.sprintf($lang['DeleteMembersComplete'], '<em>'.unhtml(stripslashes($memberdata['name'])).'</em>').'</p>';
				
			} else {
				
				$functions->redirect('admin.php', array('act' => 'delete_members'));
				
			}
			
		} else {
			
			$content = '<h2>'.$lang['DeleteMembersConfirmMemberDelete'].'</h2>';
			$content .= '<p><strong>'.sprintf($lang['DeleteMembersConfirmMemberDeleteContent'], '<em>'.unhtml(stripslashes($memberdata['name'])).'</em>').'</strong></p>';
			$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'delete_members', 'id' => $_GET['id'])).'" method="post">';
			$content .= '<p><label><input type="checkbox" name="deleteposts" value="1" />  '.$lang['DeleteMembersDeletePosts'].'</label></p>';
			$content .= '<fieldset><legend>'.$lang['DeleteMembersBanEmail'].'</legend>';
				$content .= '<label><input type="radio" name="banemail" value="email" /> '.$email.'</label> &nbsp;';
				$content .= '<label><input type="radio" name="banemail" value="wildcard" /> '.$email_wildcard.'</label> &nbsp;';
				$content .= '<label><input type="radio" name="banemail" value="0" checked="checked" /> '.$lang['No'].'</label>';
			$content .= '</fieldset>';
			$content .= '<p class="submit"><input type="submit" name="delete" value="'.$lang['Delete'].'" /> <input type="submit" value="'.$lang['Cancel'].'" /></p>';
			$content .= '</form>';
			
		}
		
	} else {
		
		$functions->redirect('admin.php', array('act' => 'delete_members'));
		
	}
	
} else {
	
	$search_member = ( !empty($_POST['search_member']) ) ? $_POST['search_member'] : '';
	
	$content = '<h2>'.$lang['DeleteMembersSearchMember'].'</h2>';
	$content .= '<p>'.$lang['DeleteMembersSearchMemberInfo'].'</p>';
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'delete_members')).'" method="post">';
	$content .= '<p>'.$lang['DeleteMembersSearchMemberExplain'].': <input type="text" name="search_member" id="search_member" size="25" maxlength="255" value="'.unhtml(stripslashes($search_member)).'" /> <input type="submit" value="'.$lang['Search'].'" /></p>';
	$content .= '</form>';
	
	if ( !empty($search_member) ) {
		
		$search_member_sql = preg_replace(array('#%#', '#_#', '#\s+#'), array('\%', '\_', ' '), $_POST['search_member']);
		$result = $db->query("SELECT id, name, displayed_name FROM ".TABLE_PREFIX."members WHERE name LIKE '%".$search_member_sql."%' OR displayed_name LIKE '%".$search_member_sql."%' ORDER BY name ASC");
		$matching_members = array();
		while ( $memberdata = $db->fetch_result($result) )
			$matching_members[$memberdata['id']] = array(unhtml(stripslashes($memberdata['name'])), unhtml(stripslashes($memberdata['displayed_name'])));
		
		if ( count($matching_members) ) {
			
			$select = '<select name="id">';
			foreach ( $matching_members as $key => $val )
				$select .= '<option value="'.$key.'">'.$val[0].' ('.$val[1].')</option>';
			$select .= '</select>';
			
			$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'delete_members')).'" method="get">';
			$content .= '<p>'.$lang['DeleteMembersSearchMemberList'].': <input type="hidden" name="act" value="delete_members" />'.$select.' <input type="submit" value="'.$lang['Delete'].'" /></p>';
			$content .= '</form>';
			
		} else {
			
			$content .= '<p>'.sprintf($lang['DeleteMembersSearchMemberNotFound'], '<em>'.unhtml(stripslashes($_POST['search_member'])).'</em>').'</p>';
			
		}
		
	}
	
	$template->set_js_onload("set_focus('search_member')");
	
}

$admin_functions->create_body('delete_members', $content);

?>
