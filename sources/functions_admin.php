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
		
		if ( is_int($variable) || is_bool($variable) || is_float($variable) ) {
		
			$variable = $variable;
			
		} elseif ( is_string($variable) ) {
			
			$variable = "'".$variable."'";
			
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
		
		if ( !is_writable($config_file) )
			trigger_error('config.php is not writable! Please chmod the file so that the webserver can write it.');
		
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
			$config_content = preg_replace('#\$'.$variable."\['".$key."'\] = .+;#", '\$'.$variable."['".$key."'] = ".$this->make_php_string($val).';', $config_content);
			
		}
		
		//
		// Write the new contents
		//
		$fp = fopen($config_file, 'w');
		fwrite($fp, addslashes($config_content));
		fclose($fp);
		
	}
	
}

?>
