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
 * ACP member management
 *
 * Gives an interface to edit members on the board.
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
	
	$result = $db->query("SELECT * FROM ".TABLE_PREFIX."members WHERE id = ".$_GET['id']);
	$memberdata = $db->fetch_result($result);
	
	if ( $memberdata['id'] ) {
		
		//
		// User exists
		//
		
		if ( !empty($_POST['name']) )
			$_POST['name'] = preg_replace('#\s+#', ' ', $_POST['name']);
		
		if ( !empty($_POST['displayed_name']) )
			$_POST['displayed_name'] = preg_replace('#\s+#', ' ', $_POST['displayed_name']);
		
		$username_taken = $displayed_name_taken = false;
		
		//
		// Check if this username already exists
		//
		if ( !empty($_POST['name']) ) {
			
			$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."members WHERE ( name = '".$_POST['name']."' OR displayed_name = '".$_POST['name']."' ) AND id <> ".$_GET['id']);
			$out = $db->fetch_result($result);
			if ( $out['count'] )
				$username_taken = true;
			
		}
		
		//
		// Check if this displayed name already exists
		//
		if ( !empty($_POST['displayed_name']) ) {
			
			$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."members WHERE ( name = '".$_POST['displayed_name']."' OR displayed_name = '".$_POST['displayed_name']."' ) AND id <> ".$_GET['id']);
			$out = $db->fetch_result($result);
			if ( $out['count'] )
				$displayed_name_taken = true;
			
		}
		
		$valid_password = ( !empty($_POST['passwd1']) && $functions->validate_password(stripslashes($_POST['passwd1']), true) );
		if ( !empty($_POST['name']) && !empty($_POST['displayed_name']) && !$username_taken && !$displayed_name_taken && !empty($_POST['email']) && preg_match(USER_PREG, $_POST['name']) && preg_match(EMAIL_PREG, $_POST['email']) && ( ( empty($_POST['passwd1']) && empty($_POST['passwd2']) ) || ( $valid_password && $_POST['passwd1'] == $_POST['passwd2'] && strlen(stripslashes($_POST['passwd1'])) >= $functions->get_config('passwd_min_length') ) ) && ( ( empty($_POST['birthday_month']) && empty($_POST['birthday_day']) && empty($_POST['birthday_year']) ) || ( valid_int($_POST['birthday_month']) && valid_int($_POST['birthday_day']) && valid_int($_POST['birthday_year']) && checkdate($_POST['birthday_month'], $_POST['birthday_day'], $_POST['birthday_year']) ) ) && isset($_POST['posts']) && valid_int($_POST['posts']) && $functions->verify_form() ) {
			
			if ( !empty($_POST['avatar']) ) {
					
				$avatar_type = 1;
				$avatar_remote = $_POST['avatar'];
				
			} else {
				
				$avatar_type = 0;
				$avatar_remote = '';
				
			}
			
			if ( !empty($_POST['birthday_month']) && valid_int($_POST['birthday_month']) && !empty($_POST['birthday_day']) && valid_int($_POST['birthday_day']) && !empty($_POST['birthday_year']) && valid_int($_POST['birthday_year']) )
				$birthday = sprintf('%04d%02d%02d', $_POST['birthday_year'], $_POST['birthday_month'], $_POST['birthday_day']);
			else
				$birthday = 0;
			
			$_POST['level'] = ( !empty($_POST['level']) && in_array($_POST['level'], array(LEVEL_ADMIN, LEVEL_MOD, LEVEL_MEMBER)) && $memberdata['id'] != $session->sess_info['user_id'] ) ? $_POST['level'] : $memberdata['level'];
			$_POST['active'] = ( isset($_POST['active']) && in_array($_POST['active'], array(USER_INACTIVE, USER_ACTIVE, USER_POTENTIAL_SPAMMER)) && $memberdata['id'] != $session->sess_info['user_id'] ) ? $_POST['active'] : $memberdata['active'];
			$active_key = ( isset($_POST['active']) && $_POST['active'] != USER_INACTIVE && $memberdata['id'] != $session->sess_info['user_id'] ) ? "active_key = ''," : '';
			$_POST['banned'] = ( !empty($_POST['banned']) && $memberdata['id'] != $session->sess_info['user_id'] ) ? 1 : 0;
			$_POST['banned_reason'] = ( !empty($_POST['banned_reason']) && $memberdata['id'] != $session->sess_info['user_id'] ) ? $_POST['banned_reason'] : '';
			
			$_POST['language'] = ( !empty($_POST['language']) && in_array($_POST['language'], $functions->get_language_packs()) ) ? $_POST['language'] : $memberdata['language'];
			$_POST['template'] = ( !empty($_POST['template']) && in_array($_POST['template'], $functions->get_template_sets()) ) ? $_POST['template'] : $memberdata['template'];
			$_POST['email_show'] = ( !empty($_POST['email_show']) ) ? 1 : 0;
			$_POST['last_login_show'] = ( !empty($_POST['last_login_show']) ) ? 1 : 0;
			$_POST['hide_from_online_list'] = ( !empty($_POST['hide_from_online_list']) ) ? 1 : 0;
			$_POST['date_format'] = ( !empty($_POST['date_format']) ) ? $_POST['date_format'] : $memberdata['date_format'];
			$_POST['timezone'] = ( valid_int($_POST['timezone']) && $functions->timezone_handler('check_existance', $_POST['timezone']) ) ? $_POST['timezone'] : $memberdata['timezone'];
			$_POST['dst'] = ( !empty($_POST['dst']) ) ? 1 : 0;
			$_POST['quickreply'] = ( !empty($_POST['quickreply']) ) ? 1 : 0;
			$_POST['return_to_topic'] = ( !empty($_POST['return_to_topic']) ) ? 1 : 0;
			$_POST['target_blank'] = ( !empty($_POST['target_blank']) ) ? 1 : 0;
			$_POST['hide_avatars'] = ( !empty($_POST['hide_avatars']) ) ? 1 : 0;
			$_POST['hide_userinfo'] = ( !empty($_POST['hide_userinfo']) ) ? 1 : 0;
			$_POST['hide_signatures'] = ( !empty($_POST['hide_signatures']) ) ? 1 : 0;
			$_POST['auto_subscribe_topic'] = ( !empty($_POST['auto_subscribe_topic']) ) ? 1 : 0;
			$_POST['auto_subscribe_reply'] = ( !empty($_POST['auto_subscribe_reply']) ) ? 1 : 0;
			
			$result = $db->query("UPDATE ".TABLE_PREFIX."members SET
				name = '".$_POST['name']."',
				displayed_name = '".$_POST['displayed_name']."',
				real_name = '".$_POST['real_name']."',
				avatar_type = ".$avatar_type.",
				avatar_remote = '".$avatar_remote."',
				birthday = '".$birthday."',
				location = '".$_POST['location']."',
				website = '".$_POST['website']."',
				occupation = '".$_POST['occupation']."',
				interests = '".$_POST['interests']."',
				signature = '".$_POST['signature']."',
				level = ".$_POST['level'].",
				active = ".$_POST['active'].",
				".$active_key."
				rank = '".$_POST['rank']."',
				banned = ".$_POST['banned'].",
				banned_reason = '".$_POST['banned_reason']."',
				posts = ".$_POST['posts'].",
				email = '".$_POST['email']."',
				msnm  = '".$_POST['msnm']."',
				yahoom = '".$_POST['yahoom']."',
				aim = '".$_POST['aim']."',
				icq = '".$_POST['icq']."',
				jabber = '".$_POST['jabber']."',
				skype = '".$_POST['skype']."',
				language = '".$_POST['language']."',
				template = '".$_POST['template']."',
				email_show = ".$_POST['email_show'].",
				last_login_show = ".$_POST['last_login_show'].",
				hide_from_online_list = ".$_POST['hide_from_online_list'].",
				date_format = '".$_POST['date_format']."',
				timezone = '".$_POST['timezone']."',
				dst = ".$_POST['dst'].",
				enable_quickreply = ".$_POST['quickreply'].",
				return_to_topic_after_posting = ".$_POST['return_to_topic'].",
				auto_subscribe_topic = ".$_POST['auto_subscribe_topic'].",
				auto_subscribe_reply = ".$_POST['auto_subscribe_reply'].",
				target_blank = ".$_POST['target_blank'].",
				hide_avatars = ".$_POST['hide_avatars'].",
				hide_userinfo = ".$_POST['hide_userinfo'].",
				hide_signatures = ".$_POST['hide_signatures']."
			WHERE id = ".$memberdata['id']);
			
			if ( !empty($_POST['passwd1']) )
				$result = $db->query("UPDATE ".TABLE_PREFIX."members SET passwd = '".md5(stripslashes($_POST['passwd1']))."' WHERE id = ".$memberdata['id']);
			
			if ( $_POST['level'] < $memberdata['level'] )
				$admin_functions->reload_moderator_perms();
			
			$content = '<p>'.sprintf($lang['MembersEditingComplete'], '<em>'.unhtml(stripslashes($_POST['name'])).'</em>').'</p>';
			
		} else {
			
			$content = '<h2>'.sprintf($lang['MembersEditingMember'], $functions->make_profile_link($memberdata['id'], $memberdata['name'], $memberdata['level'])).'</h2>';
			$content .= '<p>'.$lang['MembersEditingMemberInfo'].'</p>';
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				
				$errors = array();
				if ( empty($_POST['name']) || !preg_match(USER_PREG, $_POST['name']) )
					$errors[] = $lang['Username'];
				if ( empty($_POST['displayed_name']) )
					$errors[] = $lang['DisplayedName'];
				if ( ( !empty($_POST['passwd1']) || !empty($_POST['passwd2']) ) && $_POST['passwd1'] != $_POST['passwd2'] )
					$errors[] = $lang['Password'];
				if ( !( ( empty($_POST['birthday_month']) && empty($_POST['birthday_day']) && empty($_POST['birthday_year']) ) || ( valid_int($_POST['birthday_month']) && valid_int($_POST['birthday_day']) && valid_int($_POST['birthday_year']) && checkdate($_POST['birthday_month'], $_POST['birthday_day'], $_POST['birthday_year']) ) ) )
					$errors[] = $lang['Birthday'];
				if ( empty($_POST['email']) || !preg_match(EMAIL_PREG, $_POST['email']) )
					$errors[] = $lang['Email'];
				if ( !isset($_POST['posts']) || !valid_int($_POST['posts']) )
					$errors[] = $lang['Posts'];
				
				//
				// Show an error message
				//
				if ( count($errors) )
					$content .= '<p><strong>'.sprintf($lang['MissingFields'], join(', ', $errors)).'</strong></p>';
				
				if ( $username_taken )
					$content .= '<p><strong>'.sprintf($lang['MembersEditingMemberUsernameExists'], '<em>'.unhtml(stripslashes($_POST['name'])).'</em>').'</strong></p>';
				
				if ( $displayed_name_taken )
					$content .= '<p><strong>'.sprintf($lang['MembersEditingMemberDisplayedNameExists'], '<em>'.unhtml(stripslashes($_POST['displayed_name'])).'</em>').'</strong></p>';
				
				if ( !empty($_POST['passwd1']) && !$valid_password )
					$content .= '<p><strong>'.sprintf($lang['PasswdInfoNew'], $functions->get_config('passwd_min_length')).'</strong></p>';
				elseif ( !empty($_POST['passwd1']) && strlen(stripslashes($_POST['passwd1'])) < $functions->get_config('passwd_min_length') )
					$content .= '<p><strong>'.sprintf($lang['StringTooShort'], $lang['Password'], $functions->get_config('passwd_min_length')).'</strong></p>';
				
			}
			
			foreach ( $memberdata as $id => $val )
				$_POST[$id] = ( isset($_POST[$id]) ) ? $_POST[$id] : $val;
			
			list($birthday_year_input, $birthday_month_input, $birthday_day_input) = $functions->birthday_input_fields($_POST['birthday']);
			
			if ( $memberdata['id'] == $session->sess_info['user_id'] ) {
				
				$level_input = $lang['Administrator'].' &ndash; '.$lang['MembersEditingMemberCantChangeOwnLevel'];

				$activation_input = $lang['MembersEditingMemberCantChangeOwnActivation'];
				
				$banned_input = '<tr><td class="fieldtitle">'.$lang['MembersEditingMemberBanned'].'</td><td rowspan="2">'.$lang['MembersEditingMemberCantBanSelf'].'</td></tr><tr><td class="fieldtitle">'.$lang['MembersEditingMemberBannedReason'].'</td></tr>';

				$delete_link = $lang['MembersEditingMemberCantDeleteSelf'];
				
			} else {
				
				$level_input = '<select name="level">';
				$selected = ( $_POST['level'] == 3 ) ? ' selected="selected"' : '';
				$level_input .= '<option value="3"'.$selected.'>'.$lang['Administrator'].'</option>';
				$selected = ( $_POST['level'] != 3 ) ? ' selected="selected"' : '';
				$level_input .= '<option value="1"'.$selected.'>'.$lang['Member'].' / '.$lang['Moderator'].'</option>';
				$level_input .= '</select>';
				$level_input .= '<div class="moreinfo">'.$lang['MembersEditingLevelModInfo'].'</div>';

				$activation_input = '<select name="active">';
				$selected = ( $_POST['active'] == USER_INACTIVE ) ? ' selected="selected"' : '';
				$activation_input .= '<option value="'.USER_INACTIVE.'"'.$selected.'>'.$lang['MembersEditingActivationInactive'].'</option>';
				$selected = ( $_POST['active'] == USER_POTENTIAL_SPAMMER ) ? ' selected="selected"' : '';
				$activation_input .= '<option value="'.USER_POTENTIAL_SPAMMER.'"'.$selected.'>'.$lang['MembersEditingActivationPotentialSpammer'].'</option>';
				$selected = ( $_POST['active'] == USER_ACTIVE ) ? ' selected="selected"' : '';
				$activation_input .= '<option value="'.USER_ACTIVE.'"'.$selected.'>'.$lang['MembersEditingActivationActive'].'</option>';
				$activation_input .= '</select>';
				$activation_input .= '<div class="moreinfo">'.$lang['MembersEditingActivationInfo'].'</div>';
				
				$banned_checked = ( $_POST['banned'] ) ? ' checked="checked"' : '';
				$banned_input = '<tr><td class="fieldtitle">'.$lang['MembersEditingMemberBanned'].'</td><td><label><input type="checkbox" name="banned" value="1"'.$banned_checked.' /> '.$lang['Yes'].'</label></td></tr><tr><td class="fieldtitle">'.$lang['MembersEditingMemberBannedReason'].'</td><td><textarea rows="5" cols="30" name="banned_reason">'.unhtml(stripslashes($_POST['banned_reason'])).'</textarea><div class="moreinfo">'.$lang['HTMLEnabledField'].'</div></td></tr>';

				$delete_link = '<a href="'.$functions->make_url('admin.php', array('act' => 'delete_members', 'id' => $_GET['id'])).'">'.$lang['DeleteMembersConfirmMemberDelete'].'</a>';
				
			}
			
			$available_languages = $functions->get_language_packs();
			$language_input = '<select name="language">';
			foreach ( $available_languages as $single_language ) {
				
				$selected = ( $_POST['language'] == $single_language ) ? ' selected="selected"' : '';
				$language_input .= '<option value="'.$single_language.'"'.$selected.'>'.$single_language.'</option>';
				
			}
			$language_input .= '</select>';
			
			$available_templates = $functions->get_template_sets();
			$template_input = '<select name="template">';
			foreach ( $available_templates as $single_template ) {
				
				$selected = ( $_POST['template'] == $single_template ) ? ' selected="selected"' : '';
				$template_input .= '<option value="'.$single_template.'"'.$selected.'>'.$single_template.'</option>';
				
			}
			$template_input .= '</select>';
			
			$email_show_checked = ( $_POST['email_show'] ) ? ' checked="checked"' : '';
			$last_login_show_checked = ( $_POST['last_login_show'] ) ? ' checked="checked"' : '';
			$hide_from_online_list_checked = ( $_POST['hide_from_online_list'] ) ? ' checked="checked"' : '';
		
			$timezone_input = 'UTC/GMT <select name="timezone">';
			foreach ( $functions->timezone_handler('get_zones') as $key => $val ) {
				
				$selected = ( $_POST['timezone'] == $key ) ? ' selected="selected"' : '';
				$timezone_input .= '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
				
			}
			$timezone_input .= '</select>';
			
			$dst_checked = ( $_POST['dst'] ) ? ' checked="checked"' : '';
			$quickreply_checked = ( $_POST['enable_quickreply'] ) ? ' checked="checked"' : '';
			$return_to_topic_checked = ( $_POST['return_to_topic_after_posting'] ) ? ' checked="checked"' : '';
			$target_blank_checked = ( $_POST['target_blank'] ) ? ' checked="checked"' : '';
			$hide_avatars_checked = ( $_POST['hide_avatars'] ) ? ' checked="checked"' : '';
			$hide_userinfo_checked = ( $_POST['hide_userinfo'] ) ? ' checked="checked"' : '';
			$hide_signatures_checked = ( $_POST['hide_signatures'] ) ? ' checked="checked"' : '';
			$auto_subscribe_topic_checked = ( $_POST['auto_subscribe_topic'] ) ? ' checked="checked"' : '';
			$auto_subscribe_reply_checked = ( $_POST['auto_subscribe_reply'] ) ? ' checked="checked"' : '';
			
			$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'members', 'id' => $_GET['id'])).'" method="post">';
			$content .= '<table id="adminregulartable">';
			
			$content .= '<tr><th colspan="2">'.$lang['EditProfile'].'</th></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Username'].' <small>*</small></td><td><input type="text" size="30" name="name" id="name" maxlength="255" value="'.unhtml(stripslashes($_POST['name'])).'" /><div class="moreinfo">'.$lang['UsernameInfo'].'</div></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['DisplayedName'].' <small>*</small></td><td><input type="text" size="30" name="displayed_name" maxlength="255" value="'.unhtml(stripslashes($_POST['displayed_name'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Password'].'</td><td><input type="password" size="30" name="passwd1" maxlength="255" /><div class="moreinfo">'.sprintf($lang['PasswdInfoNew'], $functions->get_config('passwd_min_length')).'</div></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['PasswordAgain'].'</td><td><input type="password" size="30" name="passwd2" maxlength="255" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['RealName'].'</td><td><input type="text" size="30" name="real_name" maxlength="255" value="'.unhtml(stripslashes($_POST['real_name'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['AvatarURL'].'</td><td><input type="text" size="30" name="avatar" maxlength="255" value="'.unhtml(stripslashes($_POST['avatar_remote'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Birthday'].'</td><td>'.$birthday_month_input.' '.$birthday_day_input.' '.$birthday_year_input.'</td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Location'].'</td><td><input type="text" size="30" name="location" maxlength="255" value="'.unhtml(stripslashes($_POST['location'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Website'].'</td><td><input type="text" size="30" name="website" maxlength="255" value="'.unhtml(stripslashes($_POST['website'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Occupation'].'</td><td><input type="text" size="30" name="occupation" maxlength="255" value="'.unhtml(stripslashes($_POST['occupation'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Interests'].'</td><td><input type="text" size="30" name="interests" maxlength="255" value="'.unhtml(stripslashes($_POST['interests'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Signature'].'</td><td><textarea rows="5" cols="30" name="signature">'.unhtml(stripslashes($_POST['signature'])).'</textarea></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Level'].'</td><td>'.$level_input.'</td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Rank'].'</td><td><input type="text" size="30" name="rank" maxlength="255" value="'.unhtml(stripslashes($_POST['rank'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['MembersEditingActivation'].'</td><td>'.$activation_input.'</td></tr>';
				$content .= $banned_input;
				$content .= '<tr><td class="fieldtitle">'.$lang['Posts'].' <small>*</small></td><td><input type="text" size="11" name="posts" maxlength="11" value="'.unhtml(stripslashes($_POST['posts'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Delete'].'</td><td>'.$delete_link.'</td></tr>';
			
			$content .= '<tr><th colspan="2">'.$lang['ContactInfo'].'</th></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Email'].' <small>*</small></td><td><input type="text" size="30" name="email" maxlength="255" value="'.unhtml(stripslashes($_POST['email'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['MSNM'].'</td><td><input type="text" size="30" name="msnm" maxlength="255" value="'.unhtml(stripslashes($_POST['msnm'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['YahooM'].'</td><td><input type="text" size="30" name="yahoom" maxlength="255" value="'.unhtml(stripslashes($_POST['yahoom'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['AIM'].'</td><td><input type="text" size="30" name="aim" maxlength="255" value="'.unhtml(stripslashes($_POST['aim'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['ICQ'].'</td><td><input type="text" size="30" name="icq" maxlength="255" value="'.unhtml(stripslashes($_POST['icq'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Jabber'].'</td><td><input type="text" size="30" name="jabber" maxlength="255" value="'.unhtml(stripslashes($_POST['jabber'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Skype'].'</td><td><input type="text" size="30" name="skype" maxlength="255" value="'.unhtml(stripslashes($_POST['skype'])).'" /></td></tr>';
			
			$content .= '<tr><th colspan="2">'.$lang['EditOptions'].'</th></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Language'].'</td><td>'.$language_input.'</td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Template'].'</td><td>'.$template_input.'</td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['PublicEmail'].'</td><td><label><input type="checkbox" name="email_show" value="1"'.$email_show_checked.' /> '.$lang['Yes'].'</label></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['PublicLastLogin'].'</td><td><label><input type="checkbox" name="last_login_show" value="1"'.$last_login_show_checked.' /> '.$lang['Yes'].'</label></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['HideFromOnlineList'].'</td><td><label><input type="checkbox" name="hide_from_online_list" value="1"'.$hide_from_online_list_checked.' /> '.$lang['Yes'].'</label></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['DateFormat'].'</td><td><input type="text" name="date_format" size="25" maxlength="255" value="'.unhtml(stripslashes($_POST['date_format'])).'" /></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['Timezone'].'</td><td>'.$timezone_input.'</td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['DST'].'</td><td><label><input type="checkbox" name="dst" value="1"'.$dst_checked.' /> '.$lang['Enabled'].'</label></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['QuickReply'].'</td><td><label><input type="checkbox" name="quickreply" value="1"'.$quickreply_checked.' /> '.$lang['Enabled'].'</label></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['ReturnToTopicAfterPosting'].'</td><td><label><input type="checkbox" name="return_to_topic" value="1"'.$return_to_topic_checked.' /> '.$lang['Yes'].'</label></td></tr>';
				$content .= '<tr><td class="fieldtitle" rowspan="2">'.$lang['AutoSubscribe'].'</td><td><label><input type="checkbox" name="auto_subscribe_topic" value="1"'.$auto_subscribe_topic_checked.' /> '.$lang['OnPostingNewTopics'].'</label></td></tr>';
				$content .= '<tr><td><label><input type="checkbox" name="auto_subscribe_reply" value="1"'.$auto_subscribe_reply_checked.' /> '.$lang['OnPostingNewReplies'].'</label></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['OpenLinksNewWindow'].'</td><td><label><input type="checkbox" name="target_blank" value="1"'.$target_blank_checked.' /> '.$lang['Yes'].'</label></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['HideAllAvatars'].'</td><td><label><input type="checkbox" name="hide_avatars" value="1"'.$hide_avatars_checked.' /> '.$lang['Yes'].'</label></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['HideUserinfo'].'</td><td><label><input type="checkbox" name="hide_userinfo" value="1"'.$hide_userinfo_checked.' /> '.$lang['Yes'].'</label></td></tr>';
				$content .= '<tr><td class="fieldtitle">'.$lang['HideAllSignatures'].'</td><td><label><input type="checkbox" name="hide_signatures" value="1"'.$hide_signatures_checked.' /> '.$lang['Yes'].'</label></td></tr>';
			
			$content .= '<tr><td colspan="2" class="submit"><input type="submit" value="'.$lang['Edit'].'" />'.$admin_functions->form_token().' <input type="reset" value="'.$lang['Reset'].'" /></td></tr></table></form>';
			
			$template->set_js_onload("set_focus('name')");
			
		}
		
	} else {
		
		$functions->redirect('admin.php', array('act' => 'members'));
		
	}
	
} else {
	
	$search_member = ( !empty($_POST['search_member']) ) ? $_POST['search_member'] : '';
	
	$content = '<h2>'.$lang['MembersSearchMember'].'</h2>';
	$content .= '<p>'.$lang['MembersSearchMemberInfo'].'</p>';
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'members')).'" method="post">';
	$content .= '<fieldset><legend>'.$lang['MembersSearchMemberExplain'].'</legend><input type="text" name="search_member" id="search_member" size="25" maxlength="255" value="'.unhtml(stripslashes($search_member)).'" /> <input type="submit" value="'.$lang['Search'].'" /></fieldset>';
	$content .= '</form>';
	
	if ( !empty($search_member) ) {
		
		$search_member_sql = preg_replace(array('#%#', '#_#', '#\s+#'), array('\%', '\_', ' '), $_POST['search_member']);
		$result = $db->query("SELECT id, name, displayed_name, email FROM ".TABLE_PREFIX."members WHERE name LIKE '%".$search_member_sql."%' OR displayed_name LIKE '%".$search_member_sql."%' OR email LIKE '%".$search_member_sql."%' ORDER BY name ASC");
		$matching_members = array();
		while ( $memberdata = $db->fetch_result($result) )
			$matching_members[$memberdata['id']] = array(unhtml(stripslashes($memberdata['name'])), unhtml(stripslashes($memberdata['displayed_name'])), unhtml(stripslashes($memberdata['email'])));
		
		if ( count($matching_members) ) {
			
			$select = '<select name="id">';
			foreach ( $matching_members as $key => $val )
				$select .= '<option value="'.$key.'">'.$val[0].' ('.$val[1].' &mdash; '.$val[2].')</option>';
			$select .= '</select>';
			
			$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'members')).'" method="get">';
			$content .= '<fieldset><legend>'.$lang['MembersSearchMemberList'].'</legend><input type="hidden" name="act" value="members" />'.$select.' <input type="submit" value="'.$lang['Edit'].'" /></fieldset>';
			$content .= '</form>';
			
		} else {
			
			$content .= '<p>'.sprintf($lang['MembersSearchMemberNotFound'], '<em>'.unhtml(stripslashes($_POST['search_member'])).'</em>').'</p>';
			
		}
		
	}
	
	$template->set_js_onload("set_focus('search_member')");
	
}

$admin_functions->create_body('members', $content);

?>
