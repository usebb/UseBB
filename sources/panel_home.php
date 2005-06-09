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

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

if ( count($_COOKIE) >= 1 && isset($_GET['al']) && valid_int($_GET['al']) ) {
	
	if ( $_GET['al'] ) {
		
		//
		// Set the AL cookie
		//
		$functions->set_al($session->sess_info['user_id'], $session->sess_info['user_info']['passwd']);
		$msgbox_content = $lang['AutoLoginSet'];
		
	} elseif ( !$_GET['al'] ) {
		
		//
		// Unset the AL cookie
		//
		$functions->unset_al();
		$msgbox_content = $lang['AutoLoginUnset'];
		
	}
	
	$template->parse('msgbox', 'global', array(
		'box_title' => $lang['Note'],
		'content' => $msgbox_content
	));
	
} else {
	
	//
	// Some various session information
	//
	if ( count($_COOKIE) < 1 ) {
		
		$al_status = $lang['Disabled'];
		$al_change = $lang['FeatureDisabledBecauseCookiesDisabled'];
		
	} elseif ( $functions->isset_al() ) {
		
		$al_status = $lang['Enabled'];
		$al_change = '<a href="'.$functions->make_url('panel.php', array('al' => 0)).'">'.$lang['Disable'].'</a>';
		
	} else {
		
		$al_status = $lang['Disabled'];
		$al_change = '<a href="'.$functions->make_url('panel.php', array('al' => 1)).'">'.$lang['Enable'].'</a>';
		
	}
	
	$total_time = $functions->time_past($session->sess_info['started'], $session->sess_info['updated']);
	
	$template->parse('sess_info', 'panel', array(
		'sess_id_v' => $session->sess_info['sess_id'],
		'ip_addr_v' => $session->sess_info['ip_addr'],
		'started_v' => $functions->make_date($session->sess_info['started']),
		'updated_v' => $functions->make_date($session->sess_info['updated']),
		'total_time_v' => $total_time[1],
		'pages_v' => $session->sess_info['pages'],
		'al_status' => $al_status,
		'al_change' => $al_change
	));
	
}

?>
