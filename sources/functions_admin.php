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
// Security check
//
if ( $functions->get_user_level() < LEVEL_ADMIN )
	trigger_error('You can not load the admin functions class while the user is not an admin!');

class admin_functions {
	
	var $all_forums;
	
	//
	// Create the ACP menu
	//
	function create_acp_menu($location) {
		
		global $functions, $lang;
		
		$categories = array(
			'main',
			'forums',
			'various',
		);
		$items = array(
			'main' => array(
				'index',
				'version',
				'config',
			),
			'forums' => array(
				'categories',
				'forums',
			),
			'various' => array(
				'iplookup',
				'sqltoolbox',
			),
		);
		
		$out = '<ul>';
		foreach ( $categories as $category ) {
			
			$out .= '<li>'.$lang['Category-'.$category].'<ul>';
			foreach ( $items[$category] as $item ) {
				
				$selected = ( $location == $item ) ? ' class="selected"' : '';
				
				$out .= '<li'.$selected.'><a href="'.$functions->make_url('admin.php', array('act' => $item)).'">'.$lang['Item-'.$item].'</a></li>';
				
			}
			$out .= '</ul></li>';
			
		}
		$out .= '</ul>';
		
		return $out;
		
	}
	
	//
	// Create the admin body
	//
	function create_body($location, $content) {
		
		global $functions, $template, $lang;
		
		if ( $location == 'index' )
			$template->set_page_title($lang['ACP']);
		else
			$template->set_page_title('<a href="'.$functions->make_url('admin.php').'">'.$lang['ACP'].'</a>'.$template->get_config('locationbar_item_delimiter').$lang['Item-'.$location]);
		
		$template->parse('main', 'admin', array(
			'admin_menu' => $this->create_acp_menu($location),
			'admin_title' => $lang['Item-'.$location],
			'admin_content' => $content
		));
		
	}
	
	//
	// Transform a variable into a PHP string
	//
	function make_php_string($variable) {
		
		if ( function_exists('var_export') )
			return str_replace("\n", '', var_export($variable, true));
		
		if ( is_int($variable) || is_bool($variable) || is_float($variable) ) {
		
			$variable = $variable;
			
		} elseif ( is_string($variable) ) {
			
			$variable = "'".str_replace("'", "\'", $variable)."'";
			
		} elseif ( is_array($variable) || is_object($variable) ) {
			
			if ( count($variable) ) {
				
				$new_variable = array();
				
				foreach ( $variable as $variable2 )
					$new_variable[] = $this->make_php_string($variable2);
				
				$variable = 'array('.join(', ', $new_variable).')';
				
			} else {
				
				$variable = 'array()';
				
			}
			
		}
		
		return $variable;
		
	}
	
	//
	// Set forum configuration
	//
	function set_config($settings) {
		
		if ( !is_array($settings) || !count($settings) )
			return;
		
		$config_file = ROOT_PATH.'config.php';
		
		//
		// Get the contents of config.php
		//
		$fp = fopen($config_file, 'r');
		$config_content = stripslashes(fread($fp, filesize($config_file)));
		fclose($fp);
		
		//
		// Adjust values
		//
		foreach ( $settings as $key => $val ) {
			
			$variable = ( in_array($key, array('type', 'server', 'username', 'passwd', 'dbname', 'prefix')) ) ? 'dbs' : 'conf';
			
			if ( preg_match('#\s\$'.$variable."\['".$key."'\] = .+;#", $config_content) )
				$config_content = preg_replace('#(\s)\$'.$variable."\['".$key."'\] = .+;#", '\\1\$'.$variable."['".$key."'] = ".$this->make_php_string($val).';', $config_content);
			else
				$config_content = preg_replace('#(\s*?)\?>#', "\n\$".$variable."['".$key."'] = ".$this->make_php_string($val).";\\1?>", $config_content);
			
		}
		
		//
		// Write the new contents
		//
		$fp = fopen($config_file, 'w');
		fwrite($fp, addslashes($config_content));
		fclose($fp);
		
	}
	
	//
	// Forum select
	//
	function forum_select_box($input_name) {
		
		global $db;
		
		if ( !is_array($this->all_forums) ) {
			
			$result = $db->query("SELECT c.id AS cat_id, c.name AS cat_name, f.id, f.name FROM ".TABLE_PREFIX."cats c, ".TABLE_PREFIX."forums f WHERE c.id = f.cat_id ORDER BY c.sort_id ASC, c.name ASC, f.sort_id ASC, f.name ASC");
			$this->all_forums = array();
			while ( $forumdata = $db->fetch_result($result) )
				$this->all_forums[] = $forumdata;
			
		}
		
		$forums_input = '<select name="'.$input_name.'[]" size="5" multiple="multiple">';
		$seen_cats = array();
		$_POST[$input_name] = ( is_array($_POST[$input_name]) ) ? $_POST[$input_name] : array();
		foreach ( $this->all_forums as $forumdata ) {
			
			if ( !in_array($forumdata['cat_id'], $seen_cats) ) {
				
				$forums_input .= ( !count($seen_cats) ) ? '' : '</optgroup>';
				$forums_input .= '<optgroup label="'.unhtml(stripslashes($forumdata['cat_name'])).'">';
				$seen_cats[] = $forumdata['cat_id'];
				
			}
			
			$selected = ( in_array($forumdata['id'], $_POST[$input_name]) ) ? ' selected="selected"' : '';
			$forums_input .= '<option value="'.$forumdata['id'].'"'.$selected.'>'.unhtml(stripslashes($forumdata['name'])).'</option>';
			
		}
		$forums_input .= '</optgroup></select>';
		return $forums_input;
		
	}
	
	//
	// Delete forums
	//
	function delete_forums($condition) {
		
		global $db;
		
		//
		// Get the forum ID's and counts
		//
		$result = $db->query("SELECT id, topics, posts FROM ".TABLE_PREFIX."forums WHERE ".$condition);
		$forum_ids = array();
		$topics = $posts = 0;
		while ( $forumdata = $db->fetch_result($result) ) {
			
			$forum_ids[] = $forumdata['id'];
			$topics = $topics + $forumdata['topics'];
			$posts = $posts + $forumdata['posts'];
			
		}
		
		if ( count($forum_ids) ) {
			
			//
			// Delete the forums
			//
			$db->query("DELETE FROM ".TABLE_PREFIX."forums WHERE id IN(".join(', ', $forum_ids).")");
			
			//
			// Delete the posts and topic subscriptions
			//
			$result = $db->query("SELECT id FROM ".TABLE_PREFIX."topics WHERE forum_id IN(".join(', ', $forum_ids).")");
			while ( $topicdata = $db->fetch_result($result) ) {
				
				$db->query("DELETE FROM ".TABLE_PREFIX."posts WHERE topic_id = ".$topicdata['id']);
				$db->query("DELETE FROM ".TABLE_PREFIX."subscriptions WHERE topic_id = ".$topicdata['id']);
				
			}
			
			//
			// Delete the topics
			//
			$db->query("DELETE FROM ".TABLE_PREFIX."topics WHERE forum_id IN(".join(', ', $forum_ids).")");
			
			//
			// Update the stats
			//
			$result = $db->query("UPDATE ".TABLE_PREFIX."stats SET content = content-".$topics." WHERE name = 'topics'");
			$result = $db->query("UPDATE ".TABLE_PREFIX."stats SET content = content-".$posts." WHERE name = 'posts'");
			
			//
			// Reload moderator perms for the affected forums
			//
			$this->reload_moderator_perms($forum_ids);
			
		}
		
	}
	
	//
	// Reload moderator permissions for certain forums
	//
	function reload_moderator_perms($forum_ids) {
		
		global $db;
		
		//
		// Get an array of forums that do exist
		//
		$result = $db->query("SELECT id FROM ".TABLE_PREFIX."forums WHERE id IN(".join(', ', $forum_ids).")");
		$existing_forums = array();
		while ( $forumdata = $db->fetch_result($result) )
			$existing_forums[] = $forumdata['id'];
		
		//
		// Get an array of forums that were deleted
		//
		$deleted_forums = array_diff($forum_ids, $existing_forums);
		
		//
		// Get a list of all forums given in the parameter, including deleted forums
		// Each user_id is an array key, if it is an empty array(), his moderator
		// status needs to be unset. Only moderators are fetched, no admins.
		//
		$result = $db->query("SELECT m.forum_id, m.user_id FROM ".TABLE_PREFIX."moderators m, ".TABLE_PREFIX."members m2 WHERE m.user_id = m2.id AND m2.level = ".LEVEL_MOD." AND forum_id IN(".join(', ', $forum_ids).")");
		$moderators = array();
		while ( $moderatordata = $db->fetch_result($result) ) {
			
			//
			// New array for the moderator's forums
			//
			if ( !array_key_exists($moderatordata['user_id'], $moderators) )
				$moderators[$moderatordata['user_id']] = array();
			
			//
			// Only add this forum if it was not deleted
			//
			if ( !count($deleted_forums) || !in_array($moderatordata['forum_id'], $deleted_forums) )
				$moderators[$moderatordata['user_id']][] = $moderatordata['forum_id'];
			
		}
		
		//
		// Unset moderator statuses for moderators which had their forums deleted
		//
		if ( count($deleted_forums) ) {
			
			$unset_moderators = array();
			foreach ( $moderators as $user_id => $forums ) {
				
				if ( !count($forums) ) {
					
					$unset_moderators[] = $user_id;
					unset($moderators[$user_id]); // so we have a clean array later on
					
				}
				
			}
			$db->query("UPDATE ".TABLE_PREFIX."members SET level = 1 WHERE id IN(".join(', ', $unset_moderators).")");
			$db->query("DELETE FROM ".TABLE_PREFIX."moderators WHERE forum_id IN(".join(', ', $deleted_forums).")");
			
		}
		
		//
		// Give users moderator status when they have been added as a moderator
		//
		if ( count($moderators) )
			$db->query("UPDATE ".TABLE_PREFIX."members SET level = 2 WHERE level = 1 AND id IN(".join(', ', array_keys($moderators)).")");
		
	}
	
}

?>
