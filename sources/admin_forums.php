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

/**
 * ACP forum management
 *
 * Gives an interface to manage forums on the board.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2005 UseBB Team
 * @package	UseBB
 * @subpackage	ACP
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
				
				if ( $forum['sort_id'] != $forum_sort_id )
					$db->query("UPDATE ".TABLE_PREFIX."forums SET sort_id = ".$forum_sort_id." WHERE id = ".$forum['id']);
				
			}
			$forums = $admin_functions->get_forums_array();
			$content .= '<p>'.$lang['ForumsSortChangesApplied'].'</p>';
			
		} elseif ( $_GET['do'] == 'autosort' ) {
			
			$db->query("UPDATE ".TABLE_PREFIX."forums SET sort_id = 0");
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
	
} elseif ( $_GET['do'] == 'delete' && !empty($_GET['id']) && array_key_exists($_GET['id'], $forums) ) {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		if ( !empty($_POST['delete']) ) {
			
			if ( !empty($_POST['move_contents']) && array_key_exists($_POST['move_contents'], $forums) ) {
				
				$result = $db->query("SELECT f.id, f.topics, f.posts, f.last_topic_id, t.last_post_id FROM ".TABLE_PREFIX."forums f LEFT JOIN ".TABLE_PREFIX."topics t ON t.id = f.last_topic_id WHERE f.id IN(".$_GET['id'].", ".$_POST['move_contents'].")");
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
				
			$admin_functions->delete_forums('id = '.$_GET['id'], false);
			
		}
		
		$functions->redirect('admin.php', array('act' => 'forums'));
		
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
	
} elseif ( ( $_GET['do'] == 'edit' && !empty($_GET['id']) && array_key_exists($_GET['id'], $forums) ) || $_GET['do'] == 'add' ) {
	
	$cats = $admin_functions->get_cats_array();
	
	if ( !count($cats) ) {
		
		$content = '<h2>'.$lang['ForumsAddNewForum'].'</h2><p>'.$lang['CategoriesNoCatsExist'].'</p>';
		
	} else {
		
		if ( $_GET['do'] == 'edit' ) {
			
			$foruminfo = $forums[$_GET['id']];
			foreach ( range(0, 9) as $authid )
				$foruminfo['auth'.$authid] = $foruminfo['auth'][$authid];
			
		}
		
		$user_levels = array(LEVEL_GUEST, LEVEL_MEMBER, LEVEL_MOD, LEVEL_ADMIN);
		$default_auth = '0011222223';
		
		if ( !empty($_POST['name']) && !empty($_POST['cat_id']) && array_key_exists($_POST['cat_id'], $cats) ) {
			
			$_POST['descr'] = ( !empty($_POST['descr']) ) ? $_POST['descr'] : '';
			$_POST['auto_lock'] = ( !empty($_POST['auto_lock']) && valid_int($_POST['auto_lock']) ) ? $_POST['auto_lock'] : 0;
			$_POST['status'] = ( !empty($_POST['status']) ) ? 1 : 0;
			$_POST['increase_post_count'] = ( !empty($_POST['increase_post_count']) ) ? 1 : 0;
			$_POST['hide_mods_list'] = ( !empty($_POST['hide_mods_list']) ) ? 1 : 0;
			
			if ( $_GET['do'] == 'edit' ) {
				
				$_POST['auth'] = '';
				foreach ( range(0, 9) as $authid )
					$_POST['auth'] .= ( isset($_POST['auth'.$authid]) && valid_int($_POST['auth'.$authid]) && in_array($_POST['auth'.$authid], $user_levels) ) ? $_POST['auth'.$authid] : $foruminfo['auth'.$authid];
				
				$db->query("UPDATE ".TABLE_PREFIX."forums SET
					name = '".$_POST['name']."',
					cat_id = '".$_POST['cat_id']."',
					descr = '".$_POST['descr']."',
					status = ".$_POST['status'].",
					auth = '".$_POST['auth']."',
					auto_lock = ".$_POST['auto_lock'].",
					increase_post_count = ".$_POST['increase_post_count'].",
					hide_mods_list = ".$_POST['hide_mods_list']."
				WHERE id = ".$_GET['id']);
				
			} else {
				
				$_POST['auth'] = '';
				foreach ( range(0, 9) as $authid )
					$_POST['auth'] .= ( !empty($_POST['auth'.$authid]) && valid_int($_POST['auth'.$authid]) && in_array($_POST['auth'.$authid], $user_levels) ) ? $_POST['auth'.$authid] : $default_auth[$authid];
				
				$db->query("INSERT INTO ".TABLE_PREFIX."forums VALUES(NULL, '".$_POST['name']."', '".$_POST['cat_id']."', '".$_POST['descr']."', ".$_POST['status'].", 0, 0, 0, 0, '".$_POST['auth']."', ".$_POST['auto_lock'].", ".$_POST['increase_post_count'].", ".$_POST['hide_mods_list'].")");
				
			}
			
			$functions->redirect('admin.php', array('act' => 'forums'));
			
		} else {
			
			if ( $_GET['do'] == 'edit' )
				$content = '<h2>'.sprintf($lang['ForumsEditingForum'], '<em>'.unhtml(stripslashes($foruminfo['name'])).'</em>').'</h2>';
			else
				$content = '<h2>'.$lang['ForumsAddNewForum'].'</h2>';
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				
				$errors = array();
				if ( empty($_POST['name']) )
					$errors[] = $lang['ForumsForumName'];
				if ( empty($_POST['cat_id']) || !array_key_exists($_POST['cat_id'], $cats) )
					$errors[] = $lang['ForumsCatName'];
				
				$content .= '<p><strong>'.sprintf($lang['MissingFields'], join(', ', $errors)).'</strong></p>';
				
			}
			
			if ( $_GET['do'] == 'edit' ) {
				
				foreach ( $foruminfo as $id => $val )
					$_POST[$id] = ( isset($_POST[$id]) ) ? $_POST[$id] : $val;
				
				$form = $functions->make_url('admin.php', array('act' => 'forums', 'do' => 'edit', 'id' => $_GET['id']));
				$action = $lang['Edit'];
				
			} else {
				
				$fields = array('name', 'cat_id', 'descr', 'status', 'auto_lock', 'increase_post_count', 'hide_mods_list');
				foreach ( range(0, 9) as $authid )
					$fields[] = 'auth'.$authid;
				
				foreach ( $fields as $id )
					$_POST[$id] = ( isset($_POST[$id]) ) ? $_POST[$id] : '';
				
				if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
					
					$_POST['status'] = 1;
					$_POST['increase_post_count'] = 1;
					foreach ( range(0, 9) as $authid )
						$_POST['auth'.$authid] = $default_auth[$authid];
					
				}
				
				$form = $functions->make_url('admin.php', array('act' => 'forums', 'do' => 'add'));
				$action = $lang['Add'];
				
			}
			
			$_POST['auto_lock'] = ( valid_int($_POST['auto_lock']) && $_POST['auto_lock'] > 0 ) ? $_POST['auto_lock'] : '';
			$status_checked = ( $_POST['status'] ) ? ' checked="checked"' : '';
			$increase_post_count_checked = ( $_POST['increase_post_count'] ) ? ' checked="checked"' : '';
			$hide_mods_list_checked = ( $_POST['hide_mods_list'] ) ? ' checked="checked"' : '';
			
			$category_select = '<select name="cat_id">';
			foreach ( $cats as $cat ) {
				
				$selected = ( $_POST['cat_id'] == $cat['id'] ) ? ' selected="selected"' : '';
				$category_select .= '<option value="'.$cat['id'].'"'.$selected.'>'.unhtml(stripslashes($cat['name'])).'</option>';
				
			}
			$category_select .= '</select>';
			
			$content .= '<form action="'.$form.'" method="post">';
			$content .= '<table id="adminforumstable">';
				$content .= '<tr><th colspan="2">'.$lang['ForumsGeneral'].'</th></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['ForumsForumName'].'</td><td><input type="text" size="30" name="name" id="name" maxlength="255" value="'.unhtml(stripslashes($_POST['name'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['ForumsCatName'].'</td><td>'.$category_select.'</td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['ForumsDescription'].'</td><td><textarea name="descr" rows="3" cols="50">'.unhtml(stripslashes($_POST['descr'])).'</textarea></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['ForumsStatus'].'</td><td><input type="checkbox" name="status" id="status" value="1"'.$status_checked.' /><label for="status"> '.$lang['ForumsStatusOpen'].'</label></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['ForumsAutoLock'].'</td><td><input type="text" size="11" name="auto_lock" maxlength="11" value="'.$_POST['auto_lock'].'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['ForumsIncreasePostCount'].'</td><td><input type="checkbox" name="increase_post_count" id="increase_post_count" value="1"'.$increase_post_count_checked.' /><label for="increase_post_count"> '.$lang['Yes'].'</label></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['ForumsHideModsList'].'</td><td><input type="checkbox" name="hide_mods_list" id="hide_mods_list" value="1"'.$hide_mods_list_checked.' /><label for="hide_mods_list"> '.$lang['Yes'].'</label></td></tr>';
				
				$content .= '<tr><th colspan="2">'.$lang['ForumsAuth'].'</th></tr><tr><td colspan="2"><strong>'.$lang['ForumsAuthNote'].'</strong></td></tr>';
				
				foreach ( range(0, 9) as $authid ) {
					
					$level_input = '<select name="auth'.$authid.'">';
					foreach ( $user_levels as $level_mode ) {
						
						$selected = ( $_POST['auth'.$authid] == $level_mode ) ? ' selected="selected"' : '';
						$level_input .= '<option value="'.$level_mode.'"'.$selected.'>'.$lang['Forums-level'.$level_mode].'</option>';
						
					}
					$level_input .= '</select>';
					$content .= '<tr><td class="fieldtitle">'.$lang['Forums-auth'.$authid].'</td><td>'.$level_input.'</td></tr>';
					
				}
				
			$content .= '<tr><td colspan="2" class="submit"><input type="submit" value="'.$action.'" /> <input type="reset" value="'.$lang['Reset'].'" /></td></tr></table></form>';
			
			$template->set_js_onload("set_focus('name')");
			
		}
		
	}
	
}

$admin_functions->create_body('forums', $content);

?>
