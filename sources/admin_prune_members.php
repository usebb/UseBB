<?php

/*
	Copyright (C) 2003-2006 UseBB Team
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
 * ACP member pruning
 *
 * Ables to prune members.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2006 UseBB Team
 * @package	UseBB
 * @subpackage	ACP
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

if ( !empty($_POST['type']) && in_array($_POST['type'], array('never_activated', 'never_posted', 'not_logged_in')) && ( ( $_POST['type'] == 'never_activated' && !empty($_POST['na_registered_days_ago']) && valid_int($_POST['na_registered_days_ago']) && $_POST['na_registered_days_ago'] > 0 ) || ( $_POST['type'] == 'never_posted' && !empty($_POST['np_registered_days_ago']) && valid_int($_POST['np_registered_days_ago']) && $_POST['np_registered_days_ago'] > 0 ) || ( $_POST['type'] == 'not_logged_in' && !empty($_POST['last_logged_in']) && valid_int($_POST['last_logged_in']) && $_POST['last_logged_in'] > 0 ) ) && !empty($_POST['confirm']) ) {
	
	//
	// Get all member ID's to prune
	//
	switch ( $_POST['type'] ) {
		
		case 'never_activated':
			$query_where_part = "active = 0 AND last_login = 0 AND regdate < ".( time() - $_POST['na_registered_days_ago'] * 86400 );
			break;
		case 'never_posted':
			$query_where_part = "posts = 0 AND regdate < ".( time() - $_POST['np_registered_days_ago'] * 86400 );
			break;
		case 'not_logged_in':
			$query_where_part = "last_login < ".( time() - $_POST['last_logged_in'] * 86400 );
		
	}
	
	if ( !empty($_POST['exclude_admins']) )
		$query_where_part .= " AND level <> ".LEVEL_ADMIN;
	if ( !empty($_POST['exclude_mods']) )
		$query_where_part .= " AND level <> ".LEVEL_MOD;
	
	$result = $db->query("SELECT id, name, posts FROM ".TABLE_PREFIX."members WHERE ".$query_where_part);
	$prune_members = array();
	
	while ( $memberdata = $db->fetch_result($result) ) {
		
		$prune_members[] = $memberdata['id'];
		
		if ( $memberdata['posts'] )
			$db->query("UPDATE ".TABLE_PREFIX."posts SET poster_id = 0, poster_guest = '".$memberdata['name']."' WHERE poster_id = ".$memberdata['id']);
		
	}
	
	if ( count($prune_members) ) {
		
		$db->query("UPDATE ".TABLE_PREFIX."posts SET post_edit_by = 0 WHERE post_edit_by IN(".join(', ', $prune_members).")");
		$db->query("DELETE FROM ".TABLE_PREFIX."subscriptions WHERE user_id IN(".join(', ', $prune_members).")");
		$db->query("DELETE FROM ".TABLE_PREFIX."moderators WHERE user_id IN(".join(', ', $prune_members).")");
		$db->query("DELETE FROM ".TABLE_PREFIX."members WHERE id IN(".join(', ', $prune_members).")");
		$db->query("DELETE FROM ".TABLE_PREFIX."sessions WHERE user_id IN(".join(', ', $prune_members).")");
		$db->query("UPDATE ".TABLE_PREFIX."stats SET content = content-".count($prune_members)." WHERE name = 'members'");
		
	}
	
	$content = '<p>'.sprintf($lang['PruneMembersDone'], count($prune_members)).'</p>';
	
} else {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		$errors = array();
		if ( empty($_POST['type']) || !in_array($_POST['type'], array('never_activated', 'never_posted', 'not_logged_in')) )
			$errors[] = $lang['PruneMembersType'];
		if ( !empty($_POST['type']) && $_POST['type'] == 'never_activated' && ( empty($_POST['na_registered_days_ago']) || !valid_int($_POST['na_registered_days_ago']) || $_POST['na_registered_days_ago'] <= 0 ) )
			$errors[] = sprintf($lang['PruneMembersRegisteredDaysAgo'], '<em>x</em>');
		if ( !empty($_POST['type']) && $_POST['type'] == 'never_posted' && ( empty($_POST['np_registered_days_ago']) || !valid_int($_POST['np_registered_days_ago']) || $_POST['np_registered_days_ago'] <= 0 ) )
			$errors[] = sprintf($lang['PruneMembersRegisteredDaysAgo'], '<em>x</em>');
		if ( !empty($_POST['type']) && $_POST['type'] == 'not_logged_in' && ( empty($_POST['last_logged_in']) || !valid_int($_POST['last_logged_in']) || $_POST['last_logged_in'] <= 0 ) )
			$errors[] = sprintf($lang['PruneMembersLastLoggedIn'], '<em>x</em>');
		
		//
		// Show an error message
		//
		if ( count($errors) )
			$content .= '<p><strong>'.sprintf($lang['MissingFields'], join(', ', $errors)).'</strong></p>';
		
		$never_activated_checked = ( !empty($_POST['type']) && $_POST['type'] == 'never_activated' ) ? ' checked="checked"' : '';
		$_POST['na_registered_days_ago'] = ( !empty($_POST['type']) && $_POST['type'] == 'never_activated' && !empty($_POST['na_registered_days_ago']) && valid_int($_POST['na_registered_days_ago']) && $_POST['na_registered_days_ago'] > 0 ) ? $_POST['na_registered_days_ago'] : '';
		$never_posted_checked = ( !empty($_POST['type']) && $_POST['type'] == 'never_posted' ) ? ' checked="checked"' : '';
		$_POST['np_registered_days_ago'] = ( !empty($_POST['type']) && $_POST['type'] == 'never_posted' && !empty($_POST['np_registered_days_ago']) && valid_int($_POST['np_registered_days_ago']) && $_POST['np_registered_days_ago'] > 0 ) ? $_POST['np_registered_days_ago'] : '';
		$not_logged_in_checked = ( !empty($_POST['type']) && $_POST['type'] == 'not_logged_in' ) ? ' checked="checked"' : '';
		$_POST['last_logged_in'] = ( !empty($_POST['type']) && $_POST['type'] == 'not_logged_in' && !empty($_POST['last_logged_in']) && valid_int($_POST['last_logged_in']) && $_POST['last_logged_in'] > 0 ) ? $_POST['last_logged_in'] : '';
		$exclude_admins_checked = ( !empty($_POST['exclude_admins']) ) ? ' checked="checked"' : '';
		$exclude_mods_checked = ( !empty($_POST['exclude_mods']) ) ? ' checked="checked"' : '';
		
		//
		// Not confirmed
		//
		if ( empty($_POST['confirm']) )
			$content .= '<p><strong>'.$lang['PruneMembersNotConfirmed'].'</strong></p>';
		
	} else {
		
		$content = '<p>'.$lang['PruneMembersExplain'].'</p>';
		
		$never_activated_checked = ' checked="checked"';
		$_POST['na_registered_days_ago'] = 30;
		$never_posted_checked = '';
		$_POST['np_registered_days_ago'] = '';
		$not_logged_in_checked = '';
		$_POST['last_logged_in'] = '';
		$exclude_admins_checked = ' checked="checked"';
		$exclude_mods_checked = ' checked="checked"';
		
	}
	
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'prune_members')).'" method="post">';
		$content .= '<fieldset><legend><label><input type="radio" name="type" value="never_activated"'.$never_activated_checked.' /> '.$lang['PruneMembersTypeNeverActivated'].'</label></legend>';
			$content .= sprintf($lang['PruneMembersRegisteredDaysAgo'], '<input type="text" name="na_registered_days_ago" size="4" maxlength="255" value="'.$_POST['na_registered_days_ago'].'" />');
		$content .= '</fieldset>';
		$content .= '<fieldset><legend><label><input type="radio" name="type" value="never_posted"'.$never_posted_checked.' /> '.$lang['PruneMembersTypeNeverPosted'].'</label></legend>';
			$content .= sprintf($lang['PruneMembersRegisteredDaysAgo'], '<input type="text" name="np_registered_days_ago" size="4" maxlength="255" value="'.$_POST['np_registered_days_ago'].'" />');
		$content .= '</fieldset>';
		$content .= '<fieldset><legend><label><input type="radio" name="type" value="not_logged_in"'.$not_logged_in_checked.' /> '.$lang['PruneMembersTypeInactive'].'</label></legend>';
			$content .= sprintf($lang['PruneMembersLastLoggedIn'], '<input type="text" name="last_logged_in" size="4" maxlength="255" value="'.$_POST['last_logged_in'].'" />');
		$content .= '</fieldset>';
		
		$content .= '<fieldset><legend>'.$lang['PruneMembersExclude'].'</legend>';
			$content .= '<label><input type="checkbox" name="exclude_admins" value="1"'.$exclude_admins_checked.' /> '.$lang['Administrators'].'</label> ';
			$content .= '<label><input type="checkbox" name="exclude_mods" value="1"'.$exclude_mods_checked.' /> '.$lang['Moderators'].'</label>';
		$content .= '</fieldset>';
		
		$content .= '<p><label><input type="checkbox" name="confirm" value="1" /> '.$lang['PruneMembersConfirmText'].'</label></p>';
		$content .= '<p class="submit"><input type="submit" value="'.$lang['PruneMembersStart'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></p>';
	$content .= '</form>';
	
}

$admin_functions->create_body('prune_members', $content);

?>
