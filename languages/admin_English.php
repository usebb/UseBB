<?php

/*
	Copyright (C) 2003-2006 UseBB Team
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

$lang['RunningBadACPModule'] = 'UseBB can not run this module because one or more aspects are missing (no $usebb_module object found and/or missing run_module() object method).';

$lang['Category-main'] = 'General';
$lang['Item-index'] = 'ACP Index';
$lang['Item-version'] = 'Version Check';
$lang['Item-config'] = 'General Configuration';
$lang['Category-forums'] = 'Forums';
$lang['Item-categories'] = 'Manage Categories';
$lang['Item-forums'] = 'Manage Forums';
$lang['Category-various'] = 'Various';
$lang['Item-iplookup'] = 'IP Address Lookup';
$lang['Item-sqltoolbox'] = 'SQL Toolbox';
$lang['Item-modules'] = 'ACP Modules';
$lang['Category-members'] = 'Members';
$lang['Item-members'] = 'Edit Members';
$lang['Item-delete_members'] = 'Delete Members';
$lang['Item-register_members'] = 'Register Members';
$lang['Item-activate_members'] = 'Activate Members';
$lang['Item-prune_forums'] = 'Prune Forums';
$lang['Item-prune_members'] = 'Prune Members';
$lang['Category-pruning'] = 'Pruning';
$lang['Item-dnsbl'] = 'DNSBL Bans';

$lang['IndexWelcome'] = 'Welcome to the Admin Control Panel of your UseBB forum. From here you can control all aspects of your board, setting the configuration, control forums, members, etc.';
$lang['IndexSystemInfo'] = 'System Info';
$lang['IndexUseBBVersion'] = 'UseBB version';
$lang['IndexPHPVersion'] = 'PHP version';
$lang['IndexSQLServer'] = 'SQL server driver';
$lang['IndexHTTPServer'] = 'HTTP server';
$lang['IndexOS'] = 'Operating system';
$lang['IndexLinks'] = 'Links';
$lang['IndexUnactiveMembers'] = 'Unactivated Members';
$lang['IndexNoUnactiveMembers'] = 'There are no members awaiting admin activation.';
$lang['IndexOneUnactiveMember'] = 'There is one member awaiting admin activation.';
$lang['IndexMoreUnactiveMembers'] = 'There are %d members awaiting admin activation.';
$lang['IndexWarning'] = 'Warning!';
$lang['IndexUnwritableConfig'] = 'At this moment, %s is not writable by PHP. This might break ACP modules or modifications. To make it writable, change the file\'s permissions with a FTP client or perform a chmod operation on it. Contact your host in case of problems.';

$lang['VersionFailed'] = 'The forum could not determine the latest version (%s disabled). Please often check %s to make sure you have the latest one.';
$lang['VersionLatestVersionTitle'] = 'This is the latest version';
$lang['VersionLatestVersion'] = 'This forum is powered by UseBB %s which is the latest stable version.';
$lang['VersionNeedUpdateTitle'] = 'New version available!';
$lang['VersionNeedUpdate'] = 'This forum running UseBB %s needs to be updated to version %s to stay secure and bug free! Visit %s to download the latest version.';
$lang['VersionBewareDevVersionsTitle'] = 'Development version found';
$lang['VersionBewareDevVersions'] = 'This forum is running %s however %s is still the latest stable version. Beware of the problems and uncompatibilities that might exist with development versions.';

$lang['ConfigInfo'] = 'On this page you can edit all settings of your UseBB forum. Be careful altering the database configuration. Fields marked with an asterisk (*) are required.';
$lang['ConfigSet'] = 'The new configuration has been set. It will be visible upon loading a new page.';
$lang['ConfigMissingFields'] = 'Some fields were missing or incorrect (i.e. text where a number was expected). Please check the following fields:';
$lang['ConfigBoard-type'] = 'Type';
$lang['ConfigBoard-server'] = 'Server';
$lang['ConfigBoard-username'] = 'Username';
$lang['ConfigBoard-passwd'] = 'Password';
$lang['ConfigBoard-dbname'] = 'Database name';
$lang['ConfigBoard-prefix'] = 'Table prefix';
$lang['ConfigBoardSection-general'] = 'General board';
$lang['ConfigBoardSection-cookies'] = 'Cookies';
$lang['ConfigBoardSection-cookies-info'] = 'You may leave these blank for auto-detection.';
$lang['ConfigBoardSection-sessions'] = 'Sessions';
$lang['ConfigBoardSection-page_counts'] = 'Page counts';
$lang['ConfigBoardSection-date_time'] = 'Dates &amp; Times';
$lang['ConfigBoardSection-database'] = 'Database configuration';
$lang['ConfigBoardSection-database-info'] = 'Only change this when you are sure the settings will work immediately.';
$lang['ConfigBoardSection-advanced'] = 'Advanced settings';
$lang['ConfigBoardSection-email'] = 'E-mail';
$lang['ConfigBoardSection-additional'] = 'Additional features';
$lang['ConfigBoardSection-user_rights'] = 'User rights';
$lang['ConfigBoardSection-min_levels'] = 'Minimum access levels';
$lang['ConfigBoardSection-min_levels-info'] = 'These are the minimal levels required to access each item.';
$lang['ConfigBoardSection-layout'] = 'Layout settings';
$lang['ConfigBoardSection-security'] = 'Security';
$lang['ConfigBoard-admin_email'] = 'Admin e-mail address';
$lang['ConfigBoard-board_descr'] = 'Board description';
$lang['ConfigBoard-board_keywords'] = 'Board keywords';
$lang['ConfigBoard-board_keywords-info'] = 'Seperated by commas.';
$lang['ConfigBoard-board_name'] = 'Board name';
$lang['ConfigBoard-date_format'] = 'Date format';
$lang['ConfigBoard-date_format-info'] = 'Same syntax as PHP\'s date().';
$lang['ConfigBoard-language'] = 'Default language';
$lang['ConfigBoard-session_name'] = 'Session name';
$lang['ConfigBoard-session_name-info'] = 'Only alphanumeric characters, no spaces. Must contain at least one letter.';
$lang['ConfigBoard-template'] = 'Default template';
$lang['ConfigBoard-active_topics_count'] = 'Active topics count';
$lang['ConfigBoard-avatars_force_height'] = 'Force avatars height (px)';
$lang['ConfigBoard-avatars_force_width'] = 'Force avatars width (px)';
$lang['ConfigBoard-avatars_force_width-info'] = 'Leave empty or zero to disable.';
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
$lang['ConfigBoard-session_max_lifetime'] = 'Maximum session lifetime (minutes)';
$lang['ConfigBoard-show_edited_message_timeout'] = 'Edited message note timeout';
$lang['ConfigBoard-show_edited_message_timeout-info'] = 'When the post was edited in these seconds after posting, the edited note remains hidden.';
$lang['ConfigBoard-topicreview_posts'] = 'Topic review post count';
$lang['ConfigBoard-topics_per_page'] = 'Topics per page';
$lang['ConfigBoard-view_active_topics_min_level'] = 'Active topics';
$lang['ConfigBoard-view_detailed_online_list_min_level'] = 'Detailed online list';
$lang['ConfigBoard-view_forum_stats_box_min_level'] = 'Statistics box';
$lang['ConfigBoard-view_hidden_email_addresses_min_level'] = 'Minimum level for viewing hidden e-mail addresses';
$lang['ConfigBoard-view_memberlist_min_level'] = 'Member list';
$lang['ConfigBoard-view_search_min_level'] = 'Search engine';
$lang['ConfigBoard-view_stafflist_min_level'] = 'Staff list';
$lang['ConfigBoard-view_stats_min_level'] = 'Statistics page';
$lang['ConfigBoard-view_contactadmin_min_level'] = 'Contact admin link';
$lang['ConfigBoard-allow_multi_sess'] = 'Allow multiple sessions per IP';
$lang['ConfigBoard-board_closed'] = 'Close the board';
$lang['ConfigBoard-board_closed-info'] = 'Admins will still be able to log in.';
$lang['ConfigBoard-cookie_secure'] = 'Secure cookies';
$lang['ConfigBoard-cookie_secure-info'] = 'Encrypted cookies (HTTPS only)';
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
$lang['ConfigBoard-friendly_urls-info'] = 'Requires Apache and mod_rewrite. Will disable URL session ID\'s.';
$lang['ConfigBoard-guests_can_access_board'] = 'Guests can access the board';
$lang['ConfigBoard-guests_can_see_contact_info'] = 'Guests can see contact information in profiles';
$lang['ConfigBoard-guests_can_view_profiles'] = 'Guests can view member profiles';
$lang['ConfigBoard-hide_avatars'] = 'Hide all avatars';
$lang['ConfigBoard-hide_signatures'] = 'Hide all signatures';
$lang['ConfigBoard-hide_userinfo'] = 'Hide user information';
$lang['ConfigBoard-rel_nofollow'] = 'Enable Google\'s nofollow';
$lang['ConfigBoard-rel_nofollow-info'] = 'This will make Google ignore all BBCode links.';
$lang['ConfigBoard-return_to_topic_after_posting'] = 'Return to the topic after posting';
$lang['ConfigBoard-sig_allow_bbcode'] = 'Enable BBCode in signatures';
$lang['ConfigBoard-sig_allow_smilies'] = 'Enable smilies in signatures';
$lang['ConfigBoard-sig_max_length'] = 'Max signature length';
$lang['ConfigBoard-single_forum_mode'] = 'Single forum mode';
$lang['ConfigBoard-single_forum_mode-info'] = 'Displays only visible forum as forum index.';
$lang['ConfigBoard-target_blank'] = 'BBCode links open new window';
$lang['ConfigBoard-target_blank-info'] = 'Might break XHTML validation!';
$lang['ConfigBoard-activation_mode'] = 'Activation mode';
$lang['ConfigBoard-activation_mode0'] = 'No activation';
$lang['ConfigBoard-activation_mode1'] = 'E-mail activation';
$lang['ConfigBoard-activation_mode2'] = 'Admin activation';
$lang['ConfigBoard-board_closed_reason'] = 'Board closed reason';
$lang['ConfigBoard-board_url'] = 'Board URL';
$lang['ConfigBoard-board_url-info'] = 'Complete URL including trailing slash; blank for auto-detect.';
$lang['ConfigBoard-cookie_domain'] = 'Cookie domain';
$lang['ConfigBoard-cookie_path'] = 'Cookie path';
$lang['ConfigBoard-session_save_path'] = 'Session save path';
$lang['ConfigBoard-session_save_path-info'] = 'Custom session data save path; only absolute directory names.';
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
$lang['ConfigBoard-output_compression2'] = 'Enable Gzip';
$lang['ConfigBoard-output_compression3'] = 'Compress HTML + Gzip';
$lang['ConfigBoard-level0'] = 'Guests';
$lang['ConfigBoard-level1'] = 'Members';
$lang['ConfigBoard-level2'] = 'Moderators';
$lang['ConfigBoard-level3'] = 'Administrators';
$lang['ConfigBoard-enable_acp_modules'] = 'Enable ACP modules';
$lang['ConfigBoard-disable_registrations'] = 'Disable user registrations';
$lang['ConfigBoard-disable_registrations-info'] = 'Users can still be registered via the ACP.';
$lang['ConfigBoard-disable_registrations_reason'] = 'Disable user registrations reason';
$lang['ConfigBoard-allow_duplicate_emails'] = 'Allow duplicate e-mail addresses';
$lang['ConfigBoard-enable_badwords_filter'] = 'Enable badwords filter';
$lang['ConfigBoard-enable_ip_bans'] = 'Enable IP address banning';
$lang['ConfigBoard-show_raw_entities_in_code'] = 'Show raw entities in [code] tags.';
$lang['ConfigBoard-show_raw_entities_in_code-info'] = 'Show the raw entity code instead of its HTML representation.';
$lang['ConfigBoard-username_min_length'] = 'Username minimum length';
$lang['ConfigBoard-username_max_length'] = 'Username maximum length';
$lang['ConfigBoard-show_never_activated_members'] = 'Show never activated members';
$lang['ConfigBoard-show_never_activated_members-info'] = 'Show these on the stats box and member list.';
$lang['ConfigBoard-enable_registration_log'] = 'Enable registration log';
$lang['ConfigBoard-enable_registration_log-info'] = 'Writes a registration log in a text file.';
$lang['ConfigBoard-registration_log_file'] = 'Registration log file';
$lang['ConfigBoard-registration_log_file-info'] = 'Relative to the forum\'s directory, or absolute path.';

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
$lang['ForumsModerators'] = 'Moderators';
$lang['ForumsModeratorsExplain'] = 'Usernames (not displayed names), seperated by commas. Case-insensitive.';
$lang['ForumsModeratorsUnknown'] = 'Unknown member(s): %s.';
$lang['ForumsHideModsList'] = 'Hide moderator list';
$lang['ForumsSortID'] = 'Sort ID';
$lang['ForumsMissingFields'] = 'Some required fields were missing. Please fill them in correctly.';
$lang['ForumsSortChangesApplied'] = 'Your changes to the sort ID\'s have been applied.';
$lang['ForumsConfirmForumDelete'] = 'Confirm forum deletion';
$lang['ForumsConfirmForumDeleteContent'] = 'Are you sure you want to delete the forum %s? This action is irreversible!';
$lang['ForumsMoveContents'] = 'Move the contents of the forum to %s';
$lang['ForumsMoveModerators'] = 'When moving contents, also move moderators.';
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

$lang['IPLookupSearchHostname'] = 'Search hostname';
$lang['IPLookupSearchUsernames'] = 'Search username(s)';
$lang['IPLookupResult'] = 'The hostname corresponding to the IP address %s is %s.';
$lang['IPLookupNotFound'] = 'No corresponding hostname for %s could be found.';
$lang['IPLookupUsernamesSingular'] = 'The username %s was used by %s to post messages.';
$lang['IPLookupUsernamesPlural'] = 'The %d usernames %s were used by %s to post messages.';
$lang['IPLookupUsernamesNotFound'] = 'No usernames for %s could be found.';

$lang['SQLToolboxWarningTitle'] = 'Important Warning!';
$lang['SQLToolboxWarningContent'] = 'Be very careful using the raw query tool. Executing ALTER, DELETE, TRUNCATE or other types of queries may irreversibly damage your forum! Only use this when you know what you are doing.';
$lang['SQLToolboxExecuteQuery'] = 'Execute Query';
$lang['SQLToolboxExecuteQueryInfo'] = 'Enter an SQL query to execute. Eventually, results will be shown in a second text box.';
$lang['SQLToolboxExecute'] = 'Execute';
$lang['SQLToolboxExecutedSuccessfully'] = 'Query executed successfully.';
$lang['SQLToolboxMaintenance'] = 'Maintenance';
$lang['SQLToolboxMaintenanceInfo'] = 'These functions optimize (and repair) the SQL tables used by UseBB. Optimizing the tables often enough is recommended for larger boards.';
$lang['SQLToolboxRepairTables'] = 'Repair tables';
$lang['SQLToolboxOptimizeTables'] = 'Optimize tables';
$lang['SQLToolboxMaintenanceNote'] = 'Note: this does not restore any lost data in the database.';

$lang['ModulesInfo'] = 'ACP modules able you to extend the ACP with your own features or features made by 3rd party programmers. Modules can be found via the UseBB website: %s.';
$lang['ModulesLongName'] = 'Long name';
$lang['ModulesShortName'] = 'Short name';
$lang['ModulesCategory'] = 'Category';
$lang['ModulesFilename'] = 'Filename';
$lang['ModulesDeleteNotPermitted'] = 'Not permitted';
$lang['ModulesDisabled'] = 'ACP modules have been disabled in the board configuration.';
$lang['ModulesNoneAvailable'] = 'No modules are available at this time.';
$lang['ModulesUpload'] = 'Upload module';
$lang['ModulesUploadInfo'] = 'Enter a local filename of a UseBB ACP module to upload it.';
$lang['ModulesUploadDuplicateModule'] = 'A module under the filename %s already exists. Please remove it first.';
$lang['ModulesUploadNoValidModule'] = 'The file %s is not a valid UseBB module.';
$lang['ModulesUploadFailed'] = 'Could not install the module %s. Copying failed.';
$lang['ModulesUploadDisabled'] = 'The module directory is not writable. Uploading has been disabled. To enable, make the directory %s writable by the webserver (try chmod 777).';
$lang['ModulesConfirmModuleDelete'] = 'Confirm module deletion';
$lang['ModulesConfirmModuleDeleteInfo'] = 'Are you sure you want to delete the module %s (%s)?';

$lang['MembersSearchMember'] = 'Search member';
$lang['MembersSearchMemberInfo'] = 'Enter a (part of a) username or displayed name to edit.';
$lang['MembersSearchMemberExplain'] = 'Username or displayed name';
$lang['MembersSearchMemberNotFound'] = 'No members with username or displayed name %s found.';
$lang['MembersSearchMemberList'] = 'The following members were found';
$lang['MembersEditingMember'] = 'Editing member %s';
$lang['MembersEditingMemberInfo'] = 'Update the user\'s info and submit the form. Fields marked with an asterisk (*) are required.';
$lang['MembersEditingMemberUsernameExists'] = 'The username %s already exists as a username or displayed name.';
$lang['MembersEditingMemberDisplayedNameExists'] = 'The displayed name %s already exists as a username or displayed name.';
$lang['MembersEditingMemberBanned'] = 'Banned';
$lang['MembersEditingMemberBannedReason'] = 'Reason for ban';
$lang['MembersEditingMemberCantChangeOwnLevel'] = 'You can\'t change your own level.';
$lang['MembersEditingMemberCantBanSelf'] = 'You can\'t ban yourself.';
$lang['MembersEditingComplete'] = 'The profile of the member %s was edited successfully.';

$lang['DeleteMembersSearchMember'] = 'Search member';
$lang['DeleteMembersSearchMemberInfo'] = 'Enter a (part of a) username or displayed name to delete.';
$lang['DeleteMembersSearchMemberExplain'] = 'Username or displayed name';
$lang['DeleteMembersSearchMemberNotFound'] = 'No members with username or displayed name %s found.';
$lang['DeleteMembersSearchMemberList'] = 'The following members were found';
$lang['DeleteMembersConfirmMemberDelete'] = 'Confirm member deletion';
$lang['DeleteMembersConfirmMemberDeleteContent'] = 'Are you sure you want to delete the member %s? This is irreversible!';
$lang['DeleteMembersComplete'] = 'Deletion of member %s complete.';

$lang['RegisterMembersExplain'] = 'Here you can preregister member accounts. Just fill in the following information to create an account.';
$lang['RegisterMembersComplete'] = 'Registration of user %s is complete. The user can log in right away.';

$lang['ActivateMembersExplain'] = 'This is a list of unactivated members on your forum. Here you can approve accounts manually. An asterisk (*) means the user account has been active before.';
$lang['ActivateMembersNoMembers'] = 'No members to list.';
$lang['ActivateMembersListAdmin'] = 'Admin approval';
$lang['ActivateMembersListEmail'] = 'Email approval';
$lang['ActivateMembersListAll'] = 'All';

$lang['PruneForumsStart'] = 'Start Pruning';
$lang['PruneForumsExplain'] = 'By pruning forums, you can delete or move old topics and keep your forum clean.';
$lang['PruneForumsForums'] = 'Forums to prune';
$lang['PruneForumsAction'] = 'Action';
$lang['PruneForumsActionLock'] = 'Lock';
$lang['PruneForumsActionMove'] = 'Move';
$lang['PruneForumsActionDelete'] = 'Delete';
$lang['PruneForumsMoveTo'] = 'Move topics to';
$lang['PruneForumsTopicAge'] = 'Topic age';
$lang['PruneForumsTopicAgeField'] = 'Last reply %s days ago.';
$lang['PruneForumsMoveToForumSelectedForPruning'] = 'The &quot;move to&quot; forum cannot be selected for pruning.';
$lang['PruneForumsConfirm'] = 'Confirm';
$lang['PruneForumsConfirmText'] = 'I understand this action is irreversible.';
$lang['PruneForumsNotConfirmed'] = 'You need to confirm this action first.';
$lang['PruneForumsDone'] = 'Pruning has completed. %d topics were pruned.';
$lang['PruneForumsExcludeStickies'] = 'Exclude sticky topics';

$lang['PruneMembersExplain'] = 'By pruning members you can clean up the forum\'s user base by removing unactivated or inactive user accounts.';
$lang['PruneMembersTypeNeverActivated'] = 'Never activated members';
$lang['PruneMembersRegisteredDaysAgo'] = 'Registered at least %s days ago.';
$lang['PruneMembersTypeNeverPosted'] = 'Members that never posted';
$lang['PruneMembersTypeInactive'] = 'Inactive members';
$lang['PruneMembersLastLoggedIn'] = 'Logged in the last time at least %s days ago.';
$lang['PruneMembersExclude'] = 'Exclude';
$lang['PruneMembersConfirmText'] = 'I understand this action is irreversible.';
$lang['PruneMembersStart'] = 'Start Pruning';
$lang['PruneMembersNotConfirmed'] = 'You need to confirm this action first.';
$lang['PruneMembersDone'] = 'Pruning has completed. %d members were pruned.';

$lang['DNSBLIPBansDisabled'] = 'IP address banning disabled';
$lang['DNSBLIPBansDisabledInfo'] = 'For DNSBL powered banning to work, IP address banning must be enabled.';
$lang['DNSBLNotAvailable'] = 'DNSBL banning unavailable';
$lang['DNSBLNotAvailableInfo'] = 'DNSBL banning is not possible on this server due to the absense of %s. This is normal if you are on Windows. Consider migrating to GNU/Linux.';
$lang['DNSBLGeneralInfo'] = 'Open proxies are often used to post spam or abusive messages. Using UseBB\'s protection system, most of these proxies can be detected and banned automatically. Herefore blacklists are queried for information about the visitor\'s IP address.';
$lang['DNSBLEnableOpenDNSBLBan'] = 'Enable DNSBL powered banning';
$lang['DNSBLServers'] = 'DNS BlackList (DNSBL) servers';
$lang['DNSBLMinPositiveHits'] = 'At least %s positive hits are required to ban an IP address.';
$lang['DNSBLRecheckMinutes'] = 'Recheck allowed IP addresses every %s minutes (0 to disable).';
$lang['DNSBLEnableOpenDNSBLBanWildcard'] = 'Enable wildcard banning %s (not recommended)';
$lang['DNSBLUnwantedBansInfo'] = 'Some blacklists are quite aggressive and may block safe IP addresses (SORBS aggregate also blocks some dynamic IP ranges).';
$lang['DNSBLSlownessInfo'] = 'Note using many of these blacklists together may cause slowness upon creating a new session on your forum.';
$lang['DNSBLAggregatesInfo'] = 'Some of these servers are aggregates that query multiple blacklists at once. Please check whether you don\'t include a blacklist twice. Indirectly querying a blacklist twice causes useless requests and may be considered an abuse.';
$lang['DNSBLHighTrafficInfo'] = 'If you have a high traffic forum, consider contacting the blacklists\' administration for help first, before using them over here.';
$lang['DNSBLSettingsSaved'] = 'DNSBL banning settings saved.';
$lang['DNSBLWhitelist'] = 'Whitelist';
$lang['DNSBLWhitelistInfo'] = 'One IP address or hostname per line (* and ? can be used as wildcards).';

?>
