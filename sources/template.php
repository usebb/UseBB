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
// Create the template handlers
//
class template {
	
	//
	// Variables
	//
	var $loaded_sections;
	var $templates;
	var $body;
	
	//
	// Define arrays
	//
	function template() {
		
		$this->loaded_sections = array();
		$this->templates = array();
		$this->body = '';
		
	}
	
	//
	// Load a given template section in the template array
	//
	function load_section($section) {
		
		global $functions;
		
		if ( !in_array($section, $this->loaded_sections) ) {
			
			$templates_file = ROOT_PATH.'templates/'.$functions->get_config('template').'/'.$section.'.tpl.php';
			if ( !file_exists($templates_file) || !is_readable($templates_file) )
				$functions->usebb_die('Template', 'Unable to load '.$section.' templates file for set "'.$functions->get_config('template').'"!', __FILE__, __LINE__);
			else
				require($templates_file);
			
			$this->templates = array_merge($this->templates, $templates);
			$this->loaded_sections[] = $section;
			unset($templates);
			
		}
		
	}
	
	//
	// Get the template config
	//
	function get_config($setting) {
		
		global $functions;
		
		//
		// Load the template set
		//
		$this->load_section('global');
		
		if ( isset($this->templates['config'][$setting]) )
			return $this->templates['config'][$setting];
		else
			$functions->usebb_die('Template', 'The template configuration variable "'.$setting.'" does not exist!', __FILE__, __LINE__);
		
	}
	
	//
	// Parse a template
	//
	function parse($name='', $section, $vars=array()) {
		
		global $functions;
		
		//
		// Load the template set
		//
		$this->load_section($section);
		
		$vars = ( is_array($vars) && count($vars) ) ? $vars : array();
		$vars['img_dir'] = ROOT_PATH.'templates/'.$functions->get_config('template').'/gfx/';
		
		if ( !isset($this->templates[$name]) )
			$functions->usebb_die('Template', 'Unable to load "'.$name.'" template in '.$section.' templates file for set "'.$functions->get_config('template').'"!', __FILE__, __LINE__);
		
		$current_template = $this->templates[$name];
		foreach ( $vars as $key => $val ) {
			
			if ( is_array($val) )
				$functions->usebb_die('Template', 'Unable to translate "{'.$key.'}" to an array!', __FILE__, __LINE__);
			$current_template = str_replace('{'.$key.'}', $val, $current_template);
			
		}
		$this->body .= $current_template;
		
	}
	
	//
	// Set the page title
	//
	function set_page_title($title) {
		
		$this->body = str_replace('{page_title}', $title, $this->body);
		
	}
	
	//
	// Output the page body
	//
	function body($enable_debugmessages=true) {
		
		global $db, $functions, $timer;
		
		//
		// Debug features
		//
		if ( $functions->get_config('debug') && $enable_debugmessages ) {
			
			//
			// Timer for checking parsetime
			//
			$timer['end'] = explode(' ', microtime());
			$timer['end'] = (float)$timer['end'][1] + (float)$timer['end'][0];
			$parsetime = round($timer['end'] - $timer['begin'], 2);
			
			//
			// Get the server load, use '?' when lookup fails
			//
			if ( !($serverload = $functions->get_server_load()) )
				$serverload = '?';
			
			if ( $functions->get_config('debug') === 1 ) {
				
				//
				// List parsetime and queries in short
				//
				$debug_output = '<div style="text-align:center"><small>PT: '.$parsetime.' - SL: '.$serverload.' - TPLS: '.count($this->loaded_sections).' - SQL: '.count($db->get_used_queries()).'</small></div>';
				
			} elseif ( $functions->get_config('debug') === 2 ) {
				
				//
				// Lists parsetime and queries fully
				//
				$debug_output = '<div><strong>Debug mode</strong><br />Parse time: '.$parsetime.'<br />Server load: '.$serverload.'<br />Used template sets ('.count($this->loaded_sections).'): <select size="1"><option value="">'.join('</option><option value="">', $this->loaded_sections).'</option></select><br />Used queries ('.count($db->get_used_queries()).'):<br /><textarea rows="10" cols="50" readonly="readonly">'.htmlspecialchars(join("\n\n", $db->get_used_queries())).'</textarea></div>';
				
			}
			
			if ( preg_match('#</body>#i', $this->body) )
				$this->body = preg_replace('#</body>#i', $debug_output.'</body>', $this->body);
			else
				$this->body .= $debug_output;
			
		}
		
		//
		// Compression and output
		//
		if ( $functions->get_config('output_compression') === 1 || $functions->get_config('output_compression') === 3 )
			$this->body = $functions->compress_sourcecode($this->body);
		
		echo $this->body;
		
	}
	
}

?>
