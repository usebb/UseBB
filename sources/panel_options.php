<?php

/*
    Copyright (C) 2003-2004 UseBB Team
	http://usebb.sourceforge.net

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

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

//
// Set the page title
//
$template->set_page_title($lang['EditOptions']);

if ( !empty($_POST['submitted']) ) {
	
	//
	// Update the user's preferences
	//
	$email_show = ( isset($_POST['email_show']) ) ? 1 : 0;
	$last_login_show = ( isset($_POST['last_login_show']) ) ? 1 : 0;
	
	if ( !($result = $db->query("UPDATE ".TABLE_PREFIX."users SET
		email_show      = ".$email_show.",
		last_login_show = ".$last_login_show.",
		date_format     = '".$_POST['date_format']."'
	WHERE id = ".$sess_info['user_info']['id'])) )
		usebb_die('SQL', 'Unable to update user information!', __FILE__, __LINE__);
	
	header('Location: index.php');
	exit();
	
}

$email_show_checked = ( $sess_info['user_info']['email_show'] ) ? ' checked="checked"' : '';
$last_login_show_checked = ( $sess_info['user_info']['last_login_show'] ) ? ' checked="checked"' : '';

$template->parse('edit_options', array(
	'form_begin'     => '<form action="'.usebb_make_url('panel.php', array('a' => 'editoptions')).'" method="post">',
	'edit_options'    => $lang['EditOptions'],
	'email_show' => $lang['PublicEmail'],
	'email_show_input' => '<input type="checkbox" name="email_show" id="email_show" value="yes"'.$email_show_checked.' /> <label for="email_show">'.$lang['Yes'].'</label>',
	'last_login_show' => $lang['PublicLastLogin'],
	'last_login_show_input' => '<input type="checkbox" name="last_login_show" id="last_login_show" value="yes"'.$last_login_show_checked.' /> <label for="last_login_show">'.$lang['Yes'].'</label>',
	'date_format' => $lang['DateFormat'],
	'date_format_input'     => '<input type="text" name="date_format" size="25" maxlength="255" value="'.$config['date_format'].'" />',
	'submit_button'    => '<input type="submit" name="submit" value="'.$lang['EditOptions'].'" />',
	'reset_button'     => '<input type="reset" value="'.$lang['Reset'].'" />',
	'form_end'         => '<input type="hidden" name="submitted" value="true" /></form>'
));

?>