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
 * ACP member deletion
 *
 * Gives an interface to delete members.
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

if ( !empty($_GET['id']) && valid_int($_GET['id']) ) {
	
	$result = $db->query("SELECT id, name, email, level FROM ".TABLE_PREFIX."members WHERE id = ".$_GET['id']);
	$memberdata = $db->fetch_result($result);
	
	if ( $memberdata['id'] && $memberdata['id'] != $session->sess_info['user_id'] ) {
		
		$sfs_key = $functions->get_config('sfs_api_key');
		$enable_ip_bans = (bool) $functions->get_config('enable_ip_bans');

		$ip_addr = $db->query("SELECT poster_ip_addr FROM ".TABLE_PREFIX."posts WHERE poster_id = ".$memberdata['id']." ORDER BY id DESC LIMIT 1");
		$ip_addr = $db->fetch_result($ip_addr);
		$ip_addr = $ip_addr['poster_ip_addr'];
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			if ( !empty($_POST['delete']) && $functions->verify_form(false) ) {
				
				//
				// Delete the member, don't ban email here
				//
				$admin_functions->delete_members('id = '.$_GET['id'], !empty($_POST['deleteposts']), false);

				//
				// Eventually, ban the given email address (mask)
				//
				if ( !empty($_POST['banemail']) && !empty($_POST['email']) ) {

					$toban = $_POST['email'];
					$db->query("DELETE FROM ".TABLE_PREFIX."bans WHERE email = '".$toban."'");
					$db->query("INSERT INTO ".TABLE_PREFIX."bans VALUES(NULL, '', '".$toban."', '')");

				}

				if ( $enable_ip_bans && !empty($_POST['banipaddr']) && !empty($_POST['ipaddr']) ) {

					$toban = $_POST['ipaddr'];
					$db->query("DELETE FROM ".TABLE_PREFIX."bans WHERE ip_addr = '".$toban."'");
					$db->query("INSERT INTO ".TABLE_PREFIX."bans VALUES(NULL, '', '', '".$toban."')");

				}

				if ( !empty($_POST['sfs-submit']) && !empty($sfs_key) && !empty($ip_addr) ) {

					$functions->sfs_api_submit(array(
						'username' => stripslashes($memberdata['name']), 
						'email' => $memberdata['email'], 
						'ip_addr' => $ip_addr
					));

				}

				$content = '<p>'.sprintf($lang['DeleteMembersComplete'], '<em>'.unhtml(stripslashes($memberdata['name'])).'</em>').'</p>';
				
			} else {
				
				$functions->redirect('admin.php', array('act' => 'delete_members'));
				
			}
			
		} else {
			
			$content = '<h2>'.$lang['DeleteMembersConfirmMemberDelete'].'</h2>';
			$content .= '<p><strong>'.sprintf($lang['DeleteMembersConfirmMemberDeleteContent'], $functions->make_profile_link($memberdata['id'], $memberdata['name'], $memberdata['level'])).'</strong></p>';
			$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'delete_members', 'id' => $_GET['id'])).'" method="post">';
			$content .= '<p><label><input type="checkbox" name="deleteposts" value="1" />  '.$lang['DeleteMembersDeletePosts'].'</label></p>';
			$content .= '<p><label><input type="checkbox" name="banemail" value="1" />  '.$lang['DeleteMembersBanEmail'].' </label><input type="text" name="email" size="'.strlen($memberdata['email']).'" maxlength="255" value="'.$memberdata['email'].'" /></p>';

			if ( $enable_ip_bans && !empty($ip_addr) )
				$content .= '<p><label><input type="checkbox" name="banipaddr" value="1" />  '.$lang['DeleteMembersBanIPAddress'].' </label><input type="text" name="ipaddr" size="'.strlen($ip_addr).'" maxlength="15" value="'.$ip_addr.'" /></p>';

			if ( !empty($sfs_key) && !empty($ip_addr) )
				$content .= '<p><label><input type="checkbox" name="sfs-submit" value="1" />  '.sprintf($lang['DeleteMembersSFSSubmit'], unhtml(stripslashes($memberdata['name'])), $memberdata['email'], $ip_addr).' </label></p>';

			$content .= '<p class="submit"><input type="submit" name="delete" value="'.$lang['Delete'].'" />'.$admin_functions->form_token().' <input type="submit" value="'.$lang['Cancel'].'" /></p>';
			$content .= '</form>';
			
		}
		
	} elseif ( $memberdata['id'] == $session->sess_info['user_id'] ) {
		
		$content = '<h2>'.$lang['Note'].'</h2>';
		$content .= '<p>'.$lang['MembersEditingMemberCantDeleteSelf'].'</p>';
		
	} else {
		
		$functions->redirect('admin.php', array('act' => 'delete_members'));
		
	}
	
} else {
	
	$search_member = ( !empty($_POST['search_member']) ) ? $_POST['search_member'] : '';
	
	$content = '<h2>'.$lang['DeleteMembersSearchMember'].'</h2>';
	$content .= '<p>'.$lang['DeleteMembersSearchMemberInfo'].'</p>';
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'delete_members')).'" method="post">';
	$content .= '<fieldset><legend>'.$lang['DeleteMembersSearchMemberExplain'].'</legend><input type="text" name="search_member" id="search_member" size="25" maxlength="255" value="'.unhtml(stripslashes($search_member)).'" /> <input type="submit" value="'.$lang['Search'].'" /></fieldset>';
	$content .= '</form>';
	
	if ( !empty($search_member) ) {
		
		$search_member_sql = preg_replace(array('#%#', '#_#', '#\s+#'), array('\%', '\_', ' '), $_POST['search_member']);
		$result = $db->query("SELECT id, name, displayed_name, email FROM ".TABLE_PREFIX."members WHERE ( name LIKE '%".$search_member_sql."%' OR displayed_name LIKE '%".$search_member_sql."%' OR email LIKE '%".$search_member_sql."%' ) AND id <> ".$session->sess_info['user_id']." ORDER BY name ASC");
		$matching_members = array();
		while ( $memberdata = $db->fetch_result($result) )
			$matching_members[$memberdata['id']] = array(unhtml(stripslashes($memberdata['name'])), unhtml(stripslashes($memberdata['displayed_name'])), unhtml(stripslashes($memberdata['email'])));
		
		if ( count($matching_members) ) {
			
			$select = '<select name="id">';
			foreach ( $matching_members as $key => $val )
				$select .= '<option value="'.$key.'">'.$val[0].' ('.$val[1].' &mdash; '.$val[2].')</option>';
			$select .= '</select>';
			
			$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'delete_members')).'" method="get">';
			$content .= '<fieldset><legend>'.$lang['DeleteMembersSearchMemberList'].'</legend><input type="hidden" name="act" value="delete_members" />'.$select.' <input type="submit" value="'.$lang['Delete'].'" /></fieldset>';
			$content .= '</form>';
			
		} else {
			
			$content .= '<p>'.sprintf($lang['DeleteMembersSearchMemberNotFound'], '<em>'.unhtml(stripslashes($_POST['search_member'])).'</em>').'</p>';
			
		}
		
	}
	
	$template->set_js_onload("set_focus('search_member')");
	
}

$admin_functions->create_body('delete_members', $content);

?>
