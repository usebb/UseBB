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
// Add slashes to a global variable
//
function usebb_slashes_to_global($global) {
	
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
function usebb_trim_global($global) {
	
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
// Interactive URL builder
//
function usebb_make_url($filename, $vars='') {
	
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
function usebb_make_date($stamp) {
	
	global $config;
	
	$date = date($config['date_format'], $stamp);
	return $date;
	
}

//
// Generate an e-mail link
//
function usebb_show_email($user) {
	
	global $sess_info, $config, $lang;
	
	if ( isset($sess_info['user_info']) && $sess_info['user_info']['level'] >= intval($config['view_hidden_email_addresses_min_level']) ) {
		
		//
		// The viewing user is an administrator
		//
		if ( $config['email_view_level'] == 1 )
			return '<a href="'.usebb_make_url('mail.php', array('id' => $user['id'])).'">'.$lang['SendMessage'].'</a>';
		elseif ( !$config['email_view_level'] || $config['email_view_level'] == 2 || $config['email_view_level'] == 3 )
			return '<a href="mailto:'.$user['email'].'">'.$user['email'].'</a>';
		
	} else {
		
		//
		// The viewing user is not an administrator
		//
		if ( !$config['email_view_level'] || !$user['email_show'] && $user['id'] != $sess_info['user_id'] )
			return $lang['Hidden'];
		elseif ( $config['email_view_level'] == 1 )
			return '<a href="'.usebb_make_url('mail.php', array('id' => $user['id'])).'">'.$lang['SendMessage'].'</a>';
		elseif ( $config['email_view_level'] == 2 )
			return str_replace('@', ' at ', $user['email']);
		elseif ( $config['email_view_level'] == 3 )
			return '<a href="mailto:'.$user['email'].'">'.$user['email'].'</a>';
		
	}
	
}

//
// Generate a random key
//
function usebb_random_key() {
	
	$characters = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890';
	$length = 10;
	$key = '';
	
	for ( $i=0; $i<$length; $i++ )
		$key .= $characters[mt_rand(0, strlen($characters)-1)];
	
	return $key;
	
}

//
// Send an email
//
function usebb_mail($subject, $rawbody, $bodyvars='', $from_name, $from_email, $to) {
	
	global $config;
	
	$body = $rawbody;
	$bodyvars['board_name'] = $config['board_name'];
	$bodyvars['board_link'] = $config['board_url'];
	
	foreach ( $bodyvars as $key => $val )
		$body = str_replace('['.$key.']', $val, $body);
	
	if ( !mail($to, $subject, $body, 'From: '.$from_name.' <'.$from_email.'>'."\r\n".'X-Mailer: UseBB '.USEBB_VERSION) )
		usebb_die('Mail', 'Unable to send e-mail!', __FILE__, __LINE__);
	
}

//
// Set the remember cookie
//
function usebb_set_al($value) {
	
	global $config;
	
	setcookie($config['session_name'].'_al', $value, gmmktime()+31536000, $config['cookie_path'], $config['cookie_domain'], $config['cookie_secure']);
	
}

//
// Unset the remember cookie
//
function usebb_unset_al() {
	
	global $config;
	
	setcookie($config['session_name'].'_al', '', gmmktime()-31536000, $config['cookie_path'], $config['cookie_domain'], $config['cookie_secure']);
	
}

//
// Authorization function
// Defines whether a user has permission to take a certain action.
//
function usebb_auth($authint, $action) {
	
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
function usebb_markup($string, $bbcode, $smilies) {
	
	$string = nl2br($string);
	return $string;
	
}

?>