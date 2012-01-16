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
 * ACP forum pruning
 *
 * Ables to prune forums.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2012 UseBB Team
 * @package	UseBB
 * @subpackage	ACP
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

$forum_ids = $admin_functions->get_forums_array();
$forum_ids = array_keys($forum_ids);

if ( !empty($_POST['forums']) && is_array($_POST['forums']) && count($_POST['forums']) ) {
	
	$sanatized_forums = array();
	foreach ( $_POST['forums'] as $forum ) {
		
		if ( valid_int($forum) && in_array($forum, $forum_ids) )
			$sanatized_forums[] = $forum;
		
	}
	$_POST['forums'] = $sanatized_forums;
	
} else {
	
	$_POST['forums'] = array();
	
}

if ( count($_POST['forums']) && !empty($_POST['action']) && ( $_POST['action'] == 'delete' || ( $_POST['action'] == 'move' && !empty($_POST['move_to']) && valid_int($_POST['move_to']) && in_array($_POST['move_to'], $forum_ids) && !in_array($_POST['move_to'], $_POST['forums']) ) || $_POST['action'] == 'lock' ) && !empty($_POST['latest_post']) && valid_int($_POST['latest_post']) && $_POST['latest_post'] > 0 && !empty($_POST['confirm']) && $functions->verify_form() ) {
	
	//
	// What we need:
	// - topic ID's te prune
	// - total number of topics
	// - total number of posts
	//
	
	$lock_topics_part = ( $_POST['action'] == 'lock' ) ? " AND t.status_locked = 0" : "";
	$exclude_stickies_part = ( !empty($_POST['exclude_stickies']) ) ? " AND t.status_sticky = 0" : "";	
	$result = $db->query("SELECT t.id as topic_id, f.id as forum_id, t.count_replies FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."forums f, ".TABLE_PREFIX."posts p WHERE t.forum_id = f.id AND p.id = t.last_post_id AND f.id IN (".join(', ', $_POST['forums']).") AND p.post_time < ".( time() - $_POST['latest_post'] * 86400 ).$exclude_stickies_part.$lock_topics_part);
	
	$forums = $topics = array();
	$total = array('topics' => 0, 'posts' => 0);
	while ( $topicdata = $db->fetch_result($result) ) {
		
		if ( !array_key_exists($topicdata['forum_id'], $forums) )
			$forums[$topicdata['forum_id']] = array('topics' => 0, 'posts' => 0);
		
		$forums[$topicdata['forum_id']]['topics']++;
		$forums[$topicdata['forum_id']]['posts'] += ($topicdata['count_replies']+1);
		
		$total['topics']++;
		$total['posts'] += ($topicdata['count_replies']+1);
		
		$topics[] = $topicdata['topic_id'];
		
	}
	
	if ( $total['topics'] ) {
		
		if ( $_POST['action'] == 'move' ) {
			
			//
			// Move topics
			//
			$db->query("UPDATE ".TABLE_PREFIX."topics SET forum_id = ".$_POST['move_to']." WHERE id IN (".join(', ', $topics).")");
			foreach ( $forums as $id => $val )
				$db->query("UPDATE ".TABLE_PREFIX."forums SET topics = topics - ".$val['topics'].", posts = posts - ".$val['posts']." WHERE id = ".$id);
			$db->query("UPDATE ".TABLE_PREFIX."forums SET topics = topics + ".$total['topics'].", posts = posts + ".$total['posts']." WHERE id = ".$_POST['move_to']);
			
		} elseif ( $_POST['action'] == 'delete' ) {
			
			//
			// Delete topics
			//
			$db->query("DELETE FROM ".TABLE_PREFIX."topics WHERE id IN (".join(', ', $topics).")");
			$db->query("DELETE FROM ".TABLE_PREFIX."posts WHERE topic_id IN (".join(', ', $topics).")");
			$db->query("DELETE FROM ".TABLE_PREFIX."subscriptions WHERE topic_id IN (".join(', ', $topics).")");

			foreach ( $forums as $id => $val )
				$db->query("UPDATE ".TABLE_PREFIX."forums SET topics = topics - ".$val['topics'].", posts = posts - ".$val['posts']." WHERE id = ".$id);

			$functions->set_stats('topics', - $total['topics'], true);
			$functions->set_stats('posts', - $total['posts'], true);
			
		} else {
			
			//
			// Lock topics
			//
			$db->query("UPDATE ".TABLE_PREFIX."topics SET status_locked = 1 WHERE id IN (".join(', ', $topics).")");
			
		}
		
	}
	
	$db->query("UPDATE ".TABLE_PREFIX."forums SET last_topic_id = 0 WHERE topics = 0");
	
	$content = '<p>'.sprintf($lang['PruneForumsDone'], $total['topics']).'</p>';
	
} else {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		$errors = array();
		if ( !count($_POST['forums']) )
			$errors[] = $lang['PruneForumsForums'];
		if ( empty($_POST['action']) || !in_array($_POST['action'], array('delete', 'move', 'lock')) )
			$errors[] = $lang['PruneForumsAction'];
		if ( !empty($_POST['action']) && $_POST['action'] == 'move' && ( empty($_POST['move_to']) || !valid_int($_POST['move_to']) || !in_array($_POST['move_to'], $forum_ids) ) )
			$errors[] = $lang['PruneForumsMoveTo'];
		if ( empty($_POST['latest_post']) || !valid_int($_POST['latest_post']) || $_POST['latest_post'] <= 0 )
			$errors[] = $lang['PruneForumsTopicAge'];
		
		//
		// Show an error message
		//
		if ( count($errors) )
			$content .= '<p><strong>'.sprintf($lang['MissingFields'], join(', ', $errors)).'</strong></p>';
		
		//
		// Move to forum may not be selected for pruning
		//
		if ( count($_POST['forums']) && !empty($_POST['action']) && $_POST['action'] == 'move' && !empty($_POST['move_to']) && valid_int($_POST['move_to']) && in_array($_POST['move_to'], $forum_ids) && in_array($_POST['move_to'], $_POST['forums']) )
			$content .= '<p><strong>'.$lang['PruneForumsMoveToForumSelectedForPruning'].'</strong></p>';
		
		$delete_checked = ( !empty($_POST['action']) && $_POST['action'] == 'delete' ) ? ' checked="checked"' : '';
		$move_checked = ( !empty($_POST['action']) && $_POST['action'] == 'move' ) ? ' checked="checked"' : '';
		$lock_checked = ( !empty($_POST['action']) && $_POST['action'] == 'lock' ) ? ' checked="checked"' : '';
		$exclude_stickies_checked = ( !empty($_POST['exclude_stickies']) ) ? ' checked="checked"' : '';
		$_POST['latest_post'] = ( !empty($_POST['latest_post']) && valid_int($_POST['latest_post']) && $_POST['latest_post'] > 0 ) ? $_POST['latest_post'] : '';
		
		//
		// Not confirmed
		//
		if ( empty($_POST['confirm']) )
			$content .= '<p><strong>'.$lang['PruneForumsNotConfirmed'].'</strong></p>';
		
	} else {
		
		$content = '<p>'.$lang['PruneForumsExplain'].'</p>';
		
		$delete_checked = '';
		$move_checked = '';
		$lock_checked = ' checked="checked"';
		$exclude_stickies_checked = ' checked="checked"';
		$_POST['latest_post'] = '';
		
	}
	
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'prune_forums')).'" method="post">';
	$content .= '<table id="adminregulartable">';
		$content .= '<tr><td class="fieldtitle">'.$lang['PruneForumsForums'].'</td><td>'.$admin_functions->forum_select_box('forums').'</td></tr>';
		$content .= '<tr><td class="fieldtitle">'.$lang['PruneForumsAction'].'</td><td>';
			$content .= '<label><input type="radio" name="action" value="lock"'.$lock_checked.' /> '.$lang['PruneForumsActionLock'].'</label> ';
			$content .= '<label><input type="radio" name="action" value="move"'.$move_checked.' /> '.$lang['PruneForumsActionMove'].'</label> ';
			$content .= '<label><input type="radio" name="action" value="delete"'.$delete_checked.' /> '.$lang['PruneForumsActionDelete'].'</label>';
		$content .= '</td></tr>';
		$content .= '<tr><td class="fieldtitle">'.$lang['PruneForumsMoveTo'].'</td><td>'.$admin_functions->forum_select_box('move_to', false).'</td></tr>';
		$content .= '<tr><td class="fieldtitle">'.$lang['PruneForumsTopicAge'].'</td><td>'.sprintf($lang['PruneForumsTopicAgeField'], '<input type="text" name="latest_post" size="4" maxlength="4" value="'.$_POST['latest_post'].'" />').'</td></tr>';
		$content .= '<tr><td class="fieldtitle">'.$lang['PruneForumsExcludeStickies'].'</td><td><label><input type="checkbox" name="exclude_stickies" value="1"'.$exclude_stickies_checked.' /> '.$lang['PruneForumsExcludeStickies'].'</label></td></tr>';
		$content .= '<tr><td class="fieldtitle">'.$lang['PruneForumsConfirm'].'</td><td><label><input type="checkbox" name="confirm" value="1" /> '.$lang['PruneForumsConfirmText'].'</label></td></tr>';
		$content .= '<tr><td colspan="2" class="submit"><input type="submit" value="'.$lang['PruneForumsStart'].'" />'.$admin_functions->form_token().' <input type="reset" value="'.$lang['Reset'].'" /></td></tr>';
	$content .= '</table></form>';
	
}

$admin_functions->create_body('prune_forums', $content);

?>
