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
// Various functions
//
class functions {
	
	var $board_config;
	var $statistics;
	var $enabled_templates;
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
					
				} else {
					
					$global[$key] = addslashes($val);
					
				}
				
			}
			
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
					
				} else {
					
					$global[$key] = trim($val);
					
				}
				
			}
			
			return $global;
			
		}
		
	}
	
	//
	// General error die function
	//
	function usebb_die($errno, $error, $file, $line) {
		
		global $db, $dbs;
		
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
		
		if ( $errtype == 'SQL' )
			$line--;
		
		$log_msg = '[UseBB Error] ['.date('D M d G:i:s Y').'] ['.$errtype.' - '.$error.'] ['.$file.':'.$line.']';
		error_log($log_msg);
		
		$html_msg  = '<html>
	<head>
		<title>UseBB General Error</title>
		<style type="text/css">
			body {
				font-family: sans-serif;
				font-size: 10pt;
			}
			h1 {
				color: #336699;
			}
			blockquote {
				width: 50%;
				border-top: 2px solid silver;
				border-bottom: 2px solid silver;
				font-family: monospace;
				font-size: 8pt;
			}
		</style>
	</head>
	<body>
		<h1>UseBB General Error</h1>
		<p>An error was encoutered. We apologize for any inconvenience.</p>
		<blockquote>
			<p>In file <strong>'.substr(str_replace(dirname($file), '', $file), 1).'</strong> on line <strong>'.$line.'</strong>:</p><p><em>'.$errtype.'</em> - '.$error.'</p>';
		if ( $errtype == 'SQL' ) {
			
			if ( $this->get_config('debug') )
				$html_msg .= '
			<p>SQL query causing the error:<br /><textarea rows="5" cols="50" readonly="readonly">'.end($db->get_used_queries()).'</textarea></p>';
			else 
				$html_msg .= '
			<p>Enable debug mode to see the erroneous SQL query.</p>';
			
		}
		$html_msg .= '
		</blockquote>
		<p>This error should probably not have occured, so please report it to the webmaster. Thank you for your help.</p>
		<p>If you are the webmaster of this board and you believe this is a bug, please send a bug report.</p>
	</body>
</html>';
		die($html_msg);
		
	}
	
	//
	// Get configuration variables
	//
	function get_config($setting) {
		
		global $session, $conf;
		
		if ( !isset($this->board_config) ) {
			
			$this->board_config = $conf;
			unset($conf);
			
		}
		
		//
		// Member preferences
		//
		if ( is_array($session->sess_info) && $session->sess_info['user_id'] && isset($session->sess_info['user_info'][$setting]) ) {
			
			$keep_default = FALSE;
			
			if ( $setting == 'language' || $setting == 'template' ) {
				
				if ( !in_array($session->sess_info['user_info'][$setting], $this->board_config['available_'.$setting.'s']) )
					$keep_default = TRUE;
				
			}
			
			if ( !$keep_default )
				$this->board_config[$setting] = stripslashes($session->sess_info['user_info'][$setting]);
			
		}
		
		if ( isset($this->board_config[$setting]) )
			return $this->board_config[$setting];
		else
			return '';
		
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
					
					if ( !($result = $db->query("SELECT id, name FROM ".TABLE_PREFIX."members ORDER BY id DESC LIMIT 1")) )
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
		
		if ( $this->get_config('friendly_urls') && in_array($filename, array('index.php', 'panel.php', 'faq.php', 'search.php', 'active.php', 'forum.php', 'topic.php', 'profile.php', 'post.php', 'edit.php')) ) {
			
			$url = str_replace('.php', '', $filename);
			foreach ( $vars as $key => $val ) {
				
				if ( in_array($key, array('forum', 'topic', 'post', 'quotepost', 'al')) )
					$url .= '-'.$key.$val;
				else
					$url .= '-'.$val;
				
			}
			return $url.'.html';
			
		}
		
		$url = $filename;
		
		//
		// Session IDs will be passed in the URL if
		//		- no cookies are accepted
		// AND
		//		- PHP isn't configured to pass SIDs by default
		//		OR
		//		- the URL is not in HTML
		// This allows users not using browsers supporting cookies
		// to stay logged in or use the same session ID.
		//
		$SID = SID;
		if ( !empty($SID) && ( !$html || ( $html && !@ini_get('session.use_trans_sid') ) ) ) {
			
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
		if ( !empty($SID) && !preg_match('/'.preg_quote($SID, '/').'$/', $url) ) {
			
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
			header('Location: '.$this->get_config('board_url').$this->make_url('panel.php', array('act' => 'login'), false));
			
		} else {
			
			$template->set_page_title($lang['Note']);
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Note'],
				'content' => $lang['NotPermitted']
			));
			
		}
		
	}
	
	//
	// Generate a date given a timestamp
	//
	function make_date($stamp) {
		
		return gmdate($this->get_config('date_format'), $stamp + (3600 * $this->get_config('timezone')) + (3600 * $this->get_config('dst')));
		
	}
	
	//
	// Generate a time past string
	//
	function time_past($timestamp, $until='') {
		
		global $lang;
		
		$until = ( is_int($until) ) ? $until : time();
		
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
			$string_parts[] = $val.' '.$lang[ucfirst($key)];
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
		
		$body = str_replace("\n", "\r\n", str_replace("\r\n", "\n", $rawbody));
		$bodyvars['board_name'] = $this->get_config('board_name');
		$bodyvars['board_link'] = $this->get_config('board_url');
		$bodyvars['admin_email'] = $this->get_config('admin_email');
		
		foreach ( $bodyvars as $key => $val )
			$body = str_replace('['.$key.']', $val, $body);
		
		if ( !mail($to, $subject, $body, 'From: '.$from_name.' <'.$from_email.'>'."\r\n".'X-Mailer: UseBB/'.USEBB_VERSION) )
			$this->usebb_die('Mail', 'Unable to send e-mail!', __FILE__, __LINE__);
		
	}
	
	//
	// Set the remember cookie
	//
	function set_al($userid, $passwdhash) {
		
		setcookie($this->get_config('session_name').'_al', $userid.':'.$passwdhash, time()+31536000, $this->get_config('cookie_path'), $this->get_config('cookie_domain'), $this->get_config('cookie_secure'));
		
	}
	
	//
	// Unset the remember cookie
	//
	function unset_al() {
		
		setcookie($this->get_config('session_name').'_al', '', time()-31536000, $this->get_config('cookie_path'), $this->get_config('cookie_domain'), $this->get_config('cookie_secure'));
		
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
		
		if ( $session->sess_info['ip_banned'] || ( $this->get_config('board_closed') && $this->get_user_level() < 3 ) )
			return FALSE;
		
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
			
			if ( !$this->get_config('guests_can_access_board') )
				return FALSE;
			else
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
	// Return a list of moderators, clickable and seperated with commas
	//
	function get_mods_list($forum, $listarray=false) {
		
		global $db, $lang;
		
		$forum_moderators = array();
		
		if ( is_array($listarray) && count($listarray) ) {
			
			foreach ( $listarray as $modsdata ) {
				
				if ( $modsdata['forum_id'] == $forum )
					$forum_moderators[] = $this->make_profile_link($modsdata['id'], $modsdata['name'], $modsdata['level']);
				
			}
			
			if ( !count($forum_moderators) ) {
				
				return $lang['Nobody'];
				
			}
			
		} else {
			
			if ( !($result = $db->query("SELECT u.id, u.name, u.level FROM ".TABLE_PREFIX."members u, ".TABLE_PREFIX."moderators m WHERE m.forum_id = ".$forum." AND m.user_id = u.id ORDER BY u.name")) )
				$this->usebb_die('SQL', 'Unable to get forum moderators list!', __FILE__, __LINE__);
				
			if ( !$db->num_rows($result) ) {
				
				return $lang['Nobody'];
				
			} else {
				
				while ( $modsdata = $db->fetch_result($result) )
					$forum_moderators[] = $this->make_profile_link($modsdata['id'], $modsdata['name'], $modsdata['level']);
				
			}
			
		}
		
		//
		// Join all values in the array
		//
		return join(', ', $forum_moderators);
		
	}
	
	//
	// Return a clickable list of pages
	//
	function make_page_links($pages_number, $current_page, $items_number, $items_per_page, $page_name, $page_id_val, $back_forward_links=TRUE) {
		
		global $lang;
		
		if ( intval($items_number) > intval($items_per_page) ) {
			
			$page_links = array();
			
			for ( $i = 1; $i <= $pages_number; $i++ ) {
				
				if ( $current_page != $i )
					$page_links[] = '<a href="'.$this->make_url($page_name, array('id' => $page_id_val, 'page' => $i)).'">'.$i.'</a>';
				else
					$page_links[] = '['.$i.']';
				
			}
			
			$page_links = join(' ',$page_links);
			
			if ( $current_page > 1 && $back_forward_links )
				$page_links = '<a href="'.$this->make_url($page_name, array('id' => $page_id_val, 'page' => $current_page-1)).'">&lt;</a> '.$page_links;
			
			if ( $current_page < $pages_number && $back_forward_links )
				$page_links .= ' <a href="'.$this->make_url($page_name, array('id' => $page_id_val, 'page' => $current_page+1)).'">&gt;</a>';
			
			$page_links = sprintf($lang['PageLinks'], $page_links);
			
		} else {
			
			$page_links = sprintf($lang['PageLinks'], '1');
			
		}
		
		return $page_links;
		
	}
	
	//
	// Apply BBCode and smilies to a string
	//
	function markup($string, $bbcode=true, $smilies=true, $html=false) {
		
		global $template, $lang;
		
		if ( !$html )
			$string = htmlspecialchars($string);
		
		if ( $bbcode ) {
			
			$target_blank = ( $this->get_config('target_blank') ) ? ' target="_blank"' : '';
			
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
								
								if ( $i < $end_tags_count )
									$string_part .= preg_replace(array('#\[#', '#\]#'), array('&#91;', '&#93;'), $string_part2);
								else
									$string_part .= $string_part2;
								$i++;
								
							} else {
								
								if ( $i === $end_tags_count )
									$string_part .= preg_replace(array('#\[#', '#\]#'), array('&#91;', '&#93;'), $string_part2);
								else
									$string_part .= $string_part2;
								
							}
							
						}
						
					}
					$new_string_parts[] = $string_part;
					
				}
				$string = join('', $new_string_parts);
				preg_match_all("#\[code\](.*?)\[/code\]#is", $string, $matches);				
				foreach ( $matches[1] as $oldpart ) {
					
					$newpart = preg_replace(array('#\[#', '#\]#'), array('&#91;', '&#93;'), $oldpart);
					$string = str_replace($oldpart, $newpart, $string);
					
				}
				$string = preg_replace("#\[code\](.*?)\[/code\]#is", sprintf($template->get_config('code_format'), '\\1'), $string);
				
			}
			
			//
			// Needed by some BBCode regexps
			//
			$string = ' '.$string.' ';
			
			//
			// All kinds of regexps
			//
			$regexps = array(
				// [b]text[/b]
					"#\[b\](.*?)\[/b\]#is" => '<strong>\\1</strong>',
				// [i]text[/i]
					"#\[i\](.*?)\[/i\]#is" => '<em>\\1</em>',
				// [u]text[/u]
					"#\[u\](.*?)\[/u\]#is" => '<em class="underline">\\1</em>',
				// [img]image[/img]
					"#\[img\]([\w]+?://[^ \"\n\r\t<]*?)\.(gif|png|jpe?g)\[/img\]#is" => '<img src="\\1.\\2" alt="'.$lang['UserPostedImage'].'" />',
				// [url]http://www.usebb.net[/url]
					"#\[url\]([\w]+?://[^ \"\n\r\t<]*?)\[/url\]#is" => '<a href="\\1"'.$target_blank.'>\\1</a>',
				// [url=http://www.usebb.net]UseBB[/url]
					"#\[url=([\w]+?://[^ \"\n\r\t<]*?)\](.*?)\[/url\]#is" => '<a href="\\1"'.$target_blank.'>\\2</a>',
				// http://www.usebb.net
					"#([\s\]\[])([\w]+?://[^ \"\n\r\t<]*?)([\s\]\[])#is" => '\\1<a href="\\2"'.$target_blank.'>\\2</a>\\3',
				// [mailto]somebody@nonexistent.com[/mailto]
					"#\[mailto\]([a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/mailto\]#is" => '<a href="mailto:\\1">\\1</a>',
				// [mailto=somebody@nonexistent.com]mail me[/mailto]
					"#\[mailto=([a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\](.*?)\[/mailto\]#is" => '<a href="mailto:\\1">\\3</a>',
				// somebody@nonexistent.com
					"#([\s\]\[])([a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)([\s\]\[])#is" => '\\1<a href="mailto:\\2">\\2</a>\\4',
				// [color=red]text[/color]
					"#\[color=(.*?)\](.*?)\[/color\]#is" => '<span style="color:\\1">\\2</span>',
				// [size=14]text[/size]
					"#\[size=(.*?)\](.*?)\[/size\]#is" => '<span style="font-size:\\1pt">\\2</span>',
				// [google=keyword]text[/google]
					"#\[google=(.*?)\](.*?)\[/google\]#is" => '<a href="http://www.google.com/search?q=\\1"'.$target_blank.'>\\2</a>',
			);
			
			//
			// Now parse those regexps
			//
			foreach ( $regexps as $find => $replace )
				$string = preg_replace($find, $replace, $string);
			
			//
			// Now parse quote tags
			//
			while ( preg_match("#\[quote\](.*?)\[/quote\]#is", $string) )
				$string = preg_replace("#\[quote\](.*?)\[/quote\]#is", sprintf($template->get_config('quote_format'), $lang['Quote'], '\\1'), $string);
			while ( preg_match("#\[quote=(.*?)\](.*?)\[/quote\]#is", $string) )
				$string = preg_replace("#\[quote=(.*?)\](.*?)\[/quote\]#is", sprintf($template->get_config('quote_format'), sprintf($lang['Wrote'], '\\1'), '\\2'), $string);
			
			$string = trim($string);
			
		}
		
		if ( !$html ) {
			
			$string = str_replace("\n", "<br />", $string);
			$string = str_replace("\r", "", $string);
			
		}
		
		return $string;
		
	}
	
	//
	// Replace all whitespace by a space except in <textarea /> and <pre />
	//
	function compress_sourcecode($string) {
		
		$matches = array();
		preg_match_all("#<textarea.*?>(.*?)</textarea>#is", $string, $matches[0]);
		preg_match_all("#<pre.*?>(.*?)</pre>#is", $string, $matches[1]);
		$matches = array_merge($matches[0][0], $matches[1][0]);
		foreach ( $matches as $oldpart ) {
			
			$newpart = str_replace("\n", "\0", $oldpart);
			$string = str_replace($oldpart, $newpart, $string);
			
		}
		$string = str_replace("\r", "", $string);
		$string = preg_replace("#\s+#", ' ', $string);
		$string = str_replace("\0", "\n", $string);
		return $string;
		
	}
	
	//
	// Timezone handling
	//
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
		
		return '<a href="'.$this->make_url('profile.php', array('id' => $user_id)).'"'.$levelclass.'>'.htmlspecialchars(stripslashes($username)).'</a>';
		
	}
	
	//
	// Create a forum statistics box like on the forum index
	//
	function forum_stats_box() {
		
		global $db, $template, $lang;
		
		//
		// Timestamp for defining last updated sessions
		//
		$min_updated = time() - ( $this->get_config('online_min_updated') * 60 );
		
		//
		// Get the session and user information
		//
		if ( !($result = $db->query("SELECT u.name, u.level, u.hide_from_online_list, s.user_id AS id, s.ip_addr FROM ( ".TABLE_PREFIX."sessions s LEFT JOIN ".TABLE_PREFIX."members u ON s.user_id = u.id ) WHERE s.updated > ".$min_updated." ORDER BY s.updated DESC")) )
			$this->usebb_die('SQL', 'Unable to get online members information!', __FILE__, __LINE__);
		
		//
		// Arrays for holding a list of online guests and members.
		//
		$online_guests = $online_members = array();
		
		while ( $onlinedata = $db->fetch_result($result) ) {
			
			if ( !$onlinedata['id'] ) {
				
				//
				// This is a guest
				// Guests will only be counted per IP address
				//
				
				if ( !in_array($onlinedata['ip_addr'], $online_guests) )
					$online_guests[] = $onlinedata['ip_addr'];
				
			} else {
				
				//
				// This is a member
				//
				
				if ( !array_key_exists($onlinedata['id'], $online_members) && ( !$onlinedata['hide_from_online_list'] || $this->get_user_level() == 3 ) )
					$online_members[$onlinedata['id']] = $this->make_profile_link($onlinedata['id'], $onlinedata['name'], $onlinedata['level']);
				
			}
			
		}
		
		//
		// Online list
		//
		if ( !$this->get_config('enable_detailed_online_list') || ( !$this->get_config('guests_can_view_online_list') && $session->sess_info['user_id'] == 0 ) )
			$online_list_link = '';
		else
			$online_list_link = '<a href="'.$this->make_url('online.php').'">'.$lang['DetailedOnlineList'].'</a>';
		
		//
		// Members online
		//
		if ( count($online_members) ) {
			
			$members_online = join(', ', $online_members);
			if ( !empty($online_list_link) )
				$members_online = $online_list_link.' '.$template->get_config('item_delimiter').' '.$members_online;
			
		} else {
			
			$members_online = '';
			if ( !empty($online_list_link) )
				$members_online .= $online_list_link;
			
		}
		
		//
		// Parse the online box
		//
		$template->parse('forum_stats_box', 'various', array(
			'stats_title' => $lang['Statistics'],
			'small_stats' => sprintf($lang['IndexStats'], $this->get_stats('posts'), $this->get_stats('topics'), $this->get_stats('members')),
			'newest_member' => ( !$this->get_stats('members') ) ? '' : ' '.sprintf($lang['NewestMember'], '<a href="'.$this->make_url('profile.php', array('id' => current($this->get_stats('latest_member')))).'">'.htmlentities(stripslashes(next($this->get_stats('latest_member')))).'</a>'),
			'online_title' => $lang['OnlineUsers'],
			'users_online' => sprintf($lang['OnlineUsers'], count($online_members), count($online_guests), $this->get_config('online_min_updated')),
			'members_online' => $members_online
		));
		
	}
	
	//
	// Get the server's load avarage value
	//
	function get_server_load() {
		
		if ( file_exists('/proc/loadavg') && is_readable('/proc/loadavg') ) {
			
			//
			// We use the Linux method of getting the 3 average load
			// values of the server. This only works on Linux afaik...
			//
			$fh = fopen('/proc/loadavg', 'r');
			$out = fread($fh, 14);
			fclose($fh);
			$out = explode(' ', $out);
			return $out[0]; // we use the load average value of the past 1 minute
			
		} else {
			
			//
			// Another way is running the uptime command and using its
			// output. This should also work on FreeBSD. The var $tmp
			// is unnecessary at this moment.
			//
			$out = exec('uptime', $tmp, $retval);
			unset($tmp);
			
			if ( !$retval ) {
				
				//
				// $retval contains the exit code 0 when run successfully...
				//
				$out = explode(' ', str_replace(',', '', substr($out, -16)));
				return $out[0]; // we use the load average value of the past 1 minute
				
			} else {
				
				//
				// We can't determine the server load... The server can't access
				// /proc/loadavg, can't run uptime or is running an unsupported OS
				//
				return false;
				
			}
			
		}
		
	}
	
	//
	// Define the icon for forums and topics
	//	
	function forum_topic_icon($status, $open_status, $post_time, $poster_id, $item_type, $item_id) {
		
		global $session, $template, $lang;
		
		if ( $session->sess_info['user_id'] && $session->sess_info['user_id'] != $poster_id && $_SESSION['previous_visit'] < $post_time && $_SESSION['previous_visit'] > 0 && ( !array_key_exists($item_type.':'.$item_id, $_SESSION['viewed_items']) || $_SESSION['viewed_items'][$item_type.':'.$item_id] < $post_time ) ) {
			
			if ( $status == $open_status ) {
				
				$forum_icon = $template->get_config('open_newposts_icon');
				$forum_status = $lang['NewPosts'];
				
			} else {
				
				$forum_icon = $template->get_config('closed_newposts_icon');
				$forum_status = $lang['LockedNewPosts'];
				
			}
			
		} else {
			
			if ( $status == $open_status ) {
				
				$forum_icon = $template->get_config('open_nonewposts_icon');
				$forum_status = $lang['NoNewPosts'];
				
			} else {
				
				$forum_icon = $template->get_config('closed_nonewposts_icon');
				$forum_status = $lang['LockedNoNewPosts'];
				
			}
			
		}
		
		return array($forum_icon, $forum_status);
		
	}
	
}

?>
