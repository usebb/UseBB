<?php

/*
	Copyright (C) 2003-2004 UseBB Team
	http://usebb.sourceforge.net
	
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
	var $config;
	var $needed;
	var $requests;
	var $templates;
	
	//
	// Constructor, initializes arrays used in this class
	//
	function template() {
		
		$this->needed = array();
		$this->requests = array();
		$this->templates = array();
		
	}
	
	//
	// Get configuration variables
	//
	function get_config($setting) {
		
		global $db, $functions;
		
		if ( !isset($this->config) ) {
			
			$this->config = array();
			
			if ( !($result = $db->query("SELECT name, content FROM ".TABLE_PREFIX."templates_config WHERE template = '".$functions->get_config('template')."'")) )
				$this->usebb_die('SQL', 'Unable to get template configuration!', __FILE__, __LINE__);
			while ( $out = $db->fetch_result($result) )
				$this->config[$out['name']] = stripslashes($out['content']);
			
		}
		
		if ( isset($this->config[$setting]) )
			return $this->config[$setting];
		else
			$functions->usebb_die('General', 'The template configuration variable "'.$setting.'" does not exist!', __FILE__, __LINE__);
		
	}
	
	//
	// Add a template request and variables to the $requests var
	//
	function parse($name, $vars=array()) {
		
		global $functions;
		
		if ( !in_array($name, $this->needed) )
			$this->needed[] = $name;
		
		$vars = ( !is_array($vars) ) ? array() : $vars;
		
		$vars['img_dir'] = 'gfx/'.$functions->get_config('template').'/';
		$vars['lang'] = $functions->get_config('language');
		
		$this->requests[] = array(
			'name' => $name,
			'vars' => $vars
		);
		
	}
	
	function set_page_title($title) {
		
		foreach ( $this->requests as $key => $val ) {
			
			if ( is_array($this->requests[$key]['vars']) )
				$this->requests[$key]['vars']['page_title'] = $title;
			
		}
		
	}
	
	//
	// Build and echo the page body
	//
	function body() {
		
		global $functions, $db, $timer;
		
		//
		// Get all the templates we need
		//
		foreach ( $this->needed as $val )
			$query_where_part[] = "'".$val."'";
		$query_where_part = '( '.join(', ', $query_where_part).' )';
		if ( !($result = $db->query("SELECT name, content FROM ".TABLE_PREFIX."templates WHERE template = '".$functions->get_config('template')."' AND name IN ".$query_where_part)) )
			$functions->usebb_die('SQL', 'Unable to get contents of template "'.$functions->get_config('template').'"!', __FILE__, __LINE__);
		while ( $templates = $db->fetch_result($result) )
			$this->templates[$templates['name']] = stripslashes($templates['content']);
		
		$body = '';
		
		//
		// Build each template
		//
		foreach ( $this->requests as $request ) {
			
			//
			// When variables has been passed
			//
			if ( is_array($request['vars']) && count($request['vars']) > 0 ) {
				
				//
				// Parse the variables and add it to the body
				//				
				if ( !isset($this->templates[$request['name']]) )
					$functions->usebb_die('Template', 'Missing template "'.$request['name'].'"!', __FILE__, __LINE__);
				$current_template = $this->templates[$request['name']];
				foreach ( $request['vars'] as $key => $val )
					$current_template = str_replace('{'.$key.'}', $val, $current_template);
				$body .= $current_template."\n";
				
			} else {
				
				//
				// Just add the template to the body
				//
				$body .= $this->templates[$request['name']];
				
			}
			
		}
		
		//
		// Debug features
		//
		if ( $functions->get_config('debug') ) {
			
			//
			// Timer for checking parsetime
			//
			$timer['end'] = explode(' ', microtime());
			$timer['end'] = (float)$timer['end'][1] + (float)$timer['end'][0];
			$parsetime = round($timer['end'] - $timer['begin'], 5).'s';
			
			//
			// Lists parsetime and queries
			//
			$body = str_replace('</body>', '<div><b>Debug mode</b><br />Parse time: '.$parsetime.'<br />Used templates ('.count($this->needed).'): '.join(', ', $this->needed).'<br />Used queries ('.count($db->queries).'):<br /><textarea rows="10" cols="50" readonly="readonly">'.htmlentities(join("\n\n", $db->queries)).'</textarea></div></body>', $body);
			
		}
		
		//
		// Output compression
		//
		if ( $functions->get_config('output_compression') )
			$body = preg_replace("/>\s+</", '><', $body);
		
		echo $body;
		
	}
	
}

?>