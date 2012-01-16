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
 * Panel account information
 *
 * Gives an interface to change user information.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2012 UseBB Team
 * @package	UseBB
 * @subpackage	Panel
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

$can_add_profile_links = $functions->antispam_can_add_profile_links($session->sess_info['user_info']);

if ( !empty($_POST['displayed_name']) )
	$_POST['displayed_name'] = preg_replace('#\s+#', ' ', $_POST['displayed_name']);

$displayed_name_taken = $displayed_name_banned = $email_taken = $email_banned = false;
if ( !empty($_POST['displayed_name']) || ( !empty($_POST['email']) && $functions->validate_email($_POST['email']) ) ) {
	
	//
	// Get banned displayed_names and e-mail addresses
	//
	$result = $db->query("SELECT name, email FROM ".TABLE_PREFIX."bans WHERE name <> '' OR email <> ''");
	$banned = array('displayed_names' => array(), 'emails' => array());
	while ( $out = $db->fetch_result($result) ) {
		
		//
		// Store all the displayed_names and e-mail addresses in an array
		//
		if ( !empty($out['name']) )
			$banned['displayed_names'][] = $out['name'];
		if ( !empty($out['email']) )
			$banned['emails'][] = $out['email'];
		
	}
	
	if ( !empty($_POST['displayed_name']) ) {
		
		//
		// Check if this displayed_name already exists
		//
		$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."members WHERE ( name = '".$_POST['displayed_name']."' OR displayed_name = '".$_POST['displayed_name']."' ) AND id <> ".$session->sess_info['user_id']);
		$out = $db->fetch_result($result);
		if ( $out['count'] )
			$displayed_name_taken = true;
		
		foreach ( $banned['displayed_names'] as $banned_displayed_name ) {
			
			//
			// Allow the user to set his own displayed name equal to username,
			// even if it is banned
			//
			if ( strtolower($session->sess_info['user_info']['name']) == strtolower($_POST['displayed_name']) )
				continue;
			
			if ( preg_match('#^'.str_replace(array('\*', '\?'), array('.*', '.'), preg_quote(stripslashes($banned_displayed_name), '#')).'$#i', $_POST['displayed_name']) )
				$displayed_name_banned = true;
			
		}
		
	}
	
	if ( !empty($_POST['email']) && $functions->validate_email($_POST['email']) ) {
		
		//
		// Check if this email already exists
		//
		if ( !$functions->get_config('allow_duplicate_emails') ) {
			
			$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."members WHERE email = '".$_POST['email']."' AND id <> ".$session->sess_info['user_id']);
			$out = $db->fetch_result($result);
			if ( $out['count'] )
				$email_taken = true;
			
		}
		
		foreach ( $banned['emails'] as $banned_email ) {
			
			if ( preg_match('#^'.str_replace(array('\*', '\?'), array('.*', '.'), preg_quote($banned_email, '#')).'$#i', $_POST['email']) )
				$email_banned = true;
			
		}

		//
		// Check Stop Forum Spam API for e-mail address
		//
		$email_banned = ( $email_banned || $functions->sfs_email_banned($_POST['email']) );
		
	}
	
}

if ( !empty($_POST['displayed_name']) && entities_strlen($_POST['displayed_name']) >= $functions->get_config('username_min_length') && entities_strlen($_POST['displayed_name']) <= $functions->get_config('username_max_length') && !$displayed_name_taken && !$displayed_name_banned && !empty($_POST['email']) && !$email_taken && !$email_banned && entities_strlen($_POST['signature']) <= $functions->get_config('sig_max_length') && ( ( empty($_POST['birthday_month']) && empty($_POST['birthday_day']) && empty($_POST['birthday_year']) ) || ( valid_int($_POST['birthday_month']) && valid_int($_POST['birthday_day']) && valid_int($_POST['birthday_year']) && checkdate($_POST['birthday_month'], $_POST['birthday_day'], $_POST['birthday_year']) ) ) && !empty($_POST['email']) && $functions->validate_email($_POST['email']) && ( empty($_POST['avatar_remote']) || preg_match(IMG_PREG, $_POST['avatar_remote']) ) && ( !$can_add_profile_links || empty($_POST['website']) || preg_match(WEB_PREG, $_POST['website']) ) && $functions->verify_form() ) {
	
	//
	// Set some fields empty if not submitted
	//
	foreach ( $session->sess_info['user_info'] as $key => $val ) {

		if ( !isset($_POST[$key]) )
			$_POST[$key] = '';

	}
	
	if ( !empty($_POST['avatar_remote']) ) {
			
		$avatar_type = 1;
		$avatar_remote = $_POST['avatar_remote'];
		
	} else {
		
		$avatar_type = 0;
		$avatar_remote = '';
		
	}
	
	$activation_necessary = ( $functions->get_config('activation_mode') > 0 && $_POST['email'] != $session->sess_info['user_info']['email'] && $functions->get_user_level() < LEVEL_ADMIN );
	
	//
	// If the e-mail address changed
	//
	if ( $activation_necessary ) {
		
		switch ( intval($functions->get_config('activation_mode')) ) {
			
			case 1:
				$active = USER_INACTIVE;
				$active_key = $functions->random_key(); # used in the email url
				$active_key_md5 = md5($active_key);
				$msgbox_content = sprintf($lang['NewEmailNotActivated'], '<em>'.$session->sess_info['user_info']['name'].'</em>', $_POST['email']);
				break;
			case 2:
				$active = USER_INACTIVE;
				$active_key_md5 = '';
				$msgbox_content = sprintf($lang['NewEmailNotActivatedByAdmin'], '<em>'.$session->sess_info['user_info']['name'].'</em>', $_POST['email']);
			
		}
		
	} else {
		
		$active = $functions->user_active_value($session->sess_info['user_info']);
		$active_key_md5 = '';
		$msgbox_content = $lang['ProfileEdited'];
		
	}
	
	if ( !empty($_POST['birthday_month']) && valid_int($_POST['birthday_month']) && !empty($_POST['birthday_day']) && valid_int($_POST['birthday_day']) && !empty($_POST['birthday_year']) && valid_int($_POST['birthday_year']) )
		$birthday = sprintf('%04d%02d%02d', $_POST['birthday_year'], $_POST['birthday_month'], $_POST['birthday_day']);
	else
		$birthday = 0;
	
	//
	// Now update the users profile
	//
	$result = $db->query("UPDATE ".TABLE_PREFIX."members SET
		active        = ".$active.",
		active_key    = '".$active_key_md5."',
		email         = '".$_POST['email']."',
		avatar_type   = ".$avatar_type.",
		avatar_remote = '".$avatar_remote."',
		displayed_name = '".$_POST['displayed_name']."',
		real_name     = '".$_POST['real_name']."',
		location      = '".$_POST['location']."',
		website       = '".( $can_add_profile_links ? $_POST['website'] : $session->sess_info['user_info']['website'] )."',
		occupation    = '".$_POST['occupation']."',
		interests     = '".$_POST['interests']."',
		signature     = '".$_POST['signature']."',
		birthday      = '".$birthday."',
		msnm          = '".$_POST['msnm']."',
		yahoom        = '".$_POST['yahoom']."',
		aim           = '".$_POST['aim']."',
		icq           = '".$_POST['icq']."',
		jabber        = '".$_POST['jabber']."',
		skype         = '".$_POST['skype']."'
	WHERE id = ".$session->sess_info['user_info']['id']);
	
	//
	// Send correct e-mails
	//
	if ( $activation_necessary ) {
		
		switch ( intval($functions->get_config('activation_mode')) ) {
			
			case 1:
				$functions->usebb_mail($lang['NewEmailActivationEmailSubject'], $lang['NewEmailActivationEmailBody'], array(
					'account_name' => stripslashes($session->sess_info['user_info']['name']),
					'activate_link' => $functions->get_config('board_url').$functions->make_url('panel.php', array('act' => 'activate', 'id' => $session->sess_info['user_info']['id'], 'key' => $active_key), false)
				), $functions->get_config('board_name'), $functions->get_config('admin_email'), $_POST['email']);
				break;
			case 2:
				$functions->usebb_mail($lang['NewEmailAdminActivationEmailSubject'], $lang['NewEmailAdminActivationEmailBody'], array(
					'account_name' => stripslashes($session->sess_info['user_info']['name'])
				), $functions->get_config('board_name'), $functions->get_config('admin_email'), $_POST['email']);
			
		}
		
	}
	
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Note'],
		'content' => $msgbox_content
	));
	
} else {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		if ( $displayed_name_taken ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Note'],
				'content' => sprintf($lang['DisplayedNameTaken'], '<em>'.unhtml(stripslashes($_POST['displayed_name'])).'</em>')
			));
			
		} elseif ( $displayed_name_banned ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Note'],
				'content' => sprintf($lang['BannedUsername'], '<em>'.unhtml(stripslashes($_POST['displayed_name'])).'</em>')
			));
			
		}
		
		if ( $email_taken ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Note'],
				'content' => sprintf($lang['EmailTaken'], $_POST['email'])
			));
			
		} elseif ( $email_banned ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Note'],
				'content' => sprintf($lang['BannedEmail'], $_POST['email'])
			));
			
		}
		
		$errors = array();
		if ( empty($_POST['displayed_name']) )
			$errors[] = $lang['DisplayedName'];
		if ( !( ( empty($_POST['birthday_month']) && empty($_POST['birthday_day']) && empty($_POST['birthday_year']) ) || ( valid_int($_POST['birthday_month']) && valid_int($_POST['birthday_day']) && valid_int($_POST['birthday_year']) && checkdate($_POST['birthday_month'], $_POST['birthday_day'], $_POST['birthday_year']) ) ) )
			$errors[] = $lang['Birthday'];
		if ( empty($_POST['email']) || !$functions->validate_email($_POST['email']) )
			$errors[] = $lang['Email'];
		if ( !empty($_POST['avatar_remote']) && !preg_match(IMG_PREG, $_POST['avatar_remote']) )
			$errors[] = $lang['AvatarURL'];
		if ( $can_add_profile_links && !empty($_POST['website']) && !preg_match(WEB_PREG, $_POST['website']) )
			$errors[] = $lang['Website'];
		
		if ( count($errors) ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['MissingFields'], join(', ', $errors))
			));
			
		}
		
		if ( !empty($_POST['displayed_name']) && entities_strlen($_POST['displayed_name']) < $functions->get_config('username_min_length') ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['StringTooShort'], $lang['DisplayedName'], $functions->get_config('username_min_length'))
			));
			
		}
		
		if ( !empty($_POST['displayed_name']) && entities_strlen($_POST['displayed_name']) > $functions->get_config('username_max_length') ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['StringTooLong'], $lang['DisplayedName'], $functions->get_config('username_max_length'))
			));
			
		}
		
		if ( !empty($_POST['signature']) && entities_strlen($_POST['signature']) > $functions->get_config('sig_max_length') ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => sprintf($lang['StringTooLong'], $lang['Signature'], $functions->get_config('sig_max_length'))
			));
			
		}
		
	}
	
	//
	// Keep submitted info in the form, even when erroneous
	//
	$user_info = array();
	foreach ( $session->sess_info['user_info'] as $key => $val )
		$user_info[$key] = ( isset($_POST[$key]) ) ? $_POST[$key] : $val;
	
	list($birthday_year_input, $birthday_month_input, $birthday_day_input) = $functions->birthday_input_fields($session->sess_info['user_info']['birthday']);
	
	$textarea_rows = max(floor($template->get_config('textarea_rows') / 3), 3);

	if ( !$can_add_profile_links ) {

		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Note'],
			'content' => $lang['PotentialSpammerNoProfileLinks']
		));

	}
	
	$template->parse('edit_profile', 'panel', array(
		'form_begin'       => '<form action="'.$functions->make_url('panel.php', array('act' => 'editprofile')).'" method="post">',
		'username'         => $user_info['name'],
		'email_input'      => '<input type="text" size="50" maxlength="255" name="email" value="'.unhtml(stripslashes($user_info['email'])).'" />',
		'avatar_input'     => '<input type="text" size="50" maxlength="255" name="avatar_remote" value="'.unhtml(stripslashes($user_info['avatar_remote'])).'" />',
		'displayed_name_input'  => '<input type="text" size="50" maxlength="'.$functions->get_config('username_max_length').'" name="displayed_name" value="'.unhtml(stripslashes($user_info['displayed_name'])).'" />',
		'real_name_input'  => '<input type="text" size="50" maxlength="255" name="real_name" value="'.unhtml(stripslashes($user_info['real_name'])).'" />',
		'location_input'   => '<input type="text" size="50" maxlength="255" name="location" value="'.unhtml(stripslashes($user_info['location'])).'" />',
		'birthday_year_input' => $birthday_year_input,
		'birthday_month_input' => $birthday_month_input,
		'birthday_day_input' => $birthday_day_input,
		'website_input'    => $can_add_profile_links ? '<input type="text" size="50" maxlength="255" name="website" value="'.unhtml(stripslashes($user_info['website'])).'" />' : '<input type="text" size="50" value="- '.$lang['Disabled'].' -" disabled="disabled" />',
		'occupation_input' => '<input type="text" size="50" maxlength="255" name="occupation" value="'.unhtml(stripslashes($user_info['occupation'])).'" />',
		'interests_input'  => '<input type="text" size="50" maxlength="255" name="interests" value="'.unhtml(stripslashes($user_info['interests'])).'" />',
		'signature_input'  => '<textarea rows="'.$textarea_rows.'" cols="'.$template->get_config('textarea_cols').'" name="signature" id="tags-txtarea">'.unhtml(stripslashes($user_info['signature'])).'</textarea>',
		'bbcode_controls' => ( $functions->get_config('sig_allow_bbcode') ) ? $functions->get_bbcode_controls($can_add_profile_links) : '',
		'smiley_controls' => ( $functions->get_config('sig_allow_smilies') ) ? $functions->get_smiley_controls() : '',
		'msnm_input'       => '<input type="text" size="50" maxlength="255" name="msnm" value="'.unhtml(stripslashes($user_info['msnm'])).'" />',
		'yahoom_input'     => '<input type="text" size="50" maxlength="255" name="yahoom" value="'.unhtml(stripslashes($user_info['yahoom'])).'" />',
		'aim_input'        => '<input type="text" size="50" maxlength="255" name="aim" value="'.unhtml(stripslashes($user_info['aim'])).'" />',
		'icq_input'        => '<input type="text" size="50" maxlength="255" name="icq" value="'.unhtml(stripslashes($user_info['icq'])).'" />',
		'jabber_input'     => '<input type="text" size="50" maxlength="255" name="jabber" value="'.unhtml(stripslashes($user_info['jabber'])).'" />',
		'skype_input'      => '<input type="text" size="50" maxlength="255" name="skype" value="'.unhtml(stripslashes($user_info['skype'])).'" />',
		'visibility_info'  => ( $functions->get_config('guests_can_view_profiles') ) ? '' : '<div class="visibility-info">'.$lang['InvisibleToGuests'].'</div>',
		'submit_button'    => '<input type="submit" name="submit" value="'.$lang['OK'].'" />',
		'form_end'         => '</form>'
	), false, true);
	
}

?>
