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
 * ACP IP address lookup
 *
 * Gives an interface to do IP address to hostname lookups.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2005 UseBB Team
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

$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'iplookup')).'" method="post">';
$content .= '<p>'.$lang['IPAddress'].': <input type="text" name="ip" id="ip" size="15" maxlength="15" value="'.$ip_addr.'" /> <input type="submit" value="'.$lang['Search'].'" /></p>';
$content .= '<p><input type="checkbox" name="search_hostname" id="search_hostname" value="1" checked="checked" /><label for="search_hostname"> '.$lang['IPLookupSearchHostname'].'</label> <input type="checkbox" name="search_usernames" id="search_usernames" value="1" checked="checked" /><label for="search_usernames"> '.$lang['IPLookupSearchUsernames'].'</label></p>';
$content .= '</form>';

if ( !empty($ip_addr) && $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	
	$content .= '<hr />';
	
	if ( !empty($_POST['search_hostname']) ) {
		
		$hostname = @gethostbyaddr($ip_addr);
		
		if ( !empty($hostname) && $ip_addr != $hostname )
			$content .= '<p>'.sprintf($lang['IPLookupResult'], '<em>'.$ip_addr.'</em>', '<em>'.$hostname.'</em>').'</p>';
		else
			$content .= '<p>'.sprintf($lang['IPLookupNotFound'], '<em>'.$ip_addr.'</em>').'</p>';
		
	}
	
	if ( !empty($_POST['search_usernames']) ) {
		
		$result = $db->query("SELECT DISTINCT(u.name) as name FROM usebb_members u, usebb_posts p WHERE u.id = p.poster_id AND p.poster_ip_addr = '".$ip_addr."' ORDER BY u.name ASC");
		
		$usernames = array();
		while ( $user = $db->fetch_result($result) )
			$usernames[] = unhtml(stripslashes($user['name']));
		
		if ( count($usernames) === 1 )
			$content .= '<p>'.sprintf($lang['IPLookupUsernamesSingular'], '<em>'.$usernames[0].'</em>', '<em>'.$ip_addr.'</em>').'</p>';
		elseif ( count($usernames) > 1 )
			$content .= '<p>'.sprintf($lang['IPLookupUsernamesPlural'], count($usernames), '<em>'.join(', ', $usernames).'</em>', '<em>'.$ip_addr.'</em>').'</p>';
		else
			$content .= '<p>'.sprintf($lang['IPLookupUsernamesNotFound'], '<em>'.$ip_addr.'</em>').'</p>';
		
	}
	
}

$admin_functions->create_body('iplookup', $content);
$template->set_js_onload("set_focus('ip')");

?>
