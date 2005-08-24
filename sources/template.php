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
	var $content_type = '';
	var $character_encoding = '';
	var $parse_special_templates_only = false;
	var $loaded_sections = array();
	var $templates = array();
	var $requests = array();
	var $global_vars = array();
	var $raw_contents = array();
	var $body = '';
	
	//
	// Activate gzip compression if needed, BEFORE doing a session_start()
	//
	function template() {
		
		global $functions;
		
		if ( !defined('NO_GZIP') && ( $functions->get_config('output_compression') === 2 || $functions->get_config('output_compression') === 3 ) && !@ini_get('zlib.output_compression') )
			ob_start('ob_gzhandler');
		
	}
	
	//
	// Load a given template section in the template array
	//
	function load_section($section) {
		
		global $functions;
		
		if ( !in_array($section, $this->loaded_sections) ) {
			
			$templates_file = ROOT_PATH.'templates/'.$functions->get_config('template').'/'.$section.'.tpl.php';
			if ( !file_exists($templates_file) || !is_readable($templates_file) )
				trigger_error('Unable to load '.$section.' templates file for set "'.$functions->get_config('template').'"!');
			else
				require($templates_file);
			
			$this->templates[$section] = $templates;
			$this->loaded_sections[] = $section;
			unset($templates);
			
		}
		
	}
	
	//
	// Get the template config
	//
	function get_config($setting) {
		
		global $functions;
		
		$this->load_section('global');
		
		if ( array_key_exists($setting, $this->templates['global']['config']) )
			return $this->templates['global']['config'][$setting];
		elseif ( !$functions->get_config('hide_undefined_template_setting_warnings') )
			trigger_error('The template configuration variable "'.$setting.'" does not exist!');
		else
			return '';
		
	}
	
	//
	// Parse a template
	//
	function parse($name, $section, $variables=array(), $is_special=false) {
		
		global $functions;
		
		if ( $this->parse_special_templates_only && !$is_special )
			return;
		
		//
		// Load the template set
		//
		$this->load_section($section);
		
		if ( !array_key_exists($name, $this->templates[$section]) ) {
			
			if ( !$functions->get_config('hide_undefined_template_warnings') )			
				trigger_error('Unable to load "'.$name.'" template in '.$section.' templates file for set "'.$functions->get_config('template').'"!');
			else
				$this->templates[$section][$name] = '';
			
		}
		
		$this->requests[] = array(
			'section' => $section,
			'template_name' => $name,
			'variables' => ( is_array($variables) && count($variables) ) ? $variables : array()
		);
		
	}
	
	//
	// Add global template variables
	//
	function add_global_vars($variables, $override=false) {
		
		foreach ( $variables as $key => $val ) {
			
			if ( $override || !array_key_exists($key, $this->global_vars) )
				$this->global_vars[$key] = $val;
			
		}
		
	}
	
	//
	// Set the page title
	//
	function set_page_title($page_title) {
		
		global $functions;
		
		$this->add_global_vars(array(
			'page_title' => strip_tags($page_title),
			'location_bar' => '<a href="'.$functions->make_url('index.php').'">'.unhtml($functions->get_config('board_name')).'</a>'.$this->get_config('locationbar_item_delimiter').$page_title
		), true);
		
	}
	
	//
	// Add raw content, used for the ACP
	//
	function add_raw_content($content) {
		
		$this->requests[] = array(
			'raw' => true,
			'num' => count($this->raw_contents)
		);
		
		$this->raw_contents[] = $content;
		
	}
	
	//
	// Set the Javascript onload statement
	//
	function set_js_onload($statement) {
		
		$this->add_global_vars(array(
			'js_onload' => ' onload="javascript:'.$statement.'"'
		), true);
		
	}
	
	//
	// Output the page body
	//
	function body() {
		
		global $db, $functions, $timer, $lang, $session;
		
		//
		// Eventually set the content type and charset
		//
		$content_type = $this->get_config('content_type');
		if ( empty($this->content_type) )
			$this->content_type = ( !empty($content_type) ) ? $content_type : 'text/html';
		if ( empty($this->character_encoding) )
			$this->character_encoding = $lang['character_encoding'];
		
		//
		// Set content type and charset
		//
		header('Content-Type: '.$this->content_type.'; charset='.$this->character_encoding);
		
		//
		// Debug features
		//
		if ( $functions->get_config('debug') ) {
			
			//
			// Timer for checking parsetime
			//
			$timer['end'] = explode(' ', microtime());
			$timer['end'] = (float)$timer['end'][1] + (float)$timer['end'][0];
			$parsetime = round($timer['end'] - $timer['begin'], 4);
			
			$debug_info = array();
			$debug_info[] = $lang['ParseTime'].': '.$parsetime.' s';
			if ( $serverload = $functions->get_server_load() )
				$debug_info[] = $lang['ServerLoad'].': '.$serverload;
			$debug_info[] = $lang['TemplateSections'].': '.count($this->loaded_sections);
			$debug_info[] = $lang['SQLQueries'].': '.count($db->get_used_queries());
			
			if ( $functions->get_config('debug') === 1 ) {
				
				//
				// List parsetime and queries in short
				//
				$debug_info_small = sprintf($this->get_config('debug_info_small'), join($this->get_config('item_delimiter'), $debug_info));
				$debug_info_large = '';
				
			} elseif ( $functions->get_config('debug') === 2 ) {
				
				//
				// Lists parsetime and queries fully
				//
				$debug_info_small = '';
				$debug_info_large = sprintf($this->get_config('debug_info_large'), '<div><strong>'.$lang['DebugMode'].'</strong>'.$this->get_config('item_delimiter').join($this->get_config('item_delimiter'), $debug_info).':</div><textarea rows="10" cols="50" readonly="readonly">'.unhtml(join("\n\n", $db->get_used_queries())).'</textarea>');
				
			}
			
		} else {
			
			$debug_info_small = '';
			$debug_info_large = '';
			
		}
		$this->add_global_vars(array(
			'debug_info_small' => $debug_info_small,
			'debug_info_large' => $debug_info_large
		));
		
		//
		// Add some global template variables such as content type and charset
		//
		$this->add_global_vars(array(
			'content_type' => $this->content_type,
			'character_encoding' => $this->character_encoding,
			'language_code' => ( !empty($lang['language_code']) ) ? $lang['language_code'] : 'en',
			'text_direction' => ( !empty($lang['text_direction']) ) ? $lang['text_direction'] : 'ltr',
			'img_dir' => ROOT_PATH.'templates/'.$functions->get_config('template').'/gfx/',
			'css_url' => ROOT_PATH.'templates/'.$functions->get_config('template').'/styles.css',
			'acp_css_head_link' => ( $session->sess_info['location'] == 'admin' ) ? '<link rel="stylesheet" type="text/css" href="'.ROOT_PATH.'templates/'.$functions->get_config('template').'/admin.css" />' : '',
			'js_onload' => ''
		));
		
		//
		// Parse all templates
		//
		foreach ( $this->requests as $request ) {
			
			if ( isset($request['raw']) ) {
				
				$this->body .= "\n".$this->raw_contents[$request['num']]."\n";
				continue;
				
			}
			
			$current_template = $this->templates[$request['section']][$request['template_name']];
			$finds = $replaces = array();
			
			if ( preg_match('#\{l_[a-zA-Z]+\}#', $current_template) ) {
				
				foreach ( $lang as $key => $val ) {
					
					if ( !is_array($val) ) {
						
						$finds[] = '#\{l_'.preg_quote($key, '#').'\}#';
						$replaces[] = $val;
						
					}
					
				}
				
			}
			
			$request['variables'] = array_merge($this->global_vars, $request['variables']);
			
			foreach ( $request['variables'] as $key => $val ) {
				
				$finds[] = '#\{'.preg_quote($key, '#').'\}#';
				$replaces[] = $val;
				
			}
			
			#foreach ( $replaces as $key => $val )
			#	$replaces[$key] = preg_replace(array('#\{#', '#\}#'), array('&#123;', '&#125;'), $val);
			
			$current_template = preg_replace($finds, $replaces, $current_template);
			$this->body .= $current_template;
			
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
