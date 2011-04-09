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
 * ACP functions
 *
 * Contains the admin_functions class with functions for the ACP. This file can only be included when being admin.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2010 UseBB Team
 * @package	UseBB
 * @subpackage Core
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

//
// Security check
//
if ( !defined('IS_INSTALLER') && $functions->get_user_level() < LEVEL_ADMIN )
	trigger_error('You can not load the admin functions class while the user is not an admin!', E_USER_ERROR);

/**
 * ACP functions
 *
 * Functions only usable in the ACP.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2010 UseBB Team
 * @package	UseBB
 * @subpackage Core
 */
class admin_functions {
	
	/**
	 * @var array Contains the ACP menu structure
	 */
	var $acp = array(
		'main' => array(
			'index',
			'version',
			'config',
			'logout'
		),
		'forums' => array(
			'categories',
			'forums',
		),
		'members' => array(
			'members',
			'activate_members',
			'register_members',
			'delete_members',
		),
		'pruning' => array(
			'prune_forums',
			'prune_members',
		),
		'security' => array(
			'bans',
			'dnsbl',
			'badwords',
		),
		'various' => array(
			'mass_email',
			'iplookup',
			'sqltoolbox',
			'modules',
		),
	);
	/**
	 * @var array Contains information about available ACP modules
	 */
	var $acp_modules = array();
	/**
	 * @var array Contains filenames of available ACP modules.
	 */
	var $acp_modules_files = array();
	/**
	 * @access private
	 */
	var $all_forums;
	
	/**
	 * Loads available ACP modules and fills $acp_modules(_files)
	 */
	function admin_functions() {
		
		global $functions;
		global $lang;
		
		//
		// Load ACP modules
		//
		$modules_dir = ROOT_PATH.'sources/modules/';
		if ( $functions->get_config('enable_acp_modules') && file_exists($modules_dir) && is_dir($modules_dir) ) {
			
			$handle = opendir($modules_dir);
			
			while ( false !== ( $module_name = readdir($handle) ) ) {
				
				$usebb_module_info = $this->check_module($module_name);
				
				//
				// If valid module and not exist yet
				//
				if ( is_array($usebb_module_info) && !array_key_exists($usebb_module_info['short_name'], $this->acp_modules) ) {
					
					//
					// Eventually create a new category
					//
					if ( !array_key_exists($usebb_module_info['acp_category'], $this->acp) ) {
						
						$this->acp[$usebb_module_info['acp_category']] = array();
						$lang['Category-'.$usebb_module_info['acp_category']] = $usebb_module_info['new_acp_category_long_name'];
						
					}
					
					//
					// Add the filename and save to module list and menu
					//
					$usebb_module_info['filename'] = $module_name;
					$this->acp_modules_files[] = $module_name;
					$this->acp_modules[$usebb_module_info['short_name']] = $usebb_module_info;
					$this->acp[$usebb_module_info['acp_category']][] = 'mod_'.$usebb_module_info['short_name'];
					
				}
				
			}
			
			closedir($handle);
			
		}
		
	}
	
	/**
	 * Read a remote URL into string
	 *
	 * @param string $url URL
	 * @returns string Contents
	 */
	function read_remote_file($url) {
		
		if ( function_exists('curl_init') && function_exists('curl_exec') ) {
			
			//
			// cURL
			//
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$contents = trim(curl_exec($curl));
			curl_close($curl);

			return $contents;
			
		}

		//
		// URL fopen()
		//
		if ( !ini_get('allow_url_fopen') )
			return false;
		
		$fp = fopen($url, 'r');

		if ( !$fp )
			return false;

		$contents = '';

		if ( function_exists('stream_get_contents') ) {
			
			//
			// PHP 5 stream
			//
			$contents = trim(stream_get_contents($fp));
			
		} else {
			
			//
			// fread() packet reading
			//
			while ( !feof($fp) )
				$contents .= fread($fp, 8192);
			$contents = trim($contents);
			
		}

		fclose($fp);

		return $contents;
		
	}
	
	/**
	 * Check if a .php file is a valid UseBB ACP module
	 *
	 * @param string $module_name Module filename
	 * @param string $actual_module_name Actual module filename (not checked on extension)
	 * @returns mixed Array with module info or false when module is invalid
	 */
	function check_module($module_name, $actual_module_name='') {
		
		if ( substr($module_name, -4) == '.php' ) {
			
			$module_name = ( !empty($actual_module_name) ) ? $actual_module_name : ROOT_PATH.'sources/modules/'.$module_name;
			
			ob_start();
			require($module_name);
			ob_end_clean();
			
			//
			// Valid information returned?
			//
			if ( isset($usebb_module_info) && is_array($usebb_module_info) && !empty($usebb_module_info['acp_category']) && ( array_key_exists($usebb_module_info['acp_category'], $this->acp) || !empty($usebb_module_info['new_acp_category_long_name']) ) && !empty($usebb_module_info['short_name']) && !empty($usebb_module_info['long_name']) ) {
				
				//
				// This is a valid module, return module info
				//
				return $usebb_module_info;
				
			} else {
				
				//
				// No UseBB module info found
				//
				return false;
				
			}
			
		} else {
			
			//
			// Not a .php file
			//
			return false;
			
		}
		
	}
	
	/**
	 * Run a UseBB module
	 *
	 * @param string $module_name Module short name
	 */
	function run_module($module_name) {
		
		global $lang;
		
		//
		// Load the module
		//
		define('RUN_MODULE', true);
		ob_start();
		require(ROOT_PATH.'sources/modules/'.$this->acp_modules[$module_name]['filename']);
		ob_end_clean();
		
		//
		// Able to run?
		//
		$content = ( isset($usebb_module) && is_object($usebb_module) && method_exists($usebb_module, 'run_module') ) ? $usebb_module->run_module() : '<p><strong>'.$lang['RunningBadACPModule'].'</strong></p>';
		
		$this->create_body('mod_'.$module_name, $content);
		
	}
	
	/**
	 * Create the ACP menu
	 *
	 * @param string $location Current ACP location
	 * @returns string HTML ACP menu
	 */
	function create_acp_menu($location) {
		
		global $functions, $lang;
		
		$out = '<ul>';
		foreach ( $this->acp as $category_name => $category ) {
			
			$out .= '<li>'.$lang['Category-'.$category_name].'<ul>';
			foreach ( $this->acp[$category_name] as $item ) {
				
				$selected = ( $location == $item ) ? ' class="selected"' : '';
				$use_token = ( $item == 'logout' );
				$name = ( preg_match('#^mod_([A-Za-z0-9\-_\.]+)$#', $item, $module_name) ) ? $this->acp_modules[$module_name[1]]['long_name'] : $lang['Item-'.$item];
				$out .= '<li'.$selected.'><a href="'.$functions->make_url('admin.php', array('act' => $item), true, true, false, $use_token).'">'.$name.'</a></li>';
				
			}
			$out .= '</ul></li>';
			
		}
		$out .= '</ul>';
		
		return $out;
		
	}
	
	/**
	 * Create the admin body
	 *
	 * @param string $location Current ACP location
	 * @param string $content ACP HTML content
	 */
	function create_body($location, $content) {
		
		global $functions, $template, $lang;
		
		if ( empty($content) )
			$functions->redirect('admin.php');
		
		$name = ( preg_match('#^mod_([A-Za-z0-9\-_\.]+)$#', $location, $module_name) ) ? $this->acp_modules[$module_name[1]]['long_name'] : $lang['Item-'.$location];
		
		if ( $location == 'index' )
			$template->set_page_title($lang['ACP']);
		else
			$template->set_page_title('<a href="'.$functions->make_url('admin.php').'">'.$lang['ACP'].'</a>'.$template->get_config('locationbar_item_delimiter').$name);
		
		$template->parse('main', 'admin', array(
			'admin_menu' => $this->create_acp_menu($location),
			'admin_title' => $name,
			'admin_content' => $content
		));
		
		if ( strpos($content, '<form') !== false ) {
			
			//
			// Count input fields, disable logout for large forms
			//
			$count = substr_count($content, '<input') + substr_count($content, '<select') + substr_count($content, '<textarea');
			if ( $count >= 7 )
				$this->disable_logout();

		}
		
	}
	
	/**
	 * Transform a variable into legal PHP code using var_export().
	 *
	 * @param mixed $variable Variable to transform to PHP
	 * @returns string PHP code
	 */
	function make_php_string($variable) {
		
		return str_replace("\n", '', var_export($variable, true));
			
	}
	
	/**
	 * Set forum configuration
	 *
	 * @param array $settings Array containing forum settings to change
	 */
	function set_config($settings) {
		
		global $functions;
		
		if ( !is_array($settings) || !count($settings) )
			return;
		
		$config_file = ROOT_PATH.'config.php';
		
		//
		// Get the contents of config.php
		//
		$fp = fopen($config_file, 'r');
		$config_content = fread($fp, filesize($config_file));
		fclose($fp);
		
		//
		// Adjust values
		//
		foreach ( $settings as $key => $val ) {
			
			$variable = ( in_array($key, array('type', 'server', 'username', 'passwd', 'dbname', 'prefix')) ) ? 'dbs' : 'conf';
			
			if ( $variable == 'dbs' || in_array($key, $functions->board_config_defined) )
				$config_content = preg_replace('#(\s)?\$'.$variable."\['".$key."'\] = .+;#", '\\1\$'.$variable."['".$key."'] = ".$this->make_php_string($val).';', $config_content);
			else
				$config_content = preg_replace('#(\s*?)?\?>#', "\n\$".$variable."['".$key."'] = ".$this->make_php_string($val).";\\1?>", $config_content);
			
		}
		
		if ( !is_writable(ROOT_PATH.'config.php') ) {
			
			//
			// Make config.php downloadable
			//
			ob_end_clean(); // get rid of gzip output buffers
			header('Content-Type: application/x-httpd-php');
			header('Content-disposition: attachment; filename="config.php"');
			die($config_content);
			
		} else {
			
			//
			// Write the new contents
			//
			$fp = fopen($config_file, 'w');
			fwrite($fp, $config_content);
			fclose($fp);
			
		}
		
	}
	
	/**
	 * Make a forum select box
	 *
	 * @param string $input_name HTML <select /> name attribute
	 * @param bool $multiple Allow multiple selections
	 * @param array $filter_ids Array containing forum ID's to exclude
	 * @param string $add HTML to add as last elements in <select />
	 * @returns string HTML <select />
	 */
	function forum_select_box($input_name, $multiple=true, $filter_ids=array(), $add='') {
		
		global $db;
		
		if ( !is_array($this->all_forums) ) {
			
			$result = $db->query("SELECT c.id AS cat_id, c.name AS cat_name, f.id, f.name FROM ".TABLE_PREFIX."cats c, ".TABLE_PREFIX."forums f WHERE c.id = f.cat_id ORDER BY c.sort_id ASC, c.name ASC, f.sort_id ASC, f.name ASC");
			$this->all_forums = array();
			while ( $forumdata = $db->fetch_result($result) )
				$this->all_forums[] = $forumdata;
			
		}
		
		$forums_input = '';
		$seen_cats = array();
		$items = 0;
		
		if ( !empty($_POST[$input_name]) ) {
			
			$_POST[$input_name] = ( $multiple ) ? $_POST[$input_name] : array($_POST[$input_name]);
			
		} else {
			
			$_POST[$input_name] = array();
			
		}
		
		$_POST[$input_name] = ( isset($_POST[$input_name]) && is_array($_POST[$input_name]) ) ? $_POST[$input_name] : array();
		foreach ( $this->all_forums as $forumdata ) {
			
			if ( is_array($filter_ids) && in_array($forumdata['id'], $filter_ids) )
				continue;
			
			if ( !in_array($forumdata['cat_id'], $seen_cats) ) {
				
				$forums_input .= ( !count($seen_cats) ) ? '' : '</optgroup>';
				$forums_input .= '<optgroup label="'.unhtml(stripslashes($forumdata['cat_name'])).'">';
				$seen_cats[] = $forumdata['cat_id'];
				$items++;
				
			}
			
			$selected = ( in_array($forumdata['id'], $_POST[$input_name]) ) ? ' selected="selected"' : '';
			$forums_input .= '<option value="'.$forumdata['id'].'"'.$selected.'>'.unhtml(stripslashes($forumdata['name'])).'</option>';
			$items++;
			
		}
		$forums_input .= '</optgroup>'.$add;

		if ( $multiple )
			return '<select name="'.$input_name.'[]" size="'.$items.'" multiple="multiple">'.$forums_input.'</select>';
		else
			return '<select name="'.$input_name.'">'.$forums_input.'</select>';
		
	}
	
	/**
	 * Cleanly delete forums
	 *
	 * @param string $condition SQL condition to match forums
	 * @param bool $change_stats Change the global statistics
	 */
	function delete_forums($condition, $change_stats=true) {
		
		global $functions, $db;
		
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
			if ( $change_stats ) {
				
				$functions->set_stats('topics', - $topics, true);
				$functions->set_stats('posts', - $posts, true);
				
			}
			
			//
			// Reload moderator perms
			//
			$this->reload_moderator_perms();
			
		}
		
	}
	
	/**
	 * Reload moderator permissions
	 *
	 * - Deletes moderator entries for unexisting forums
	 * - Set user level for moderators to 2 when set to 1
	 * - Set user level for non-moderators to 1 when set to 2
	 */
	function reload_moderator_perms() {
		
		global $db;
		
		//
		// Get an array of existing forums
		//
		$existing_forums = $this->get_forums_array();
		$existing_forums = array_keys($existing_forums);
		
		if ( count($existing_forums) ) {
			
			//
			// Delete moderator entries from unexisting forums
			//
			$db->query("DELETE FROM ".TABLE_PREFIX."moderators WHERE forum_id NOT IN(".join(', ', $existing_forums).")");
			
			//
			// Get all moderator user ID's
			//
			$result = $db->query("SELECT DISTINCT(user_id) as user_id FROM ".TABLE_PREFIX."moderators");
			$all_moderators = array();
			while ( $modsdata = $db->fetch_result($result) )
				$all_moderators[] = $modsdata['user_id'];
			
			//
			// Set user levels right
			//
			if ( count($all_moderators) ) {
				
				$db->query("UPDATE ".TABLE_PREFIX."members SET level = 2 WHERE level = 1 AND id IN(".join(', ', $all_moderators).")");
				$db->query("UPDATE ".TABLE_PREFIX."members SET level = 1 WHERE level = 2 AND id NOT IN(".join(', ', $all_moderators).")");
				
			} else {
				
				$db->query("UPDATE ".TABLE_PREFIX."members SET level = 1 WHERE level = 2");
				
			}
			
		} else {
			
			//
			// Remove all moderator permissions
			//
			$db->query("DELETE FROM ".TABLE_PREFIX."moderators");
			$db->query("UPDATE ".TABLE_PREFIX."members SET level = 1 WHERE level = 2");
			
		}
		
	}
	
	/**
	 * Get categories
	 *
	 * @returns array Array containing category information
	 */
	function get_cats_array() {
		
		global $db;
		
		$result = $db->query("SELECT * FROM ".TABLE_PREFIX."cats ORDER BY sort_id ASC, name");
		$cats = array();
		while ( $catinfo = $db->fetch_result($result) )
			$cats[$catinfo['id']] = $catinfo;
		
		return $cats;
		
	}
	
	/**
	 * Get forums
	 *
	 * @returns array Array containing forum information
	 */
	function get_forums_array() {
		
		global $db;
		
		$result = $db->query("SELECT f.*, c.id as cat_id, c.name as cat_name FROM ".TABLE_PREFIX."forums f, ".TABLE_PREFIX."cats c WHERE c.id = f.cat_id ORDER BY c.sort_id ASC, c.name ASC, f.sort_id ASC, f.name ASC");
		$forums = array();
		while ( $foruminfo = $db->fetch_result($result) )
			$forums[$foruminfo['id']] = $foruminfo;
		
		return $forums;
		
	}

	/**
	 * Disable automatic logout
	 */
	function disable_logout() {
		
		//
		// This variable is used in admin.php
		//
		$_SESSION['admin_disable_logout'] = true;

	}

	/**
	 * Form token input field
	 *
	 * @returns string Input field
	 */
	function form_token() {

		global $functions;
		
		$token = $functions->generate_token();
		
		return '<input type="hidden" name="_form_token_" value="'.$token.'" />';

	}

	/**
	 * Set ACP info message
	 *
	 * @param string $msg Message
	 */
	function set_acp_msg($msg) {

		$_SESSION['acp_msg'] = $msg;

	}

	/**
	 * Show ACP info message
	 *
	 * @returns string Message
	 */
	function show_acp_msg() {

		if ( empty($_SESSION['acp_msg']) )
			return '';

		$result = '<p>'.$_SESSION['acp_msg'].'</p>';
		unset($_SESSION['acp_msg']);

		return $result;

	}
	
}

?>
