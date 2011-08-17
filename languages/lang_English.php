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

//
// Initialize a new translations holder array
//
$lang = array();

//
// Translation settings
// Uncomment and change when necessary for translations
//
#$lang['character_encoding'] = 'iso-8859-1';
#$lang['language_code'] = 'en';
#$lang['text_direction'] = 'ltr';

//
// Define translations
//
$lang['Home'] = 'Home';
$lang['YourPanel'] = 'Your Panel';
$lang['Register'] = 'Register';
$lang['FAQ'] = 'FAQ';
$lang['Search'] = 'Search';
$lang['ActiveTopics'] = 'Active Topics';
$lang['LogIn'] = 'Log In';
$lang['LogOut'] = 'Log Out (%s)';
$lang['MemberList'] = 'Member List';
$lang['StaffList'] = 'Staff List';
$lang['Statistics'] = 'Statistics';
$lang['ContactAdmin'] = 'Contact Admin';
$lang['Forum'] = 'Forum';
$lang['Topics'] = 'Topics';
$lang['Posts'] = 'Posts';
$lang['LatestPost'] = 'Latest Post';
$lang['RSSFeed'] = 'RSS Feed';
$lang['NewPosts'] = 'New posts';
$lang['NoNewPosts'] = 'No new posts';
$lang['LockedNewPosts'] = 'Locked (new posts)';
$lang['LockedNoNewPosts'] = 'Locked (no new posts)';
$lang['Locked'] = 'Locked';
$lang['LastLogin'] = 'Last login';
$lang['VariousInfo'] = 'Various Information';
$lang['IndexStats'] = 'This board contains %d posts in %d topics and has %d registered members.';
$lang['NewestMemberExtended'] = 'Welcome to our newest member: %s.';
$lang['Username'] = 'Username';
$lang['CurrentPassword'] = 'Current password';
$lang['UserID'] = 'User ID';
$lang['NoSuchForum'] = 'The forum %s does not exist (anymore) at this board!';
$lang['WrongPassword'] = 'Sorry, but that password isn\'t correct! Request a new password via the login form if you\'ve forgotten it.';
$lang['Reset'] = 'Reset';
$lang['SendPassword'] = 'Send a new password';
$lang['RegisterNewAccount'] = 'Register a new account';
$lang['RememberMe'] = 'Remember me';
$lang['Yes'] = 'Yes';
$lang['No'] = 'No';
$lang['NotActivated'] = 'Your account %s has not been activated yet. Please check your mail box you\'ve registered with on this forum for instructions on how to activate your account.';
$lang['NotActivatedByAdmin'] = 'The administrator of this forum has not yet activated your account %s. Please allow some time for this to happen.';
$lang['Error'] = 'Error';
$lang['Profile'] = 'Profile of %s';
$lang['Level'] = 'Level';
$lang['Administrator'] = 'Administrator';
$lang['Moderator'] = 'Moderator';
$lang['Registered'] = 'Registered';
$lang['Email'] = 'E-mail address';
$lang['ContactInfo'] = 'Contact Information';
$lang['Password'] = 'Password';
$lang['PasswordAgain'] = 'Password (again)';
$lang['EverythingRequired'] = 'All fields are required!';
$lang['RegisteredNotActivated'] = 'Your account %s has now been created. An e-mail has been dispatched to %s with instructions on how to activate your account. You need to activate in order to log in with your account.';
$lang['RegisteredActivated'] = 'Your account %s has now been created. You can log in right away.';
$lang['Never'] = 'Never';
$lang['Member'] = 'Member';
$lang['RegistrationActivationEmailBody'] = 'Hello,

This is the forum software of [board_name] speaking. You have just registered an account named [account_name], but it has not been activated yet. Please click the link below to activate your account:

[activate_link]

or copy-n-paste it to your browser. Then you can log in using this username and password:

Username: [account_name]
Password: [password]

If you forget your password, you can request a new one via the link on the login form. Thank you for registering!

[board_name]
[board_link]
[admin_email]';
$lang['NoForums'] = 'This is an empty board. The administrator has not yet created any forums.';
$lang['AlreadyActivated'] = 'Account with ID %d has already been activated.';
$lang['Activate'] = 'Activate';
$lang['Activated'] = 'Your account %s has now been (re)activated. You will be logged in automatically.';
$lang['WrongActivationKey'] = 'We were unable to activate your account with ID %d. The activation key is incorrect. Are you sure you didn\'t request a new password in the mean time?';
$lang['RegisterIt'] = 'You can create it via the \'Register\' link.';
$lang['BoardClosed'] = 'Board Closed';
$lang['SendpwdEmailBody'] = 'Hello,

This is the forum software of [board_name] speaking. You have just requested a new password for account [account_name]. You can log in using this username and password:

Username: [account_name]
Password: [password]

If you forget your password, you can request a new one via the link on the login form. 

[board_name]
[board_link]
[admin_email]';
$lang['SendpwdEmailSubject'] = 'New Password';
$lang['SendpwdActivated'] = 'The new password for your account %s has been sent to %s.';
$lang['ForumIndex'] = 'Forum Index';
$lang['MissingFields'] = 'The following required fields were missing or incorrect: %s.';
$lang['TermsOfUseContent'] = 'You acknowledge that all posts found at this board are the opinions of its authors and not of the web site\'s webmaster, forum administrators or moderators, except for posts written by one of them.

You agree not to post any abusive, offensive, obscene or inappropriate content or content that is forbidden by law on these forums. Doing so can cause your account on these forums to get banned or removed and your internet service provider eventually noticed about your behaviour. Therefore, your IP address is stored with every post you place. You also agree that administrators and moderators are permitted to edit, delete, move or lock your topics when they think this is needed.

All information you post to these forums is being stored in a database system for future reference. The board administrators will not redistribute this information without your permission or obligation by legal issues. However, nor the webmaster, administrators or moderators nor the UseBB Project can be held responsible when any information is being leaked as a result of a hacking attempt.

This board uses cookies to store temporary information needed by the forum system on your local computer. Also, a cookie can store your user ID and your password in an encrypted form to enable automatic login of your account if you chose to enable this. If you don\'t want any cookies to be stored on your computer, refer to your browser\'s manual about disabling cookies.

By clicking the "I accept" button, you agree to these terms and conditions.';
$lang['TermsOfUse'] = 'Terms Of Use';
$lang['RegistrationActivationEmailSubject'] = 'Account Activation';
$lang['NeedToBeLoggedIn'] = 'You need to be logged in to do this. Click the \'Log In\' link to log in or \'Register\' to create a new account.';
$lang['WrongEmail'] = 'Sorry, but %s isn\'t the correct e-mail address for your account %s. If you can\'t remember your e-mail address, please contact the board admin.';
$lang['Topic'] = 'Topic';
$lang['Author'] = 'Author';
$lang['Replies'] = 'Replies';
$lang['Views'] = 'Views';
$lang['Note'] = 'Note';
$lang['Hidden'] = 'Hidden';
$lang['ACP'] = 'ACP';
$lang['SendMessage'] = 'Send a message';
$lang['NoViewableForums'] = 'You don\'t have permission to view any forums with this user level. If you are not logged in, do so. If you are logged in, you probably shouldn\'t be here.';
$lang['Rank'] = 'Rank';
$lang['Location'] = 'Location';
$lang['Website'] = 'Website';
$lang['Occupation'] = 'Occupation';
$lang['Interests'] = 'Interests';
$lang['MSNM'] = 'Windows Live Messenger';
$lang['YahooM'] = 'Yahoo! Messenger';
$lang['AIM'] = 'AIM';
$lang['ICQ'] = 'ICQ';
$lang['Jabber'] = 'Jabber/XMPP';
$lang['BannedIP'] = 'Your IP address %s has been banned from this board.';
$lang['Avatar'] = 'Avatar';
$lang['AvatarURL'] = 'Avatar URL';
$lang['BannedUser'] = 'Banned Account';
$lang['BannedUserExplain'] = 'Your account %s is being banned from this board. The reason is:';
$lang['BannedUsername'] = 'The username %s is being banned from this board. Please choose another one.';
$lang['BannedEmail'] = 'The e-mail address %s is being banned from this board. Please choose another one.';
$lang['PostsPerDay'] = 'Posts per day';
$lang['BoardClosedOnlyAdmins'] = 'Only administrators can log in when the board is closed.';
$lang['NoPosts'] = 'No Posts';
$lang['NoActivetopics'] = 'This board currently does not have any active topics.';
$lang['AuthorDate'] = 'By %s on %s';
$lang['ByAuthor'] = 'By: %s';
$lang['OnDate'] = 'On: %s';
$lang['Re'] = 'Re:';
$lang['MailForm'] = 'Send an e-mail to %s';
$lang['SendEmail'] = 'Send a message to %s';
$lang['NoMails'] = 'This user has chosen not to receive any e-mails.';
$lang['UserEmailBody'] = 'Hello,

This is the forum software of [board_name] speaking. The user [username] has sent this message to you via our board. The message body follows.

[board_name]
[board_link]
[admin_email]

-----

[body]';
$lang['EmailSent'] = 'Your e-mail to %s has been sent succesfully!';
$lang['To'] = 'To';
$lang['From'] = 'From';
$lang['Subject'] = 'Subject';
$lang['Body'] = 'Body';
$lang['Send'] = 'Send';
$lang['EditProfile'] = 'Edit Profile';
$lang['EditOptions'] = 'Edit Options';
$lang['EditPasswd'] = 'Edit Password';
$lang['PanelHome'] = 'Panel Home';
$lang['NewEmailNotActivated'] = 'Your profile has been edited succesfully. Because you have changed the e-mail address for your account %s, you need to reactivate it. An e-mail has been sent to %s with instructions on how to do this. You will be logged out in the mean time.';
$lang['Required'] = 'Required';
$lang['ViewProfile'] = 'View Profile';
$lang['NewEmailActivationEmailBody'] = 'Hello,

This is the forum software of [board_name] speaking. You have just altered the e-mail address of your account [account_name], but it has not been reactivated yet. Please click the link below to reactivate your account:

[activate_link]

or copy-n-paste it to your browser.

If you forget your password, you can request a new one via the link on the login form.

[board_name]
[board_link]
[admin_email]';
$lang['NewEmailActivationEmailSubject'] = 'Account Reactivation';
$lang['Signature'] = 'Signature';
$lang['SessionInfo'] = 'Session Information';
$lang['SessionID'] = 'Session ID';
$lang['IPAddress'] = 'IP address';
$lang['Seconds'] = 'Seconds';
$lang['Updated'] = 'Updated';
$lang['Pages'] = 'Pages';
$lang['AutoLogin'] = 'Auto login';
$lang['Enabled'] = 'Enabled';
$lang['Disabled'] = 'Disabled';
$lang['Enable'] = 'Enable';
$lang['Disable'] = 'Disable';
$lang['AutoLoginSet'] = 'The auto login cookie has now been set.';
$lang['AutoLoginUnset'] = 'The auto login cookie has now been unset.';
$lang['RegistrationEmailBody'] = 'Hello,

This is the forum software of [board_name] speaking. You have just registered an account named [account_name]. You can log in using this username and password:

Username: [account_name]
Password: [password]

If you forget your password, you can request a new one via the link on the login form. Thank you for registering!

[board_name]
[board_link]
[admin_email]';
$lang['RegistrationEmailSubject'] = 'Account Registration';
$lang['PublicEmail'] = 'Public e-mail address';
$lang['PublicLastLogin'] = 'Public last login time';
$lang['DateFormat'] = 'Date format';
$lang['DateFormatHelp'] = 'The date format syntax equals to the %s function in PHP.';
$lang['Again'] = 'Again';
$lang['NewPassword'] = 'New password';
$lang['NewPasswordAgain'] = 'New password (again)';
$lang['PasswordEdited'] = 'Your password has been edited succesfully.';
$lang['DetailedOnlineList'] = 'Detailed online list';
$lang['Detailed'] = 'Detailed';
$lang['OptionsEdited'] = 'Your board options have been edited succesfully.';
$lang['ProfileEdited'] = 'Your profile has been edited succesfully.';
$lang['Started'] = 'Started';
$lang['Minutes'] = 'Minutes';
$lang['Hours'] = 'Hours';
$lang['Days'] = 'Days';
$lang['Weeks'] = 'Weeks';
$lang['TotalTime'] = 'Total time';
$lang['NoTopics'] = 'This forum does not contain any topics. You could post the first one!';
$lang['NotPermitted'] = 'You don\'t have the appropriate permissions to do this. If in doubt, contact the administrator.';
$lang['Language'] = 'Language';
$lang['Template'] = 'Template';
$lang['NoSuchMember'] = 'The member %s does not exist (anymore) at this board.';
$lang['FeatureDisabledBecauseCookiesDisabled'] = 'This feature is disabled because this board can not set or read cookies with your browser.';
$lang['LogOutConfirm'] = 'Are you sure you wish to log out?';
$lang['Cancel'] = 'Cancel';
$lang['Timezone'] = 'Timezone';
$lang['DST'] = 'Daylight saving times';
$lang['Sticky'] = 'Sticky';
$lang['PostNewTopic'] = 'New Topic';
$lang['ForumIsLocked'] = 'Forum is locked';
$lang['NoSuchTopic'] = 'The topic %s does not exist (anymore) at this board.';
$lang['PostReply'] = 'Post Reply';
$lang['TopicIsLocked'] = 'Topic is locked';
$lang['Post'] = 'Post';
$lang['Edit'] = 'Edit';
$lang['Delete'] = 'Delete';
$lang['Quote'] = 'Quote';
$lang['Wrote'] = '%s wrote';
$lang['ViewingIP'] = 'IP: %s';
$lang['TopicIsLockedExplain'] = 'The topic you are trying to post in is locked. Only authorized people can still post replies.';
$lang['Content'] = 'Content';
$lang['Options'] = 'Options';
$lang['EnableBBCode'] = 'Enable BBCode.';
$lang['EnableSmilies'] = 'Enable smilies.';
$lang['EnableSig'] = 'Enable signature.';
$lang['EnableHTML'] = 'Enable HTML.';
$lang['LockTopicAfterPost'] = 'Lock topic after posting.';
$lang['Guest'] = 'Guest';
$lang['BackToPrevious'] = 'Back to the previous page';
$lang['NoSuchPost'] = 'The post %s does not exist (anymore) at this board.';
$lang['UserPostedImage'] = 'User posted image';
$lang['ForumIsLockedExplain'] = 'This forum is locked. Only authorized people can post new topics.';
$lang['MakeTopicSticky'] = 'Make the topic sticky.';
$lang['QuickReply'] = 'Quick reply';
$lang['ReturnToTopicAfterPosting'] = 'Return to the topic after posting';
$lang['ModeratorList'] = 'Moderators: %s.';
$lang['Nobody'] = 'Nobody';
$lang['DeleteTopic'] = 'Delete topic';
$lang['MoveTopic'] = 'Move topic';
$lang['LockTopic'] = 'Lock topic';
$lang['UnlockTopic'] = 'Unlock topic';
$lang['MakeSticky'] = 'Make sticky';
$lang['ConfirmDeleteTopic'] = 'Are you sure you want to delete the topic %s in the forum %s? This is irreversible!';
$lang['MakeNormalTopic'] = 'Make normal topic';
$lang['OldForum'] = 'Old forum';
$lang['NewForum'] = 'New forum';
$lang['IAccept'] = 'I accept';
$lang['IDontAccept'] = 'I don\'t accept';
$lang['OpenLinksNewWindow'] = 'Open external links in new windows';
$lang['HideAllAvatars'] = 'Hide all avatars';
$lang['HideUserinfo'] = 'Hide user information';
$lang['HideAllSignatures'] = 'Hide all signatures';
$lang['HideFromOnlineList'] = 'Hide from online list';
$lang['PageLinks'] = 'Page: %s';
$lang['Preview'] = 'Preview';
$lang['DeletePost'] = 'Delete post';
$lang['ConfirmDeletePost'] = 'Are you sure you want to delete this post in the topic %s? This is irreversible!';
$lang['EditPost'] = 'Edit post';
$lang['PostEditInfo'] = 'Last edit by %s on %s.';
$lang['PasswdInfoNew'] = 'The password can only contain alphanumeric characters and symbols. For security, it must contain at least one letter and one number, and have a minimum length of %d characters.';
$lang['PasswdInfo'] = $lang['PasswdInfoNew']; // Keep old key for mods etc.
$lang['SubscribeTopic'] = 'Subscribe';
$lang['UnsubscribeTopic'] = 'Unsubscribe';
$lang['NewReplyEmailBody'] = 'Hello,

This is the forum software of [board_name] speaking. Someone ([poster_name]) posted a reply to a topic you are subscribed to ("[topic_title]"). To view the reply, please click the following link: [topic_link].

Click the following link if you wish to unsubscribe from the topic (requires login): [unsubscribe_link].

[board_name]
[board_link]
[admin_email]';
$lang['NewReplyEmailSubject'] = 'New reply in "%s"';
$lang['SubscribedTopic'] = 'You are now subscribed to this topic.';
$lang['UnsubscribedTopic'] = 'You are now unsubscribed from this topic.';
$lang['SubscribeToThisTopic'] = 'Subscribe to this topic.';
$lang['OK'] = 'OK';
$lang['Subscriptions'] = 'Subscriptions';
$lang['NoSubscribedTopics'] = 'You currently aren\'t subscribed to any topic.';
$lang['LatestUpdate'] = 'Latest update';
$lang['Unknown'] = 'Unknown';
$lang['PostingTopic'] = 'Posting a topic in %s';
$lang['PostingReply'] = 'Posting a reply in %s';
$lang['MovingTopic'] = 'Moving topic %s';
$lang['DeletingTopic'] = 'Deleting topic %s';
$lang['EditingPost'] = 'Editing post in %s';
$lang['DeletingPost'] = 'Deleting post in %s';
$lang['DebugMode'] = 'Debug mode';
$lang['ParseTime'] = 'Parse time';
$lang['ServerLoad'] = 'Server load';
$lang['TemplateSections'] = 'Template sections';
$lang['SQLQueries'] = 'SQL queries';
$lang['MemoryUsage'] = 'Memory usage';
$lang['MegaByteShort'] = 'MB';
$lang['RealName'] = 'Real name';
$lang['Skype'] = 'Skype';
$lang['Administrators'] = 'Administrators';
$lang['Moderators'] = 'Moderators';
$lang['TopicReview'] = 'Topic review';
$lang['ViewMorePosts'] = 'View more posts';
$lang['DisplayedName'] = 'Displayed name';
$lang['UsernameInfo'] = 'A username can only contain alphanumeric characters, spaces, _ and -.';
$lang['Code'] = 'Code';
$lang['Img'] = 'Img';
$lang['URL'] = 'URL';
$lang['Color'] = 'Color';
$lang['Size'] = 'Size';
$lang['ViewingForum'] = 'Forum: %s';
$lang['ViewingTopic'] = 'Topic: %s';
$lang['FloodIntervalWarning'] = 'The administrator has specified that you can only post with intervals of %d seconds. Please wait at least %d seconds before resubmitting the form.';
$lang['AutoSubscribe'] = 'Auto subscribe';
$lang['OnPostingNewTopics'] = 'On posting topics';
$lang['OnPostingNewReplies'] = 'On posting replies';
$lang['UnsubscribeSelected'] = 'Unsubscribe selected';
$lang['SelectedTopicsUnsubscribed'] = 'You are now unsubscribed from the selected topics.';
$lang['Birthday'] = 'Birthday';
$lang['Age'] = 'Age';
$lang['Month'] = 'Month';
$lang['Day'] = 'Day';
$lang['Year'] = 'Year';
$lang['PoweredBy'] = '%s is powered by %s';
$lang['ForumSoftware'] = 'Forum Software';
$lang['GeneralStats'] = 'General statistics';
$lang['Members'] = 'Members';
$lang['TopicsPerDay'] = 'Topics per day';
$lang['MembersPerDay'] = 'Members per day';
$lang['BoardStarted'] = 'Board started';
$lang['BoardAge'] = 'Board age';
$lang['NewestMember'] = 'Newest member';
$lang['MostActiveTopics'] = 'Most active topics';
$lang['MostViewedTopics'] = 'Most viewed topics';
$lang['PostsPerMember'] = 'Posts per member';
$lang['PostsPerForum'] = 'Posts per forum';
$lang['Categories'] = 'Categories';
$lang['Forums'] = 'Forums';
$lang['TopicsPerMember'] = 'Topics per member';
$lang['TopicsPerForum'] = 'Topics per forum';
$lang['MostActiveMembers'] = 'Most active members';
$lang['MostActiveForums'] = 'Most active forums';
$lang['DisplayedNameTaken'] = 'Sorry, %s has already been taken as a login name or a displayed name.';
$lang['SearchKeywords'] = 'Search keywords';
$lang['SearchMode'] = 'Search mode';
$lang['SearchAuthor'] = 'Search author';
$lang['SearchForums'] = 'Search forums';
$lang['AllForums'] = 'All forums';
$lang['NoSearchResults'] = 'Sorry, but no results were found matching the criteria you supplied.';
$lang['SearchMembersPosts'] = 'Search member\'s posts';
$lang['CurrentPage'] = 'Current page';
$lang['MemberGuestOnline'] = 'In the last %d minutes, %d member (%d hidden) and %d guest have visited the forums.';
$lang['MembersGuestOnline'] = 'In the last %d minutes, %d members (%d hidden) and %d guest have visited the forums.';
$lang['MemberGuestsOnline'] = 'In the last %d minutes, %d member (%d hidden) and %d guests have visited the forums.';
$lang['MembersGuestsOnline'] = 'In the last %d minutes, %d members (%d hidden) and %d guests have visited the forums.';
$lang['WhosOnline'] = 'Who\'s Online';
$lang['Done'] = 'Done';
$lang['KeywordsExplain'] = 'Any keywords of at least %d characters separated by spaces.';
$lang['BCCMyself'] = 'Send a copy to my own e-mail address.';
$lang['Save'] = 'Save';
$lang['Add'] = 'Add';
$lang['MarkAllAsRead'] = 'Mark all as read';
$lang['MarkAllAsReadDone'] = 'All forums and topics are now marked as read.';
$lang['StringTooShort'] = '%s is too short, at least %d characters are required.';
$lang['StringTooLong'] = '%s is too long, only %d characters are allowed.';
$lang['Upload'] = 'Upload';
$lang['RegistrationsDisabled'] = 'Registrations disabled';
$lang['PostFormShortcut'] = 'Press Alt+S (Control+S on Mac OS) to quickly send this form.';
$lang['EditThisMember'] = 'Edit this member';
$lang['EmailTaken'] = 'The e-mail address %s is already being used on this board. Please choose an other address.';
$lang['RegisteredNotActivatedByAdmin'] = 'Your account %s has now been created. Before you will be able to login, the administrator of this forum must activate your account. Please allow some time for this to happen.';
$lang['AdminActivationEmailBody'] = 'Hello,

This is the forum software of [board_name] speaking. You have just registered an account named [account_name]. The administrator of the forum still needs to activate your account. Once this has happened, you can login using the following:

Username: [account_name]
Password: [password]

If you forget your password, you can request a new one via the link on the login form. Thank you for registering!

[board_name]
[board_link]
[admin_email]';
$lang['AdminActivationEmailSubject'] = 'Account awaiting activation';
$lang['NewEmailNotActivatedByAdmin'] = 'Your profile has been edited succesfully. Because you have changed the e-mail address for your account %s, the admin of this board needs to reactivate it. You will be logged out in the mean time.';
$lang['NewEmailAdminActivationEmailBody'] = 'Hello,

This is the forum software of [board_name] speaking. You have just altered the e-mail address of your account [account_name], but it has not been reactivated yet. The administrator of the board first needs to reactivate your account. Please allow some time for this to happen.

[board_name]
[board_link]
[admin_email]';
$lang['NewEmailAdminActivationEmailSubject'] = 'Account awaiting reactivation';
$lang['AdminActivatedAccountEmailBody'] = 'Hello,

This is the forum software of [board_name] speaking. The administrator has activated your account [account_name]. You can now log in.

[board_name]
[board_link]
[admin_email]';
$lang['AdminActivatedAccountEmailSubject'] = 'Account activated';
$lang['Sort'] = 'Sort';
$lang['SortBy'] = 'Sort by';
$lang['SortBy-displayed_name'] = 'Username';
$lang['SortBy-real_name'] = 'Real name';
$lang['SortBy-level'] = 'Level';
$lang['SortBy-rank'] = 'Rank';
$lang['SortBy-regdate'] = 'Registered';
$lang['SortBy-posts'] = 'Posts';
$lang['SortBy-latest_post'] = 'Latest post';
$lang['SortBy-topic_title'] = 'Topic title';
$lang['SortBy-forum'] = 'Forum';
$lang['SortBy-author'] = 'Author';
$lang['SortBy-replies'] = 'Replies';
$lang['SortBy-views'] = 'Views';
$lang['SortOrder-asc'] = 'Ascending';
$lang['SortOrder-desc'] = 'Descending';
$lang['NoUsersFound'] = 'No members found.';
$lang['SaveConfigManually'] = 'Because config.php is not writable, UseBB cannot save the configuration values itself. Please save the following contents to config.php and upload if necessary.';
$lang['ShowResultsAs'] = 'Show results as';
$lang['ShowMode-topics'] = 'Topics';
$lang['ShowMode-posts'] = 'Posts';
$lang['Mode-and'] = 'All keywords (AND)';
$lang['Mode-or'] = 'Any keywords (OR)';
$lang['ExactMatch'] = 'Exact match';
$lang['IncludeGuests'] = 'Include guests';
$lang['Top'] = 'Top';
$lang['Bottom'] = 'Bottom';
$lang['Action'] = 'Action';
$lang['AntiSpamQuestion'] = 'Anti-spam question';
$lang['AntiSpamQuestionInfo'] = 'As an anti-spam measure, you need to answer this question correctly in order to access this component of the forum. Only one question per session will be asked, while registering will disable it completely. Thank you for your understanding.';
$lang['Question'] = 'Question';
$lang['Answer'] = 'Answer';
$lang['AntiSpamQuestionMathPlus'] = 'How much is %d plus %d?';
$lang['AntiSpamQuestionMathMinus'] = 'How much is %d minus %d?';
$lang['AntiSpamWrongAnswer'] = 'The submitted answer is wrong. Please fill in the correct answer to access this component.';
$lang['WrongUsernamePassword'] = 'The username (%s) and password combination is not correct. Please try again.';
$lang['WrongUsernameEmail'] = 'The username (%s) and email address combination does not exist. Please try again.';
$lang['All'] = 'All';
$lang['Staff'] = 'Staff';
$lang['Guests'] = 'Guests';
$lang['ShowOnly'] = 'Show only';
$lang['InvalidFormTokenNotice'] = 'The security token is invalid or has expired.

If you were led here through a third-party link or website, please ignore this page!

If you were making changes and submitting the form yourself, please resubmit.';
$lang['InvalidURLTokenNotice']  = 'The security token is invalid or has expired.

If you were led here through a third-party link or website, please ignore this page!

Otherwise, please reload the original page and revisit the link.';
$lang['Name'] = 'Name';
$lang['GuestName'] = '%s (guest)';
$lang['RSSFeedForTopic'] = 'RSS Feed for topic %s';
$lang['RSSFeedForForum'] = 'RSS Feed for forum %s';
$lang['PotentialSpammer'] = 'Potential spammer';
$lang['PotentialSpammerNoProfileLinks'] = 'You have the (temporary) status of potential spammer, which means no profile links can be set or will be rendered. Thank you for your understanding.';
$lang['PotentialSpammerNoPostLinks'] = 'You have the (temporary) status of potential spammer, which means no links in your posts will be rendered. Thank you for your understanding.';
$lang['InvisibleToGuests'] = 'Profiles are currently invisible to anonymous users (guests).';
$lang['SearchResults'] = 'Search results';

?>
