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

//
// Page footer
//
$link_bar = array();
if ( $functions->get_user_level() == 3 )
	$link_bar[] = '<a href="'.$functions->make_url('admin.php').'">'.$lang['ACP'].'</a>';
	
if ( $functions->get_config('enable_memberlist') && $functions->get_user_level() >= $functions->get_config('view_memberlist_min_level') )
	$link_bar[] = '<a href="'.$functions->make_url('members.php').'">'.$lang['MemberList'].'</a>';
	
if ( $functions->get_config('enable_stafflist') && $functions->get_user_level() >= $functions->get_config('view_stafflist_min_level') )
	$link_bar[] = '<a href="'.$functions->make_url('members.php', array('act' => 'staff')).'">'.$lang['StaffList'].'</a>';
	
if ( $functions->get_config('enable_rss') && $functions->get_user_level() >= $functions->get_config('view_rss_min_level') )
	$link_bar[] = '<a href="'.$functions->make_url('rss.php').'">'.$lang['RSSFeed'].'</a>';
	
if ( $functions->get_config('enable_stats') && $functions->get_user_level() >= $functions->get_config('view_stats_min_level') )
	$link_bar[] = '<a href="'.$functions->make_url('stats.php').'">'.$lang['Statistics'].'</a>';
	
if ( $functions->get_config('enable_contactadmin') && $functions->get_user_level() >= $functions->get_config('view_contactadmin_min_level') )
	$link_bar[] = '<a href="mailto:'.$functions->get_config('admin_email').'">'.$lang['ContactAdmin'].'</a>';

#####
# We request not to remove the following copyright notice including the link to the UseBB Home Page.
# This shows your respect to everyone involved in UseBB's development and promotes the use of UseBB.
# If you don't want to show the Copyright notice, just leave the Powered by UseBB line. If you
# completely alter or remove the notice, support at our community forums will be affected.
#####

$template->parse('normal_footer', 'global', array(
	'link_bar' => ( count($link_bar) ) ? join(' '.$template->get_config('item_delimiter').' ', $link_bar) : '',
	'usebb_version' => USEBB_VERSION
));

//
// Output the page body
//
$template->body();

//
// Disconnect database connection
//
$db->disconnect();

?>
