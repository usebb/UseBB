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
$conf['admin_email'] = 'email@example.net';
$conf['allow_multi_sess'] = 1;
$conf['auto_free_sql_results'] = 1;
$conf['available_languages'] = array('English', 'Nederlands');
$conf['available_templates'] = array('default');
$conf['avatars_force_width'] = 65;
$conf['avatars_force_height'] = 65;
$conf['board_closed'] = 0;
$conf['board_closed_reason'] = 'Just closed...';
$conf['board_descr'] = 'My board\'s description';
$conf['board_name'] = 'My Community';
$conf['board_url'] = '';
$conf['cookie_domain'] = '';
$conf['cookie_path'] = '';
$conf['cookie_secure'] = 0;
$conf['date_format'] = 'D M d, Y g:i a';
$conf['debug'] = 1;
$conf['disable_info_emails'] = 0;
$conf['dst'] = 0;
$conf['email_view_level'] = 1;
$conf['enable_contactadmin'] = 1;
$conf['enable_detailed_online_list'] = 1;
$conf['enable_forum_stats_box'] = 1;
$conf['enable_memberlist'] = 1;
$conf['enable_quickreply'] = 1;
$conf['enable_rss'] = 1;
$conf['enable_stafflist'] = 1;
$conf['enable_stats'] = 1;
#$conf['exclude_forums_active_topics'] = array(7);
$conf['flood_interval'] = 30;
$conf['friendly_urls'] = 1;
$conf['guests_can_access_board'] = 1;
$conf['guests_can_view_profiles'] = 1;
$conf['hide_avatars'] = 0;
$conf['hide_signatures'] = 0;
$conf['hide_undefined_config_setting_warnings'] = 1;
$conf['hide_undefined_template_setting_warnings'] = 0;
$conf['hide_undefined_template_warnings'] = 0;
$conf['hide_userinfo'] = 0;
$conf['kick_user_to_only_viewable_forum'] = 0;
$conf['language'] = 'English';
$conf['members_per_page'] = 25;
$conf['online_min_updated'] = 30;
$conf['output_compression'] = 2;
$conf['passwd_min_length'] = 8;
$conf['posts_per_page'] = 25;
$conf['rel_nofollow'] = 0;
$conf['return_to_topic_after_posting'] = 1;
$conf['session_max_lifetime'] = 60;
$conf['session_name'] = 'usebb';
$conf['session_save_path'] = '';
$conf['sig_allow_bbcode'] = 1;
$conf['sig_allow_smilies'] = 1;
$conf['target_blank'] = 0;
$conf['template'] = 'default';
$conf['timezone'] = 0;
$conf['topicreview_posts'] = 5;
$conf['topics_per_page'] = 25;
$conf['trash_forum'] = 0;
$conf['username_max_length'] = 25;
$conf['users_must_activate'] = 1;
$conf['view_detailed_online_list_min_level'] = 1;
$conf['view_forum_stats_box_min_level'] = 0;
$conf['view_hidden_email_addresses_min_level'] = 3;
$conf['view_memberlist_min_level'] = 0;
$conf['view_stafflist_min_level'] = 0;
$conf['view_stats_min_level'] = 1;
$conf['view_contactadmin_min_level'] = 1;

?>
