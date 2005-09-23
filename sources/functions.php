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
 * Functions
 *
 * Contains all kinds of procedural functions and the functions class.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2005 UseBB Team
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
	
	return preg_replace(array('#&#', '#&amp;\#([0-9]+)#', '#&\#(160|173|8192|8193|8194|8195|8196|8197|8198|8199|8120|8201|8202|8203|8204|8205|8206|8207)#', '#<#', '#>#', '#"#'), array('&amp;', '&#\\1', '&amp;#\\1', '&lt;', '&gt;', '&quot;'), $string);
	
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
 * Functions
 *
 * All kinds of functions used everywhere.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2005 UseBB Team
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
	/**#@-*/
	
	/**
	 * @access private
	 */
	function usebb_die($errno, $error, $file, $line) {
		
		global $db, $dbs;
		
		//
		// Don't show various errors on PHP5
		//
		if ( intval(substr(phpversion(), 0, 1)) > 4 ) {
			
			$ignore_warnings = array(
				'var: Deprecated. Please use the public/private/protected modifiers',
				'Trying to get property of non-object',
			);
			if ( in_array($error, $ignore_warnings) )
				return;
			
		}
		
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
			1024 => 'E_USER_NOTICE',
			2048 => 'E_STRICT'
		);
		$errtype = ( preg_match('#^SQL: #', $error) ) ? 'SQL_ERROR' : $errtypes[$errno];
		
		if ( $errtype == 'SQL_ERROR' )
			$error = ( $this->get_config('debug') >= 2 ) ? substr($error, 5) : 'Fatal SQL error!';
		
		error_log('[UseBB Error] ['.date('D M d G:i:s Y').'] ['.$errtype.' - '.preg_replace('#(\s+|\s)#', ' ', $error).'] ['.$file.':'.$line.']');
		
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
				color: #336699;
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
		<p>An error was encoutered. We apologize for any inconvenience.</p>
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
			<p>SQL query causing the error (<strong>sensitive information</strong>):<br /><textarea rows="10" cols="60" readonly="readonly">'.unhtml(end($used_queries)).'</textarea></p>';
					
					if ( preg_match("#^Table '.+' doesn't exist#", $error) )
						$html_msg .= '
			<p><strong>Note:</strong> It seems like there are missing tables. Did you already install UseBB properly? See the docs/INSTALL document. Also check the table prefix set in config.php.</p>';
					
				} elseif ( is_array($this->board_config) ) {
					
					$html_msg .= '
			<p>Enable debug mode level 2 to see the error and erroneous SQL query.</p>';
					
				}
				
			}
			
		} else {
			
			if ( $this->get_config('debug') >= 2 ) {
				
				ob_start();
				print_r(debug_backtrace());
				$backtrace = ob_get_contents();
				ob_end_clean();
				$html_msg .= '
			<p>Backtrace (<strong>sensitive information</strong>):<br /><textarea rows="10" cols="60" readonly="readonly">'.unhtml($backtrace).'</textarea></p>';
				
			} elseif ( is_array($this->board_config) ) {
				
				$html_msg .= '
			<p>Enable debug mode level 2 to see a backtrace.</p>';
				
			}
			
		}
		
		$html_msg .= '
		</blockquote>
		<p>This error should probably not have occured, so please report it to the webmaster. Thank you for your help.</p>
		<p>If you are the webmaster of this board and you believe this is a bug, please send a bug report.</p>
	</body>
</html>';
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
		
		if ( $original ) {
			
			//
			// Return unedited config
			//
			if ( array_key_exists($setting, $this->board_config_original) )
				return $this->board_config_original[$setting];
			else
				return '';
			
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
				
				if ( !in_array($setting, array('board_url', 'cookie_path', 'hide_undefined_config_setting_warnings')) && isset($this->board_config['hide_undefined_config_setting_warnings']) && !$this->board_config['hide_undefined_config_setting_warnings'] ) {
					
					//
					// Trigger error when a config value wasn't found and
					// hide_undefined_config_setting_warnings is explicitly false.
					//
					trigger_error('Unable to get config setting "'.$setting.'"!'."\n\n".'To disable these warnings on a non-development board, set the config setting "hide_undefined_config_setting_warnings" to 1.');
					
				} elseif ( $setting == 'board_url' ) {
					
					//
					// Automatically find the board URL
					//
					$path_parts = pathinfo($_SERVER['SCRIPT_NAME']);
					if ( !preg_match('#/$#', $path_parts['dirname']) )
						$path_parts['dirname'] .= '/';
					$protocol = ( isset($_SERVER['HTTPS']) ) ? 'https' : 'http';
					$set_to = $protocol.'://'.$_SERVER['HTTP_HOST'].$path_parts['dirname'];
					
				} elseif ( $setting == 'cookie_path' ) {
					
					//
					// Automatically find the board path
					//
					$path_parts = pathinfo($_SERVER['SCRIPT_NAME']);
					$set_to = $path_parts['dirname'];
					
				} elseif ( $setting == 'search_limit_results' || $setting == 'sig_max_length' ) {
					
					//
					// Set these to 1000 when the value is missing from the config
					//
					$set_to = 1000;
					
				} elseif ( $setting == 'search_nonindex_words_min_length' ) {
					
					//
					// Set this to 3 when the value is missing from the config
					//
					$set_to = 3;
					
				} else {
					
					//
					// Set all other missing settings to false
					//
					$set_to = false;
					
				}
				
				//
				// Set the new value
				//
				$this->board_config[$setting] = $set_to;
				
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
				
				$result = $db->query("SELECT id, displayed_name, regdate FROM ".TABLE_PREFIX."members ORDER BY id DESC LIMIT 1");
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
				trigger_error('The statistic variable "'.$stat.'" does not exist!');
			
		}
		
	}
	
	/**
	 * Interactive URL builder
	 *
	 * @param string $filename .php filename to link to
	 * @param array $vars GET variabeles
	 * @param bool $html Return HTML URL
	 * @param bool $enable_sid Enable session ID's
	 * @returns string URL
	 */
	function make_url($filename, $vars=array(), $html=true, $enable_sid=true) {
		
		global $session;
		
		if ( $this->get_config('friendly_urls') && $filename != 'admin.php' ) {
			
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
			
			$_SERVER['HTTP_USER_AGENT'] = ( !empty($_SERVER['HTTP_USER_AGENT']) ) ? $_SERVER['HTTP_USER_AGENT'] : '';
			
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
						trigger_error('Section "'.$section.'" for language pack "'.$language.'" could not be found. No English fallback was available. Please use an updated language pack or also upload the English one.');
					
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
				
				if ( function_exists('mb_internal_encoding') )
					mb_internal_encoding($lang['character_encoding']);
				
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
		
		$format = ( !empty($format) ) ? $format : $this->get_config('date_format');
		
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
		$length = 10;
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
		
		//
		// Eventually use the right language and character encoding which may be passed
		// in the parameters when another language is used (e.g. subscription notices)
		//
		$language = ( !empty($language) ) ? $language : $this->get_config('language');
		$charset = ( !empty($charset) ) ? $charset : $lang['character_encoding'];
		
		//
		// Set the correct mb_language when neccessary (only for Japanese, English or UTF-8)
		//
		if ( function_exists('mb_language') ) {
			
			if ( in_array($language, array('Japanese', 'ja', 'English', 'en')) )
				mb_language($language);
			elseif ( strtolower($charset) == 'utf-8' )
				mb_language('uni');
			else
				mb_language('en');
			
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
		
		if ( function_exists('mb_encode_mimeheader') ) {
			
			$from_name = mb_encode_mimeheader($from_name);
			$subject = mb_encode_mimeheader($subject);
			
		}
		
		$headers[] = 'MIME-Version: 1.0';
		if ( !empty($bcc_email) )
			$headers[] = 'Bcc: '.$bcc_email;
		$headers[] = 'Date: '.date('r');
		$headers[] = 'X-Mailer: UseBB/'.USEBB_VERSION;
		$headers[] = 'From: '.$from_name.' <'.$from_email.'>';
		
		if ( function_exists('mb_send_mail') ) {
			
			if ( !mb_send_mail($to, $subject, $body, join($cr, $headers)) )
				trigger_error('Unable to send e-mail!');
			
		} else {
			
			if ( strtolower($charset) == 'utf-8' )
				$headers[] = 'Content-Transfer-Encoding: 8bit';
			
			if ( !mail($to, $subject, $body, join($cr, $headers)) )
				trigger_error('Unable to send e-mail!');
			
		}
		
		//
		// Reset to board's default language
		//
		$board_default_lang = $this->get_config('language');
		if ( $language != $board_default_lang && function_exists('mb_language') ) {
			
			if ( in_array($board_default_lang, array('Japanese', 'ja', 'English', 'en')) )
				mb_language($board_default_lang);
			elseif ( strtolower($board_default_lang) == 'utf-8' )
				mb_language('uni');
			else
				mb_language('en');
			mb_internal_encoding($lang['character_encoding']);
			
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
		setcookie($this->get_config('session_name').'_al', serialize($content), time()+31536000, $this->get_config('cookie_path'), $this->get_config('cookie_domain'), $this->get_config('cookie_secure'));
		
	}
	
	/**
	 * Unset the remember cookie
	 */
	function unset_al() {
		
		setcookie($this->get_config('session_name').'_al', '', time()-31536000, $this->get_config('cookie_path'), $this->get_config('cookie_domain'), $this->get_config('cookie_secure'));
		
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
			trigger_error('You first need to call $session->update() before you can get any session info.');
		
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
	 * Return a list of moderators, clickable and seperated with commas
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
	 * @returns string HTML
	 */
	function make_page_links($pages_number, $current_page, $items_number, $items_per_page, $page_name, $page_id_val=NULL, $back_forward_links=true, $url_vars=array()) {
		
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
						$page_links[] = '<a href="'.$this->make_url($page_name, $url_vars).'">'.$i.'</a>';
						
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
					$page_links = '<a href="'.$this->make_url($page_name, $url_vars).'">&lt;</a> '.$page_links;
					
				}
				if ( $current_page < $pages_number ) {
					
					$url_vars['page'] = $current_page+1;
					$page_links .= ' <a href="'.$this->make_url($page_name, $url_vars).'">&gt;</a>';
					
				}
				if ( $current_page > 2 ) {
					
					$url_vars['page'] = 1;
					$page_links = '<a href="'.$this->make_url($page_name, $url_vars).'">&laquo;</a> '.$page_links;
					
				}
				if ( $current_page+1 < $pages_number ) {
					
					$url_vars['page'] = $pages_number;
					$page_links .= ' <a href="'.$this->make_url($page_name, $url_vars).'">&raquo;</a>';
					
				}
				
			}
			
			$page_links = sprintf($lang['PageLinks'], $page_links);
			
		} else {
			
			$page_links = sprintf($lang['PageLinks'], '1');
			
		}
		
		return $page_links;
		
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
			
		}
		
		if ( $bbcode ) {
			
			$target_blank = ( $this->get_config('target_blank') ) ? ' target="_blank"' : '';
			$rel_nofollow = ( $this->get_config('rel_nofollow') ) ? ' rel="nofollow"' : '';
			
			//
			// Difficult parsing of code tags
			//
			if ( preg_match('#\[code\](.*?)\[/code\]#is', $string) ) {
				
				$string_parts = preg_split('#(\[code\])#is', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
				$new_string_parts = array();
				foreach ( $string_parts as $string_part ) {
					
					if ( preg_match_all('#(\[/code\])#is', $string_part, $matches) ) {
						
						$end_tags_count = count($matches[0]);
						$string_parts2 = preg_split('#(\[/code\])#is', $string_part, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
						$i = 1;
						$string_part = '';
						foreach ( $string_parts2 as $string_part2 ) {
							
							if ( preg_match('#\[/code\]#is', $string_part2) ) {
								
								if ( $i < $end_tags_count ) {
									
									$string_part .= preg_replace('#<img src="(.*?)" alt="(.*?)" />#', '\\2', preg_replace(array('#\[#', '#\]#'), array('&#91;', '&#93;'), $string_part2));
									
								} else {
									
									$string_part .= $string_part2;
									
								}
								$i++;
								
							} else {
								
								if ( $i === $end_tags_count ) {
									
									$string_part .= preg_replace('#<img src="(.*?)" alt="(.*?)" />#', '\\2', preg_replace(array('#\[#', '#\]#'), array('&#91;', '&#93;'), $string_part2));
									
								} else {
									
									$string_part .= $string_part2;
									
								}
								
							}
							
						}
						
					}
					$new_string_parts[] = $string_part;
					
				}
				$string = join('', $new_string_parts);
				preg_match_all("#\[code\](.*?)\[/code\]#is", $string, $matches);				
				foreach ( $matches[1] as $oldpart ) {
					
					$newpart = preg_replace(array('#\[#', '#\]#', "#\n#", "#\r#"), array('&#91;', '&#93;', '<br />', ''), $oldpart);
					$string = str_replace($oldpart, $newpart, $string);
					
				}
				$string = preg_replace("#\[code\](.*?)\[/code\]#is", sprintf($template->get_config('code_format'), '\\1'), $string);
				
			}
			
			//
			// Parse URL's and e-mail addresses
			//
			$ignore_chars = "^a-z0-9"; # warning, rawly included in regex!
			$ignore_chars_url_end = "^a-z0-9/"; # to include trailing /
			$string = preg_replace(array(
				"#([\s][".$ignore_chars."]*?)([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)([".$ignore_chars_url_end."]*?[\s])#is",
				"#([\s][".$ignore_chars."]*?)([a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)([".$ignore_chars."]*?[\s])#is"
			), array(
				'\\1<a href="\\2"'.$target_blank.$rel_nofollow.'>\\2</a>\\3',
				'\\1<a href="mailto:\\2">\\2</a>\\4'
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
					"#\[img\]([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)\.(gif|png|jpe?g)\[/img\]#is" => '<img src="\\1.\\2" alt="'.$lang['UserPostedImage'].'" />',
				// [url]http://www.usebb.net[/url]
					"#\[url\]([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)\[/url\]#is" => '<a href="\\1"'.$target_blank.$rel_nofollow.'>\\1</a>',
				// [url=http://www.usebb.net]UseBB[/url]
					"#\[url=([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)\](.*?)\[/url\]#is" => '<a href="\\1"'.$target_blank.$rel_nofollow.'>\\2</a>',
				// [mailto]somebody@nonexistent.com[/mailto]
					"#\[mailto\]([a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/mailto\]#is" => '<a href="mailto:\\1">\\1</a>',
				// [mailto=somebody@nonexistent.com]mail me[/mailto]
					"#\[mailto=([a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\](.*?)\[/mailto\]#is" => '<a href="mailto:\\1">\\3</a>',
				// [color=red]text[/color]
					"#\[color=([\#a-z0-9]+)\](.*?)\[/color\]#is" => '<span style="color:\\1">\\2</span>',
				// [size=999]too big text[/size]
					"#\[size=([0-9]{3,})\](.*?)\[/size\]#is" => '\\2',
				// [size=14]text[/size]
					"#\[size=([0-9]*?)\](.*?)\[/size\]#is" => '<span style="font-size:\\1pt">\\2</span>',
				// [google=keyword]text[/google]
					"#\[google=(.*?)\](.*?)\[/google\]#is" => '<a href="http://www.google.com/search?q=\\1"'.$target_blank.$rel_nofollow.'>\\2</a>',
			);
			
			//
			// Now parse those regexps
			//
			foreach ( $regexps as $find => $replace )
				$string = preg_replace($find, $replace, $string);
			
			//
			// Now parse quote tags
			//
			while ( preg_match("#\[quote(=.*?)?\](\s.*?\s)\[/quote\]#is", $string, $matches) )
				$string = preg_replace('#'.preg_quote($matches[2], '#').'#is', trim($matches[2]), $string);
			while ( preg_match("#\[quote\](.*?)\[/quote\]#is", $string) )
				$string = preg_replace("#\[quote\](.*?)\[/quote\]#is", sprintf($template->get_config('quote_format'), $lang['Quote'], '\\1'), $string);
			while ( preg_match("#\[quote=(.*?)\](.*?)\[/quote\]#is", $string) )
				$string = preg_replace("#\[quote=(.*?)\](.*?)\[/quote\]#is", sprintf($template->get_config('quote_format'), sprintf($lang['Wrote'], '\\1'), '\\2'), $string);
			
		}
		
		if ( !$html ) {
			
			$string = str_replace("\n", "<br />\n", $string);
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
		
		if ( !isset($this->badwords) ) {
			
			$result = $db->query("SELECT word, replacement FROM ".TABLE_PREFIX."badwords ORDER BY word ASC");
			$this->badwords = array();
			while ( $data = $db->fetch_result($result) )
				$this->badwords['#\b(' . str_replace('\*', '\w*?', preg_quote(stripslashes($data['word']), '#')) . ')\b#i'] = stripslashes($data['replacement']);
			
		}
		
		foreach ( $this->badwords as $badword => $replacement )
			$string = preg_replace($badword, $replacement, $string);
		
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
	 * @returns float Server load average
	 */
	function get_server_load() {
		
		$found_load = false;
		
		if ( strstr(PHP_OS, 'WIN') !== false ) {
			
			//
			// Afaik we can't get this on Windows
			//
			return false;
			
		} else {
			
			if ( file_exists('/proc/loadavg') && is_readable('/proc/loadavg') ) {
				
				//
				// We use the Linux method of getting the 3 average load
				// values of the server. This only works on Linux afaik...
				//
				$fh = fopen('/proc/loadavg', 'r');
				$out = fread($fh, 14);
				fclose($fh);
				if ( preg_match('#([0-9]+\.[0-9]{2}) ([0-9]+\.[0-9]{2}) ([0-9]+\.[0-9]{2})#', $out, $match) )
					return (float)$match[1]; // we use the load average value of the past 1 minute
				else
					$found_load = false;
				
			}
			
			if ( !$found_load ) {
				
				//
				// Another way is running the uptime command and using its
				// output. This should also work on FreeBSD. The var $tmp
				// is unnecessary at this moment.
				//
				$tmp = array();
				$out = @exec('uptime', $tmp, $retval);
				unset($tmp);
				
				if ( !$retval ) {
					
					//
					// $retval contains the exit code 0 when ran successfully...
					//
					if ( preg_match('#([0-9]+\.[0-9]{2}), ([0-9]+\.[0-9]{2}), ([0-9]+\.[0-9]{2})#', $out, $match) )
						return (float)$match[1]; // we use the load average value of the past 1 minute
					else
						return false;
					
				} else {
					
					//
					// We can't determine the server load... The server can't access
					// /proc/loadavg, can't run uptime or is running an unsupported OS
					//
					return false;
					
				}
				
			}
			
		}
		
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
		
		if ( $session->sess_info['user_id'] && !is_array($this->updated_forums) ) {
			
			$result = $db->query("SELECT t.id, t.forum_id, p.post_time FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p WHERE p.id = t.last_post_id AND p.post_time > ".$_SESSION['previous_visit']);
			$this->updated_forums = array();
			while ( $topicsdata = $db->fetch_result($result) ) {
				
				if ( !in_array($topicsdata['forum_id'], $this->updated_forums) && ( !array_key_exists($topicsdata['id'], $_SESSION['viewed_topics']) || $_SESSION['viewed_topics'][$topicsdata['id']] < $topicsdata['post_time'] ) )
					$this->updated_forums[] = $topicsdata['forum_id'];
				
			}
			
		}
		
		if ( $session->sess_info['user_id'] && in_array($id, $this->updated_forums) ) {
			
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
		
		if ( $session->sess_info['user_id'] && $_SESSION['previous_visit'] < $post_time && ( !array_key_exists($id, $_SESSION['viewed_topics']) || $_SESSION['viewed_topics'][$id] < $post_time ) ) {
			
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
				
				if ( preg_match('#^[^\.]#', $template_name) && file_exists('./templates/'.$template_name.'/global.tpl.php') )
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
		
		$tables = array();
		$result = $db->query("SHOW TABLES LIKE '".TABLE_PREFIX."%'");
		while ( $out = $db->fetch_result($result) )
			$tables[] = current($out);
		return $tables;
		
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
		
		@header('Location: '.$url);
		die('<meta http-equiv="refresh" content="0;URL='.$url.'" />');
		
	}
	
}

?>
