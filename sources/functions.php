<?php

/*
	Copyright (C) 2003-2006 UseBB Team
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
 * Functions
 *
 * Contains all kinds of procedural functions and the functions class.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2006 UseBB Team
 * @package	UseBB
 * @subpackage Core
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

/**
 * Adds slashes to and trim an array
 *
 * @param array $global Array to slash/trim
 * @returns array Slashed/trimmed array
 */
function slash_trim_global($global) {
	
	if ( is_array($global) ) {
		
		foreach ( $global as $key => $val ) {
			
			if ( is_array($val) ) {
				
				$global[$key] = slash_trim_global($val);
			
			} else {
				
				if ( !get_magic_quotes_gpc() )
					$val = addslashes($val);
				$global[$key] = trim($val);
				
			}
			
		}
		
	}
	
	return $global;
	
}

/**
 * Disable HTML in a string without disabling entities
 *
 * @param string $string String to un-HTML
 * @returns string Parsed $string
 */
function unhtml($string) {
	
	global $lang;
	
	$string = preg_replace(array('#&#', '#&amp;\#([0-9]+)#', '#&\#(160|173|8192|8193|8194|8195|8196|8197|8198|8199|8120|8201|8202|8203|8204|8205|8206|8207)#', '#<#', '#>#', '#"#', '#&\#?[a-z0-9]+$#'), array('&amp;', '&#\\1', '&amp;#\\1', '&lt;', '&gt;', '&quot;', ''), $string);
	
	//
	// If the character encoding isn't UTF-8, only keep valid ASCII characters:
	// all characters between 31 and 128, except tab, new line and carriage return
	//
	if ( strtolower($lang['character_encoding']) == 'utf-8' )
		return $string;
	
	$ascii = range(0, 31);
	
	//
	// We perform this twice to protect unpatched PHP versions
	// against the Zend_Hash_Del_Key_Or_Index vulnerability.
	//
	unset($ascii[9], $ascii[10], $ascii[13]);
	unset($ascii[9], $ascii[10], $ascii[13]);
	
	$ascii_replace = array();
	foreach ( $ascii as $val )
		$ascii_replace[chr($val)] = '';
	
	return strtr($string, $ascii_replace);
	
}

/**
 * Gives the length of a string and counts a HTML entitiy as one character.
 *
 * @param string $string String to find length of
 * @returns int Length of $string
 */
function entities_strlen($string) {
	
	return strlen(preg_replace('#&\#?[a-z0-9]+;#', '.', $string));
	
}

/**
 * Right trim a string to $length characters, keeping entities as one character.
 *
 * @param string $string String to trim
 * @param int $length Length of new string
 * @returns string Trimmed string
 */
function entities_rtrim($string, $length) {
	
	$new_string = '';
	$new_length = $pos = 0;
	$entity_open = false;
	
    if ( function_exists('mb_language') && mb_language() != 'neutral') {
    	
        $strlen = 'mb_strlen';
        $substr = 'mb_substr';
        
    } else {
    	
        $strlen = 'strlen';
        $substr = 'substr';
        
    }
    
	while ( $pos < $strlen($string) && ( $new_length < $length || $entity_open ) ) {
		
		$char = $substr($string, $pos, 1);
		
		if ( $char == '&' ) {
			
			$entity_open = true;
			
		} elseif ( $char == ';' && $entity_open ) {
			
			$entity_open = false;
			$new_length++;
			
		} elseif ( !$entity_open ) {
			
			$new_length++;
			
		}
		
		$new_string .= $char;
		$pos++;
		
	}
	
	return $new_string;
	
}

/**
 * Check if a variable contains a valid integer
 *
 * @param string $string String to check
 * @returns bool Contains valid integer
 */
function valid_int($string) {
	
	if ( $string == strval(intval($string)) )
		return true;
	else
		return false;
	
}

/**
 * checkdnsrr replacement for Windows
 *
 * @author Zend.com
 * @link http://www.zend.com/codex.php?id=370&single=1
 * @param string $host host
 * @param string $type type
 * @returns bool Contains valid integer
 */
function checkdnsrr_win($host, $type='') {
	
	$types = array(
		'A',
		'MX',
		'NS',
		'SOA',
		'PTR',
		'CNAME',
		'AAAA',
		'A6',
		'SRV',
		'NAPTR',
		'ANY'
	);
	$type = ( !empty($type) && in_array($type, $types) ) ? $type : 'MX';
	
	$output = array();
	@exec('nslookup -type='.$type.' '.$host, $output);
	
	foreach ( $output as $line ) {
		
		if ( preg_match('#^'.$host.'#', $line) )
			return true;
		
	}
	
	return false;
	
} 

/**
 * Functions
 *
 * All kinds of functions used everywhere.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2006 UseBB Team
 * @package	UseBB
 * @subpackage Core
 */
class functions {
	
	/**#@+
	 * @access private
	 */
	var $board_config;
	var $board_config_original;
	var $statistics = array();
	var $languages = array();
	var $language_sections = array();
	var $mod_auth;
	var $badwords;
	var $updated_forums;
	var $available = array('templates' => array(), 'languages' => array());
	var $db_tables = array();
	var $server_load;
	var $is_mbstring;
	/**#@-*/
	
	/**
	 * @access private
	 */
	function usebb_die($errno, $error, $file, $line) {
		
		global $db, $dbs, $template;
		
		//
		// Ignore various warnings:
		//  - ini_set() and ini_get() disabled
		//  - exec() disabled
		//  - var: Deprecated (PHP 5)
		//  - property of non-object (bug(?) in some old PHP 5 version)
		//  - zend.ze1_compatibility_mode notice
		//  - errors regarding /proc/loadavg
		//  - errors regarding unknown languages for mb_language()
		//  - errors regarding unserialize()
		//
		$ignore_warnings = array(
			'ini_set() has been disabled for security reasons',
			'ini_get() has been disabled for security reasons',
			'exec() has been disabled for security reasons'
		);
		if ( version_compare(phpversion(), '5.0.0', '>=') ) {
			
			$ignore_warnings[] = 'var: Deprecated. Please use the public/private/protected modifiers';
			$ignore_warnings[] = 'Trying to get property of non-object';
			
		}
		if ( in_array($error, $ignore_warnings) || preg_match('#(zend\.ze1_compatibility_mode|/proc/loadavg|mb_language|unserialize)#', $error) )
			return;
		
		//
		// Error processing...
		//
		
		$errtypes = array(
			1 => 'E_ERROR',
			2 => 'E_WARNING',
			4 => 'E_PARSE',
			8 => 'E_NOTICE',
			16 => 'E_CORE_ERROR',
			32 => 'E_CORE_WARNING',
			64 => 'E_COMPILE_ERROR',
			128 => 'E_COMPILE_WARNING',
			256 => 'E_USER_ERROR',
			512 => 'E_USER_WARNING',
			1024 => 'E_USER_NOTICE'
		);
		
		$errtype = ( preg_match('#^SQL: #', $error) ) ? 'SQL_ERROR' : $errtypes[$errno];
		
		if ( $errtype == 'SQL_ERROR' )
			$error = substr($error, 5);
		
		error_log('[UseBB Error] ['.date('D M d G:i:s Y').'] ['.$errtype.' - '.preg_replace('#(\s+|\s)#', ' ', $error).'] ['.$file.':'.$line.']');
		
		if ( preg_match('#^mysql#', $error) && $this->get_config('debug') < 2 )
			$error = preg_replace("#'[^ ]+'?@'?[^ ]+'#", '<em>-filtered-</em>', $error);
		
		$html_msg  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>UseBB General Error</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<style type="text/css">
			body {
				font-family: sans-serif;
				font-size: 10pt;
			}
			h1 {
				color: #369;
			}
			blockquote {
				width: 55%;
				border-top: 2px solid silver;
				border-bottom: 2px solid silver;
				font-family: monospace;
				font-size: 8pt;
			}
			#error {
				color: #7f0000;
			}
			textarea {
				width: 98%;
				border: 1px solid silver;
				padding: 3px;
			}
		</style>
	</head>
	<body>
		<h1>UseBB General Error</h1>
		<p>An error was encountered. We apologize for any inconvenience.</p>
		<blockquote>
			<p>In file <strong>'.substr(str_replace(dirname($file), '', $file), 1).'</strong> on line <strong>'.$line.'</strong>:</p>
			<p id="error"><em>'.$errtype.'</em> - '.nl2br($error).'</p>';
				
		if ( $errtype == 'SQL_ERROR' ) {
			
			//
			// FIXME
			// Needs to be done this way due to bug(?): http://bugs.php.net/bug.php?id=33643
			//
			$used_queries = $db->get_used_queries();
			
			if ( count($used_queries) ) {
				
				if ( $this->get_config('debug') >= 2 ) {
					
					$html_msg .= '
			<p>SQL query causing the error:</p><p><textarea rows="10" cols="60" readonly="readonly">'.unhtml(end($used_queries)).'</textarea></p>';
					
				} elseif ( is_array($this->board_config) ) {
					
					$html_msg .= '
			<p>Enable debug mode level 2 to see the error and erroneous SQL query.</p>';
					
				}
				
			}
			
		}
		
		$html_msg .= '
		</blockquote>';
		
		//
		// Installation note if
		// - mysql*() error "Access denied for user"
		// - sql error "Table 'x' doesn't exist" or "Access denied for user"
		//
		if ( ( preg_match('#^mysql#i', $error) && preg_match("#Access denied for user#i", $error) ) ||
		( $errtype == 'SQL_ERROR' && preg_match("#(Table '.+' doesn't exist|Access denied for user)#i", $error) ) ) {
			
			$html_msg .= '
		<p>It seems UseBB is not installed yet. If you are the webmaster of this board, please see <a href="docs/index.html">docs/index.html</a> for installation instructions.</p>';
			
		} else {
			
			$html_msg .= '
		<p>This error should probably not have occured, so please report it to the webmaster. Thank you for your help.</p>
		<p>If you are the webmaster of this board and you believe this is a bug, please send a bug report.</p>';
			
		}
		
	$html_msg .= '
	</body>
</html>';
		
		if ( isset($template) )
			ob_end_clean();
		die($html_msg);
		
	}
	
	/**
	 * Get configuration variables
	 *
	 * @param string $setting Setting to retrieve
	 * @param bool $original Use original config.php configuration
	 * @returns mixed Value of setting
	 */
	function get_config($setting, $original=false) {
		
		global $session;
		
		//
		// Load settings
		//
		if ( !isset($this->board_config) && isset($GLOBALS['conf']) ) {
			
			$this->board_config = $this->board_config_original = $GLOBALS['conf'];
			unset($GLOBALS['conf']);
			
		}
		
		if ( defined('IS_INSTALLER') || $original ) {
			
			//
			// Fix config name change
			//
			if ( $setting == 'activation_mode' && !array_key_exists($setting, $this->board_config_original) )
				return $this->get_config('users_must_activate');
			
			//
			// Return unedited config
			//
			if ( array_key_exists($setting, $this->board_config_original) )
				return $this->board_config_original[$setting];
			else
				return false;
			
		} else {
			
			//
			// Member preferences
			//
			if ( isset($session) && isset($session->sess_info) && !empty($session->sess_info['user_id']) && array_key_exists($setting, $session->sess_info['user_info']) ) {
				
				$keep_default = false;
				
				if ( $setting == 'language' ) {
					
					//
					// Keep default when missing language pack
					//
					if ( !in_array($session->sess_info['user_info'][$setting], $this->get_language_packs()) )
						$keep_default = true;
					
				} elseif ( $setting == 'template' ) {
					
					//
					// Keep default when missing template set
					//
					if ( !in_array($session->sess_info['user_info'][$setting], $this->get_template_sets()) )
						$keep_default = true;
					
				}
				
				//
				// Overwrite board setting with user setting
				//
				if ( !$keep_default )
					$this->board_config[$setting] = stripslashes($session->sess_info['user_info'][$setting]);
				
			}
			
			//
			// Fill in missing settings
			//
			if ( is_array($this->board_config) && !array_key_exists($setting, $this->board_config) || ( is_string($this->board_config[$setting]) && trim($this->board_config[$setting]) === '' ) ) {
				
				switch ( $setting ) {
					
					case 'board_url':
						$path_parts = pathinfo($_SERVER['SCRIPT_NAME']);
						if ( !preg_match('#/$#', $path_parts['dirname']) )
							$path_parts['dirname'] .= '/';
						$protocol = ( isset($_SERVER['HTTPS']) ) ? 'https' : 'http';
						$set_to = $protocol.'://'.$_SERVER['HTTP_HOST'].$path_parts['dirname'];
						break;
					
					case 'cookie_domain':
						$set_to = ( !empty($_SERVER['SERVER_NAME']) && preg_match('#^([a-z0-9\-]+\.){1,}[a-z]{2,}$#i', $_SERVER['SERVER_NAME']) ) ? preg_replace('#^www\.#', '.', $_SERVER['SERVER_NAME']) : '';
						break;
					
					case 'cookie_path':
						$set_to = '/';
						break;
					
					case 'search_limit_results':
					case 'sig_max_length':
						$set_to = 1000;
						break;
					
					case 'search_nonindex_words_min_length':
					case 'username_min_length':
						$set_to = 3;
						break;
					
					case 'enable_ip_bans':
					case 'enable_badwords_filter':
					case 'guests_can_see_contact_info':
					case 'show_raw_entities_in_code':
					case 'show_never_activated_members':
					case 'disable_xhtml_header':
					case 'hide_db_config_acp':
						$set_to = true;
						break;
					
					case 'activation_mode':
						$set_to = $this->get_config('users_must_activate');
						break;
					
					case 'view_search_min_level':
					case 'view_active_topics_min_level':
						$set_to = LEVEL_GUEST;
						break;
					
					case 'dnsbl_powered_banning_whitelist':
					case 'dnsbl_powered_banning_servers':
						$set_to = array();
						break;
					
					case 'username_max_length':
						$set_to = 30;
						break;
					
					case 'edit_post_timeout':
						$set_to = 300;
						break;
					
					default:
						$set_to = false;
					
				}
				
				//
				// Set the new value
				//
				$this->board_config[$setting] = $set_to;
				
			} elseif ( is_array($this->board_config) && array_key_exists($setting, $this->board_config) ) {
				
				//
				// Fix crappy settings
				//
				if ( $setting == 'board_url' && !preg_match('#/$#', $this->board_config[$setting]) )
					$this->board_config[$setting] .= '/';
				if ( $setting == 'session_name' && ( !preg_match('#^[A-Za-z0-9]+$#', $this->board_config[$setting]) || preg_match('#^[0-9]+$#', $this->board_config[$setting]) ) )
					$this->board_config[$setting] = 'usebb';
					
				
			}
			
			//
			// Return setting
			//
			return $this->board_config[$setting];
			
		}
		
	}
	
	/**
	 * Get board statistics
	 *
	 * @param string $stat Statistical value to retrieve
	 * @returns mixed Statistical value
	 */
	function get_stats($stat) {
		
		global $db;
		
		if ( $stat == 'categories' ) {
			
			//
			// Get the category count
			//
			if ( !array_key_exists($stat, $this->statistics) ) {
				
				$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."cats");
				$out = $db->fetch_result($result);
				$this->statistics[$stat] = $out['count'];
				
			}
			
			return $this->statistics[$stat];
			
		} elseif ( $stat == 'forums' || $stat == 'viewable_forums' ) {
			
			//
			// Get the forums
			//
			if ( !array_key_exists($stat, $this->statistics) ) {
				
				$result = $db->query("SELECT id, auth FROM ".TABLE_PREFIX."forums");
				$this->statistics['forums'] = 0;
				$this->statistics['viewable_forums'] = 0;
				
				while ( $forumdata = $db->fetch_result($result) ) {
					
					//
					// We also set the other statistic: (viewable_)forums
					// This might save a query
					//
					
					// forums
					$this->statistics['forums']++;
					
					// viewable_forums
					if ( $this->auth($forumdata['auth'], 'view', $forumdata['id']) )
						$this->statistics['viewable_forums']++;
					
				}
				
			}
			
			return $this->statistics[$stat];
			
		} elseif ( $stat == 'latest_member' ) {
			
			//
			// Get the latest member
			//
			if ( !array_key_exists($stat, $this->statistics) ) {
				
				$never_activated_sql = ( $this->get_config('show_never_activated_members') ) ? "" : " WHERE ( active <> 0 OR last_login <> 0 )";
				$result = $db->query("SELECT id, displayed_name, regdate FROM ".TABLE_PREFIX."members".$never_activated_sql." ORDER BY id DESC LIMIT 1");
				$this->statistics[$stat] = $db->fetch_result($result);
				
			}
			
			return $this->statistics[$stat];
			
		} else {
			
			if ( !array_key_exists($stat, $this->statistics) ) {
				
				$result = $db->query("SELECT name, content FROM ".TABLE_PREFIX."stats");
				while ( $out = $db->fetch_result($result) )
					$this->statistics[$out['name']] = $out['content'];
				
			}
			
			if ( array_key_exists($stat, $this->statistics) )
				return $this->statistics[$stat];
			else
				trigger_error('The statistic variable "'.$stat.'" does not exist!', E_USER_ERROR);
			
		}
		
	}
	
	/**
	 * Interactive URL builder
	 *
	 * @param string $filename .php filename to link to
	 * @param array $vars GET variabeles
	 * @param bool $html Return HTML URL
	 * @param bool $enable_sid Enable session ID's
	 * @param bool $force_php Force linking to .php files
	 * @returns string URL
	 */
	function make_url($filename, $vars=array(), $html=true, $enable_sid=true, $force_php=false) {
		
		global $session;
		
		if ( !$force_php && !defined('IS_INSTALLER') && $this->get_config('friendly_urls') && $filename != 'admin.php' ) {
			
			//
			// Friendly URL's
			//
			$url = preg_replace('#\.php$#', '', $filename);
			foreach ( $vars as $key => $val ) {
				
				if ( in_array($key, array('forum', 'topic', 'post', 'quotepost', 'al')) )
					$url .= '-'.urlencode($key.$val);
				else
					$url .= '-'.urlencode($val);
				
			}
			$url .= ( $filename == 'rss.php' ) ? '.xml' : '.html';
			
		} else {
			
			$url = $filename;
			$vars = ( is_array($vars) ) ? $vars : array();
			if ( isset($vars[$this->get_config('session_name').'_sid']) )
				unset($vars[$this->get_config('session_name').'_sid']);
			
			//
			// Pass session ID's
			//
			if ( defined('SID') ) {
				
				$SID = SID;
				$SID_parts = explode('=', $SID, 2);
				
			}
			
			if ( $enable_sid && !empty($SID) && !preg_match('#Googlebot#i', $_SERVER['HTTP_USER_AGENT']) && ( !$html || ( $html && !@ini_get('session.use_trans_sid') ) ) )
				$vars[$SID_parts[0]] = $SID_parts[1];
			
			if ( count($vars) ) {
				
				$url .= '?';
				
				if ( $html ) {
					
					foreach ( $vars as $key => $val )
						$safe[] = urlencode($key).'='.urlencode($val);
					$url .= join('&amp;', $safe);
					
				} else {
					
					foreach ( $vars as $key => $val )
						$safe[] = $key.'='.$val;
					$url .= join('&', $safe);
					
				}
				
			}
			
		}
		
		return $url;
		
	}
	
	/**
	 * Attaches a SID to URLs which should contain one (e.g. referer URLs)
	 *
	 * @param string $url URL
	 * @returns string URL
	 */
	function attach_sid($url) {
		
		$SID = SID;
		if ( !$this->get_config('friendly_urls') && !empty($SID) && !preg_match('/'.preg_quote($SID, '/').'$/', $url) ) {
			
			if ( strpos($url, '?') )
				return $url.'&'.$SID;
			else
				return $url.'?'.$SID;
			
		} else {
			
			return $url;
			
		}
		
	}
	
	/**
	 * Fetch a language file
	 *
	 * @param string $language Language name (default language is used when missing)
	 * @param string $section Section name (main section is used when missing)
	 * @returns array Language variables
	 */
	function fetch_language($language='', $section='') {
		
		$language = ( !empty($language) && in_array($language, $this->get_language_packs()) ) ? $language : $this->get_config('language');
		$section = ( !empty($section) ) ? $section : 'lang';
		
		if ( !array_key_exists($language, $this->language_sections) || !in_array($section, $this->language_sections[$language]) ) {
			
			if ( $section != 'lang' ) {
				
				$lang = $GLOBALS['lang'];
				if ( !file_exists(ROOT_PATH.'languages/'.$section.'_'.$language.'.php') ) {
					
					if ( $language != 'English' && in_array('English', $this->get_language_packs()) )
						require(ROOT_PATH.'languages/'.$section.'_English.php');
					else
						trigger_error('Section "'.$section.'" for language pack "'.$language.'" could not be found. No English fallback was available. Please use an updated language pack or also upload the English one.', E_USER_ERROR);
					
				} else {
					
					require(ROOT_PATH.'languages/'.$section.'_'.$language.'.php');
					
					if ( $language != 'English' && in_array('English', $this->get_language_packs()) )
						$lang = array_merge($this->fetch_language('English', $section), $lang);
					
				}
				
			} else {
				
				require(ROOT_PATH.'languages/'.$section.'_'.$language.'.php');
				
				if ( $language != 'English' && in_array('English', $this->get_language_packs()) )
					$lang = array_merge($this->fetch_language('English', $section), $lang);
				
				if ( empty($lang['character_encoding']) )
					$lang['character_encoding'] = 'iso-8859-1';
				
				if ( function_exists('mb_internal_encoding') ) {
					
					//  setting mbstring.
					$mb_internal_encoding = ( $lang['character_encoding'] == 'iso-8859-8-i' ) ? 'iso-8859-8' : $lang['character_encoding'];

					$is_mb_language = @mb_language($language);
					$is_mb_internal_encoding = @mb_internal_encoding($mb_internal_encoding);
					
					if ( $is_mb_language !== FALSE || $is_mb_internal_encoding !== FALSE) {
						
						$this->is_mbstring = TRUE;
						
					} else {
						 
						//  mbstring can not be used, it resets then.
						mb_language('neutral');
						mb_internal_encoding('ISO-8859-1');
						
					}

					//  reset other parameters.
					ini_set('mbstring.http_input', 'pass');
					ini_set('mbstring.http_output', 'pass');
					ini_set('mbstring.func_overload', 0);
					ini_set('mbstring.substitute_character', 'none');
				}
				
			}
			
			$this->languages[$language] = $lang;
		}
		
		if ( !array_key_exists($language, $this->language_sections) )
			$this->language_sections[$language] = array();
		$this->language_sections[$language][] = $section;
		
		return $this->languages[$language];
		
	}
	
	/**
	 * Kick a user to the login form
	 */
	function redir_to_login() {
		
		global $session, $template, $lang;
		
		if ( !$session->sess_info['user_id'] ) {
			
			$_SESSION['referer'] = $_SERVER['REQUEST_URI'];
			$this->redirect('panel.php', array('act' => 'login'));
			
		} else {
			
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			$template->set_page_title($lang['Note']);
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Note'],
				'content' => $lang['NotPermitted']
			));
			
		}
		
	}
	
	/**
	 * Generate a date given a timestamp
	 *
	 * @param int $stamp Unix timestamp
	 * @param string $format Date format syntax (identical to PHP's date() - default is used when missing)
	 * @param bool $keep_gmt Use GMT and no time zones
	 * @param bool $translate Localize dates
	 * @returns string Date
	 */
	function make_date($stamp, $format='', $keep_gmt=false, $translate=true) {
		
		global $lang;
		
		$format = ( !empty($format) ) ? $format : strip_tags($this->get_config('date_format'));
		
		if ( $keep_gmt )
			$date = gmdate($format, $stamp);
		else
			$date = gmdate($format, $stamp + (3600 * $this->get_config('timezone')) + (3600 * $this->get_config('dst')));
		
		if ( $translate && array_key_exists('date_translations', $lang) && is_array($lang['date_translations']) )
			$date = ucfirst(strtr($date, $lang['date_translations']));
		
		return $date;
		
	}
	
	/**
	 * Generate a time past string
	 *
	 * @param int $timestamp Unix timestamp
	 * @param int $until Calculate time past until this Unix timestamp (current is used when missing)
	 * @returns string Time past
	 */
	function time_past($timestamp, $until=null) {
	
		global $lang;
	
		$seconds = ( ( is_int($until) ) ? $until : time() ) - $timestamp;
	
		$times = array();
		$sections = array(
			'weeks' => 604800,
			'days' => 86400,
			'hours' => 3600,
			'minutes' => 60,
			'seconds' => 1
		);
	
		foreach( $sections as $what => $length ) {
			
			if ( $seconds >= $length ) {
				
				$times[$what] = ( $length >0 ) ? floor($seconds / $length) : $length;
				$seconds %= $length;
				
			}
			
		}
	
		$sections = array();
		foreach ( $times as $key => $val )
			$sections[] = $val.' '.$lang[ucfirst($key)];
	
		return array($times, join(', ', $sections));
	
	}
	
	/**
	 * Generate an e-mail link
	 *
	 * @param array $user User information
	 * @returns string HTML
	 */
	function show_email($user) {
		
		global $session, $lang;
		
		if ( $this->get_user_level() >= intval($this->get_config('view_hidden_email_addresses_min_level')) ) {
			
			//
			// The viewing user is an administrator
			//
			if ( $this->get_config('email_view_level') == 1 )
				return '<a href="'.$this->make_url('mail.php', array('id' => $user['id'])).'">'.$lang['SendMessage'].'</a>';
			elseif ( !$this->get_config('email_view_level') || $this->get_config('email_view_level') == 2 || $this->get_config('email_view_level') == 3 )
				return '<a href="mailto:'.$user['email'].'">'.$user['email'].'</a>';
			
		} else {
			
			//
			// The viewing user is not an administrator
			//
			if ( !$this->get_config('email_view_level') || !$user['email_show'] && $user['id'] != $session->sess_info['user_id'] )
				return $lang['Hidden'];
			elseif ( $this->get_config('email_view_level') == 1 )
				return '<a href="'.$this->make_url('mail.php', array('id' => $user['id'])).'">'.$lang['SendMessage'].'</a>';
			elseif ( $this->get_config('email_view_level') == 2 )
				return str_replace('@', ' at ', $user['email']);
			elseif ( $this->get_config('email_view_level') == 3 )
				return '<a href="mailto:'.$user['email'].'">'.$user['email'].'</a>';
			
		}
		
	}
	
	/**
	 * Generate a random key
	 *
	 * @returns string Random key
	 */
	function random_key() {
		
		$characters = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890';
		$length = ( $this->get_config('passwd_min_length') > 10 ) ? $this->get_config('passwd_min_length') : 10;
		$key = '';
		
		for ( $i=0; $i<$length; $i++ ) {
			
			//
			// Make a seed for the random key generator
			// This is needed on PHP < 4.2.0
			//
			$seed = explode(' ', microtime());
			mt_srand($seed[0] * $seed[1]);
			$key .= $characters[mt_rand(0, strlen($characters)-1)];
			
		}
		
		return $key;
		
	}
	
	/**
	 * Send an email
	 *
	 * Why don't they just send me an e-mail? -- Belgian ad for coffee
	 *
	 * @param string $subject Subject of e-mail
	 * @param string $rawbody Body of e-mail
	 * @param array $bodyvars Variables for e-mail body
	 * @param string $from_name Name of sender
	 * @param string $from_email E-mail of sender
	 * @param string $to E-mail of recipient
	 * @param string $bcc_email E-mail of BCC recipient (no BCC when missing)
	 * @param string $language Language name the e-mail is in (default language when missing)
	 * @param string $charset Character set the e-mail is in (default charset when missing)
	 */
	function usebb_mail($subject, $rawbody, $bodyvars=array(), $from_name, $from_email, $to, $bcc_email='', $language='', $charset='') {
		
		global $lang;
		
		$bodyvars = ( is_array($bodyvars) ) ? $bodyvars : array();
		
		$is_enable_mbstring = ( function_exists('mb_language') && mb_language() != 'neutral' );

		//
		// Eventually use the right language and character encoding which may be passed
		// in the parameters when another language is used (e.g. subscription notices)
		//
		$language = ( !empty($language) ) ? $language : $this->get_config('language');
		$charset = ( !empty($charset) ) ? $charset : $lang['character_encoding'];
		
		//
		// Set the correct mb_language when neccessary (when mbstring enabled)
		//
		$is_mbstring = FALSE;
		if ( $this->is_mbstring ) {
			
			$backup_mb_language = mb_language();
			$backup_mb_internal_encoding = mb_internal_encoding();
			
			if ( @mb_language($language) !== FALSE && @mb_internal_encoding($charset) !== FALSE )
				$is_mbstring = TRUE;
			
		}
		
		$body = str_replace(array("\r\n", "\r"), "\n", $rawbody);
		
		//
		// Windows: \r\n; other: \n
		//
		$cr = ( strstr(PHP_OS, 'WIN') !== false ) ? "\r\n" : "\n";
		$body = str_replace("\n", $cr, $rawbody);
		
		$bodyvars['board_name'] = $this->get_config('board_name');
		$bodyvars['board_link'] = $this->get_config('board_url');
		$bodyvars['admin_email'] = $this->get_config('admin_email');
		
		foreach ( $bodyvars as $key => $val )
			$body = str_replace('['.$key.']', $val, $body);
		
		$headers = array();
		
		if ( $is_mbstring && function_exists('mb_encode_mimeheader') ) {
			
			$from_name = mb_encode_mimeheader($from_name);
			
		} else {
			
			if ( strtolower($charset) == 'utf-8' ) {

				$subject = '=?'.$charset.'?B?'.base64_encode($subject).'?=';
				$from_name = '=?'.$charset.'?B?'.base64_encode($from_name).'?=';

			}
			
		}
		
		if ( !empty($bcc_email) )
			$headers[] = 'Bcc: '.$bcc_email;
		$headers[] = 'Date: '.date('r');
		$headers[] = 'Message-Id: '.sprintf("<%s.%s>", substr(md5(time()), 4, 10), $from_email);
		$headers[] = 'X-Mailer: UseBB';
		
		//
		// Fix for hosts that require From to be a domain name hosted on the same host
		// So, instead we can use a Reply-To header to contain the sender email
		//
		if ( $from_email != $this->get_config('admin_email') && $this->get_config('email_reply-to_header') ) {
			
			$headers[] = 'From: '.$from_name.' <'.$this->get_config('admin_email').'>';
			$headers[] = 'Reply-To: '.$from_email;
			
		} else {
			
			$headers[] = 'From: '.$from_name.' <'.$from_email.'>';
			
		}
		
		$is_safe_mode = in_array(strtolower(ini_get('safe_mode')), array('1', 'on'));

		if ( $is_mbstring && function_exists('mb_send_mail') ) {

			$mail_func = 'mb_send_mail';

		} else {
			
			$mail_func = 'mail';
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-Type: text/plain; charset='.$charset;
			if ( preg_match('/^(iso-8859-|iso-2022-)/i', $charset))
				$headers[] = 'Content-Transfer-Encoding: 7bit';
			else
				$headers[] = 'Content-Transfer-Encoding: 8bit';
			
		}

		if ( $is_safe_mode )
			$mail_result = $mail_func($to, $subject, $body, join($cr, $headers));
		else
			$mail_result = $mail_func($to, $subject, $body, join($cr, $headers), '-f'.$from_email);

		if ( !$mail_result )
			trigger_error('Unable to send e-mail!', E_USER_ERROR);
		
		//
		// Restored language and character encoding.
		//
		if ( $this->is_mbstring ) {
			
			mb_language($backup_mb_language);
			mb_internal_encoding($backup_mb_internal_encoding);
			
		}
		
	}
	
	/**
	 * Set the remember cookie
	 *
	 * @param int $user_id User ID
	 * @param string $passwd_hash Password hash
	 */
	function set_al($user_id, $passwd_hash) {
		
		$content = array(
			intval($user_id),
			$passwd_hash
		);
		$this->setcookie($this->get_config('session_name').'_al', serialize($content), time()+31536000);
		
	}
	
	/**
	 * Unset the remember cookie
	 */
	function unset_al() {
		
		$this->setcookie($this->get_config('session_name').'_al', '');
		
	}
	
	/**
	 * Is the remember cookie set?
	 *
	 * @returns bool Remember cookie set
	 */
	function isset_al() {
		
		if ( !empty($_COOKIE[$this->get_config('session_name').'_al']) )
			return true;
		else
			return false;
		
	}
	
	/**
	 * Get the remember cookie's value
	 *
	 * @returns mixed Array with user ID and password hash -or- false when not set
	 */
	function get_al() {
		
		if ( $this->isset_al() ) {
			
			$content = stripslashes($_COOKIE[$this->get_config('session_name').'_al']);
			if ( substr($content, 0, 1) == 'a' )
				return unserialize($content);
			else
				return explode(':', $content, 2);
			
		} else {
			
			return false;
			
		}
		
	}
	
	/**
	 * Get the user's level
	 *
	 * @returns int User level
	 */
	function get_user_level() {
		
		global $session;
		
		if ( !isset($session->sess_info['user_id']) )
			trigger_error('You first need to call $session->update() before you can get any session info.', E_USER_ERROR);
		
		if ( $session->sess_info['user_id'] )
			return $session->sess_info['user_info']['level'];
		else
			return LEVEL_GUEST;
		
	}
	
	/**
	 * Authorization function
	 *
	 * Defines whether a user has permission to take a certain action.
	 *
	 * @param string $auth_int Authorization "integer" (string because of leading zeroes)
	 * @param string $action Action to establish
	 * @param int $forum_id ID of forum
	 * @param bool $self For own account
	 * @param array $alternative_user_info When not for own account, array with user information
	 * @returns bool Allowed
	 */
	function auth($auth_int, $action, $forum_id, $self=true, $alternative_user_info=null) {
		
		global $session, $db;
		
		if ( $self )
			$user_info = ( $session->sess_info['user_id'] ) ? $session->sess_info['user_info'] : array('id' => LEVEL_GUEST, 'level' => LEVEL_GUEST);
		else
			$user_info = $alternative_user_info;
		
		if ( ( $self && $session->sess_info['ip_banned'] ) || ( $this->get_config('board_closed') && $user_info['level'] < LEVEL_ADMIN ) )
			return false;
		
		//
		// Define the user level
		//
		if ( $user_info['id'] ) {
			
			if ( $user_info['level'] == LEVEL_MOD ) {
				
				if ( !is_array($this->mod_auth) ) {
					
					$result = $db->query("SELECT forum_id FROM ".TABLE_PREFIX."moderators WHERE user_id = ".$user_info['id']);
					$this->mod_auth = array();
					while ( $out = $db->fetch_result($result) )
						$this->mod_auth[] = intval($out['forum_id']);
					
				}
				
				if ( in_array($forum_id, $this->mod_auth) )
					$userlevel = LEVEL_MOD;
				else
					$userlevel = LEVEL_MEMBER;
				
			} else {
				
				$userlevel = $user_info['level'];
				
			}
			
		} else {
			
			if ( !$this->get_config('guests_can_access_board') )
				return false;
			else
				$userlevel = LEVEL_GUEST;
			
		}
		
		//
		// Get the part of the auth integer that
		// corresponds with the action given
		//
		$actions = array(
			'view' => 0,
			'read' => 1,
			'post' => 2,
			'reply' => 3,
			'edit' => 4,
			'move' => 5,
			'delete' => 6,
			'lock' => 7,
			'sticky' => 8,
			'html' => 9
		);
		$min_level = intval($auth_int[$actions[$action]]);
		
		//
		// If the user level is equal or greater than the
		// auth integer, return a true, otherwise return a false.
		//
		if ( $userlevel >= $min_level )
			return true;
		else
			return false;
		
	}
	
	/**
	 * Return a list of moderators, clickable and separated with commas
	 *
	 * @param int $forum Forum ID
	 * @param array $listarray Array with all moderators (automatically requested when missing)
	 * @returns string Moderator list
	 */
	function get_mods_list($forum, $listarray=false) {
		
		global $db, $lang;
		
		$forum_moderators = array();
		
		if ( is_array($listarray) && count($listarray) ) {
			
			foreach ( $listarray as $modsdata ) {
				
				if ( $modsdata['forum_id'] == $forum )
					$forum_moderators[] = $this->make_profile_link($modsdata['id'], $modsdata['displayed_name'], $modsdata['level']);
				
			}
			
			if ( !count($forum_moderators) ) {
				
				return $lang['Nobody'];
				
			}
			
		} else {
			
			$result = $db->query("SELECT u.id, u.displayed_name, u.level FROM ".TABLE_PREFIX."members u, ".TABLE_PREFIX."moderators m WHERE m.forum_id = ".$forum." AND m.user_id = u.id ORDER BY u.displayed_name");
			while ( $modsdata = $db->fetch_result($result) )
				$forum_moderators[] = $this->make_profile_link($modsdata['id'], $modsdata['displayed_name'], $modsdata['level']);
				
			if ( !count($forum_moderators) ) {
				
				return $lang['Nobody'];
				
			}
			
		}
		
		//
		// Join all values in the array
		//
		return join(', ', $forum_moderators);
		
	}
	
	/**
	 * Return a clickable list of pages
	 *
	 * @param int $pages_number Total number of pages
	 * @param int $current_page Current page
	 * @param int $items_number Number of items
	 * @param int $items_per_page Items per page
	 * @param string $page_name .php page name
	 * @param int $page_id_val URL id GET value
	 * @param bool $back_forward_links Enable back and forward links
	 * @param array $url_vars Other URL vars
	 * @param bool $force_php Force linking to .php files
	 * @returns string HTML
	 */
	function make_page_links($pages_number, $current_page, $items_number, $items_per_page, $page_name, $page_id_val=NULL, $back_forward_links=true, $url_vars=array(), $force_php=false) {
		
		global $lang;
		
		if ( intval($items_number) > intval($items_per_page) ) {
			
			$page_links = array();
			$page_links_groups_length = 4;
			
			if ( !$current_page ) {
				
				$current_page = $pages_number+1;
				$page_links_groups_length++;
				
			}
			
			for ( $i = 1; $i <= $pages_number; $i++ ) {
				
				if ( $current_page != $i ) {
					
					if ( $i+$page_links_groups_length >= $current_page && $i-$page_links_groups_length <= $current_page ) {
						
						if ( valid_int($page_id_val) )
							$url_vars['id'] = $page_id_val;
						$url_vars['page'] = $i;
						$page_links[] = '<a href="'.$this->make_url($page_name, $url_vars, true, true, $force_php).'">'.$i.'</a>';
						
					} else {
						
						if ( end($page_links) != '...' )
							$page_links[] = '...';
						
					}
					
				} else {
					
					$page_links[] = '<strong>'.$i.'</strong>';
					
				}
				
			}
			
			$page_links = join(' ',$page_links);
			
			if ( $back_forward_links ) {
				
				if ( valid_int($page_id_val) )
					$url_vars['id'] = $page_id_val;
				
				if ( $current_page > 1 ) {
					
					$url_vars['page'] = $current_page-1;
					$page_links = '<a href="'.$this->make_url($page_name, $url_vars, true, true, $force_php).'">&lt;</a> '.$page_links;
					
				}
				if ( $current_page < $pages_number ) {
					
					$url_vars['page'] = $current_page+1;
					$page_links .= ' <a href="'.$this->make_url($page_name, $url_vars, true, true, $force_php).'">&gt;</a>';
					
				}
				if ( $current_page > 2 ) {
					
					$url_vars['page'] = 1;
					$page_links = '<a href="'.$this->make_url($page_name, $url_vars, true, true, $force_php).'">&laquo;</a> '.$page_links;
					
				}
				if ( $current_page+1 < $pages_number ) {
					
					$url_vars['page'] = $pages_number;
					$page_links .= ' <a href="'.$this->make_url($page_name, $url_vars, true, true, $force_php).'">&raquo;</a>';
					
				}
				
			}
			
			$page_links = sprintf($lang['PageLinks'], $page_links);
			
		} else {
			
			$page_links = sprintf($lang['PageLinks'], '1');
			
		}
		
		return $page_links;
		
	}
	
	/**
	 * Removes BBCode
	 *
	 * @param string $string Text string to clean
	 * @returns string Cleaned text
	 */
	function bbcode_clear($string) {
		
		$existing_tags = array('code', 'b', 'i', 'u', 's', 'img', 'url', 'mailto', 'color', 'size', 'google', 'quote');
		return preg_replace('#\[/?('.join($existing_tags, '|').')[^\]]*\]#i', '', $string);
		
	}
	
	/**
	 * Cleans up BBCode for parsing
	 *
	 * Automatically called from within ::markup.
	 *
	 * @param string $string Text string to preparse
	 * @returns string Corrected BBCoded text
	 */
	function bbcode_prepare($string) {
		
		$string = trim($string);
		$existing_tags = array('code', 'b', 'i', 'u', 's', 'img', 'url', 'mailto', 'color', 'size', 'google', 'quote');
		
		$parts = array_reverse(preg_split('#(\[/?[^\]\s]+\])#', $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY));
		
		$open_tags = $open_parameters = array();
		$new_string = '';
		
		while ( count($parts) ) {
			
			$part = array_pop($parts);
			$matches = array();
			
			//
			// Add open tag
			//
			if ( preg_match('#^\[([a-z]+)([^\]]+)?\]$#', $part, $matches) ) {
				
				//
				// Transform tags
				//
				if ( end($open_tags) == 'code' ) {
					
					$new_string .= str_replace(array('[', ']'), array('&#91;', '&#93;'), $part);
					continue;
					
				}
				
				//
				// Is already open
				//
				if ( $matches[1] != 'quote' && in_array($matches[1], $open_tags) )
					continue;
				
				//
				// Only add this if it exists
				//
				if ( in_array($matches[1], $existing_tags) ) {
					
					array_push($open_tags, $matches[1]);
					array_push($open_parameters, ( isset($matches[2]) ) ? $matches[2] : '');
					
				}
				
				$new_string .= $part;
				continue;
				
			}
			
			//
			// Add close tag
			//
			if ( preg_match('#^\[/([a-z]+)\]$#', $part, $matches) ) {
				
				//
				// Transform tags
				//
				if ( end($open_tags) == 'code' && $matches[1] != 'code' ) {
					
					$new_string .= str_replace(array('[', ']'), array('&#91;', '&#93;'), $part);
					continue;
					
				}
				
				//
				// Unexisting tag
				//
				if ( !in_array($matches[1], $existing_tags) ) {
					
					$new_string .= $part;
					continue;
					
				}
				
				//
				// Is current open tag
				//
				if ( end($open_tags) == $matches[1] ) {
					
					array_pop($open_tags);
					array_pop($open_parameters);
					
					$new_string .= $part;
					continue;
					
				}
				
				//
				// Is other open tag
				//
				if ( in_array($matches[1], $open_tags) ) {
					
					$to_reopen_tags = $to_reopen_parameters = array();
					
					while ( $open_tag = array_pop($open_tags) ) {
						
						$open_parameter = array_pop($open_parameters);
						$new_string .= '[/'.$open_tag.']';
						
						if ( $open_tag == $matches[1] )
							break;
						
						array_push($to_reopen_tags, $open_tag);
						array_push($to_reopen_parameters, $open_parameter);
						
					}
					
					$to_reopen_tags = array_reverse($to_reopen_tags);
					$to_reopen_parameters = array_reverse($to_reopen_parameters);
					
					while ( $open_tag = array_pop($to_reopen_tags) ) {
						
						$open_parameter = array_pop($to_reopen_parameters);
						
						$new_string .= '['.$open_tag.$open_parameter.']';
						array_push($open_tags, $open_tag);
						array_push($open_parameters, $open_parameter);
						
					}
					
				}
				
			} else {
				
				//
				// Plain text
				//
				$new_string .= ( end($open_tags) == 'code' && $this->get_config('show_raw_entities_in_code') ) ? str_replace('&#', '&amp;#', $part) : $part;
				
			}
			
		}
		
		//
		// Close opened tags
		//
		while ( $open_tag = array_pop($open_tags) ) {
			
			$open_parameter = array_pop($open_parameters);
			$new_string .= '[/'.$open_tag.$open_parameter.']';
			
		}
		
		//
		// Remove empties
		//
		foreach ( $existing_tags as $existing_tag )
			$new_string = preg_replace('#\[('.$existing_tag.')([^\]]+)?\]\[/(\1)\]#', '', $new_string);
		
		return $new_string;
		
	}
	
	/**
	 * Apply BBCode and smilies to a string
	 *
	 * @param string $string String to markup
	 * @param bool $bbcode Enable BBCode
	 * @param bool $smilies Enable smilies
	 * @param bool $html Enable HTML
	 * @param bool $full_path_smilies Enable full path smilies
	 * @returns string HTML
	 */
	function markup($string, $bbcode=true, $smilies=true, $html=false, $full_path_smilies=false) {
		
		global $db, $template, $lang;
		
		$string = preg_replace('#(script|about|applet|activex|chrome):#is', '\\1&#058;', $string);
		
		//
		// Needed by some BBCode regexps and smilies
		//
		$string = ' '.$string.' ';
		
		if ( !$html )
			$string = unhtml($string);
		
		if ( $smilies ) {
			
			$all_smilies = $template->get_config('smilies');
			krsort($all_smilies);
			$full_path = ( $full_path_smilies ) ? $this->get_config('board_url') : '';
			
			foreach ( $all_smilies as $pattern => $img )
				$string = preg_replace('#([^"])('.preg_quote(unhtml($pattern), '#').')([^"])#', '\\1<img src="'.$full_path.'templates/'.$this->get_config('template').'/smilies/'.$img.'" alt="'.unhtml($pattern).'" />\\3', $string);
			
			//
			// Entity + smiley fix
			//
			$string = preg_replace('#(&[a-z0-9]+)<img src="[^"]+" alt="([^"]+)" />#', '\\1\\2', $string);
			
		}
		
		if ( $bbcode ) {
			
			$string = ' '.$this->bbcode_prepare($string).' ';
			
			$rel = array();
			if ( $this->get_config('target_blank') )
				$rel[] = 'external';
			if ( $this->get_config('rel_nofollow') )
				$rel[] = 'nofollow';
			$rel = ( count($rel) ) ? ' rel="'.join($rel, ' ').'"' : '';
			
			//
			// Parse quote tags
			//
			// Might seem a bit difficultly done, but trimming doesn't work the usual way
			//
			$matches = array();
			while ( preg_match("#\[quote\](.*?)\[/quote\]#is", $string, $matches) )
				$string = preg_replace("#\[quote\]".preg_quote($matches[1], '#')."\[/quote\]#is", sprintf($template->get_config('quote_format'), $lang['Quote'], trim($matches[1])), $string);
			while ( preg_match("#\[quote=(.*?)\](.*?)\[/quote\]#is", $string, $matches) )
				$string = preg_replace("#\[quote=".preg_quote($matches[1], '#')."\]".preg_quote($matches[2], '#')."\[/quote\]#is", sprintf($template->get_config('quote_format'), sprintf($lang['Wrote'], $matches[1]), trim($matches[2])), $string);
			
			//
			// Parse code tags
			//
			preg_match_all("#\[code\](.*?)\[/code\]#is", $string, $matches);				
			foreach ( $matches[1] as $oldpart ) {
				
				$newpart = preg_replace(array('#<img src="[^"]+" alt="([^"]+)" />#', "#\n#", "#\r#"), array('\\1', '<br />', ''), $oldpart); // replace smiley image tags
				$string = str_replace('[code]'.$oldpart.'[/code]', '[code]'.$newpart.'[/code]', $string);
				
			}
			$string = preg_replace("#\[code\](.*?)\[/code\]#is", sprintf($template->get_config('code_format'), '\\1'), $string);
			
			//
			// Parse URL's and e-mail addresses enclosed in special characters
			//
			$ignore_chars = "^a-z0-9"; # warning, rawly included in regex!
			$ignore_chars_url_end = "^a-z0-9/"; # to include trailing /
			$string = preg_replace(array(
				"#([\s][".$ignore_chars."]*?)([\w]+?://[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)]*?)([".$ignore_chars_url_end."]*?[\s])#is",
				"#([\s][".$ignore_chars."]*?)(www\.[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)]*?)([".$ignore_chars_url_end."]*?[\s])#is",
				"#([\s][".$ignore_chars."]*?)([a-z0-9&\-_\.\+]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)([".$ignore_chars."]*?[\s])#is"
			), array(
				'\\1<a href="\\2" title="\\2"'.$rel.'>\\2</a>\\3',
				'\\1<a href="http://\\2" title="http://\\2">\\2</a>\\3',
				'\\1<a href="mailto:\\2" title="\\2">\\2</a>\\4'
			), $string);
			
			//
			// All kinds of BBCode regexps
			//
			$regexps = array(
				// [b]text[/b]
					"#\[b\](.*?)\[/b\]#is" => '<strong>\\1</strong>',
				// [i]text[/i]
					"#\[i\](.*?)\[/i\]#is" => '<em>\\1</em>',
				// [u]text[/u]
					"#\[u\](.*?)\[/u\]#is" => '<u>\\1</u>',
				// [s]text[/s]
					"#\[s\](.*?)\[/s\]#is" => '<del>\\1</del>',
				// [img]image[/img]
					"#\[img\]([\w]+?://[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)]*?)\[/img\]#is" => '<img src="\\1" alt="'.$lang['UserPostedImage'].'" />',
				// www.usebb.net
					"#([\s])(www\.[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)]*?)#is" => '\\1<a href="http://\\2" title="http://\\2"'.$rel.'>\\2</a>\\3',
				// ftp.usebb.net
					"#([\s])(ftp\.[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)]*?)([\s])#is" => '\\1<a href="ftp://\\2" title="ftp://\\2"'.$rel.'>\\2</a>\\3',
				// [url]http://www.usebb.net[/url]
					"#\[url\]([\w]+?://[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)]*?)\[/url\]#is" => '<a href="\\1" title="\\1"'.$rel.'>\\1</a>',
				// [url=http://www.usebb.net]UseBB[/url]
					"#\[url=([\w]+?://[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)]*?)\](.*?)\[/url\]#is" => '<a href="\\1" title="\\1"'.$rel.'>\\2</a>',
				// [mailto]somebody@nonexistent.com[/mailto]
					"#\[mailto\]([a-z0-9&\-_\.\+]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/mailto\]#is" => '<a href="mailto:\\1" title="\\1">\\1</a>',
				// [mailto=somebody@nonexistent.com]mail me[/mailto]
					"#\[mailto=([a-z0-9&\-_\.\+]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\](.*?)\[/mailto\]#is" => '<a href="mailto:\\1" title="\\1">\\3</a>',
				// [color=red]text[/color]
					"#\[color=([\#a-z0-9]+)\](.*?)\[/color\]#is" => '<span style="color:\\1">\\2</span>',
				// [size=999]too big text[/size]
					"#\[size=([0-9]{3,})\](.*?)\[/size\]#is" => '\\2',
				// [size=14]text[/size]
					"#\[size=([0-9]*?)\](.*?)\[/size\]#is" => '<span style="font-size:\\1pt">\\2</span>',
				// [google=keyword]text[/google]
					"#\[google=(.*?)\](.*?)\[/google\]#is" => '<a href="http://www.google.com/search?q=\\1"'.$rel.'>\\2</a>',
			);
			
			//
			// Now parse those regexps
			//
			foreach ( $regexps as $find => $replace )
				$string = preg_replace($find, $replace, $string);
			
		}
		
		if ( !$html ) {
			
			$string = str_replace("\n", "<br />", $string);
			$string = str_replace("\r", "", $string);
			
		}
		
		return trim($string);
		
	}
	
	/**
	 * Return the BBCode control buttons
	 *
	 * @returns string HTML BBCode controls
	 */
	function get_bbcode_controls() {
		
		global $lang, $template;
		
		$controls = array(
			array('[b]', '[/b]', 'B', 'font-weight: bold'),
			array('[i]', '[/i]', 'I', 'font-style: italic'),
			array('[u]', '[/u]', 'U', 'text-decoration: underline'),
			array('[s]', '[/s]', 'S', 'text-decoration: line-through'),
			array('[quote]', '[/quote]', $lang['Quote'], ''),
			array('[code]', '[/code]', $lang['Code'], ''),
			array('[img]', '[/img]', $lang['Img'], ''),
			array('[url=http://www.example.com]', '[/url]', $lang['URL'], ''),
			array('[color=red]', '[/color]', $lang['Color'], ''),
			array('[size=14]', '[/size]', $lang['Size'], '')
		);
		
		$out = array();
		foreach ( $controls as $data ) {
			
			$out[] = '<a href="javascript:insert_tags(\''.$data[0].'\', \''.$data[1].'\')" style="'.$data[3].'">'.$data[2].'</a>';
			
		}
		
		return join($template->get_config('post_form_bbcode_seperator'), $out);
		
	}
	
	/**
	 * Return the smiley control graphics
	 *
	 * @returns string HTML smiley controls
	 */
	function get_smiley_controls() {
		
		global $template;
		
		$smilies = $template->get_config('smilies');
		$smilies = array_unique($smilies);
		$out = array();
		foreach ( $smilies as $pattern => $img ) {
			
			$out[] = '<a href="javascript:insert_smiley(\''.addslashes(unhtml($pattern)).'\')"><img src="templates/'.$this->get_config('template').'/smilies/'.$img.'" alt="'.unhtml($pattern).'" /></a>';
			
		}
		
		return join($template->get_config('post_form_smiley_seperator'), $out);
		
	}
	
	/**
	 * Censor text
	 *
	 * @param string $string Text to censor
	 * @returns string Censored text
	 */
	function replace_badwords($string) {
		
		global $db;
		
		if ( $this->get_config('enable_badwords_filter') ) {
			
			//
			// Algorithm borrowed from phpBB
			//
			if ( !isset($this->badwords) ) {
				
				$result = $db->query("SELECT word, replacement FROM ".TABLE_PREFIX."badwords ORDER BY word ASC");
				$this->badwords = array();
				while ( $data = $db->fetch_result($result) )
					$this->badwords['#\b(' . str_replace('\*', '\w*?', preg_quote(stripslashes($data['word']), '#')) . ')\b#i'] = stripslashes($data['replacement']);
				
			}
			
			foreach ( $this->badwords as $badword => $replacement )
				$string = preg_replace($badword, $replacement, $string);
			
		}
		
		return $string;
		
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
	 * Timezone handling
	 *
	 * @param string $action 'get_zones' or 'check_existance'
	 * @param mixed $param Time zone param for 'check_existance'
	 * @returns mixed Array with timezones or bool
	 */
	function timezone_handler($action, $param=NULL) {
		
		$timezones = array(
			'-12' => '-12:00',
			'-11' => '-11:00',
			'-10' => '-10:00',
			'-9' => '-9:00',
			'-8' => '-8:00',
			'-7' => '-7:00',
			'-6' => '-6:00',
			'-5' => '-5:00',
			'-4' => '-4:00',
			'-3.5' => '-3:30',
			'-3' => '-3:00',
			'-2' => '-2:00',
			'-1' => '-1:00',
			'0' => '+0:00',
			'+1' => '+1:00',
			'+2' => '+2:00',
			'+3' => '+3:00',
			'+3.5' => '+3:30',
			'+4' => '+4:00',
			'+4.5' => '+4:30',
			'+5' => '+5:00',
			'+5.5' => '+5:30',
			'+6' => '+6:00',
			'+7' => '+7:00',
			'+8' => '+8:00',
			'+9' => '+9:00',
			'+9.5' => '+9:30',
			'+10' => '+10:00',
			'+11' => '+11:00',
			'+12' => '+12:00',
		);
		
		if ( $action == 'get_zones' ) {
			
			return $timezones;
			
		} elseif ( $action == 'check_existance' ) {
			
			if ( !empty($timezones[$param]) )
				return true;
			else
				return false;
			
		}
		
	}

	/**
	 * Make a user's profile link
	 *
	 * @param int $user_id User ID
	 * @param string $username Username
	 * @param int $level Level
	 * @returns string HTML
	 */
	function make_profile_link($user_id, $username, $level) {
		
		switch ( $level ) {
			
			case 3:
				$levelclass = ' class="administrator"';
				break;
			case 2:
				$levelclass = ' class="moderator"';
				break;
			case 1:
				$levelclass = '';
				break;
			
		}
		
		return '<a href="'.$this->make_url('profile.php', array('id' => $user_id)).'"'.$levelclass.'>'.unhtml(stripslashes($username)).'</a>';
		
	}
	
	/**
	 * Create a forum statistics box like on the forum index
	 */
	function forum_stats_box() {
		
		global $db, $template, $lang, $session;
		
		if ( $this->get_config('enable_forum_stats_box') && $this->get_user_level() >= $this->get_config('view_forum_stats_box_min_level') ) {
			
			//
			// Timestamp for defining last updated sessions
			//
			$min_updated = time() - ( $this->get_config('online_min_updated') * 60 );
			
			//
			// Get the session and user information
			//
			$result = $db->query("SELECT u.displayed_name, u.level, u.hide_from_online_list, s.user_id AS id, s.ip_addr FROM ( ".TABLE_PREFIX."sessions s LEFT JOIN ".TABLE_PREFIX."members u ON s.user_id = u.id ) WHERE s.updated > ".$min_updated." ORDER BY s.updated DESC");
			
			//
			// Arrays for holding a list of online guests and members.
			//
			$count = array(
				'total_members' => 0,
				'hidden_members' => 0,
				'guests' => 0
			);
			$list = array(
				'members' => array(),
				'guests' => array()
			);
			$memberlist = array();
			
			while ( $onlinedata = $db->fetch_result($result) ) {
				
				if ( !$onlinedata['id'] ) {
					
					//
					// This is a guest
					// Guests will only be counted per IP address
					//
					if ( !in_array($onlinedata['ip_addr'], $list['guests']) ) {
						
						$count['guests']++;
						$list['guests'][] = $onlinedata['ip_addr'];
						
					}
					
				} else {
					
					//
					// This is a member
					//
					if ( !in_array($onlinedata['id'], $list['members']) ) {
						
						if ( !$onlinedata['hide_from_online_list'] ) {
							
							$memberlist[] = $this->make_profile_link($onlinedata['id'], $onlinedata['displayed_name'], $onlinedata['level']);
							
						} else {
							
							if ( $this->get_user_level() == LEVEL_ADMIN )
								$memberlist[] = '<em>'.$this->make_profile_link($onlinedata['id'], $onlinedata['displayed_name'], $onlinedata['level']).'</em>';
							
							$count['hidden_members']++;
							
						}
						
						$count['total_members']++;
						$list['members'][] = $onlinedata['id'];
						
					}
					
				}
				
			}
			
			$latest_member = $this->get_stats('latest_member');
			
			if ( $count['total_members'] === 1 && $count['guests'] === 1 )
				$users_online = $lang['MemberGuestOnline'];
			elseif ( $count['total_members'] !== 1 && $count['guests'] === 1 )
				$users_online = $lang['MembersGuestOnline'];
			elseif ( $count['total_members'] === 1 && $count['guests'] !== 1 )
				$users_online = $lang['MemberGuestsOnline'];
			else
				$users_online = $lang['MembersGuestsOnline'];
			
			//
			// Parse the online box
			//
			$template->parse('forum_stats_box', 'various', array(
				'small_stats' => sprintf($lang['IndexStats'], $this->get_stats('posts'), $this->get_stats('topics'), $this->get_stats('members')),
				'newest_member' => ( !$this->get_stats('members') ) ? '' : ' '.sprintf($lang['NewestMemberExtended'], '<a href="'.$this->make_url('profile.php', array('id' => $latest_member['id'])).'">'.unhtml(stripslashes($latest_member['displayed_name'])).'</a>'),
				'users_online' => sprintf($users_online, $this->get_config('online_min_updated'), $count['total_members'], $count['hidden_members'], $count['guests']),
				'members_online' => ( count($memberlist) ) ? join(', ', $memberlist) : '',
				'detailed_list_link' => ( $this->get_config('enable_detailed_online_list') && $this->get_user_level() >= $this->get_config('view_detailed_online_list_min_level') ) ? '<a href="'.$this->make_url('online.php').'">'.$lang['Detailed'].'</a>' : ''
			));
			
		}
		
	}
	
	/**
	 * Get the server's load avarage value
	 *
	 * @param integer $which What load variable to call ('all' for an array of all)
	 * @returns float Server load average
	 */
	function get_server_load($which=1) {
		
		//
		// Afaik, this does not exist at Windows
		//
		if ( strstr(PHP_OS, 'WIN') !== false )
			return false;
		
		//
		// Load has not been requested yet
		//
		if ( is_null($this->server_load) ) {
			
			$found_load = false;
			
			//
			// First attempt: reading /proc/loadavg
			//
			$file = '/proc/loadavg';
			if ( file_exists($file) && is_readable($file) ) {
				
				$fh = fopen($file, 'r');
				
				if ( is_resource($fh) ) {
					
					$out = fread($fh, 1024);
					fclose($fh);
					
					if ( preg_match('#([0-9]+\.[0-9]{2}) ([0-9]+\.[0-9]{2}) ([0-9]+\.[0-9]{2})#', $out, $match) ) {
						
						$this->server_load = array(
							(float)$match[1],
							(float)$match[2],
							(float)$match[3]
						);
						$found_load = true;
						
					}
					
				}
				
			}
			
			if ( !$found_load ) {
				
				//
				// Second attempt: executing uptime
				//
				$tmp = array();
				$retval = 1;
				$out = @exec('uptime', $tmp, $retval);
				unset($tmp);
				
				if ( !$retval ) {
					
					if ( preg_match('#([0-9]+\.[0-9]{2}), ([0-9]+\.[0-9]{2}), ([0-9]+\.[0-9]{2})#', $out, $match) ) {
						
						$this->server_load = array(
							(float)$match[1],
							(float)$match[2],
							(float)$match[3]
						);
						
					} else {
						
						$this->server_load = false;
						
					}
					
				} else {
					
					$this->server_load = false;
					
				}
				
			}
			
		}
		
		if ( !$this->server_load )
			return false;
		elseif ( $which == 'all' )
			return $this->server_load;
		elseif ( is_int($which) )
			return $this->server_load[$which-1];
		
	}
	
	/**
	 * Define the icon for forums
	 *
	 * @param int $id Forum ID
	 * @param bool $open Open (or locked)
	 * @param int $post_time Unix timestamp of update
	 * @returns array Array with forum icon and status
	 */
	function forum_icon($id, $open, $post_time) {
		
		global $db, $session, $template, $lang;
		
		if ( $session->sess_info['user_id'] && !empty($_SESSION['previous_visit']) && !is_array($this->updated_forums) ) {
			
			$result = $db->query("SELECT t.id, t.forum_id, p.post_time FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p WHERE p.id = t.last_post_id AND p.post_time > ".$_SESSION['previous_visit']);
			$this->updated_forums = array();
			while ( $topicsdata = $db->fetch_result($result) ) {
				
				if ( !in_array($topicsdata['forum_id'], $this->updated_forums) && ( !array_key_exists('t'.$topicsdata['id'], $_SESSION['viewed_topics']) || $_SESSION['viewed_topics']['t'.$topicsdata['id']] < $topicsdata['post_time'] ) )
					$this->updated_forums[] = $topicsdata['forum_id'];
				
			}
			
		}
		
		if ( $session->sess_info['user_id'] && !empty($_SESSION['previous_visit']) && in_array($id, $this->updated_forums) ) {
			
			if ( $open ) {
				
				$forum_icon = $template->get_config('open_newposts_icon');
				$forum_status = $lang['NewPosts'];
				
			} else {
				
				$forum_icon = $template->get_config('closed_newposts_icon');
				$forum_status = $lang['LockedNewPosts'];
				
			}
			
		} else {
			
			if ( $open ) {
				
				$forum_icon = $template->get_config('open_nonewposts_icon');
				$forum_status = $lang['NoNewPosts'];
				
			} else {
				
				$forum_icon = $template->get_config('closed_nonewposts_icon');
				$forum_status = $lang['LockedNoNewPosts'];
				
			}
			
		}
		
		return array($forum_icon, $forum_status);
		
	}
	
	/**
	 * Define the icon for topics
	 *
	 * @param int $id Topic ID
	 * @param bool $locked Locked (or open)
	 * @param int $post_time Unix timestamp of update
	 * @returns array Array with topic icon and status
	 */
	function topic_icon($id, $locked, $post_time) {
		
		global $session, $template, $lang;
		
		if ( $session->sess_info['user_id'] && !empty($_SESSION['previous_visit']) && $_SESSION['previous_visit'] < $post_time && ( !array_key_exists('t'.$id, $_SESSION['viewed_topics']) || $_SESSION['viewed_topics']['t'.$id] < $post_time ) ) {
			
			if ( !$locked ) {
				
				$topic_icon = $template->get_config('open_newposts_icon');
				$topic_status = $lang['NewPosts'];
				
			} else {
				
				$topic_icon = $template->get_config('closed_newposts_icon');
				$topic_status = $lang['LockedNewPosts'];
				
			}
			
		} else {
			
			if ( !$locked ) {
				
				$topic_icon = $template->get_config('open_nonewposts_icon');
				$topic_status = $lang['NoNewPosts'];
				
			} else {
				
				$topic_icon = $template->get_config('closed_nonewposts_icon');
				$topic_status = $lang['LockedNoNewPosts'];
				
			}
			
		}
		
		return array($topic_icon, $topic_status);
		
	}
	
	/**
	 * Return birthday input fields
	 *
	 * @param string $input Input birthday field
	 * @returns array Input fields
	 */
	function birthday_input_fields($input) {
		
		global $lang;
		$months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			$birthday_year = $_POST['birthday_year'];
			$birthday_month = $_POST['birthday_month'];
			$birthday_day = $_POST['birthday_day'];
			
		} else {
			
			$birthday = $input;
			$birthday_year = ( $birthday ) ? intval(substr($birthday, 0, 4)) : '';
			$birthday_month = ( $birthday ) ? intval(substr($birthday, 4, 2)) : 0;
			$birthday_day = ( $birthday ) ? intval(substr($birthday, 6, 2)) : 0;
			
		}
		$birthday_month_input = '<select name="birthday_month"><option value="">'.$lang['Month'].'</option>';
		for ( $i = 1; $i <= 12; $i++ ) {
			
			$selected = ( $birthday_month == $i ) ? ' selected="selected"' : '';
			$month_name = ( array_key_exists('date_translations', $lang) && is_array($lang['date_translations']) ) ? $lang['date_translations'][$months[$i-1]] : $months[$i-1];
			$birthday_month_input .= '<option value="'.$i.'"'.$selected.'>'.$month_name.'</option>';
			
		}
		$birthday_month_input .= '</select>';
		$birthday_day_input = '<select name="birthday_day"><option value="">'.$lang['Day'].'</option>';
		for ( $i = 1; $i <= 31; $i++ ) {
			
			$selected = ( $birthday_day == $i ) ? ' selected="selected"' : '';
			$birthday_day_input .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
			
		}
		$birthday_day_input .= '</select>';
		$birthday_year_input = '<select name="birthday_year"><option value="">'.$lang['Year'].'</option>';
		for ( $i = intval(date('Y')); $i >= 1900; $i-- ) {
			
			$selected = ( $birthday_year == $i ) ? ' selected="selected"' : '';
			$birthday_year_input .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
			
		}
		$birthday_year_input .= '</select>';
		
		return array($birthday_year_input, $birthday_month_input, $birthday_day_input);
		
	}
	
	/**
	 * Calculate the age of a person based on a birthday date
	 *
	 * @param int $birthday Unix timestamp
	 * @returns int Age
	 */
	function calculate_age($birthday) {
		
		$month = intval(substr($birthday, 4, 2));
		$day = intval(substr($birthday, 6, 2));
		$year = intval(substr($birthday, 0, 4));
		
		//
		// Because Windows doesn't allow dates before 1970 with mktime(),
		// we perform a trick to calculate dates before 1970.
		//
		if ( $year < 1970 ) {
			
			$years_before_unix_epoch = 1970 - $year;
			$false_year = $year + ( $years_before_unix_epoch * 2 );
			$timestamp = mktime(0, 0, 0, $month, $day, $false_year);
			$timestamp -= ( $years_before_unix_epoch * 31556926 * 2 );
			
		} else {
			
			$timestamp = mktime(0, 0, 0, $month, $day, $year);
			
		}
		
		return floor((time()-$timestamp)/31556926);
		
	}
	
	/**
	 * Get a list of template sets
	 *
	 * @returns array List of available template sets
	 */
	function get_template_sets() {
		
		if ( !count($this->available['templates']) ) {
			
			$handle = opendir(ROOT_PATH.'templates');
			while ( false !== ( $template_name = readdir($handle) ) ) {
				
				if ( is_dir(ROOT_PATH.'templates/'.$template_name) && is_readable(ROOT_PATH.'templates/'.$template_name) && ( $this->get_user_level() == LEVEL_ADMIN || preg_match('#^[^\.]#', $template_name) ) && file_exists(ROOT_PATH.'templates/'.$template_name.'/global.tpl.php') )
					$this->available['templates'][] = $template_name;
				
			}
			closedir($handle);
			sort($this->available['templates']);
			reset($this->available['templates']);
			
		}
		
		return $this->available['templates'];
		
	}
	
	/**
	 * Get a list of language packs
	 *
	 * @returns array List of available language packs
	 */
	function get_language_packs() {
		
		if ( !count($this->available['languages']) ) {
			
			$handle = opendir(ROOT_PATH.'languages');
			while ( false !== ( $language_name = readdir($handle) ) ) {
				
				if ( preg_match('#^lang_(.+)\.php$#', $language_name, $language_name) )
					$this->available['languages'][] = $language_name[1];
				
			}
			closedir($handle);
			sort($this->available['languages']);
			reset($this->available['languages']);
			
		}
		
		return $this->available['languages'];
		
	}
	
	/**
	 * Return the sql tables with the table prefix
	 *
	 * @returns array List of SQL tables with UseBB table prefix
	 */
	function get_usebb_tables() {
		
		global $db;
		
		if ( !count($this->db_tables) ) {
			
			$result = $db->query("SHOW TABLES LIKE '".TABLE_PREFIX."%'");
			while ( $out = $db->fetch_result($result) )
				$this->db_tables[] = current($out);
			
		}
		
		return $this->db_tables;
		
	}
	
	/**
	 * Redirect the user to a certain location within UseBB
	 *
	 * @param string $page .php file to link to
	 * @param array $vars Array with GET variables
	 * @param string $anchor HTML anchor
	 */
	function redirect($page, $vars=array(), $anchor='') {
		
		$goto = $this->get_config('board_url').$this->make_url($page, $vars, false);
		if ( !empty($anchor) )
			$goto .= '#'.$anchor;
		$this->raw_redirect($goto);
		
	}
	
	/**
	 * Redirect with a predefined URL
	 *
	 * @param string $url URL
	 */
	function raw_redirect($url) {
		
		//
		// Don't use Location on IIS
		//
		if ( !preg_match('#Microsoft\-IIS#', $_SERVER['SERVER_SOFTWARE']) )
			@header('Location: '.$url);
		die('<meta http-equiv="refresh" content="0;URL='.$url.'" />');
		
	}
	
	/**
	 * Validate an email address
	 *
	 * @param string $email_address Email address
	 * @returns bool Valid
	 */
	function validate_email($email_address) {
		
		if ( !preg_match(EMAIL_PREG, $email_address) )
			return false;
		
		if ( $this->get_config('enable_email_dns_check') ) {
			
			$parts = explode('@', $email_address);
			$on_windows = ( strstr(PHP_OS, 'WIN') !== false );
			
			if ( function_exists('checkdnsrr') && !$on_windows ) {
				
				return checkdnsrr($parts[1], 'MX');
				
			} elseif ( $on_windows ) {
				
				return checkdnsrr_win($parts[1], 'MX');
				
			}
			
		}
		
		return true;
		
	}
	
	/**
	 * Set a cookie
	 *
	 * This function takes care of past expire values for empty cookies, and
	 * uses the httpOnly flag as of PHP 5.2.0RC2.
	 *
	 * @param string $name Name
	 * @param string $value Value
	 * @param int $expires Expire timestamp (when necessary)
	 */
	function setcookie($name, $value, $expires=null) {
		
		$expires = ( is_null($expires) && empty($value) ) ? time()-31536000 : $expires;
		$secure = ( $this->get_config('cookie_secure') ) ? 1 : 0;
		
		//
		// Use httpOnly flag as of PHP 5.2.0RC2
		//
		if ( version_compare(phpversion(), '5.2.0RC2', '>=') )
			setcookie($name, $value, $expires, $this->get_config('cookie_path'), $this->get_config('cookie_domain'), $secure, true);
		else
			setcookie($name, $value, $expires, $this->get_config('cookie_path'), $this->get_config('cookie_domain'), $secure);
		
	}
	
}

?>
