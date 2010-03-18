<?php

/*
	Copyright (C) 2003-2007 UseBB Team
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
 * @copyright	Copyright (C) 2003-2007 UseBB Team
 * @package	UseBB
 * @subpackage	ACP
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

if ( !empty($_GET['id']) && valid_int($_GET['id']) ) {
	
	$result = $db->query("SELECT id, name FROM ".TABLE_PREFIX."members WHERE id = ".$_GET['id']);
	$memberdata = $db->fetch_result($result);
	
	if ( $memberdata['id'] ) {
		
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
					
					$userid = $memberdata['id'];
					$topic_replies = $forum_lasts = $topic_firsts = $topic_lasts = array();
					
					// 1. Delete post entries
					while ( $result = $db->query("SELECT t.id, t.count_replies, t.first_post_id, t.last_post_id, p.id AS post_id, f.forum_id, f.last_topic_id FROM ".TABLE_PREFIX."posts p, ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f WHERE t.id = p.topic_id AND f.id = t.forum_id AND p.poster_id = ".$userid) ) {
						
						// Calculate the remanining replies in the end
						if ( !array_key_exists($result['id'], $topic_replies) )
							$topic_replies[$result['id']] = $result['count_replies']-1;
						else
							$topic_replies[$result['id']]--;
						
						// Collect topics with to reset first/last ID
						if ( $result['first_post_id'] == $result['post_id'] )
							$topic_firsts[] = $result['id'];
						if ( $result['last_post_id'] == $result['post_id'] ) {
							
							$topic_lasts[] = $result['id'];
							if ( $result['id'] == $result['last_topic_id'] )
								$forum_lasts = $result['forum_id'];
						}

					}
					$db->query("DELETE FROM ".TABLE_PREFIX."posts WHERE poster_id = ".$userid);
					
					// 2. Delete topic entries for no replies
					$delete_topics = array();
					foreach ( $topic_replies as $topic => $replies ) {
						
						// Collect empty topics
						if ( $replies < 1 )
							$delete_topics[] = $topic;
						
					}
					if ( count($delete_topics) ) {
						
						$db->query("DELETE FROM ".TABLE_PREFIX."topics WHERE id IN(".implode(',', $delete_topics).")");
						$db->query("DELETE FROM ".TABLE_PREFIX."subscriptions WHERE topic_id IN(".implode(',', $delete_topics).")");
						
					}

					// 3. Adjust topic first and last post IDs
					foreach ( $topic_firsts as $topic ) {
						
						if ( array_key_exists($topic, $delete_topics) )
							continue;

						$result = $db->query("SELECT id FROM ".TABLE_PREFIX."posts WHERE topic_id = ".$topic." ORDER BY id ASC LIMIT 1");
						$db->query("UPDATE ".TABLE_PREFIX."topics SET first_post_id = ".$result['id']." WHERE id = ".$topic);	
					}
					foreach ( $topic_lasts as $topic ) {
						
						if ( array_key_exists($topic, $delete_topics) )
							continue;

						$result = $db->query("SELECT id FROM ".TABLE_PREFIX."posts WHERE topic_id = ".$topic." ORDER BY id DESC LIMIT 1");
						$db->query("UPDATE ".TABLE_PREFIX."topics SET last_post_id = ".$result['id']." WHERE id = ".$topic);	
					}
					
					// 4. Adjust topic reply counts
					foreach ( $topic_replies as $topic => $replies ) {
						
						if ( array_key_exists($topic, $delete_topics) )
							continue;

						$db->query("UPDATE ".TABLE_PREFIX."topics SET count_replies = ".$replies." WHERE id = ".$topic);
						
					}
					
					// 5. Adjust forum latest updated topic
					foreach ( $forum_lasts as $forum ) {
						
						
						
					}
					
					// 6. Adjust forum counters
					
					// 7. Adjust global stats
					
				} else {
					
					//
					// Reassign the posts to guest
					//
					$db->query("UPDATE ".TABLE_PREFIX."posts SET poster_id = 0, poster_guest = '".$memberdata['name']."' WHERE poster_id = ".$_GET['id']);
					
				}
				
				$db->query("UPDATE ".TABLE_PREFIX."posts SET post_edit_by = 0 WHERE post_edit_by = ".$_GET['id']);
				$db->query("DELETE FROM ".TABLE_PREFIX."subscriptions WHERE user_id = ".$_GET['id']);
				$db->query("DELETE FROM ".TABLE_PREFIX."moderators WHERE user_id = ".$_GET['id']);
				$db->query("DELETE FROM ".TABLE_PREFIX."members WHERE id = ".$_GET['id']);
				$db->query("DELETE FROM ".TABLE_PREFIX."sessions WHERE user_id = ".$_GET['id']);
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
	$content .= '<p>'.$lang['DeleteMembersSearchMemberExplain'].': <input type="text" name="search_member" id="search_member" size="20" maxlength="255" value="'.unhtml(stripslashes($search_member)).'" /> <input type="submit" value="'.$lang['Search'].'" /></p>';
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
