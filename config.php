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
 * Configuration
 *
 * Contains configuration settings.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2012 UseBB Team
 * @package	UseBB
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
$dbs['persistent'] = 0;

//
// Initialize a new configuration holder array
//
$conf = array();

//
// Define configuration
//
$conf['acp_auto_logout'] = 10;
$conf['activation_mode'] = 1;
$conf['active_topics_count'] = 25;
$conf['active_topics_max_age'] = 30;
$conf['admin_email'] = 'example@example.net';
$conf['allow_multi_sess'] = 1;
$conf['allow_multi_sess_per_user'] = 0;
$conf['allow_duplicate_emails'] = 0;
$conf['antispam_disable_post_links'] = 0;
$conf['antispam_disable_profile_links'] = 0;
$conf['antispam_status_max_posts'] = 0;
$conf['antispam_status_for_guests'] = 0;
$conf['antispam_question_mode'] = 0;
$conf['antispam_question_questions'] = array ();
$conf['avatars_force_width'] = 0;
$conf['avatars_force_height'] = 0;
$conf['board_closed'] = 0;
$conf['board_closed_reason'] = 'Just closed...';
$conf['board_descr'] = 'My board\'s description';
$conf['board_keywords'] = 'forum,board,community,usebb';
$conf['board_name'] = 'My Community';
$conf['board_url'] = '';
$conf['contactadmin_custom_url'] = '';
$conf['cookie_domain'] = '';
$conf['cookie_httponly'] = 1;
$conf['cookie_path'] = '';
$conf['cookie_secure'] = 0;
$conf['date_format'] = 'D M d, Y g:i a';
$conf['debug'] = 0;
$conf['disable_registrations'] = 0;
$conf['disable_registrations_reason'] = 'No new users allowed at this time.';
$conf['disable_xhtml_header'] = 1;
$conf['dnsbl_powered_banning_globally'] = 0;
$conf['dnsbl_powered_banning_min_hits'] = 2;
$conf['dnsbl_powered_banning_recheck_minutes'] = 0;
$conf['dnsbl_powered_banning_servers'] = array ();
$conf['dnsbl_powered_banning_whitelist'] = array (  0 => '127.0.0.1',  1 => '*.googlebot.com',);
$conf['dst'] = 0;
$conf['edit_post_timeout'] = 900;
$conf['email_view_level'] = 1;
$conf['email_reply-to_header'] = 0;
$conf['enable_acp_modules'] = 0;
$conf['enable_badwords_filter'] = 0;
$conf['enable_contactadmin'] = 1;
$conf['enable_contactadmin_form'] = 1;
$conf['enable_detailed_online_list'] = 1;
$conf['enable_dnsbl_powered_banning'] = 0;
$conf['enable_email_dns_check'] = 0;
$conf['enable_error_log'] = 0;
$conf['enable_forum_stats_box'] = 1;
$conf['enable_ip_bans'] = 0;
$conf['enable_memberlist'] = 1;
$conf['enable_quickreply'] = 1;
$conf['enable_registration_log'] = 0;
$conf['enable_rss'] = 1;
$conf['enable_rss_per_forum'] = 1;
$conf['enable_rss_per_topic'] = 1;
$conf['enable_stafflist'] = 0;
$conf['enable_stats'] = 0;
$conf['error_log_log_hidden'] = 0;
$conf['exclude_forums_active_topics'] = array ();
$conf['exclude_forums_rss'] = array ();
$conf['exclude_forums_stats'] = array ();
$conf['flood_interval'] = 30;
$conf['friendly_urls'] = 0;
$conf['force_latin1_db'] = 0;
$conf['ga_account'] = '';
$conf['ga_domain'] = '';
$conf['ga_mode'] = 0;
$conf['guests_can_access_board'] = 1;
$conf['guests_can_see_contact_info'] = 0;
$conf['guests_can_view_profiles'] = 0;
$conf['hide_avatars'] = 0;
$conf['hide_db_config_acp'] = 0;
$conf['hide_signatures'] = 0;
$conf['hide_userinfo'] = 0;
$conf['language'] = 'English';
$conf['mass_email_msg_recipients'] = 50;
$conf['members_per_page'] = 25;
$conf['online_min_updated'] = 30;
$conf['output_compression'] = 0;
$conf['passwd_min_length'] = 6;
$conf['posts_per_page'] = 25;
$conf['registration_log_file'] = '';
$conf['rel_nofollow'] = 0;
$conf['return_to_topic_after_posting'] = 1;
$conf['rss_items_count'] = 25;
$conf['search_limit_results'] = 500;
$conf['search_nonindex_words_min_length'] = 3;
$conf['sendmail_sender_parameter'] = 0;
$conf['session_max_lifetime'] = 60;
$conf['session_name'] = 'usebb';
$conf['session_save_path'] = '';
$conf['sfs_api_key'] = '';
$conf['sfs_email_check'] = 0;
$conf['sfs_max_lastseen'] = 0;
$conf['sfs_min_frequency'] = 0;
$conf['sfs_save_bans'] = 0;
$conf['show_edited_message_timeout'] = 120;
$conf['show_never_activated_members'] = 0;
$conf['show_posting_links_to_guests'] = 1;
$conf['show_raw_entities_in_code'] = 1;
$conf['sig_allow_bbcode'] = 1;
$conf['sig_allow_smilies'] = 1;
$conf['sig_max_length'] = 500;
$conf['single_forum_mode'] = 0;
$conf['target_blank'] = 0;
$conf['template'] = 'default';
$conf['timezone'] = 0;
$conf['topicreview_posts'] = 5;
$conf['topics_per_page'] = 25;
$conf['username_min_length'] = 3;
$conf['username_max_length'] = 30;
$conf['view_active_topics_min_level'] = 0;
$conf['view_contactadmin_min_level'] = 1;
$conf['view_detailed_online_list_min_level'] = 1;
$conf['view_forum_stats_box_min_level'] = 1;
$conf['view_hidden_email_addresses_min_level'] = 3;
$conf['view_memberlist_min_level'] = 1;
$conf['view_search_min_level'] = 0;
$conf['view_stafflist_min_level'] = 0;
$conf['view_stats_min_level'] = 1;

?>
