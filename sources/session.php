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
// Create the session handlers
//
class session {
	
	//
	// This session's ID
	//
	var $sess_id;

	//
	// Start or continue a session
	//
	function start($name) {
		
		//
		// Set the session name
		//
		session_name($name);
		//
		// Start the session
		//
		session_start();
		//
		// Save the current session ID
		//
		$this->sess_id = session_id();
		
		
	}
	
	//
	// Update the session table for this session
	//
	function update($location='', $user_id='') {
		
		global $config, $db, $_COOKIE;
		
		//
		// Some required workarounds...
		//
		$location = addslashes($location);
		$current_time = gmmktime();
		
		//
		// First, get the user's IP address
		//
		$ip_addr = ( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		
		//
		// Get banned IP addresses
		//
		if ( !($result = $db->query("SELECT ip_addr FROM ".TABLE_PREFIX."bans WHERE ip_addr <> ''")) )
			usebb_die('SQL', 'Unable to get banned IP adresses!', __FILE__, __LINE__);
		$ip_banned = FALSE;
		if ( $db->num_rows($result) > 0 ) {
			
			while ( $out = $db->fetch_result($result) ) {
				
				$banned_ip = $out['ip_addr'];
				$banned_ip = str_replace('.', '\.', $banned_ip);
				$banned_ip = str_replace('*', '[0-9]+', $banned_ip);
				$banned_ip = str_replace('?', '[0-9]', $banned_ip);
				if ( preg_match('/^'.$banned_ip.'$/', $ip_addr) )
					$ip_banned = TRUE;
				$banned_ip = $out['ip_addr'];
				$banned_ip = str_replace('*', '%', $banned_ip);
				$banned_ip = str_replace('?', '_', $banned_ip);
				$banned_ips_sql[] = "ip_addr LIKE '".$banned_ip."'";
				
			}
			
		}
		
		//
		// Remove older clone sessions if needed
		//
		if ( !$config['allow_multi_sess'] ) {
			
			$add_to_remove_query[] = "( ip_addr = '".$ip_addr."' AND sess_id <> '".$this->sess_id."' )";
			
		}
		
		//
		// Remove outdated sessions if needed
		//
		if ( $config['sess_max_lifetime'] ) {
			
			$min_updated = $current_time - ( $config['sess_max_lifetime'] * 60 );
			$add_to_remove_query[] = "updated < ".$min_updated;
			
		}
		
		//
		// Remove sessions with banned IP addresses
		//
		if ( isset($banned_ips_sql) ) {
			
			$add_to_remove_query[] = join(' OR ', $banned_ips_sql);
			
		}
		
		//
		// Now run the cleanup query
		//
		if ( is_array($add_to_remove_query) ) {
			
			$add_to_remove_query = join(' OR ', $add_to_remove_query);
			if ( !$db->query("DELETE FROM ".TABLE_PREFIX."sessions WHERE ".$add_to_remove_query) )
				usebb_die('SQL', 'Unable to cleanup the session table!', __FILE__, __LINE__);
			
		}
		
		if ( $ip_banned ) {
			
			//
			// Return the sess_info array with the banned key and
			// IP address if this IP address is banned
			//
			$sess_info = array(
				'sess_id' => $this->sess_id,
				'user_id' => 0,
				'ip_addr' => $ip_addr,
				'updated' => $current_time,
				'ip_banned' => TRUE
			);
			return $sess_info;
			
		} else {
			
			//
			// Get information about the current session
			//
			if ( !($result = $db->query("SELECT user_id, started, location, pages FROM ".TABLE_PREFIX."sessions WHERE sess_id = '".$this->sess_id."'")) )
				usebb_die('SQL', 'Unable to get current session info!', __FILE__, __LINE__);
			$sess_info = $db->fetch_result($result);
			
			//
			// Auto login
			//
			if ( isset($_COOKIE[$config['session_name'].'_al']) && $sess_info['user_id'] == 0 ) {
				
				//
				// If there is a remember cookie
				// and the user is not logged in...
				//
				$cookie_data = explode(':', $_COOKIE[$config['session_name'].'_al'], 2);
				
				if ( !is_numeric($cookie_data[0]) || $cookie_data[0] <= 0 ) {
					
					$user_id = 0;
					usebb_unset_al();
					
				} else {
					
					if ( !($result = $db->query("SELECT * FROM ".TABLE_PREFIX."users WHERE id = ".$cookie_data[0])) )
						usebb_die('SQL', 'Unable to get user information!', __FILE__, __LINE__);
					
					if ( $db->num_rows($result) > 0 ) {
						
						$user_info = $db->fetch_result($result);
						
						//
						// If the encrypted password in the cookie equals to the password in the database
						// the user is active and not banned and [ the board is not closed or the user is an admin ]
						//
						if ( $cookie_data[1] == $user_info['passwd'] && $user_info['active'] && !$user_info['banned'] && ( !$config['board_closed'] || $user_info['level'] == 3 ) ) {
							
							//
							// Change the user id that will be entered in the DB below
							// and renew the cookie (or it will not work anymore after a year)
							//
							$user_id = $cookie_data[0];
							usebb_set_al($_COOKIE[$config['session_name'].'_al']);
							
						} else {
							
							$user_id = 0;
							usebb_unset_al();
							
						}
						
					} else {
						
						$user_id = 0;
						usebb_unset_al();
						
					}
					
				}
				
			}
			
			//
			// Insert the new session info or update the existing session info
			//
			if ( $sess_info['started'] ) {
				
				if ( empty($user_id) && $user_id !== 0 )
					$user_id = $sess_info['user_id'];
				
				//
				// Update the location and page count if a page has been passed
				//
				if ( empty($location) ) {
					
					$location = $sess_info['location'];
					$pages = $sess_info['pages'];
					
				} else {
					
					$pages = $sess_info['pages'] + 1;
					
				}
				
			} else {
				
				if ( empty($user_id) )
					$user_id = 0;
				
				$pages = 1;
				
			}
			
			if ( $user_id > 0 && !isset($user_info) ) {
				
				if ( !($result = $db->query("SELECT * FROM ".TABLE_PREFIX."users WHERE id = ".$user_id)) )
					usebb_die('SQL', 'Unable to get user information!', __FILE__, __LINE__);
				
				if ( $db->num_rows($result) > 0 ) {
					
					$user_info = $db->fetch_result($result);
					
					if ( !$user_info['active'] || $user_info['banned'] || ( $config['board_closed'] && $user_info['level'] != 3 ) )
						$user_id = 0;
					
				} else {
					
					$user_id = 0;
					
				}
				
			}
			
			if ( $sess_info['started'] )
				$update_query = "UPDATE ".TABLE_PREFIX."sessions SET user_id = ".$user_id.", ip_addr = '".$ip_addr."', updated = ".$current_time.", location = '".$location."', pages = ".$pages." WHERE sess_id = '".$this->sess_id."'";
			else
				$update_query = "INSERT INTO ".TABLE_PREFIX."sessions VALUES ( '".$this->sess_id."', ".$user_id.", '".$ip_addr."', ".$current_time.", ".$current_time.", '".$location."', ".$pages." )";
			
			if ( !$db->query($update_query) )
				usebb_die('SQL', 'Unable to update session information!', __FILE__, __LINE__);
			
			//
			// Update the last login timestamp of the user
			//
			if ( $sess_info['user_id'] != $user_id ) {
				
				if ( $user_id !== 0 ) {
					
					if ( !$db->query("UPDATE ".TABLE_PREFIX."users SET last_login = ".$current_time." WHERE id = ".$user_id) )
						usebb_die('SQL', 'Unable to update user information!', __FILE__, __LINE__);
					
				} else {
					
					usebb_unset_al();
					
				}
			}
			
			//
			// Now return the session information
			//
			$sess_info = array(
				'sess_id' => $this->sess_id,
				'user_id' => $user_id,
				'ip_addr' => $ip_addr,
				'updated' => $current_time,
				'location' => $location,
				'pages' => $pages,
				'ip_banned' => FALSE
			);
			if ( isset($user_info) )
				$sess_info['user_info'] = $user_info;
			return $sess_info;
			
		}
		
	}
	
}

?>