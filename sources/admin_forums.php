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

$forums = $admin_functions->get_forums_array();
$filled_in = true;
foreach ( $forums as $forum ) {
	
	if ( !isset($_POST['sort_id-'.$forum['id']]) || !valid_int($_POST['sort_id-'.$forum['id']]) )
		$filled_in = false;
	
}

$_GET['do'] = ( !empty($_GET['do']) ) ? $_GET['do'] : 'index';

if ( in_array($_GET['do'], array('index', 'adjustsortids', 'autosort')) ) {
	
	$content = '<p>'.$lang['ForumsInfo'].'</p>';
	$content .= '<ul id="adminfunctionsmenu">';
		$content .= '<li><a href="'.$functions->make_url('admin.php', array('act' => 'forums', 'do' => 'add')).'">'.$lang['ForumsAddNewForum'].'</a></li> ';
		$content .= '<li><a href="'.$functions->make_url('admin.php', array('act' => 'forums', 'do' => 'adjustsortids')).'">'.$lang['ForumsAdjustSortIDs'].'</a></li> ';
		$content .= '<li><a href="'.$functions->make_url('admin.php', array('act' => 'forums', 'do' => 'autosort')).'">'.$lang['ForumsSortAutomatically'].'</a></li> ';
	$content .= '</ul>';
	
	if ( !count($forums) ) {
		
		$content .= '<p>'.$lang['ForumsNoForumsExist'].'</p>';
		
	} else {
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			if ( $filled_in ) {
				
				foreach ( $forums as $forum )
					$db->query("UPDATE ".TABLE_PREFIX."forums SET sort_id = ".$_POST['sort_id-'.$forum['id']]." WHERE id = ".$forum['id']);
				$forums = $admin_functions->get_forums_array();
				$content .= '<p>'.$lang['ForumsSortChangesApplied'].'</p>';
				
			} else {
				
				$content .= '<p><strong>'.$lang['ForumsMissingFields'].'</strong></p>';
				
			}
			
		} elseif ( $_GET['do'] == 'adjustsortids' ) {
			
			$cat_id = 0;
			foreach ( $forums as $forum ) {
				
				if ( $forum['cat_id'] != $cat_id ) {
					
					$forum_sort_id = 1;
					$cat_id = $forum['cat_id'];
					
				} else {
					
					$forum_sort_id++;
					
				}
				
				$db->query("UPDATE ".TABLE_PREFIX."forums SET sort_id = ".$forum_sort_id." WHERE id = ".$forum['id']);
				
			}
			$forums = $admin_functions->get_forums_array();
			$content .= '<p>'.$lang['ForumsSortChangesApplied'].'</p>';
			
		} elseif ( $_GET['do'] == 'autosort' ) {
			
			foreach ( $forums as $forum )
				$db->query("UPDATE ".TABLE_PREFIX."forums SET sort_id = 0 WHERE id = ".$forum['id']);
			$forums = $admin_functions->get_forums_array();
			$content .= '<p>'.$lang['ForumsSortChangesApplied'].'</p>';
			
		}
		
		$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'forums')).'" method="post">';
		$content .= '<table id="adminforumstable"><tr><th>'.$lang['ForumsForumName'].'</th><th class="action">'.$lang['Edit'].'</th><th class="action">'.$lang['Delete'].'</th><th class="action">'.$lang['ForumsSortID'].'</th></tr>';
		
		$i = 1;
		if ( count($forums) ) {
			
			$cat_id = 0;
			foreach ( $forums as $forum ) {
				
				if ( $forum['cat_id'] != $cat_id ) {
					
					$content .= '<tr><th colspan="4"><em>'.unhtml(stripslashes($forum['cat_name'])).'</em></th></tr>';
					$cat_id = $forum['cat_id'];
					
				}
				
				$content .= '<tr><td><em>'.unhtml(stripslashes($forum['name'])).'</em></td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'forums', 'do' => 'edit', 'id' => $forum['id'])).'">'.$lang['Edit'].'</a></td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'forums', 'do' => 'delete', 'id' => $forum['id'])).'">'.$lang['Delete'].'</a></td><td class="action"><input type="text" name="sort_id-'.$forum['id'].'" value="'.$forum['sort_id'].'" size="3" maxlength="11" tabindex="'.$i.'" /></td></tr>';
				$i++;
				
			}
			
		}
		
		$content .= '<tr><td colspan="4" class="submit"><input type="submit" value="'.$lang['Save'].'" tabindex="'.$i.'" /> <input type="reset" value="'.$lang['Reset'].'" tabindex="'.($i+1).'" /></td></tr></table></form>';
		
	}
	
} elseif ( $_GET['do'] == 'delete' && !empty($_GET['id']) && valid_int($_GET['id']) && array_key_exists($_GET['id'], $forums) ) {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		if ( !empty($_POST['delete']) ) {
			
			if ( !empty($_POST['move_contents']) && valid_int($_POST['move_contents']) && array_key_exists($_POST['move_contents'], $forums) ) {
				
				$result = $db->query("SELECT f.id, f.topics, f.posts, f.last_topic_id, t.last_post_id FROM ".TABLE_PREFIX."forums f, ".TABLE_PREFIX."topics t WHERE t.id = f.last_topic_id AND f.id IN(".$_GET['id'].", ".$_POST['move_contents'].")");
				$old_forum = $new_forum = array();
				while ( $forumdata = $db->fetch_result($result) ) {
					
					if ( $forumdata['id'] == $_GET['id'] )
						$old_forum = $forumdata;
					else
						$new_forum = $forumdata;
					
				}
				
				$topics = $old_forum['topics'] + $new_forum['topics'];
				$posts = $old_forum['posts'] + $new_forum['posts'];
				$last_topic_id = ( $old_forum['last_post_id'] > $new_forum['last_post_id'] ) ? $old_forum['last_topic_id'] : $new_forum['last_topic_id'];
				
				$db->query("UPDATE ".TABLE_PREFIX."topics SET forum_id = ".$_POST['move_contents']." WHERE forum_id = ".$_GET['id']);
				$db->query("UPDATE ".TABLE_PREFIX."forums SET topics = ".$topics.", posts = ".$posts.", last_topic_id = ".$last_topic_id." WHERE id = ".$_POST['move_contents']);
				
			}
				
			$admin_functions->delete_forums('id = '.$_GET['id']);
			
		}
		
		header('Location: '.$functions->get_config('board_url').$functions->make_url('admin.php', array('act' => 'forums')));
		
	} else {
		
		$content = '<h2>'.$lang['ForumsConfirmForumDelete'].'</h2>';
		$content .= '<p><strong>'.sprintf($lang['ForumsConfirmForumDeleteContent'], '<em>'.unhtml(stripslashes($forums[$_GET['id']]['name'])).'</em>').'</strong></p>';
		$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'forums', 'do' => 'delete', 'id' => $_GET['id'])).'" method="post">';
		if ( count($forums) >= 2 ) {
			
			$content .= '<p>'.sprintf($lang['ForumsMoveContents'], $admin_functions->forum_select_box('move_contents', false, array($_GET['id']), '<option value="" class="strong">-'.$lang['ForumsDeleteContents'].'-</option>')).'</p>';
			
		}
		$content .= '<p class="submit"><input type="submit" name="delete" value="'.$lang['Delete'].'" /> <input type="submit" value="'.$lang['Cancel'].'" /></p>';
		$content .= '</form>';
		
	}
	
}/* elseif ( $_GET['do'] == 'edit' && !empty($_GET['id']) && valid_int($_GET['id']) && array_key_exists($_GET['id'], $forums) ) {
	
	$foruminfo = $forums[$_GET['id']];
	
	if ( !empty($_POST['name']) ) {
		
		$db->query("UPDATE ".TABLE_PREFIX."forums SET name = '".$_POST['name']."' WHERE id = ".$_GET['id']);
		header('Location: '.$functions->get_config('board_url').$functions->make_url('admin.php', array('act' => 'forums')));
		
	} else {
		
		$content = '<h2>'.sprintf($lang['ForumsEditingForum'], '<em>'.unhtml(stripslashes($foruminfo['name'])).'</em>').'</h2>';
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
			$content .= '<p><strong>'.sprintf($lang['MissingFields'], $lang['ForumsForumName']).'</strong></p>';
		
		$submitted_forum_info = array();
		foreach ( array('name') as $key )
			$submitted_forum_info[$key] = ( isset($_POST[$key]) ) ? $_POST[$key] : $foruminfo[$key];
		
		$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'forums', 'do' => 'edit', 'id' => $_GET['id'])).'" method="post">';
		$content .= '<table id="adminforumstable">';
		$content .= '<tr><td class="fieldtitle">'.$lang['ForumsForumName'].'</td><td><input type="text" size="30" name="name" id="name" maxlength="255" value="'.unhtml(stripslashes($submitted_forum_info['name'])).'" /></td></tr>';
		$content .= '<tr><td colspan="2" class="submit"><input type="submit" value="'.$lang['Edit'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></td></tr></table></form>';
		
		$template->set_js_onload("set_focus('name')");
		
	}
	
} elseif ( $_GET['do'] == 'add' ) {
	
	if ( !empty($_POST['name']) ) {
		
		$db->query("INSERT INTO ".TABLE_PREFIX."forums VALUES(NULL, '".$_POST['name']."', 0)");
		header('Location: '.$functions->get_config('board_url').$functions->make_url('admin.php', array('act' => 'forums')));
		
	} else {
		
		$content = '<h2>'.$lang['ForumsAddNewForum'].'</h2>';
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
			$content .= '<p><strong>'.sprintf($lang['MissingFields'], $lang['ForumsForumName']).'</strong></p>';
		
		$submitted_forum_info = array();
		foreach ( array('name') as $key )
			$submitted_forum_info[$key] = ( isset($_POST[$key]) ) ? $_POST[$key] : '';
		
		$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'forums', 'do' => 'add')).'" method="post">';
		$content .= '<table id="adminforumstable">';
		$content .= '<tr><td class="fieldtitle">'.$lang['ForumsForumName'].'</td><td><input type="text" size="30" name="name" id="name" maxlength="255" value="'.unhtml(stripslashes($submitted_forum_info['name'])).'" /></td></tr>';
		$content .= '<tr><td colspan="2" class="submit"><input type="submit" value="'.$lang['Add'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></td></tr></table></form>';
		
		$template->set_js_onload("set_focus('name')");
		
	}
	
}*/

$admin_functions->create_body('forums', $content);

?>
