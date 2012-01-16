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
 * Member activation
 *
 * Gives an interface to activate member accounts.
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

$list_modes = array('admin', 'email', 'all');
$_GET['show'] = ( !empty($_GET['show']) && in_array($_GET['show'], $list_modes) ) ? $_GET['show'] : $list_modes[0];

if ( !empty($_GET['id']) && valid_int($_GET['id']) ) {
	
	$result = $db->query("SELECT id, name, level, active, posts, language, email FROM ".TABLE_PREFIX."members WHERE id = ".$_GET['id']." AND active = ".USER_INACTIVE);
	$memberdata = $db->fetch_result($result);
	
	if ( $memberdata['id'] && $functions->verify_url(false) ) {
		
		$db->query("UPDATE ".TABLE_PREFIX."members SET active = ".$functions->user_active_value($memberdata, FALSE, TRUE).", active_key = '' WHERE id = ".$_GET['id']);
		
		$user_lang = $functions->fetch_language($memberdata['language']);
		
		$functions->usebb_mail($user_lang['AdminActivatedAccountEmailSubject'], $user_lang['AdminActivatedAccountEmailBody'], array(
			'account_name' => stripslashes($memberdata['name'])
		), $functions->get_config('board_name'), $functions->get_config('admin_email'), $memberdata['email'], null, $memberdata['language'], $user_lang['character_encoding']);
		
	}
	
	$functions->redirect('admin.php', array('act' => 'activate_members', 'show' => $_GET['show']));
	
} else {
	
	$content = '<p>'.$lang['ActivateMembersExplain'].'</p>';
	
	$content .= '<ul id="adminfunctionsmenu">';
	foreach ( $list_modes as $mode ) {
		
		if ( $_GET['show'] == $mode ) {
			
			switch ( $mode ) {
				
				case 'admin':
					$query_where_part = "active = ".USER_INACTIVE." AND active_key = ''";
					break;
				case 'email':
					$query_where_part = "active = ".USER_INACTIVE." AND active_key <> ''";
					break;
				case 'all':
					$query_where_part = "active = ".USER_INACTIVE;
					break;
				
			}
			
			$content .= '<li>'.$lang['ActivateMembersList'.ucfirst($mode)].'</li> ';
			
		} else {
			
			$content .= '<li><a href="'.$functions->make_url('admin.php', array('act' => 'activate_members', 'show' => $mode)).'">'.$lang['ActivateMembersList'.ucfirst($mode)].'</a></li> ';
			
		}
		
	}
	$content .= '</ul>';
	
	$result = $db->query("SELECT id, name, regdate, last_login, email FROM ".TABLE_PREFIX."members WHERE ".$query_where_part." ORDER BY regdate ASC");
	$unactivated = array();
	while ( $userinfo = $db->fetch_result($result) )
		$unactivated[] = $userinfo;
	
	if ( count($unactivated) ) {
		
		$content .= '<table id="adminregulartable"><tr><th>'.$lang['Username'].'</th><th>'.$lang['Registered'].'</th><th class="action">'.$lang['Activate'].'</th><th class="action">'.$lang['Edit'].'</th><th>'.$lang['Delete'].'</th></tr>';
		foreach ( $unactivated as $userinfo ) {
			
			$logged_in_previously = ( $userinfo['last_login'] ) ? ' <small>*</small>' : '';
			$content .= '<tr><td><a href="'.$functions->make_url('profile.php', array('id' => $userinfo['id'])).'" title="'.$userinfo['email'].'"><em>'.unhtml(stripslashes($userinfo['name'])).'</em></a>'.$logged_in_previously.'</td><td>'.$functions->make_date($userinfo['regdate']).'</td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'activate_members', 'show' => $_GET['show'], 'id' => $userinfo['id']), true, true, false, true).'">'.$lang['Activate'].'</a></td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'members', 'id' => $userinfo['id'])).'">'.$lang['Edit'].'</a></td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'delete_members', 'id' => $userinfo['id'])).'">'.$lang['Delete'].'</a></td></tr>';
			
		}
		$content .= '</table>';
		
	} else {
		
		$content .= '<p>'.$lang['ActivateMembersNoMembers'].'</p>';
		
	}
	
}

$admin_functions->create_body('activate_members', $content);

?>
