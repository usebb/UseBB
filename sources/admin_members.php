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
 * ACP member management
 *
 * Gives an interface to edit members on the board.
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

$content = '';

if ( !empty($_GET['id']) && valid_int($_GET['id']) ) {
	
	$result = $db->query("SELECT * FROM usebb_members WHERE id = ".$_GET['id']);
	$memberdata = $db->fetch_result($result);
	
	if ( $memberdata['id'] ) {
		
		//
		// User exists
		//
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			
			
		} else {
			
			
			
		}
		
	}
	
} else {
	
	$search_member = ( !empty($_POST['search_member']) ) ? $_POST['search_member'] : '';
	
	$content = '<h2>'.$lang['MembersSearchMember'].'</h2>';
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'members')).'" method="post">';
	$content .= '<p>'.$lang['MembersSearchMemberExplain'].': <input type="text" name="search_member" id="search_member" size="20" maxlength="255" value="'.unhtml(stripslashes($search_member)).'" /> <input type="submit" value="'.$lang['Search'].'" /></p>';
	$content .= '</form>';
	
	if ( !empty($search_member) ) {
		
		$search_member_sql = preg_replace(array('#%#', '#_#', '#\s+#'), array('\%', '\_', ' '), $_POST['search_member']);
		$result = $db->query("SELECT id, name, displayed_name FROM usebb_members WHERE name LIKE '%".$search_member_sql."%' OR displayed_name LIKE '%".$search_member_sql."%' ORDER BY name ASC");
		$matching_members = array();
		while ( $memberdata = $db->fetch_result($result) )
			$matching_members[$memberdata['id']] = array(unhtml(stripslashes($memberdata['name'])), unhtml(stripslashes($memberdata['displayed_name'])));
		
		if ( count($matching_members) ) {
			
			$select = '<select name="id">';
			foreach ( $matching_members as $key => $val )
				$select .= '<option value="'.$key.'">'.$val[0].' ('.$val[1].')</option>';
			$select .= '</select>';
			
			$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'members')).'" method="get">';
			$content .= '<p>'.$lang['MembersSearchMemberList'].': <input type="hidden" name="act" value="members" />'.$select.' <input type="submit" value="'.$lang['Edit'].'" /></p>';
			$content .= '</form>';
			
		} else {
			
			$content .= '<p>'.sprintf($lang['MembersSearchMemberNotFound'], '<em>'.unhtml(stripslashes($_POST['search_member'])).'</em>').'</p>';
			
		}
		
	}
	
	$template->set_js_onload("set_focus('search_member')");
	
}

$admin_functions->create_body('members', $content);

?>
