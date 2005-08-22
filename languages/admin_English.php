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
$lang['IndexOS'] = 'Operating system';
$lang['IndexLinks'] = 'Links';

$lang['VersionFailed'] = 'The forum could not determine the latest version (%s disabled). Please often check %s to make sure you have the latest one.';
$lang['VersionLatestVersion'] = 'This forum is powered by UseBB %s which is the latest stable version.';
$lang['VersionNeedUpdate'] = 'This forum running UseBB %s needs to be updated to version %s to stay secure and bug free! Visit %s to download the latest version.';
$lang['VersionBewareDevVersions'] = 'This forum is running %s however %s is still the latest stable version. Beware of the problems and uncompatibilities that might exist with development versions.';

$lang['ConfigInfo'] = 'On this page you can edit all settings of your UseBB forum. Be careful altering the database configuration. Fields marked with an asterisk (*) are required.';
$lang['ConfigSet'] = 'The new configuration has been set. It will be visible upon loading a new page.';
$lang['ConfigMissingFields'] = 'Some fields were missing or incorrect (i.e. text were a number was expected). Please check the following fields:';
$lang['ConfigBoard-type'] = 'Type';
$lang['ConfigBoard-server'] = 'Server';
$lang['ConfigBoard-username'] = 'Username';
$lang['ConfigBoard-passwd'] = 'Password';
$lang['ConfigBoard-dbname'] = 'Database name';
$lang['ConfigBoard-prefix'] = 'Table prefix';
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
$lang['ConfigBoard-search_limit_results'] = 'Limit search results to x items';
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

$lang['CategoriesInfo'] = 'This section gives you the control over the various categories that exist at your board.';
$lang['CategoriesAddNewCat'] = 'Add a new category';
$lang['CategoriesAdjustSortIDs'] = 'Adjust the sort ID\'s';
$lang['CategoriesSortAutomatically'] = 'Sort categories automatically';
$lang['CategoriesNoCatsExist'] = 'This board does not contain any categories yet.';
$lang['CategoriesCatName'] = 'Category name';
$lang['CategoriesSortID'] = 'Sort ID';
$lang['CategoriesMissingFields'] = 'Some required fields were missing. Please fill them in correctly.';
$lang['CategoriesSortChangesApplied'] = 'Your changes to the sort ID\'s have been applied.';
$lang['CategoriesConfirmCatDelete'] = 'Confirm category deletion';
$lang['CategoriesConfirmCatDeleteContent'] = 'Are you sure you want to delete the category %s? This action is irreversible!';
$lang['CategoriesMoveContents'] = 'Move the contents of the category to %s';
$lang['CategoriesDeleteContents'] = 'Delete the contents';
$lang['CategoriesEditingCat'] = 'Editing category %s';

$lang['ForumsInfo'] = 'This section gives you the control over the various forums that exist at your board.';
$lang['ForumsAddNewForum'] = 'Add a new forum';
$lang['ForumsAdjustSortIDs'] = 'Adjust the sort ID\'s';
$lang['ForumsSortAutomatically'] = 'Sort forums automatically';
$lang['ForumsNoForumsExist'] = 'This board does not contain any forums yet.';
$lang['ForumsForumName'] = 'Forum name';
$lang['ForumsCatName'] = 'Parent category';
$lang['ForumsDescription'] = 'Description';
$lang['ForumsStatus'] = 'Status';
$lang['ForumsStatusOpen'] = 'Open';
$lang['ForumsAutoLock'] = 'Auto lock topics after x replies';
$lang['ForumsIncreasePostCount'] = 'Increase users\' post count';
$lang['ForumsHideModsList'] = 'Hide moderator list';
$lang['ForumsSortID'] = 'Sort ID';
$lang['ForumsMissingFields'] = 'Some required fields were missing. Please fill them in correctly.';
$lang['ForumsSortChangesApplied'] = 'Your changes to the sort ID\'s have been applied.';
$lang['ForumsConfirmForumDelete'] = 'Confirm forum deletion';
$lang['ForumsConfirmForumDeleteContent'] = 'Are you sure you want to delete the forum %s? This action is irreversible!';
$lang['ForumsMoveContents'] = 'Move the contents of the forum to %s';
$lang['ForumsDeleteContents'] = 'Delete the contents';
$lang['ForumsEditingForum'] = 'Editing forum %s';
$lang['ForumsGeneral'] = 'General settings';
$lang['ForumsAuth'] = 'Authorization settings';
$lang['ForumsAuthNote'] = 'Settings do not inherit each other!';
$lang['Forums-level0'] = 'Guests';
$lang['Forums-level1'] = 'Members';
$lang['Forums-level2'] = 'Moderators';
$lang['Forums-level3'] = 'Administrators';
$lang['Forums-auth0'] = 'View forum';
$lang['Forums-auth1'] = 'Read topics';
$lang['Forums-auth2'] = 'Post new topics';
$lang['Forums-auth3'] = 'Reply to topics';
$lang['Forums-auth4'] = 'Edit other\'s posts';
$lang['Forums-auth5'] = 'Move topics';
$lang['Forums-auth6'] = 'Delete topics and posts';
$lang['Forums-auth7'] = 'Lock topics';
$lang['Forums-auth8'] = 'Sticky topics';
$lang['Forums-auth9'] = 'Post as HTML (dangerous)';

$lang['IPLookupInfo'] = 'Enter an IP address to lookup the corresponding hostname.';
$lang['IPLookupResult'] = 'The hostname corresponding to the IP address %s is %s.';
$lang['IPLookupNotFound'] = 'No corresponding hostname for %s could be found.';

$lang['SQLToolboxWarningTitle'] = 'Important Warning!';
$lang['SQLToolboxWarningContent'] = 'Be very careful using the raw query tool. Executing ALTER, DELETE, TRUNCTATE or other types of queries may irreversibly damage your forum! Only use this when you know what you are doing.';
$lang['SQLToolboxExecuteQuery'] = 'Execute Query';
$lang['SQLToolboxExecuteQueryInfo'] = 'Enter an SQL query to execute. Eventually, results will be shown in a second text box.';
$lang['SQLToolboxExecute'] = 'Execute';
$lang['SQLToolboxExecutedSuccessfully'] = 'Query executed successfully.';
$lang['SQLToolboxMaintenance'] = 'Maintenance';
$lang['SQLToolboxMaintenanceInfo'] = 'These functions optimize (and repair) the SQL tables used by UseBB. Optimizing the tables often enough is recommended for larger boards.';
$lang['SQLToolboxRepairTables'] = 'Repair tables';
$lang['SQLToolboxOptimizeTables'] = 'Optimize tables';
$lang['SQLToolboxMaintenanceNote'] = 'Note: this does not restore any lost data in the database.';

?>
