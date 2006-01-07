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
 * ACP member deletion
 *
 * Gives an interface to delete members.
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

if ( !empty($_GET['id']) && valid_int($_GET['id']) ) {
	
	$result = $db->query("SELECT * FROM ".TABLE_PREFIX."members WHERE id = ".$_GET['id']);
	$memberdata = $db->fetch_result($result);
	
	if ( $memberdata['id'] ) {
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			if ( !empty($_POST['delete']) ) {
					
				$db->query("UPDATE ".TABLE_PREFIX."posts SET poster_id = 0, poster_guest = '".$memberdata['displayed_name']."' WHERE poster_id = ".$_GET['id']);
				$db->query("UPDATE ".TABLE_PREFIX."posts SET post_edit_by = 0 WHERE post_edit_by = ".$_GET['id']);
				$db->query("DELETE FROM ".TABLE_PREFIX."subscriptions WHERE user_id = ".$_GET['id']);
				$db->query("DELETE FROM ".TABLE_PREFIX."moderators WHERE user_id = ".$_GET['id']);
				$db->query("DELETE FROM ".TABLE_PREFIX."members WHERE id = ".$_GET['id']);
				$db->query("DELETE FROM ".TABLE_PREFIX."sessions WHERE user_id = ".$_GET['id']);
				$db->query("UPDATE ".TABLE_PREFIX."stats SET content = content-1 WHERE name = 'members'");
				
				$content = '<p>'.sprintf($lang['DeleteMembersComplete'], '<em>'.unhtml(stripslashes($memberdata['name'])).'</em>').'</p>';
				
			} else {
				
				$functions->redirect('admin.php', array('act' => 'delete_members'));
				
			}
			
		} else {
			
			$content = '<h2>'.$lang['DeleteMembersConfirmMemberDelete'].'</h2>';
			$content .= '<p><strong>'.sprintf($lang['DeleteMembersConfirmMemberDeleteContent'], '<em>'.unhtml(stripslashes($memberdata['name'])).'</em>').'</strong></p>';
			$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'delete_members', 'id' => $_GET['id'])).'" method="post">';
			$content .= '<p class="submit"><input type="submit" name="delete" value="'.$lang['Delete'].'" /> <input type="submit" value="'.$lang['Cancel'].'" /></p>';
			$content .= '</form>';
			
		}
		
	} else {
		
		$functions->redirect('admin.php', array('act' => 'delete_members'));
		
	}
	
} else {
	
	$search_member = ( !empty($_POST['search_member']) ) ? $_POST['search_member'] : '';
	
	$content = '<h2>'.$lang['DeleteMembersSearchMember'].'</h2>';
	$content .= '<p>'.$lang['DeleteMembersSearchMemberInfo'].'</p>';
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'delete_members')).'" method="post">';
	$content .= '<p>'.$lang['DeleteMembersSearchMemberExplain'].': <input type="text" name="search_member" id="search_member" size="20" maxlength="255" value="'.unhtml(stripslashes($search_member)).'" /> <input type="submit" value="'.$lang['Search'].'" /></p>';
	$content .= '</form>';
	
	if ( !empty($search_member) ) {
		
		$search_member_sql = preg_replace(array('#%#', '#_#', '#\s+#'), array('\%', '\_', ' '), $_POST['search_member']);
		$result = $db->query("SELECT id, name, displayed_name FROM ".TABLE_PREFIX."members WHERE name LIKE '%".$search_member_sql."%' OR displayed_name LIKE '%".$search_member_sql."%' ORDER BY name ASC");
		$matching_members = array();
		while ( $memberdata = $db->fetch_result($result) )
			$matching_members[$memberdata['id']] = array(unhtml(stripslashes($memberdata['name'])), unhtml(stripslashes($memberdata['displayed_name'])));
		
		if ( count($matching_members) ) {
			
			$select = '<select name="id">';
			foreach ( $matching_members as $key => $val )
				$select .= '<option value="'.$key.'">'.$val[0].' ('.$val[1].')</option>';
			$select .= '</select>';
			
			$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'delete_members')).'" method="get">';
			$content .= '<p>'.$lang['DeleteMembersSearchMemberList'].': <input type="hidden" name="act" value="delete_members" />'.$select.' <input type="submit" value="'.$lang['Delete'].'" /></p>';
			$content .= '</form>';
			
		} else {
			
			$content .= '<p>'.sprintf($lang['DeleteMembersSearchMemberNotFound'], '<em>'.unhtml(stripslashes($_POST['search_member'])).'</em>').'</p>';
			
		}
		
	}
	
	$template->set_js_onload("set_focus('search_member')");
	
}

$admin_functions->create_body('delete_members', $content);

?>
