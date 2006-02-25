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
 * Member registration
 *
 * Gives an interface to register new members.
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

if ( !empty($_POST['name']) )
	$_POST['name'] = preg_replace('#\s+#', ' ', $_POST['name']);

if ( !empty($_POST['displayed_name']) )
	$_POST['displayed_name'] = preg_replace('#\s+#', ' ', $_POST['displayed_name']);

$username_taken = false;

//
// Check if this username already exists
//
if ( !empty($_POST['name']) ) {
	
	$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."members WHERE ( name = '".$_POST['name']."' OR displayed_name = '".$_POST['name']."' )");
	$out = $db->fetch_result($result);
	if ( $out['count'] )
		$username_taken = true;
	
}

if ( !empty($_POST['name']) && !$username_taken && !empty($_POST['email']) && !empty($_POST['passwd1']) && !empty($_POST['passwd2']) && preg_match(USER_PREG, $_POST['name']) && preg_match(EMAIL_PREG, $_POST['email']) && strlen($_POST['passwd1']) >= $functions->get_config('passwd_min_length') && preg_match(PWD_PREG, $_POST['passwd1']) && $_POST['passwd1'] == $_POST['passwd2'] ) {
	
	$result = $db->query("INSERT INTO ".TABLE_PREFIX."members ( id, name, email, passwd, regdate, level, active, active_key, template, language, date_format, timezone, enable_quickreply, return_to_topic_after_posting, target_blank, hide_avatars, hide_userinfo, hide_signatures, displayed_name ) VALUES ( NULL, '".$_POST['name']."', '".$_POST['email']."', '".md5($_POST['passwd1'])."', ".time().", 1, 1, '', '".$functions->get_config('template')."', '".$functions->get_config('language')."', '".$functions->get_config('date_format')."', ".$functions->get_config('timezone').", ".$functions->get_config('enable_quickreply').", ".$functions->get_config('return_to_topic_after_posting').", ".$functions->get_config('target_blank').", ".$functions->get_config('hide_avatars').", ".$functions->get_config('hide_userinfo').", ".$functions->get_config('hide_signatures').", '".$_POST['name']."' )");
	$result = $db->query("UPDATE ".TABLE_PREFIX."stats SET content = content+1 WHERE name = 'members'");
	
	$content = '<p>'.sprintf($lang['RegisterMembersComplete'], '<em>'.unhtml(stripslashes($_POST['name'])).'</em>').'</p>';
	
} else {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		$errors = array();
		if ( empty($_POST['name']) || !preg_match(USER_PREG, $_POST['name']) )
			$errors[] = $lang['Username'];
		if ( empty($_POST['passwd1']) || empty($_POST['passwd2']) || !preg_match(PWD_PREG, $_POST['passwd1']) || $_POST['passwd1'] != $_POST['passwd2'] )
			$errors[] = $lang['Password'];
		if ( empty($_POST['email']) || !preg_match(EMAIL_PREG, $_POST['email']) )
			$errors[] = $lang['Email'];
		
		//
		// Show an error message
		//
		if ( count($errors) )
			$content .= '<p><strong>'.sprintf($lang['MissingFields'], join(', ', $errors)).'</strong></p>';
		
		if ( $username_taken )
			$content .= '<p><strong>'.sprintf($lang['MembersEditingMemberUsernameExists'], '<em>'.unhtml(stripslashes($_POST['name'])).'</em>').'</strong></p>';
		
		if ( !empty($_POST['passwd1']) && strlen($_POST['passwd1']) < $functions->get_config('passwd_min_length') )
			$content .= '<p><strong>'.sprintf($lang['StringTooShort'], $lang['Password'], $functions->get_config('passwd_min_length')).'</strong></p>';
		
	} else {
		
		$content = '<p>'.$lang['RegisterMembersExplain'].'</p>';
		
	}
	
	$_POST['name'] = ( !empty($_POST['name']) && preg_match(USER_PREG, $_POST['name']) ) ? $_POST['name'] : '';
	$_POST['email'] = ( !empty($_POST['email']) && preg_match(EMAIL_PREG, $_POST['email']) ) ? $_POST['email'] : '';
	
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'register_members')).'" method="post">';
	$content .= '<table id="adminregulartable">';
		$content .= '<tr><td class="fieldtitle">'.$lang['Username'].' <small>*</small></td><td><input type="text" size="30" name="name" id="name" maxlength="255" value="'.unhtml(stripslashes($_POST['name'])).'" /></td></tr>';
		$content .= '<tr><td class="fieldtitle">'.$lang['Password'].' <small>*</small></td><td><input type="password" size="30" name="passwd1" maxlength="255" /></td></tr>';
		$content .= '<tr><td class="fieldtitle">'.$lang['PasswordAgain'].' <small>*</small></td><td><input type="password" size="30" name="passwd2" maxlength="255" /></td></tr>';
		$content .= '<tr><td class="fieldtitle">'.$lang['Email'].' <small>*</small></td><td><input type="text" size="30" name="email" maxlength="255" value="'.unhtml(stripslashes($_POST['email'])).'" /></td></tr>';	
	$content .= '<tr><td colspan="2" class="submit"><input type="submit" value="'.$lang['Register'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></td></tr></table></form>';
	
	$template->set_js_onload("set_focus('name')");
	
}

$admin_functions->create_body('register_members', $content);

?>
