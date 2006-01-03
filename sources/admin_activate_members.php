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
 * Member activation
 *
 * Gives an interface to activate member accounts.
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

if ( !empty($_GET['id']) && valid_int($_GET['id']) ) {
	
	$result = $db->query("SELECT * FROM ".TABLE_PREFIX."members WHERE id = ".$_GET['id']." AND active = 0");
	$memberdata = $db->fetch_result($result);
	
	if ( $memberdata['id'] ) {
		
		$db->query("UPDATE ".TABLE_PREFIX."members SET active = 1, active_key = '' WHERE id = ".$_GET['id']);
		
	}
	
	$functions->redirect('admin.php', array('act' => 'activate_members'));
	
} else {
	
	$result = $db->query("SELECT id, name, regdate, last_login FROM ".TABLE_PREFIX."members WHERE active = 0 ORDER BY regdate ASC");
	$unactivated = array();
	while ( $userinfo = $db->fetch_result($result) )
		$unactivated[] = $userinfo;
	
	if ( count($unactivated) ) {
		
		$content = '<p>'.$lang['ActivateMembersExplain'].'</p>';
		
		$content .= '<table id="adminregulartable"><tr><th>'.$lang['Username'].'</th><th>'.$lang['Registered'].'</th><th class="action">'.$lang['Activate'].'</th><th class="action">'.$lang['Edit'].'</th><th>'.$lang['Delete'].'</th></tr>';
		foreach ( $unactivated as $userinfo ) {
			
			$logged_in = ( $userinfo['last_login'] ) ? ' <small>*</small>' : '';
			$content .= '<tr><td><a href="'.$functions->make_url('profile.php', array('id' => $userinfo['id'])).'"><em>'.unhtml(stripslashes($userinfo['name'])).'</em></a>'.$logged_in.'</td><td>'.$functions->make_date($userinfo['regdate']).'</td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'activate_members', 'id' => $userinfo['id'])).'">'.$lang['Activate'].'</a></td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'members', 'id' => $userinfo['id'])).'">'.$lang['Edit'].'</a></td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'delete_members', 'id' => $userinfo['id'])).'">'.$lang['Delete'].'</a></td></tr>';
			
		}
		$content .= '</table>';
		
	} else {
		
		$content = '<p>'.$lang['ActivateMembersNoMembers'].'</p>';
		
	}
	
}

$admin_functions->create_body('activate_members', $content);

?>
