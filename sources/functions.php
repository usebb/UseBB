<?php

/*
	Copyright (C) 2003-2004 UseBB Team
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
// Various functions
//
class functions {
	
	var $board_config;
	var $statistics;
	var $enabled_templates;
	var $avail_languages;
	var $mod_auth;
	
	//
	// Add slashes to a global variable
	//
	function slashes_to_global($global) {
		
		if ( is_array($global) ) {
			
			foreach ( $global as $key => $val ) {
				
				if ( is_array($global[$key]) ) {
					
					foreach ( $global[$key] as $key1 => $val1 )
						$global[$key][$key1] = addslashes($val1);
					@reset($global[$key]);
					
				} else {
					
					$global[$key] = addslashes($val);
					
				}
				
			}
			
			@reset($global);
			return $global;
			
		}
		
	}
	
	//
	// Remove spaces before and after variables
	//
	function trim_global($global) {
		
		if ( is_array($global) ) {
			
			foreach ( $global as $key => $val ) {
				
				if ( is_array($global[$key]) ) {
					
					foreach ( $global[$key] as $key1 => $val1 )
						$global[$key][$key1] = trim($val1);
					@reset($global[$key]);
					
				} else {
					
					$global[$key] = trim($val);
					
				}
				
			}
			
			@reset($global);
			return $global;
			
		}
		
	}
	
	//
	// General error die function
	//
	function usebb_die($errno, $error, $file, $line) {
		
		global $db;
		
		$log_msg = '[UseBB Error]['.$errno.']['.$error.']['.$file.':'.$line.']';
		error_log($log_msg);
		
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
			2047 => 'E_ALL'
		);
		$errtype = ( is_numeric($errno) ) ? $errtypes[$errno] : $errno;
		
		$html_msg  = '<html><head><title>UseBB General Error</title></head><body><h1>UseBB General Error</h1><blockquote><code>';
		$html_msg .= 'In file '.$file.' on line '.$line.':<br /><br />'.$errtype.' - '.$error;
		if ( $errtype == 'SQL' )
			$html_msg .= '<br /><br />Query causing the error:<br />'.end($db->queries);
		$html_msg .= '</code></blockquote><p>We are sorry for the inconvenience.</p><hr />';
		$html_msg .= '<address><a href="http://www.usebb.net">UseBB</a> '.USEBB_VERSION.' running on '.preg_replace('/<\/?address>/i', '', $_SERVER['SERVER_SIGNATURE']).'</address></body></html>';
		die($html_msg);
		
	}
	
	//
	// Get configuration variables
	//
	function get_config($setting) {
		
		global $db, $session;
		
		if ( !isset($this->board_config) ) {
			
			$this->board_config = array();
			
			if ( !($result = $db->query("SELECT name, content FROM ".TABLE_PREFIX."config")) )
				$this->usebb_die('SQL', 'Unable to get forum configuration!', __FILE__, __LINE__);
			while ( $out = $db->fetch_result($result) )
				$this->board_config[$out['name']] = stripslashes($out['content']);
			
		}
		
		//
		// Member preferences
		//
		if ( $session->sess_info['user_id'] && isset($session->sess_info['user_info'][$setting]) )
			$this->board_config[$setting] = stripslashes($session->sess_info['user_info'][$setting]);
		
		if ( isset($this->board_config[$setting]) )
			return $this->board_config[$setting];
		else
			$this->usebb_die('General', 'The configuration variable "'.$setting.'" does not exist!', __FILE__, __LINE__);
		
	}
	
	//
	// Get board statistics
	//
	function get_stats($stat) {
		
		global $db;
		
		if ( in_array($stat, array('latest_member')) ) {
			
			if ( $stat == 'latest_member' ) {
				
				//
				// Get the latest member
				//
				if ( !isset($this->statistics[$stat]) ) {
					
					if ( !($result = $db->query("SELECT id, name FROM ".TABLE_PREFIX."users ORDER BY id DESC LIMIT 1")) )
						$this->usebb_die('SQL', 'Unable to get latest member information!', __FILE__, __LINE__);
					$this->statistics[$stat] = $db->fetch_result($result);
					
				}
				
				return $this->statistics[$stat];
				
			}
			
		} else {
			
			if ( !isset($this->statistics) ) {
				
				$this->statistics = array();
				
				if ( !($result = $db->query("SELECT name, content FROM ".TABLE_PREFIX."stats")) )
					$this->usebb_die('SQL', 'Unable to get forum statistics!', __FILE__, __LINE__);
				while ( $out = $db->fetch_result($result) )
					$this->statistics[$out['name']] = $out['content'];
				
			}
			
			if ( isset($this->statistics[$stat]) )
				return $this->statistics[$stat];
			else
				$this->usebb_die('General', 'The statistic variable "'.$stat.'" does not exist!', __FILE__, __LINE__);
			
		}
		
	}
	
	//
	// Interactive URL builder
	//
	function make_url($filename, $vars=array(), $html=true) {
		
		$url = $filename;
		
		$SID = SID;
		if ( !empty($SID) ) {
			
			if ( !is_array($vars) )
				$vars = array();
			
			$SID = explode('=', $SID, 2);
			$vars[$SID[0]] = $SID[1];
			
		}
		
		if ( is_array($vars) && count($vars) >= 1 ) {
			
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
		return $url;
		
	}
	
	//
	// Attaches a SID to URLs which should contain one (e.g. referer URLs)
	//
	function attach_sid($url) {
		
		$SID = SID;
		if ( !empty($SID) && !preg_match("/".str_replace('/', '\/', $SID)."$/", $url) ) {
			
			if ( strpos($url, '?') )
				return $url.'&'.$SID;
			else
				return $url.'?'.$SID;
			
		} else {
			
			return $url;
			
		}
		
	}
	
	//
	// Kick a user to the login form
	//
	function redir_to_login() {
		
		global $session, $template, $lang;
		
		if ( !$session->sess_info['user_id'] ) {
			
			$_SESSION['referer'] = $_SERVER['REQUEST_URI'];
			header('Location: '.$this->make_url('panel.php', array('act' => 'login'), false));
			
		} else {
			
			$template->set_page_title($lang['Note']);
			$template->parse('msgbox', array(
				'box_title' => $lang['Note'],
				'content' => $lang['NotPermitted']
			));
			
		}
		
	}
	
	//
	// Generate a date given a timestamp
	//
	function make_date($stamp) {
		
		return date($this->get_config('date_format'), $stamp - ( (float)$this->get_config('timezone') * 3600 ) - ( intval($this->get_config('dst')) * 3600 ));
		
	}
	
	//
	// Generate a time past string
	//
	function time_past($timestamp, $until='') {
		
		global $lang;
		
		$until = ( is_int($until) ) ? $until : gmmktime();
		
		$times = array();
		$seconds = $until - $timestamp;
		
		// weeks
		if ( $seconds >= 604800 ) {
			
			$times['weeks'] = floor($seconds / 604800);
			$seconds = $seconds % 604800;
			
		}
		
		// days
		if ( $seconds >= 86400 ) {
			
			$times['days'] = floor($seconds / 86400);
			$seconds = $seconds % 86400;
			
		}
		
		// hours
		if ( $seconds >= 3600 ) {
			
			$times['hours'] = floor($seconds / 3600);
			$seconds = $seconds % 3600;
			
		}
		
		// minutes
		if ( $seconds >= 60 ) {
			
			$times['minutes'] = floor($seconds / 60);
			$seconds = $seconds % 60;
			
		}
		
		// seconds
		if ( $seconds > 0 ) {
			
			$times['seconds'] = $seconds;
			
		}
		
		$string_parts = array();
		foreach ( $times as $key => $val )
			$string_parts[] = $val.' '.strtolower($lang[ucfirst($key)]);
		$string = join(', ', $string_parts);
		
		return array($times, $string);
		
	}
	
	//
	// Generate an e-mail link
	//
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
	
	//
	// Generate a random key
	//
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
	
	//
	// Send an email
	//
	function usebb_mail($subject, $rawbody, $bodyvars=array(), $from_name, $from_email, $to) {
		
		if ( !is_array($bodyvars) )
			$bodyvars = array();
		
		$body = $rawbody;
		$bodyvars['board_name'] = $this->get_config('board_name');
		$bodyvars['board_link'] = $this->get_config('board_url');
		$bodyvars['admin_email'] = $this->get_config('admin_email');
		
		foreach ( $bodyvars as $key => $val )
			$body = str_replace('['.$key.']', $val, $body);
		
		if ( !mail($to, $subject, $body, 'From: '.$from_name.' <'.$from_email.'>'."\r\n".'X-Mailer: UseBB '.USEBB_VERSION) )
			$this->usebb_die('Mail', 'Unable to send e-mail!', __FILE__, __LINE__);
		
	}
	
	//
	// Set the remember cookie
	//
	function set_al($userid, $passwdhash) {
		
		setcookie($this->get_config('session_name').'_al', $userid.':'.$passwdhash, gmmktime()+31536000, $this->get_config('cookie_path'), $this->get_config('cookie_domain'), $this->get_config('cookie_secure'));
		
	}
	
	//
	// Unset the remember cookie
	//
	function unset_al() {
		
		setcookie($this->get_config('session_name').'_al', '', gmmktime()-31536000, $this->get_config('cookie_path'), $this->get_config('cookie_domain'), $this->get_config('cookie_secure'));
		
	}
	
	//
	// Is the remember cookie set?
	//
	function isset_al() {
		
		if ( isset($_COOKIE[$this->get_config('session_name').'_al']) )
			return TRUE;
		else
			return FALSE;
		
	}
	
	//
	// Get the remember cookie's value
	//
	function get_al() {
		
		$data = explode(':', $_COOKIE[$this->get_config('session_name').'_al'], 2);
		return $data;
		
	}
	
	//
	// Get the user's level
	//
	function get_user_level() {
		
		global $session;
		
		if ( $session->sess_info['user_id'] )
			return $session->sess_info['user_info']['level'];
		else
			return 0;
		
	}
	
	//
	// Authorization function
	// Defines whether a user has permission to take a certain action.
	//
	function auth($authint, $action, $forumid=0) {
		
		global $session, $db;
		
		//
		// Define the user level
		//
		if ( $session->sess_info['user_id'] ) {
			
			if ( $this->get_user_level() == 2 ) {
				
				if ( !is_array($this->mod_auth) ) {
					
					if ( !($result = $db->query("SELECT forum_id FROM ".TABLE_PREFIX."moderators WHERE user_id = ".$session->sess_info['user_id'])) )
						$this->usebb_die('SQL', 'Unable to define moderator status!', __FILE__, __LINE__);
					$this->mod_auth = array();
					while ( $out = $db->fetch_result($result) )
						$this->mod_auth[] = intval($out['forum_id']);
					
				}
				
				if ( in_array($forumid, $this->mod_auth) )
					$userlevel = 2;
				else
					$userlevel = 1;
				
			} else {
				
				$userlevel = $this->get_user_level();
				
			}
			
		} else {
			
			$userlevel = 0;
			
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
		$min_level = intval($authint[$actions[$action]]);
		
		//
		// If the user level is equal or greater than the
		// auth integer, return a TRUE, otherwise return a FALSE.
		//
		if ( $userlevel >= $min_level )
			return TRUE;
		else
			return FALSE;
		
	}
	
	//
	// Apply BBCode and smilies to a string
	//
	function markup($string, $bbcode=true, $smilies=true, $html=false) {
		
		global $template, $lang;
		
		if ( !$html )
			$string = htmlentities($string);
		
		if ( $bbcode ) {
			
			//
			// Needed by some BBCode parsers
			//
			$string = ' '.$string.' ';
			
			// [b]text[/b]
				$string = preg_replace("#\[b\](.*?)\[/b\]#is", '<b>\\1</b>', $string);
			// [i]text[/i]
				$string = preg_replace("#\[i\](.*?)\[/i\]#is", '<i>\\1</i>', $string);
			// [u]text[/u]
				$string = preg_replace("#\[u\](.*?)\[/u\]#is", '<u>\\1</u>', $string);
			// [img]image[/img]
				$string = preg_replace("#\[img\](https?|ftp)://([a-z0-9\-]+)\.([a-z0-9\-]+)(\.[a-z0-9\-]+)*/([^\s]+/)*([^\s]+)\.(gif|png|jpe?g)\[/img\]#is", '<img src="\\1://\\2.\\3\\4/\\5\\6.\\7" alt="'.$lang['UserPostedImage'].'" />', $string);
			// [url]http://www.usebb.net[/url]
				$string = preg_replace("#\[url\]([a-z]{3}[a-z]*)://([a-z0-9\-]+)\.([a-z0-9\-]+)(\.[a-z0-9\-]+)*([^\s\[\]]+)*\[/url\]#is", '<a href="\\1://\\2.\\3\\4\\5" target="_blank">\\1://\\2.\\3\\4\\5</a>', $string);
			// [url=http://www.usebb.net]UseBB[/url]
				$string = preg_replace("#\[url=([a-z]{3}[a-z]*)://([a-z0-9\-]+)\.([a-z0-9\-]+)(\.[a-z0-9\-]+)*([^\s\[\]]+)*\](.*?)\[/url\]#is", '<a href="\\1://\\2.\\3\\4\\5" target="_blank">\\6</a>', $string);
			// http://www.usebb.net
				$string = preg_replace("#\s([a-z]{3}[a-z]*)://([a-z0-9\-]+)\.([a-z0-9\-]+)(\.[a-z0-9\-]+)*([^\s\[\]]+)*\s#is", ' <a href="\\1://\\2.\\3\\4\\5" target="_blank">\\1://\\2.\\3\\4\\5</a> ', $string);
			// [mailto]somebody@nonexistent.com[/mailto]
				$string = preg_replace("#\[mailto\]([a-z0-9\.\-_]+)@([a-z0-9\-]+)(\.[a-z0-9\-]+)*\.([a-z]+)\[/mailto\]#is", '<a href="mailto:\\1@\\2\\3.\\4">\\1@\\2\\3.\\4</a>', $string);
			// [mailto=somebody@nonexistent.com]mail me[/mailto]
				$string = preg_replace("#\[mailto=([a-z0-9\.\-_]+)@([a-z0-9\-]+)(\.[a-z0-9\-]+)*\.([a-z]+)\](.*?)\[/mailto\]#is", '<a href="mailto:\\1@\\2\\3.\\4">\\5</a>', $string);
			// somebody@nonexistent.com
				$string = preg_replace("#\s([a-z0-9\.\-_]+)@([a-z0-9\-]+)(\.[a-z0-9\-]+)*\.([a-z]+)\s#is", ' <a href="mailto:\\1@\\2\\3.\\4">\\1@\\2\\3.\\4</a> ', $string);
			echo '<pre>'.$string.'</pre><hr />
			';
			// [color=red]text[/color]
				$string = preg_replace("#\[color=(.*?)\](.*?)\[/color\]#is", '<font color="\\1">\\2</font>', $string);
			
			$string = substr($string, 1, strlen($string)-1);
			
		}
		
		if ( !$html )
			$string = nl2br($string);
		
		return $string;
		
	}
	
	//
	// Get all enabled templates
	//
	function get_enabled_templates() {
		
		global $db;
		
		if ( !is_array($this->enabled_templates) ) {
			
			if ( !($result = $db->query("SELECT c1.content AS fullname, c1.template AS shortname FROM ".TABLE_PREFIX."templates_config c1, ".TABLE_PREFIX."templates_config c2 WHERE c1.template = c2.template AND c1.name = 'template_name' AND c2.name = 'is_enabled' AND c2.content = '1' ORDER BY c1.content ASC")) )
				$this->usebb_die('SQL', 'Could not query enabled templates!', __FILE__, __LINE__);
			
			if ( $db->num_rows($result) ) {
				
				$this->enabled_templates = array();
				while ( $data = $db->fetch_result($result) ) {
					
					$this->enabled_templates[] = array(
						'fullname' => stripslashes($data['fullname']),
						'shortname' => stripslashes($data['shortname'])
					);
					
				}
				
			} else {
				
				$this->usebb_die('General', 'No enabled templates found!', __FILE__, __LINE__);
				
			}
			
		}
		
		return $this->enabled_templates;
		
	}
	
	//
	// Get all available languages
	//
	function get_avail_languages() {
		
		global $db;
		
		if ( !is_array($this->avail_languages) ) {
			
			if ( !($result = $db->query("SELECT language FROM ".TABLE_PREFIX."language ORDER BY language ASC")) )
				$this->usebb_die('SQL', 'Could not query available languages!', __FILE__, __LINE__);
			
			if ( $db->num_rows($result) ) {
				
				$this->avail_languages = array();
				while ( $data = $db->fetch_result($result) ) {
					
					if ( !in_array($data['language'], $this->avail_languages) )
						$this->avail_languages[] = $data['language'];
					
				}
				
			} else {
				
				$this->usebb_die('General', 'No available languages found!', __FILE__, __LINE__);
				
			}
			
		}
		
		return $this->avail_languages;
		
	}
	
	//
	// Timezone handling
	//
	function timezone_handler($action, $param=NULL) {
		
		$timezones = array(
			'-12' => 'GMT -12:00',
			'-11' => 'GMT -11:00',
			'-10' => 'GMT -10:00',
			'-9' => 'GMT -9:00',
			'-8' => 'GMT -8:00',
			'-7' => 'GMT -7:00',
			'-6' => 'GMT -6:00',
			'-5' => 'GMT -5:00',
			'-4' => 'GMT -4:00',
			'-3.5' => 'GMT -3:30',
			'-3' => 'GMT -3:00',
			'-2' => 'GMT -2:00',
			'-1' => 'GMT -1:00',
			'0' => 'GMT',
			'+1' => 'GMT +1:00',
			'+2' => 'GMT +2:00',
			'+3' => 'GMT +3:00',
			'+3.5' => 'GMT +3:30',
			'+4' => 'GMT +4:00',
			'+4.5' => 'GMT +4:30',
			'+5' => 'GMT +5:00',
			'+5.5' => 'GMT +5:30',
			'+6' => 'GMT +6:00',
			'+7' => 'GMT +7:00',
			'+8' => 'GMT +8:00',
			'+9' => 'GMT +9:00',
			'+9.5' => 'GMT +9:30',
			'+10' => 'GMT +10:00',
			'+11' => 'GMT +11:00',
			'+12' => 'GMT +12:00',
		);
		
		if ( $action == 'get_zones' ) {
			
			return $timezones;
			
		} elseif ( $action == 'check_existance' ) {
			
			if ( !empty($timezones[$param]) )
				return TRUE;
			else
				return FALSE;
			
		}
		
	}

	//
	// Make a user's profile link
	//
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
		
		return '<a href="'.$this->make_url('profile.php', array('id' => $user_id)).'"'.$levelclass.'>'.$username.'</a>';
		
	}
	
}

?>
