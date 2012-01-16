<?php

/*
	Copyright (C) 2003-2012 UseBB Team
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
 * ACP IP address lookup
 *
 * Gives an interface to do IP address to hostname lookups.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2012 UseBB Team
 * @package	UseBB
 * @subpackage	ACP
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

$ip_addr_format = '#^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$#';

if ( !empty($_REQUEST['ip']) && preg_match($ip_addr_format, $_REQUEST['ip']) )
	$ip_addr = $_REQUEST['ip'];
elseif ( !empty($_POST['ip']) && preg_match($ip_addr_format, $_POST['ip']) )
	$ip_addr = $_POST['ip'];
else
	$ip_addr = '';

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	
	$search_hostname_checked = ( !empty($_POST['search_hostname']) ) ? ' checked="checked"' : '';
	$search_usernames_checked = ( !empty($_POST['search_usernames']) ) ? ' checked="checked"' : '';
	
} else {
	
	$search_hostname_checked = $search_usernames_checked = ' checked="checked"';
	
}

$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'iplookup')).'" method="post">';
$content .= '<p>'.$lang['IPAddress'].': <input type="text" name="ip" id="ip" size="15" maxlength="15" value="'.$ip_addr.'" /> <input type="submit" value="'.$lang['Search'].'" /></p>';
$content .= '<p><label><input type="checkbox" name="search_hostname" value="1"'.$search_hostname_checked.' /> '.$lang['IPLookupSearchHostname'].'</label> <label><input type="checkbox" name="search_usernames" value="1"'.$search_usernames_checked.' /> '.$lang['IPLookupSearchUsernames'].'</label></p>';
$content .= '</form>';

if ( !empty($ip_addr) && $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	
	if ( !empty($_POST['search_hostname']) ) {
		
		$content .= '<fieldset><legend>'.$lang['IPLookupHostname'].'</legend>';
		$hostname = @gethostbyaddr($ip_addr);
		if ( !empty($hostname) && $ip_addr != $hostname )
			$content .= '<p><em>'.$hostname.'</em></p>';
		else
			$content .= '<p>'.$lang['IPLookupHostnameNotFound'].'</p>';
		$content .= '</fieldset>';
		
	}
	
	if ( !empty($_POST['search_usernames']) ) {
		
		$content .= '<fieldset><legend>'.$lang['IPLookupUsernames'].'</legend>';
		$result = $db->query("SELECT DISTINCT u.id, u.name, u.level FROM ".TABLE_PREFIX."members u, ".TABLE_PREFIX."posts p WHERE u.id = p.poster_id AND p.poster_ip_addr = '".$ip_addr."' ORDER BY u.name ASC");
		$users = array();
		while ( $user = $db->fetch_result($result) )
			$users[] = $functions->make_profile_link($user['id'], $user['name'], $user['level']);
		if ( count($users) )
			$content .= '<p><em>'.join(', ', $users).'</em></p>';
		else
			$content .= '<p>'.$lang['IPLookupUsernamesNotFound'].'</p>';
		$content .= '</fieldset>';
		
	}
	
}

$admin_functions->create_body('iplookup', $content);
$template->set_js_onload("set_focus('ip')");

?>
