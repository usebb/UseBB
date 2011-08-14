<?php

/*
	Copyright (C) 2003-2011 UseBB Team
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
 * Template parser
 *
 * Contains the template class to do template handling.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2011 UseBB Team
 * @package	UseBB
 * @subpackage	Core
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

/**
 * Template parser
 *
 * Does all the template handling for UseBB.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2011 UseBB Team
 * @package	UseBB
 * @subpackage	Core
 */
class template {
	
	/**
	 * @var string Output content type (used in HTTP headers and templates). This may be set during runtime.
	 */
	var $content_type = '';
	/**
	 * @var string Character encoding (used in HTTP headers and templates). This may be set during runtime.
	 */
	var $character_encoding = '';
	/**
	 * @var bool Parse templates marked as special only
	 */
	var $parse_special_templates_only = false;
	/**#@+
	 * @access private
	 */
	var $loaded_sections = array();
	var $templates = array();
	var $requests = array();
	var $global_vars = array();
	var $raw_contents = array();
	var $js_onload = array();
	/**#@-*/
	
	/**
	 * Constructor for template class
	 *
	 * Activates gzip compression if needed, before doing a session_start(). 
	 * Also activates a second output buffer to trigger unwanted output from mods.
	 */
	function template() {
		
		global $functions;
		
		if ( !defined('NO_GZIP') && (int)$functions->get_config('output_compression') >= 2 && !ini_get('zlib.output_compression') )
			ob_start('ob_gzhandler');
		
		ob_start();
		
	}
	
	/**
	 * Load a given template section
	 *
	 * Called automatically when needed
	 *
	 * @param string $section Template section to load
	 */
	function load_section($section) {
		
		global $functions;
		
		if ( !in_array($section, $this->loaded_sections) ) {
			
			$templates_file = ROOT_PATH.'templates/'.$functions->get_config('template').'/'.$section.'.tpl.php';
			if ( !file_exists($templates_file) || !is_readable($templates_file) )
				trigger_error('Unable to load '.$section.' templates file for set "'.$functions->get_config('template').'"!', E_USER_ERROR);
			else
				require($templates_file);
			
			$this->templates[$section] = $templates;
			$this->loaded_sections[] = $section;
			unset($templates);
			
		}
		
	}
	
	/**
	 * Get template configuration variables
	 *
	 * @param string $setting Configuration variable
	 * @returns mixed Configuration value
	 */
	function get_config($setting) {
		
		global $functions;
		
		$this->load_section('global');
		
		if ( array_key_exists($setting, $this->templates['global']['config']) )
			return $this->templates['global']['config'][$setting];
		elseif ( !$functions->get_config('hide_undefined_template_setting_warnings') )
			trigger_error('The template configuration variable "'.$setting.'" does not exist!', E_USER_ERROR);
		else
			return '';
		
	}
	
	/**
	 * Parse a template
	 *
	 * @param string $name Template name
	 * @param string $section Template section
	 * @param array $variables Template variables
	 * @param bool $is_special Mark as a special template
	 * @param bool $enable_token Enable token
	 */
	function parse($name, $section, $variables=array(), $is_special=false, $enable_token=false) {
		
		global $functions;
		
		if ( $this->parse_special_templates_only && !$is_special )
			return;
		
		//
		// Load the template set
		//
		$this->load_section($section);
		
		if ( !array_key_exists($name, $this->templates[$section]) ) {
			
			if ( !$functions->get_config('hide_undefined_template_warnings') )			
				trigger_error('Unable to load "'.$name.'" template in '.$section.' templates file for set "'.$functions->get_config('template').'"!', E_USER_ERROR);
			else
				$this->templates[$section][$name] = '';
			
		}

		$variables = ( is_array($variables) && count($variables) ) ? $variables : array();

		if ( $enable_token )
			$this->install_token($variables);

		$this->requests[] = array(
			'section' => $section,
			'template_name' => $name,
			'variables' => $variables,
		);
		
	}

	/**
	 * Install token
	 *
	 * Called by parse(). Inserts form token into first variable containing input field.
	 *
	 * @param array $variables Template variables
	 */
	function install_token(&$variables) {

		global $functions;
		
		//
		// Find a variable where an input field was added.
		// This location is good to add a hidden field without breaking HTML validity.
		//
		$alter_key = null;
		foreach ( $variables as $key => $val ) {

			if ( strpos($val, '<input ') !== false ) {

				$alter_key = $key;
				break;

			}

		}
		
		if ( !isset($alter_key) )
			trigger_error('No template variable with input field found to install form token!', E_USER_ERROR);

		$token = $functions->generate_token();
		$variables[$alter_key] .= '<input type="hidden" name="_form_token_" value="'.$token.'" />';

	}
	
	/**
	 * Add global template variables
	 *
	 * @param array $variables Template variables
	 * @param bool $override Override variables when already exist
	 */
	function add_global_vars($variables, $override=false) {
		
		foreach ( $variables as $key => $val ) {
			
			if ( $override || !array_key_exists($key, $this->global_vars) )
				$this->global_vars[$key] = $val;
			
		}
		
	}
	
	/**
	 * Set the page title
	 *
	 * @param string $page_title Page title (may be HTML)
	 */
	function set_page_title($page_title) {
		
		global $functions;
		
		$this->add_global_vars(array(
			'page_title' => strip_tags($page_title),
			'location_bar' => ( $functions->get_config('single_forum_mode') && $functions->get_stats('viewable_forums') === 1 ) ? $page_title : '<a href="'.$functions->make_url('index.php').'">'.unhtml($functions->get_config('board_name')).'</a>'.$this->get_config('locationbar_item_delimiter').$page_title
		), true);
		
	}
	
	/**
	 * Add raw content outside templates
	 *
	 * @param string $content Raw content to place between the templates
	 * @param bool $strip_slashes Strip slashes from $content (true by default)
	 */
	function add_raw_content($content, $strip_slashes=true) {
		
		$this->requests[] = array(
			'raw' => true,
			'num' => count($this->raw_contents)
		);
		
		$this->raw_contents[] = ( $strip_slashes ) ? stripslashes($content) : $content;
		
	}
	
	/**
	 * Add a JavaScript onload statement
	 *
	 * @param string $statement JavaScript statement
	 */
	function set_js_onload($statement) {
		
		if ( substr($statement, -1) == ';' )
			$statement = substr_replace($statement, '', -1);
		
		if ( empty($statement) || in_array($statement, $this->js_onload) )
			return;
		
		$this->js_onload[] = $statement;
		
	}
	
	/**
	 * Replace all whitespace by a space except in <textarea /> and <pre />
	 *
	 * @param string $string Source code to compress
	 * @returns string Compressed source code
	 */
	function compress_sourcecode($string) {
		
		$matches = array();
		preg_match_all("#<textarea.*?>(.*?)</textarea>#is", $string, $matches[0]);
		preg_match_all("#<pre.*?>(.*?)</pre>#is", $string, $matches[1]);
		preg_match_all("#<script.*?>(.*?)</script>#is", $string, $matches[2]);
		$matches = array_merge($matches[0][0], $matches[1][0], $matches[2][0]);
		foreach ( $matches as $oldpart ) {
			
			$newpart = str_replace("\n", "\0", $oldpart);
			$string = str_replace($oldpart, $newpart, $string);
			
		}
		$string = str_replace("\r", "", $string);
		$string = preg_replace("#\s+#", ' ', $string);
		$string = str_replace("\0", "\n", $string);
		return $string;
		
	}
	
	/**
	 * Output the page body
	 *
	 * This method parses all the template data and takes care of unwanted output by triggering an error.
	 */
	function body() {
		
		global $db, $functions, $lang, $session;
		
		$body = '';
		
		//
		// Eventually set the content type and charset
		//
		$content_type = $this->get_config('content_type');
		if ( empty($this->content_type) )
			$this->content_type = ( !empty($content_type) ) ? $content_type : 'text/html';
		if ( empty($this->character_encoding) )
			$this->character_encoding = $lang['character_encoding'];
		
		//
		// application/xhtml+xml check
		// Sends as text/html when browser does not support XHTML or XHTML header is disabled
		//
		$this->content_type = ( preg_match('#^application/(xhtml\+)?xml$#i', $this->content_type) && ( empty($_SERVER['HTTP_ACCEPT']) || strstr($_SERVER['HTTP_ACCEPT'], $this->content_type) === false || $functions->get_config('disable_xhtml_header') ) ) ? 'text/html' : $this->content_type;
		
		//
		// Set content type and charset
		//
		header('Content-Type: '.$this->content_type.'; charset='.$this->character_encoding);
		
		//
		// Set cache control
		// Borrowed from phpBB 2.0.0 branch
		//
		if ( !empty($_SERVER['SERVER_SOFTWARE']) && strstr($_SERVER['SERVER_SOFTWARE'], 'Apache/2') )
			header('Cache-Control: no-cache, pre-check=0, post-check=0');
		else
			header('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
		header('Expires: 0');
		header('Pragma: no-cache');
		
		//
		// Debug features
		//
		if ( $functions->get_config('debug') != DEBUG_DISABLED && empty($session->sess_info['ip_banned']) ) {
			
			//
			// Timer for checking parsetime
			//
			$timer_end = explode(' ', microtime());
			$timer_end = (float)$timer_end[1] + (float)$timer_end[0];
			$parsetime = round($timer_end - TIMER_BEGIN, 4);
			
			$debug_info = array();
			$debug_info[] = $lang['ParseTime'].': '.$parsetime.' s';
			if ( function_exists('memory_get_peak_usage') )
				$debug_info[] = $lang['MemoryUsage'].': '.sprintf('%.2f', (memory_get_peak_usage() / 1024 / 1024)).' '.$lang['MegaByteShort'];
			if ( ( $server_load = $functions->get_server_load() ) == true )
				$debug_info[] = $lang['ServerLoad'].': '.sprintf('%.2f', $server_load);
			$debug_info[] = $lang['TemplateSections'].': '.count($this->loaded_sections);
			$debug_info[] = $lang['SQLQueries'].': '.count($db->get_used_queries());
			
			if ( $functions->get_config('debug') == DEBUG_SIMPLE ) {
				
				//
				// List parsetime and queries in short
				//
				$debug_info_small = sprintf($this->get_config('debug_info_small'), join($this->get_config('item_delimiter'), $debug_info));
				$debug_info_large = '';
				
			} elseif ( $functions->get_config('debug') == DEBUG_EXTENDED ) {
				
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
		unset($debug_info, $debug_info_small, $debug_info_large);
		
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
			'acp_css_head_link' => ( !defined('IS_INSTALLER') && $session->sess_info['location'] == 'admin' ) ? '<link rel="stylesheet" type="text/css" href="'.ROOT_PATH.'templates/'.$functions->get_config('template').'/admin.css" />' : '',
			'js_onload' => ( count($this->js_onload) ) ? ' onload="javascript:'.join(';', $this->js_onload).'"' : '',
			'more_css_classes' => '',
		));
		
		//
		// Parse all templates
		//
		foreach ( $this->requests as $request ) {
			
			if ( isset($request['raw']) ) {
				
				$body .= "\n".$this->raw_contents[$request['num']]."\n";
				continue;
				
			}
			
			$finds = $replaces = array();			
			foreach ( $request['variables'] as $key => $val ) {
				
				$finds[] = '{'.$key.'}';
				$replaces[] = str_replace(array('{', '}', '$'), array('&#123;', '&#125;', '&#36;'), $val);
				
			}
			$current_template = $this->templates[$request['section']][$request['template_name']];
			$body .= str_replace($finds, $replaces, $current_template);
			
		}
		unset($current_template);
		
		//
		// Parse global and language variables
		//
		$finds = $replaces = array();
		foreach ( $this->global_vars as $key => $val ) {
			
			$finds[] = '{'.$key.'}';
			$replaces[] = ( substr($key, 0, 3) == 'js_' ) ? $val : str_replace(array('{', '}', '$'), array('&#123;', '&#125;', '&#36;'), $val);
			
		}
		foreach ( $lang as $key => $val ) {
			
			if ( !is_string($val) || strpos($body, '{l_'.$key.'}') === false )
				continue;
			
			$finds[] = '{l_'.$key.'}';
			$replaces[] = $val;
			
		}
		$body = str_replace($finds, $replaces, $body);
		unset($finds, $replaces);
		
		//
		// Compression and output
		//
		if ( (int)$functions->get_config('output_compression') % 2 == 1 )
			$body = $this->compress_sourcecode($body);
		
		//
		// Clean bad ASCII characters
		//
		if ( strtolower($this->character_encoding) != 'utf-8' ) {
			
			$ascii = range(0, 31);
			unset($ascii[9], $ascii[10], $ascii[13]); // tab, newline and carriage return
			
			$finds = $replaces = '';
			foreach ( $ascii as $val ) {
				
				$finds .= chr($val);
				$replaces .= '.';
				
			}
			$body = strtr($body, $finds, $replaces);
			
		}
		
		ob_end_clean();
		echo trim($body);
		
	}
	
}

?>
