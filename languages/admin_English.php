<?php

/*
	Copyright (C) 2003-2011 UseBB Team
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

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

$lang['AdminLogin'] = 'Admin Login';
$lang['AdminPasswordExplain'] = 'For security reasons, you must enter your account\'s password to login into the ACP.';

$lang['RunningBadACPModule'] = 'UseBB can not run this module because one or more aspects are missing (no $usebb_module object found and/or missing run_module() object method).';
$lang['RunningACPModuleMinVersion'] = 'To run this module, UseBB version %s or later is required.';

$lang['Category-main'] = 'General';
$lang['Category-forums'] = 'Forums';
$lang['Category-various'] = 'Various';
$lang['Category-members'] = 'Members';
$lang['Category-pruning'] = 'Pruning';
$lang['Category-security'] = 'Security';
$lang['Item-index'] = 'ACP Index';
$lang['Item-version'] = 'Version Check';
$lang['Item-config'] = 'General Configuration';
$lang['Item-categories'] = 'Manage Categories';
$lang['Item-forums'] = 'Manage Forums';
$lang['Item-iplookup'] = 'IP Address Lookup';
$lang['Item-sqltoolbox'] = 'SQL Toolbox';
$lang['Item-modules'] = 'ACP Modules';
$lang['Item-members'] = 'Edit Members';
$lang['Item-delete_members'] = 'Delete Members';
$lang['Item-register_members'] = 'Register Members';
$lang['Item-activate_members'] = 'Activate Members';
$lang['Item-prune_forums'] = 'Prune Forums';
$lang['Item-prune_members'] = 'Prune Members';
$lang['Item-dnsbl'] = 'DNSBL Bans';
$lang['Item-badwords'] = 'Badwords Filter';
$lang['Item-mass_email'] = 'Mass Email';
$lang['Item-bans'] = 'Banning Management';
$lang['Item-logout'] = 'ACP Log Out';

$lang['IndexWelcome'] = 'Welcome to the Admin Control Panel of your UseBB forum. From here you can control all aspects of your board, setting the configuration, control forums, members, etc.';
$lang['IndexSystemInfo'] = 'System Info';
$lang['IndexUseBBVersion'] = 'UseBB version';
$lang['IndexPHPVersion'] = 'PHP version';
$lang['IndexSQLServer'] = 'SQL server driver';
$lang['IndexHTTPServer'] = 'HTTP server';
$lang['IndexOS'] = 'Operating system';
$lang['IndexServerLoad'] = 'Server load values';
$lang['IndexLinks'] = 'Links';
$lang['IndexUnactiveMembers'] = 'Unactivated Members';
$lang['IndexNoUnactiveMembers'] = 'There are no members awaiting admin activation.';
$lang['IndexOneUnactiveMember'] = 'There is one member awaiting admin activation.';
$lang['IndexMoreUnactiveMembers'] = 'There are %d members awaiting admin activation.';
$lang['IndexWarning'] = 'Warning!';
$lang['IndexUnwritableConfig'] = 'At this moment, %s is not writable by PHP. To make it writable, change the file\'s permissions with a FTP client or perform a chmod operation on it. Contact your host in case of problems. When not writable, you will be offered to download the updated file.';
$lang['IndexMultibyteUsage'] = 'Please note that you are currently using a translation written in a multibyte character set (%s). These translations and character sets are not officially supported on UseBB 1.';
$lang['IndexDevelopmentEnvironment'] = 'UseBB is currently set to be running in a development environment. This will show possible PHP notices to users and does not include certain security measures. On production environments this is not advised.';

$lang['VersionFailed'] = 'The forum could not determine the latest version. Please often check %s to make sure you have the latest one.';
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
$lang['ConfigBoard-persistent'] = 'Persistent connection';
$lang['ConfigBoardSection-general'] = 'General board';
$lang['ConfigBoardSection-cookies'] = 'Cookies';
$lang['ConfigBoardSection-cookies-info'] = 'You may leave these blank for auto-detection.';
$lang['ConfigBoardSection-sessions'] = 'Sessions';
$lang['ConfigBoardSection-page_counts'] = 'Page counts';
$lang['ConfigBoardSection-date_time'] = 'Dates &amp; Times';
$lang['ConfigBoardSection-date_time-info'] = 'Only applies to guests and new accounts.';
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
$lang['ConfigBoardSection-rss'] = 'RSS feeds';
$lang['ConfigBoardSection-anti_spam'] = 'Anti-spam';
$lang['ConfigBoardSection-anti_spam-info'] = 'For more information and recommendations, see the <a href="./docs/anti-spam.html">Spam Protection</a> document.';
$lang['ConfigBoard-admin_email'] = 'Admin e-mail address';
$lang['ConfigBoard-board_descr'] = 'Board description';
$lang['ConfigBoard-board_keywords'] = 'Board keywords';
$lang['ConfigBoard-board_keywords-info'] = 'Separated by commas.';
$lang['ConfigBoard-board_name'] = 'Board name';
$lang['ConfigBoard-date_format'] = 'Date format';
$lang['ConfigBoard-date_format-info'] = 'Same syntax as PHP\'s date().';
$lang['ConfigBoard-language'] = 'Default language';
$lang['ConfigBoard-language-info'] = 'Only applies to guests and new accounts.';
$lang['ConfigBoard-session_name'] = 'Session name';
$lang['ConfigBoard-session_name-info'] = 'Only alphanumeric characters, no spaces. Must contain at least one letter.';
$lang['ConfigBoard-template'] = 'Default template';
$lang['ConfigBoard-template-info'] = 'Only applies to guests and new accounts.';
$lang['ConfigBoard-active_topics_count'] = 'Active topics count';
$lang['ConfigBoard-active_topics_count-info'] = 'Keeping into account the setting \'Active topics maximum age\', less topics may be displayed.';
$lang['ConfigBoard-active_topics_max_age'] = 'Active topics maximum age';
$lang['ConfigBoard-active_topics_max_age-info'] = 'Maximum age (last reply) in days, 0 to disable.';
$lang['ConfigBoard-avatars_force_height'] = 'Max avatar height (px)';
$lang['ConfigBoard-avatars_force_height-info'] = 'Zero for unlimited.';
$lang['ConfigBoard-avatars_force_width'] = 'Max avatar width (px)';
$lang['ConfigBoard-avatars_force_width-info'] = 'Zero for unlimited.';
$lang['ConfigBoard-debug'] = 'Debug mode';
$lang['ConfigBoard-debug-info'] = 'Extended only works when not in production environment (set in source code).';
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
$lang['ConfigBoard-contactadmin_custom_url'] = 'Custom URL for &quot;Contact Admin&quot;';
$lang['ConfigBoard-contactadmin_custom_url-info'] = 'Absolute or relative URL to be used as link &ndash; overrides email link and form.';
$lang['ConfigBoard-cookie_secure'] = 'Secure cookies';
$lang['ConfigBoard-cookie_secure-info'] = 'Encrypted cookies (HTTPS only)';
$lang['ConfigBoard-cookie_httponly'] = 'Enable HTTP only cookies';
$lang['ConfigBoard-cookie_httponly-info'] = 'Adds a HttpOnly flag to the cookies, making them more secured against XSS.';
$lang['ConfigBoard-dst'] = 'Daylight saving times';
$lang['ConfigBoard-enable_contactadmin'] = 'Enable contact admin link';
$lang['ConfigBoard-enable_contactadmin_form'] = 'Use mail form instead of email link for contact admin';
$lang['ConfigBoard-enable_detailed_online_list'] = 'Enable detailed online list';
$lang['ConfigBoard-enable_forum_stats_box'] = 'Enable forum statistics box';
$lang['ConfigBoard-enable_memberlist'] = 'Enable member list';
$lang['ConfigBoard-enable_quickreply'] = 'Enable quick reply';
$lang['ConfigBoard-enable_rss'] = 'Enable general (active topics) RSS feed';
$lang['ConfigBoard-enable_rss_per_forum'] = 'Enable separate forum RSS feeds';
$lang['ConfigBoard-enable_rss_per_topic'] = 'Enable separate topic RSS feeds';
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
$lang['ConfigBoard-return_to_topic_after_posting-info'] = 'Only applies to guests and new accounts.';
$lang['ConfigBoard-sig_allow_bbcode'] = 'Enable BBCode in signatures';
$lang['ConfigBoard-sig_allow_smilies'] = 'Enable smilies in signatures';
$lang['ConfigBoard-sig_max_length'] = 'Max signature length';
$lang['ConfigBoard-single_forum_mode'] = 'Single forum mode';
$lang['ConfigBoard-single_forum_mode-info'] = 'Displays only visible forum as forum index.';
$lang['ConfigBoard-target_blank'] = 'BBCode links open new window';
$lang['ConfigBoard-target_blank-info'] = 'Only applies to guests and new accounts.';
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
$lang['ConfigBoard-exclude_forums_rss'] = 'Exclude forums from general RSS feed';
$lang['ConfigBoard-exclude_forums_stats'] = 'Exclude forums from statistics page';
$lang['ConfigBoard-timezone'] = 'Timezone';
$lang['ConfigBoard-debug0'] = 'Disabled';
$lang['ConfigBoard-debug1'] = 'Simple (parse time and counts)';
$lang['ConfigBoard-debug2'] = 'Extended (simple + SQL queries)';
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
$lang['ConfigBoard-enable_email_dns_check'] = 'Enable email address DNS checking';
$lang['ConfigBoard-enable_email_dns_check-info'] = 'Validates by looking for MX records. This may not work on all valid domains.';
$lang['ConfigBoard-edit_post_timeout'] = 'Edit post timeout';
$lang['ConfigBoard-edit_post_timeout-info'] = 'A user is only allowed to edit his posts within x seconds after posting.';
$lang['ConfigBoard-disable_xhtml_header'] = 'Disable XHTML header for XHTML templates.';
$lang['ConfigBoard-disable_xhtml_header-info'] = 'An XHTML Content-Type can only be used when the content is 100% well-formed. This is always disabled for non-XHTML browsers.';
$lang['ConfigBoard-email_reply-to_header'] = 'Use Reply-To header';
$lang['ConfigBoard-email_reply-to_header-info'] = 'Use Reply-To instead of From for user\'s email address (required on some hosts).';
$lang['ConfigBoard-mass_email_msg_recipients'] = 'Mass email message recipient count';
$lang['ConfigBoard-mass_email_msg_recipients-info'] = 'Multiple messages will be sent until all recipients have been mailed.';
$lang['ConfigBoard-sendmail_sender_parameter'] = 'Enable sendmail -f parameter.';
$lang['ConfigBoard-sendmail_sender_parameter-info'] = 'This might break the email functionality on some hosts.';
$lang['ConfigBoard-antispam_question_mode'] = 'Anti-spam question mode';
$lang['ConfigBoard-antispam_question_mode-info'] = 'Poses guests a question before they are given access to the registration, new topic and reply forms.';
$lang['ConfigBoard-antispam_question_mode0'] = 'Disabled';
$lang['ConfigBoard-antispam_question_mode1'] = 'Random math question';
$lang['ConfigBoard-antispam_question_mode2'] = 'Randomly chosen custom question';
$lang['ConfigBoard-antispam_question_questions'] = 'Custom anti-spam questions';
$lang['ConfigBoard-antispam_question_questions-info'] = 'Questions in the form of <code>question|answer</code>, separated by newlines. The answer is case-insensitive.';
$lang['ConfigBoard-enable_error_log'] = 'Enable error log';
$lang['ConfigBoard-enable_error_log-info'] = 'Logs errors using PHP\'s logging mechanism. See PHP error_log configuration option.';
$lang['ConfigBoard-error_log_log_hidden'] = 'Log errors otherwise hidden';
$lang['ConfigBoard-error_log_log_hidden-info'] = 'On production environments, some error types are hidden for users. Enabling this will still log them if possible.';
$lang['ConfigBoard-show_posting_links_to_guests'] = 'Show new topic and post reply links to guests.';
$lang['ConfigBoard-show_posting_links_to_guests-info'] = 'Shown if members can post. &ndash; Will redirect to login.';
$lang['ConfigBoard-acp_auto_logout'] = 'Auto logout from ACP after x minutes inactivity';
$lang['ConfigBoard-acp_auto_logout-info'] = 'This is disabled for pages with large forms, such as General Configuration.';
$lang['ConfigBoard-enable_dnsbl_powered_banning'] = 'Enable DNSBL powered banning';
$lang['ConfigBoard-enable_dnsbl_powered_banning-info'] = 'Requires IP address banning to be enabled.';
$lang['ConfigBoard-sfs_email_check'] = 'Stop Forum Spam: check email addresses';
$lang['ConfigBoard-sfs_email_check-info'] = 'Limited to 20,000 checks per day. See stopforumspam.com for more info.';
$lang['ConfigBoard-sfs_max_lastseen'] = 'Stop Forum Spam: maximum &quot;last seen&quot;';
$lang['ConfigBoard-sfs_max_lastseen-info'] = 'Do not block if last seen more than x days ago (0 to ignore)';
$lang['ConfigBoard-sfs_min_frequency'] = 'Stop Forum Spam: minimum frequency';
$lang['ConfigBoard-sfs_min_frequency-info'] = 'Do not block if spam frequency is lower than x (0 to ignore)';
$lang['ConfigBoard-sfs_save_bans'] = 'Stop Forum Spam: save blocks in forum\'s ban table';
$lang['ConfigBoard-sfs_save_bans-info'] = 'These addresses will remain blocked and will not be rechecked.';
$lang['ConfigBoard-sfs_api_key'] = 'Stop Forum Spam: API key';
$lang['ConfigBoard-sfs_api_key-info'] = 'Required for submissions only. See stopforumspam.com for requesting keys.';
$lang['ConfigBoard-Passwd-NotSet'] = 'Not set';
$lang['ConfigBoard-Passwd-KeepCurrent'] = 'Keep current';
$lang['ConfigBoard-Passwd-Set'] = 'Set';
$lang['ConfigBoard-Passwd-Clear'] = 'Clear';
$lang['ConfigBoard-antispam_status_max_posts'] = 'Potential spammer: maximum post count';
$lang['ConfigBoard-antispam_status_max_posts-info'] = 'Post count required before automatic removal of the potential spammer status (0 to disable).';
$lang['ConfigBoard-antispam_disable_post_links'] = 'Potential spammer: disable post links';
$lang['ConfigBoard-antispam_disable_post_links-info'] = 'Links in potential spammer\'s posts remain unclickable.';
$lang['ConfigBoard-antispam_disable_profile_links'] = 'Potential spammer: disable profile links';
$lang['ConfigBoard-antispam_disable_profile_links-info'] = 'Potential spammers can not set website field or use links in signature.';
$lang['ConfigBoard-antispam_status_for_guests'] = 'Potential spammer: apply to guests';
$lang['ConfigBoard-antispam_status_for_guests-info'] = 'Make the restrictions apply to guests as well.';
$lang['ConfigBoard-ga_mode'] = 'Google Analytics mode';
$lang['ConfigBoard-ga_mode0'] = 'A single domain';
$lang['ConfigBoard-ga_mode1'] = 'One domain with multiple subdomains';
$lang['ConfigBoard-ga_mode2'] = 'Multiple top-level domains';
$lang['ConfigBoard-ga_mode-info'] = 'Also see your Analytics account settings under \'Tracking Code\'.';
$lang['ConfigBoard-ga_account'] = 'Google Analytics Web Property ID';
$lang['ConfigBoard-ga_account-info'] = 'Will add Analytics code to your forum\'s web pages. Often in the form of <code>UA-xxxxxxx-x</code>. Empty to disable.';
$lang['ConfigBoard-ga_domain'] = 'Google Analytics domain';
$lang['ConfigBoard-ga_domain-info'] = 'Required for multiple (sub)domains.';
$lang['ConfigBoard-allow_multi_sess_per_user'] = 'Allow multiple sessions per user';
$lang['ConfigBoard-allow_multi_sess_per_user-info'] = 'When disabled, upon login all other sessions for the current user will be removed.';

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
$lang['HTMLEnabledField'] = 'This is a HTML enabled field. If you want to use special characters, make sure to use their respective HTML entities (for example &amp;amp; instead of &amp;).';
$lang['ForumsStatus'] = 'Status';
$lang['ForumsStatusOpen'] = 'Open';
$lang['ForumsAutoLock'] = 'Auto lock';
$lang['ForumsAutoLockXReplies'] = 'Lock topics after %s replies.';
$lang['ForumsIncreasePostCount'] = 'Increase users\' post count';
$lang['ForumsModerators'] = 'Moderators';
$lang['ForumsModeratorsExplain'] = 'Usernames (not displayed names), separated by commas. Case-insensitive.';
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
$lang['IPLookupHostname'] = 'Hostname';
$lang['IPLookupHostnameNotFound'] = 'No corresponding hostname found.';
$lang['IPLookupUsernames'] = 'Usernames';
$lang['IPLookupUsernamesNotFound'] = 'No corresponding usernames found.';

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
$lang['ModulesDisabled'] = 'ACP modules disabled';
$lang['ModulesDisabledInfo'] = 'ACP modules have been disabled in the board configuration.';
$lang['ModulesNoneAvailable'] = 'No modules are available at this time.';
$lang['ModulesUpload'] = 'Upload module';
$lang['ModulesUploadInfo'] = 'Enter a local filename of a UseBB ACP module to upload it.';
$lang['ModulesUploadDuplicateModule'] = 'A module under the filename %s already exists. Please remove it first.';
$lang['ModulesUploadNoValidModule'] = 'The file %s is not a valid UseBB module.';
$lang['ModulesUploadFailed'] = 'Could not install the module %s. Copying failed.';
$lang['ModulesUploadDisabled'] = 'The module directory is not writable. Uploading has been disabled. To enable, make the directory %s writable by the webserver.';
$lang['ModulesConfirmModuleDelete'] = 'Confirm module deletion';
$lang['ModulesConfirmModuleDeleteInfo'] = 'Are you sure you want to delete the module %s (%s)?';

$lang['MembersSearchMember'] = 'Search member';
$lang['MembersSearchMemberInfo'] = 'Enter a (part of a) username, displayed name or email address to edit.';
$lang['MembersSearchMemberExplain'] = 'Username, displayed name or email address';
$lang['MembersSearchMemberNotFound'] = 'No members with %s found.';
$lang['MembersSearchMemberList'] = 'The following members were found';
$lang['MembersEditingMember'] = 'Editing member %s';
$lang['MembersEditingMemberInfo'] = 'Update the user\'s info and submit the form. Fields marked with an asterisk (*) are required.';
$lang['MembersEditingMemberUsernameExists'] = 'The username %s already exists as a username or displayed name.';
$lang['MembersEditingMemberDisplayedNameExists'] = 'The displayed name %s already exists as a username or displayed name.';
$lang['MembersEditingMemberBanned'] = 'Banned';
$lang['MembersEditingMemberBannedReason'] = 'Reason for ban';
$lang['MembersEditingMemberCantChangeOwnLevel'] = 'You can\'t change your own level.';
$lang['MembersEditingMemberCantBanSelf'] = 'You can\'t ban yourself.';
$lang['MembersEditingMemberCantDeleteSelf'] = 'You can\'t delete yourself.';
$lang['MembersEditingComplete'] = 'The profile of the member %s was edited successfully.';
$lang['MembersEditingLevelModInfo'] = 'To make someone moderator, edit a forum and add the member\'s username to the moderator input field.';
$lang['MembersEditingActivation'] = 'Activation';
$lang['MembersEditingMemberCantChangeOwnActivation'] = 'You can\'t change your own activation mode.';
$lang['MembersEditingActivationInactive'] = 'Inactive';
$lang['MembersEditingActivationActive'] = 'Active';
$lang['MembersEditingActivationPotentialSpammer'] = 'Potential spammer';
$lang['MembersEditingActivationInfo'] = 'Changing activation status here will not send an email to the user. &mdash; Potential spammer status can be unset automatically based on the configuration setting of &quot;Potential spammer: maximum post count&quot;.';

$lang['DeleteMembersSearchMember'] = 'Search member';
$lang['DeleteMembersSearchMemberInfo'] = 'Enter a (part of a) username, displayed name or email address to delete.';
$lang['DeleteMembersSearchMemberExplain'] = 'Username, displayed name or email address';
$lang['DeleteMembersSearchMemberNotFound'] = 'No members with %s found.';
$lang['DeleteMembersSearchMemberList'] = 'The following members were found';
$lang['DeleteMembersConfirmMemberDelete'] = 'Confirm member deletion';
$lang['DeleteMembersConfirmMemberDeleteContent'] = 'Are you sure you want to delete the member %s? This is irreversible!';
$lang['DeleteMembersComplete'] = 'Deletion of member %s complete.';
$lang['DeleteMembersDeletePosts'] = 'Permanently delete the user\'s posts.';
$lang['DeleteMembersBanEmail'] = 'Ban email address';
$lang['DeleteMembersBanIPAddress'] = 'Ban last used IP address';
$lang['DeleteMembersSFSSubmit'] = 'Submit &quot;%s; %s; %s&quot; to Stop Forum Spam.';

$lang['RegisterMembersExplain'] = 'Here you can preregister member accounts. Just fill in the following information to create an account.';
$lang['RegisterMembersComplete'] = 'Registration of user %s is complete. The user can log in right away.';
$lang['RegisterMembersEditMember'] = 'You can further edit the created member account: %s.';

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
$lang['PruneMembersTypeProfileSpam'] = 'Profile spam accounts';
$lang['PruneMembersTypeProfileSpamExplain'] = 'Members without posts but with links in profile (signature).';
$lang['PruneMembersExclude'] = 'Exclude';
$lang['PruneMembersOptions'] = 'Options';
$lang['PruneMembersDeletePosts'] = 'Delete all pruned members\' posts.';
$lang['PruneMembersPreview'] = 'Preview Members';
$lang['PruneMembersPreviewList'] = 'With the submitted settings, %d members will be pruned.';
$lang['PruneMembersUsesCurrentSettings'] = 'Warning! %s uses the currently given settings, not the latest preview list of members.';
$lang['PruneMembersConfirmText'] = 'I understand this action is irreversible.';
$lang['PruneMembersStart'] = 'Start Pruning';
$lang['PruneMembersType'] = 'Pruning type';
$lang['PruneMembersNotConfirmed'] = 'You need to confirm this action first.';
$lang['PruneMembersDone'] = 'Pruning has completed. %d members were pruned.';

$lang['DNSBLIPBansDisabled'] = 'IP address banning disabled';
$lang['DNSBLIPBansDisabledInfo'] = 'For DNSBL powered banning to work, IP address banning must be enabled.';
$lang['DNSBLDisabled'] = 'DNSBL bans disabled';
$lang['DNSBLDisabledInfo'] = 'DNSBL bans have been disabled in the board configuration.';
$lang['DNSBLGeneralInfo'] = 'Open proxies are often used to post spam or abusive messages. Using UseBB\'s protection system, most of these proxies can be detected and banned automatically. Herefore blacklists are queried for information about the visitor\'s IP address.';
$lang['DNSBLServers'] = 'DNS BlackList (DNSBL) servers';
$lang['DNSBLServersInfo'] = 'One DNSBL server per line. Note using many of these blacklists together may cause slowness.';
$lang['DNSBLMinPositiveHits'] = 'At least %s positive hits are required to ban an IP address.';
$lang['DNSBLRecheckMinutes'] = 'Recheck allowed IP addresses every %s minutes (0 to disable).';
$lang['DNSBLSettingsSaved'] = 'DNSBL banning settings saved.';
$lang['DNSBLWhitelist'] = 'Whitelist';
$lang['DNSBLWhitelistInfo'] = 'One IP address or hostname per line (* and ? can be used as wildcards).';
$lang['DNSBLGlobally'] = 'Perform checking globally instead of only for registering/posting (not recommended).';

$lang['BadwordsInfo'] = 'Badwords can be filtered or replaced, eventually using partial matching (using *).';
$lang['BadwordsDisabled'] = 'Badwords filter disabled';
$lang['BadwordsDisabledInfo'] = 'Badwords filter has been disabled in the board configuration.';
$lang['BadwordsNoBadwordsExist'] = 'No filters exist at this forum.';
$lang['BadwordsAddBadwordWord'] = 'Word';
$lang['BadwordsAddBadwordReplacement'] = 'Replacement';

$lang['MassEmailInfo'] = 'Send mass email message to all your members or a level group.';
$lang['MassEmailRecipients'] = 'Recipients';
$lang['MassEmailRecipients-admins'] = 'Administrators';
$lang['MassEmailRecipients-mods'] = 'Moderators';
$lang['MassEmailRecipients-members'] = 'Normal members';
$lang['MassEmailSubject'] = 'Subject';
$lang['MassEmailBody'] = 'Body';
$lang['MassEmailTemplate'] = 'Hello,

This is the forum software of [board_name] speaking. The administrator has sent this mass email message via our board. The message body follows.

[board_name]
[board_link]
[admin_email]

-----

[body]';
$lang['MassEmailSent'] = 'The mass email message has been sent to %d members using %d message(s).';
$lang['MassEmailOptions'] = 'Options';
$lang['MassEmailPublicEmailsOnly'] = 'Only send to public email addresses';
$lang['MassEmailExcludeBanned'] = 'Exclude banned members';

$lang['BansInfo'] = 'Here you can control almost all banning aspects of your forum. Partial matching (using *) is possible. Individual accounts can be banned via the edit member pane.';
$lang['Bans-username'] = 'Usernames';
$lang['Bans-email'] = 'E-mail addresses';
$lang['Bans-ip_addr'] = 'IP addresses';
$lang['BansUsername'] = 'Username';
$lang['BansEmail'] = 'E-mail address';
$lang['BansIp_addr'] = 'IP address';
$lang['BansNoBansExist'] = 'No bans of this type exist at this forum.';
$lang['BansIPBansDisabledInfo'] = 'IP address banning has been disabled in the board configuration.';

?>
