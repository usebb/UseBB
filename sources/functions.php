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
// Various functions
//
class functions {
	
	var $board_config;
	
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
		$html_msg .= '</code></blockquote><p>We are sorry for the inconvenience.</p><hr />';
		$html_msg .= '<address><a href="http://usebb.sourceforge.net">UseBB</a> '.USEBB_VERSION.' running on '.preg_replace('/<\/?address>/i', '', $_SERVER['SERVER_SIGNATURE']).'</address></body></html>';
		die($html_msg);
		
	}
	
	//
	// Get configuration variables
	//
	function get_config($setting) {
		
		global $db;
		
		if ( !isset($this->board_config) ) {
			
			$this->board_config = array();
			
			if ( !($result = $db->query("SELECT name, content FROM ".TABLE_PREFIX."config")) )
				$this->usebb_die('SQL', 'Unable to get forum configuration!', __FILE__, __LINE__);
			while ( $out = $db->fetch_result($result) )
				$this->board_config[$out['name']] = $out['content'];
			
		}
		
		if ( isset($this->board_config[$setting]) )
			return $this->board_config[$setting];
		else
			return FALSE;
		
	}
	
	//
	// Interactive URL builder
	//
	function make_url($filename, $vars='') {
		
		$url = $filename;
		if ( is_array($vars) ) {
			
			$url .= '?';
			foreach ( $vars as $key => $val )
				$safe[] = urlencode($key).'='.urlencode($val);
			$url .= join('&amp;', $safe);
			
		}
		return $url;
		
	}
	
	//
	// Generate a date given a timestamp
	//
	function make_date($stamp) {
		
		$date = date($this->get_config('date_format'), $stamp);
		return $date;
		
	}
	
	//
	// Generate an e-mail link
	//
	function show_email($user) {
		
		global $sess_info, $lang;
		
		if ( isset($sess_info['user_info']) && $sess_info['user_info']['level'] >= intval($this->get_config('view_hidden_email_addresses_min_level')) ) {
			
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
			if ( !$this->get_config('email_view_level') || !$user['email_show'] && $user['id'] != $sess_info['user_id'] )
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
			$seed0 = explode(' ', microtime());
			$seed = (float)$seed0[1] + ((float)$seed0[0] * 100000);
			mt_srand($seed);
			$key .= $characters[mt_rand(0, strlen($characters)-1)];
			
		}
		
		return $key;
		
	}
	
	//
	// Send an email
	//
	function usebb_mail($subject, $rawbody, $bodyvars='', $from_name, $from_email, $to) {
		
		$body = $rawbody;
		$bodyvars['board_name'] = $this->get_config('board_name');
		$bodyvars['board_link'] = $this->get_config('board_url');
		
		foreach ( $bodyvars as $key => $val )
			$body = str_replace('['.$key.']', $val, $body);
		
		if ( !mail($to, $subject, $body, 'From: '.$from_name.' <'.$from_email.'>'."\r\n".'X-Mailer: UseBB '.USEBB_VERSION) )
			$this->usebb_die('Mail', 'Unable to send e-mail!', __FILE__, __LINE__);
		
	}
	
	//
	// Set the remember cookie
	//
	function set_al($value) {
		
		setcookie($this->get_config('session_name').'_al', $value, gmmktime()+31536000, $this->get_config('cookie_path'), $this->get_config('cookie_domain'), $this->get_config('cookie_secure'));
		
	}
	
	//
	// Unset the remember cookie
	//
	function unset_al() {
		
		setcookie($this->get_config('session_name').'_al', '', gmmktime()-31536000, $this->get_config('cookie_path'), $this->get_config('cookie_domain'), $this->get_config('cookie_secure'));
		
	}
	
	//
	// Authorization function
	// Defines whether a user has permission to take a certain action.
	//
	function auth($authint, $action) {
		
		global $sess_info;
		
		//
		// Define the user level
		//
		if ( $sess_info['user_id'] )
			$userlevel = $sess_info['user_info']['level'];
		else
			$userlevel = 0;
		
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
			'delete' => 5,
			'lock' => 6,
			'sticky' => 7
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
	function markup($string, $bbcode, $smilies) {
		
		$string = nl2br($string);
		return $string;
		
	}
	
}

?>