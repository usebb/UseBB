<?php

/*
	Copyright (C) 2003-2004 UseBB Team
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
// Initialize a new database configuration holder array
//
$dbs = array();

//
// Define database configuration
//
$dbs['type'] = 'mysql';
$dbs['server'] = 'localhost';
$dbs['username'] = 'usebb';
$dbs['passwd'] = 'usebb';
$dbs['dbname'] = 'usebb';
$dbs['prefix'] = 'usebb_';

//
// Initialize a new configuration holder array
//
$conf = array();

//
// Define configuration
//
$conf['session_name'] = 'usebb';
$conf['debug'] = 0;
$conf['allow_multi_sess'] = 1;
$conf['sess_max_lifetime'] = '60';
$conf['template'] = 'default';
$conf['output_compression'] = 0;
$conf['language'] = 'English';
$conf['board_name'] = 'My Board';
$conf['board_descr'] = 'My Board';
$conf['enable_memberlist'] = 0;
$conf['enable_stafflist'] = 0;
$conf['enable_stats'] = 0;
$conf['enable_contactadmin'] = 1;
$conf['admin_email'] = 'pc_freak@telenet.be';
$conf['date_format'] = 'D M d, Y g:i a';
$conf['enable_rss'] = 0;
$conf['online_min_updated'] = '30';
$conf['cookie_domain'] = '';
$conf['cookie_secure'] = 0;
$conf['cookie_path'] = '';
$conf['users_must_activate'] = 0;
$conf['board_url'] = 'http://localhost/usebb/usebb/';
$conf['board_closed'] = 0;
$conf['board_closed_reason'] = 'Just closed...';
$conf['username_max_length'] = '25';
$conf['guests_can_view_profiles'] = 1;
$conf['guests_can_access_board'] = 1;
$conf['email_view_level'] = 1;
$conf['view_hidden_email_addresses_min_level'] = '3';
$conf['guests_can_view_online_list'] = 1;
$conf['enable_online_list'] = 0;
$conf['sig_allow_bbcode'] = 1;
$conf['sig_allow_smilies'] = 1;
$conf['timezone'] = 0;
$conf['dst'] = 0;
$conf['enable_quickreply'] = 1;
$conf['return_to_topic_after_posting'] = 1;
$conf['kick_user_to_only_viewable_forum'] = 0;
$conf['available_languages'] = array('English');
$conf['available_templates'] = array('default');
$conf['target_blank'] = 0;
$conf['hide_avatars'] = 0;
$conf['hide_signatures'] = 0;