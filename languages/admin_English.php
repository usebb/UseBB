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

$lang['AdminLogin'] = 'Admin Login';
$lang['AdminPasswordExplain'] = 'For security reasons, you must enter your account\'s password to login into the ACP.';

$lang['Category-main'] = 'General';
$lang['Item-index'] = 'ACP Index';
$lang['Item-version'] = 'Version Check';
$lang['Item-config'] = 'General Configuration';
$lang['Category-forums'] = 'Forums';
$lang['Item-categories'] = 'Categories';
$lang['Item-forums'] = 'Forums';
$lang['Category-various'] = 'Various';
$lang['Item-iplookup'] = 'IP Address Lookup';
$lang['Item-sqltoolbox'] = 'SQL Toolbox';

$lang['IndexWelcome'] = 'Welcome to the Admin Control Panel of your UseBB forum. From here you can control all aspects of your board, setting the configuration, control forums, members, etc.';
$lang['IndexSystemInfo'] = 'System Info';
$lang['IndexUseBBVersion'] = 'UseBB version';
$lang['IndexPHPVersion'] = 'PHP version';
$lang['IndexSQLServer'] = 'SQL server driver';
$lang['IndexHTTPServer'] = 'HTTP server';
$lang['IndexLinks'] = 'Links';

$lang['VersionFailed'] = 'The forum could not determine the latest version (%s disabled). Please often check %s to make sure you have the latest one.';
$lang['VersionLatestVersion'] = 'This forum is powered by UseBB %s which is the latest stable version.';
$lang['VersionNeedUpdate'] = 'This forum running UseBB %s needs to be updated to version %s to stay secure and bug free! Visit %s to download the latest version.';
$lang['VersionBewareDevVersions'] = 'This forum is running %s however %s is still the latest stable version. Beware of the problems and uncompatibilities that might exist with development versions.';

$lang['ConfigInfo'] = 'On this page you can edit all settings of your UseBB forum. Be careful altering the database configuration. Fields marked with an asterisk (*) are required.';
$lang['ConfigSet'] = 'The new configuration has been set. It will be visible upon loading a new page.';
$lang['ConfigMissingFields'] = 'Some fields were missing or incorrect (i.e. text were a number was expected). Please check the fields marked with an asterisk (*).';
$lang['ConfigDB-type'] = 'Type';
$lang['ConfigDB-server'] = 'Server';
$lang['ConfigDB-username'] = 'Username';
$lang['ConfigDB-passwd'] = 'Password';
$lang['ConfigDB-dbname'] = 'Database name';
$lang['ConfigDB-prefix'] = 'Table prefix';
$lang['ConfigBoardSection-general'] = 'General board';
$lang['ConfigBoardSection-cookies'] = 'Cookies';
$lang['ConfigBoardSection-sessions'] = 'Sessions';
$lang['ConfigBoardSection-page_counts'] = 'Page counts';
$lang['ConfigBoardSection-date_time'] = 'Dates &amp; Times';
$lang['ConfigBoardSection-database'] = 'Database configuration';
$lang['ConfigBoardSection-advanced'] = 'Advanced settings';
$lang['ConfigBoardSection-email'] = 'E-mail';
$lang['ConfigBoardSection-additional'] = 'Additional features';
$lang['ConfigBoardSection-user_rights'] = 'User rights';
$lang['ConfigBoardSection-layout'] = 'Layout settings';
$lang['ConfigBoard-admin_email'] = 'Admin e-mail address';
$lang['ConfigBoard-board_descr'] = 'Board description';
$lang['ConfigBoard-board_keywords'] = 'Board keywords (comma seperated)';
$lang['ConfigBoard-board_name'] = 'Board name';
$lang['ConfigBoard-date_format'] = 'Date format';
$lang['ConfigBoard-language'] = 'Default language';
$lang['ConfigBoard-session_name'] = 'Session name';
$lang['ConfigBoard-template'] = 'Default template';
$lang['ConfigBoard-active_topics_count'] = 'Active topics count';
$lang['ConfigBoard-avatars_force_width'] = 'Force avatars width (px)';
$lang['ConfigBoard-avatars_force_height'] = 'Force avatars height (px)';
$lang['ConfigBoard-debug'] = 'Debug mode';
$lang['ConfigBoard-email_view_level'] = 'E-mail view level';
$lang['ConfigBoard-flood_interval'] = 'Flood interval (seconds)';
$lang['ConfigBoard-members_per_page'] = 'Members per page';
$lang['ConfigBoard-online_min_updated'] = 'Online users during last minutes';
$lang['ConfigBoard-output_compression'] = 'Output compression';
$lang['ConfigBoard-passwd_min_length'] = 'Password minimum length';
$lang['ConfigBoard-posts_per_page'] = 'Posts per page';
$lang['ConfigBoard-rss_items_count'] = 'RSS items count';
$lang['ConfigBoard-search_nonindex_words_min_length'] = 'Search keyword minimum length';
$lang['ConfigBoard-session_max_lifetime'] = 'Maximum session lifetime (seconds)';
$lang['ConfigBoard-show_edited_message_timeout'] = 'Show edited message timeout (seconds)';
$lang['ConfigBoard-topicreview_posts'] = 'Topic review post count';
$lang['ConfigBoard-topics_per_page'] = 'Topics per page';
$lang['ConfigBoard-username_max_length'] = 'Maximum username length';
$lang['ConfigBoard-view_detailed_online_list_min_level'] = 'Minimum level for viewing detailed online list';
$lang['ConfigBoard-view_forum_stats_box_min_level'] = 'Minimum level for viewing statistics box';
$lang['ConfigBoard-view_hidden_email_addresses_min_level'] = 'Minimum level for viewing hidden e-mail addresses';
$lang['ConfigBoard-view_memberlist_min_level'] = 'Minimum level for viewing member list';
$lang['ConfigBoard-view_stafflist_min_level'] = 'Minimum level for viewing staff list';
$lang['ConfigBoard-view_stats_min_level'] = 'Minimum level for viewing statistics page';
$lang['ConfigBoard-view_contactadmin_min_level'] = 'Minimum level for viewing contact admin link';
$lang['ConfigBoard-allow_multi_sess'] = 'Allow multiple sessions per IP';
$lang['ConfigBoard-board_closed'] = 'Close the board';
$lang['ConfigBoard-cookie_secure'] = 'Secure cookies (for HTTPS)';
$lang['ConfigBoard-disable_info_emails'] = 'Disable informational e-mails';
$lang['ConfigBoard-dst'] = 'Daylight saving times';
$lang['ConfigBoard-enable_contactadmin'] = 'Enable contact admin link';
$lang['ConfigBoard-enable_detailed_online_list'] = 'Enable detailed online list';
$lang['ConfigBoard-enable_forum_stats_box'] = 'Enable forum statistics box';
$lang['ConfigBoard-enable_memberlist'] = 'Enable member list';
$lang['ConfigBoard-enable_quickreply'] = 'Enable quick reply';
$lang['ConfigBoard-enable_rss'] = 'Enable RSS feed';
$lang['ConfigBoard-enable_stafflist'] = 'Enable staff list';
$lang['ConfigBoard-enable_stats'] = 'Enable statistics page';
$lang['ConfigBoard-friendly_urls'] = 'Enable friendly URL\'s';
$lang['ConfigBoard-guests_can_access_board'] = 'Guests can access the board';
$lang['ConfigBoard-guests_can_view_profiles'] = 'Guests can view member profiles';
$lang['ConfigBoard-hide_avatars'] = 'Hide all avatars';
$lang['ConfigBoard-hide_signatures'] = 'Hide all signatures';
$lang['ConfigBoard-hide_userinfo'] = 'Hide user information';
$lang['ConfigBoard-rel_nofollow'] = 'Enable Google\'s nofollow for BBCode links';
$lang['ConfigBoard-return_to_topic_after_posting'] = 'Return to the topic after posting';
$lang['ConfigBoard-sig_allow_bbcode'] = 'Enable BBCode in signatures';
$lang['ConfigBoard-sig_allow_smilies'] = 'Enable smilies in signatures';
$lang['ConfigBoard-single_forum_mode'] = 'Single forum mode (when applicable)';
$lang['ConfigBoard-target_blank'] = 'BBCode links open new window';
$lang['ConfigBoard-users_must_activate'] = 'Users must activate via e-mail';
$lang['ConfigBoard-board_closed_reason'] = 'Board closed reason';
$lang['ConfigBoard-board_url'] = 'Board URL (empty for auto-detect)';
$lang['ConfigBoard-cookie_domain'] = 'Cookie domain';
$lang['ConfigBoard-cookie_path'] = 'Cookie path';
$lang['ConfigBoard-session_save_path'] = 'Session save path';
$lang['ConfigBoard-exclude_forums_active_topics'] = 'Exclude forums from active topics';
$lang['ConfigBoard-exclude_forums_rss'] = 'Exclude forums from RSS feed';
$lang['ConfigBoard-exclude_forums_stats'] = 'Exclude forums from statistics page';
$lang['ConfigBoard-timezone'] = 'Timezone';
$lang['ConfigBoard-debug0'] = 'Disabled';
$lang['ConfigBoard-debug1'] = 'Simple debug information';
$lang['ConfigBoard-debug2'] = 'Extended debug information';
$lang['ConfigBoard-email_view_level0'] = 'Hide all e-mail addresses';
$lang['ConfigBoard-email_view_level1'] = 'Enable e-mail form';
$lang['ConfigBoard-email_view_level2'] = 'Show spam proof';
$lang['ConfigBoard-email_view_level3'] = 'Show raw';
$lang['ConfigBoard-output_compression0'] = 'Disabled';
$lang['ConfigBoard-output_compression1'] = 'Compress HTML';
$lang['ConfigBoard-output_compression2'] = 'Enable gzip';
$lang['ConfigBoard-output_compression3'] = 'Compress HTML and enable gzip';
$lang['ConfigBoard-level0'] = 'Guests';
$lang['ConfigBoard-level1'] = 'Members';
$lang['ConfigBoard-level2'] = 'Moderators';
$lang['ConfigBoard-level3'] = 'Administrators';

$lang['IPLookupResult'] = 'The hostname corresponding to the IP address %s is %s.';
$lang['IPLookupNotFound'] = 'No corresponding hostname for %s could be found.';

$lang['SQLToolboxWarningTitle'] = 'Important Warning!';
$lang['SQLToolboxWarningContent'] = 'Be very careful using the raw query tool. Executing ALTER, DELETE, TRUNCTATE or other types of queries may irreversibly damage your forum! Only use this when you know what you are doing.';
$lang['SQLToolboxExecuteQuery'] = 'Execute Query';
$lang['SQLToolboxExecute'] = 'Execute';
$lang['SQLToolboxMaintenance'] = 'Maintenance';
$lang['SQLToolboxRepairTables'] = 'Repair tables';
$lang['SQLToolboxOptimizeTables'] = 'Optimize tables';
$lang['SQLToolboxExecutedSuccessfully'] = 'Query executed successfully.';

?>
