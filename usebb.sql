# $Header$

# --------------------------------------------------------

#
# Table structure for table `usebb_bans`
#

CREATE TABLE usebb_bans (
  id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  ip_addr varchar(23) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table `usebb_bans`
#


# --------------------------------------------------------

#
# Table structure for table `usebb_cats`
#

CREATE TABLE usebb_cats (
  id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  sort_id int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table `usebb_cats`
#


# --------------------------------------------------------

#
# Table structure for table `usebb_config`
#

CREATE TABLE usebb_config (
  name varchar(255) NOT NULL default '',
  content text NOT NULL,
  PRIMARY KEY  (name)
) TYPE=MyISAM;

#
# Dumping data for table `usebb_config`
#

INSERT INTO usebb_config VALUES ('session_name', 'usebb');
INSERT INTO usebb_config VALUES ('debug', '0');
INSERT INTO usebb_config VALUES ('allow_multi_sess', '1');
INSERT INTO usebb_config VALUES ('sess_max_lifetime', '60');
INSERT INTO usebb_config VALUES ('template', 'default');
INSERT INTO usebb_config VALUES ('output_compression', '0');
INSERT INTO usebb_config VALUES ('language', 'English');
INSERT INTO usebb_config VALUES ('board_name', 'My Board');
INSERT INTO usebb_config VALUES ('board_descr', 'My Board');
INSERT INTO usebb_config VALUES ('enable_memberlist', '0');
INSERT INTO usebb_config VALUES ('enable_stafflist', '0');
INSERT INTO usebb_config VALUES ('enable_stats', '0');
INSERT INTO usebb_config VALUES ('enable_contactadmin', '1');
INSERT INTO usebb_config VALUES ('admin_email', 'your_email@host.tld');
INSERT INTO usebb_config VALUES ('date_format', 'D M d, Y g:i a');
INSERT INTO usebb_config VALUES ('enable_rss', '0');
INSERT INTO usebb_config VALUES ('online_min_updated', '30');
INSERT INTO usebb_config VALUES ('cookie_domain', '');
INSERT INTO usebb_config VALUES ('cookie_secure', '0');
INSERT INTO usebb_config VALUES ('cookie_path', '');
INSERT INTO usebb_config VALUES ('users_must_activate', '1');
INSERT INTO usebb_config VALUES ('board_url', 'http://www.host.tld/usebb/');
INSERT INTO usebb_config VALUES ('board_closed', '0');
INSERT INTO usebb_config VALUES ('board_closed_reason', 'Just closed...');
INSERT INTO usebb_config VALUES ('username_max_length', '25');
INSERT INTO usebb_config VALUES ('guests_can_view_profiles', '1');
INSERT INTO usebb_config VALUES ('guests_can_access_board', '1');
INSERT INTO usebb_config VALUES ('email_view_level', '1');
INSERT INTO usebb_config VALUES ('view_hidden_email_addresses_min_level', '3');
INSERT INTO usebb_config VALUES ('guests_can_view_online_list', '1');
INSERT INTO usebb_config VALUES ('enable_online_list', '1');

# --------------------------------------------------------

#
# Table structure for table `usebb_forums`
#

CREATE TABLE usebb_forums (
  id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  cat_id int(11) NOT NULL default '0',
  descr text NOT NULL,
  status int(1) NOT NULL default '1',
  topics int(11) NOT NULL default '0',
  posts int(11) NOT NULL default '0',
  last_topic_id int(11) NOT NULL default '0',
  sort_id int(11) NOT NULL default '0',
  auth varchar(8) NOT NULL default '00112222',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table `usebb_forums`
#


# --------------------------------------------------------

#
# Table structure for table `usebb_language`
#

CREATE TABLE usebb_language (
  name varchar(255) NOT NULL default '',
  language varchar(255) NOT NULL default '',
  content text NOT NULL
) TYPE=MyISAM;

#
# Dumping data for table `usebb_language`
#

INSERT INTO usebb_language VALUES ('Home', 'English', 'Home');
INSERT INTO usebb_language VALUES ('YourPanel', 'English', 'Your Panel');
INSERT INTO usebb_language VALUES ('Register', 'English', 'Register');
INSERT INTO usebb_language VALUES ('FAQ', 'English', 'FAQ');
INSERT INTO usebb_language VALUES ('Search', 'English', 'Search');
INSERT INTO usebb_language VALUES ('ActiveTopics', 'English', 'Active Topics');
INSERT INTO usebb_language VALUES ('LogIn', 'English', 'Log In');
INSERT INTO usebb_language VALUES ('LogOut', 'English', 'Log Out [ %s ]');
INSERT INTO usebb_language VALUES ('MemberList', 'English', 'Member List');
INSERT INTO usebb_language VALUES ('StaffList', 'English', 'Staff List');
INSERT INTO usebb_language VALUES ('Statistics', 'English', 'Statistics');
INSERT INTO usebb_language VALUES ('ContactAdmin', 'English', 'Contact Admin');
INSERT INTO usebb_language VALUES ('Forum', 'English', 'Forum');
INSERT INTO usebb_language VALUES ('Topics', 'English', 'Topics');
INSERT INTO usebb_language VALUES ('Posts', 'English', 'Posts');
INSERT INTO usebb_language VALUES ('LatestPost', 'English', 'Latest Post');
INSERT INTO usebb_language VALUES ('RSSFeed', 'English', 'RSS Feed');
INSERT INTO usebb_language VALUES ('NoNewPosts', 'English', 'No new posts');
INSERT INTO usebb_language VALUES ('Locked', 'English', 'Locked');
INSERT INTO usebb_language VALUES ('LastLogin', 'English', 'Last login');
INSERT INTO usebb_language VALUES ('VariousInfo', 'English', 'Various Information');
INSERT INTO usebb_language VALUES ('IndexStats', 'English', 'This board contains %d posts in %d topics posted by %d members.');
INSERT INTO usebb_language VALUES ('IndexLastUser', 'English', 'Welcome to our newest member, %s.');
INSERT INTO usebb_language VALUES ('OnlineUsers', 'English', 'There are %d members and %d guests online in the past %d minutes.');
INSERT INTO usebb_language VALUES ('Username', 'English', 'Username');
INSERT INTO usebb_language VALUES ('CurrentPassword', 'English', 'Current password');
INSERT INTO usebb_language VALUES ('UserID', 'English', 'User ID');
INSERT INTO usebb_language VALUES ('NoSuchUser', 'English', 'User %s does not exist (anymore) at this board!');
INSERT INTO usebb_language VALUES ('WrongPassword', 'English', 'Sorry, but that password isn\'t correct! Request a new password via the login form if you\'ve forgotten it.');
INSERT INTO usebb_language VALUES ('Reset', 'English', 'Reset');
INSERT INTO usebb_language VALUES ('SendPassword', 'English', 'Send a new password');
INSERT INTO usebb_language VALUES ('RegisterNewAccount', 'English', 'Register a new account');
INSERT INTO usebb_language VALUES ('RememberMe', 'English', 'Remember me');
INSERT INTO usebb_language VALUES ('Yes', 'English', 'Yes');
INSERT INTO usebb_language VALUES ('No', 'English', 'No');
INSERT INTO usebb_language VALUES ('NotActivated', 'English', 'Your account %s has not been activated yet. Please check your mail box you\'ve registered with on this forum for instructions on how to activate your account.');
INSERT INTO usebb_language VALUES ('Error', 'English', 'Error');
INSERT INTO usebb_language VALUES ('Profile', 'English', 'Profile of %s');
INSERT INTO usebb_language VALUES ('Level', 'English', 'Level');
INSERT INTO usebb_language VALUES ('Administrator', 'English', 'Administrator');
INSERT INTO usebb_language VALUES ('Moderator', 'English', 'Moderator');
INSERT INTO usebb_language VALUES ('Registered', 'English', 'Registered');
INSERT INTO usebb_language VALUES ('Email', 'English', 'E-mail address');
INSERT INTO usebb_language VALUES ('ContactInfo', 'English', 'Contact Information');
INSERT INTO usebb_language VALUES ('Password', 'English', 'Password');
INSERT INTO usebb_language VALUES ('EverythingRequired', 'English', 'All fields are required!');
INSERT INTO usebb_language VALUES ('UserAlreadyExists', 'English', 'Sorry, user %s already exists at this board. If it\'s yours, log in or request a new password. If not, please choose another username.');
INSERT INTO usebb_language VALUES ('RegisteredNotActivated', 'English', 'Your account %s has now been created. An e-mail has been dispatched to %s with instructions on how to activate your account. You need to activate in order to log in with your account.');
INSERT INTO usebb_language VALUES ('RegisteredActivated', 'English', 'Your account %s has now been created. An e-mail has been dispatched to %s with your login details.');
INSERT INTO usebb_language VALUES ('Never', 'English', 'Never');
INSERT INTO usebb_language VALUES ('Member', 'English', 'Member');
INSERT INTO usebb_language VALUES ('RegistrationActivationEmailBody', 'English', 'Hello,\r\n\r\nThis is the forum software of [board_name] speaking. You have just registered an account named [account_name], but it has not been activated yet. Please click the link below to activate your account:\r\n\r\n[activate_link]\r\n\r\nor copy-n-paste it to your browser. Then you can log in using this username and password:\r\n\r\nUsername: [account_name]\r\nPassword: [password]\r\n\r\nIf you forget your password, you can request a new one via the link on the login form. Thank you for registering!\r\n\r\n[board_name]\r\n[board_link]');
INSERT INTO usebb_language VALUES ('SendpwdActivationEmailSubject', 'English', 'New Password');
INSERT INTO usebb_language VALUES ('NoForums', 'English', 'This is an empty board. The administrator has not yet created any forums.');
INSERT INTO usebb_language VALUES ('AlreadyActivated', 'English', 'Account with ID %d has already been activated.');
INSERT INTO usebb_language VALUES ('Activate', 'English', 'Activate');
INSERT INTO usebb_language VALUES ('Activated', 'English', 'Your account %s has now been (re)activated. You are now able to log in with the password in the e-mail you received.');
INSERT INTO usebb_language VALUES ('WrongActivationKey', 'English', 'We were unable to activate your account with ID %d. The activation key is incorrect. Are you sure you didn\'t request a new password in the mean time?');
INSERT INTO usebb_language VALUES ('RegisterIt', 'English', 'You can create it via the \'Register\' link.');
INSERT INTO usebb_language VALUES ('BoardClosed', 'English', 'Board Closed');
INSERT INTO usebb_language VALUES ('SendpwdActivationEmailBody', 'English', 'Hello,\r\n\r\nThis is the forum software of [board_name] speaking. You have just requested a new password for account [account_name]. Please click the link below to reactivate your account:\r\n\r\n[activate_link]\r\n\r\nor copy-n-paste it to your browser. Then you can log in using this username and password:\r\n\r\nUsername: [account_name]\r\nPassword: [password]\r\n\r\nIf you forget your password, you can request a new one via the link on the login form. \r\n\r\n[board_name]\r\n[board_link]');
INSERT INTO usebb_language VALUES ('SendpwdEmailBody', 'English', 'Hello,\r\n\r\nThis is the forum software of [board_name] speaking. You have just requested a new password for account [account_name]. You can log in using this username and password:\r\n\r\nUsername: [account_name]\r\nPassword: [password]\r\n\r\nIf you forget your password, you can request a new one via the link on the login form. \r\n\r\n[board_name]\r\n[board_link]');
INSERT INTO usebb_language VALUES ('SendpwdEmailSubject', 'English', 'New Password');
INSERT INTO usebb_language VALUES ('SendpwdActivated', 'English', 'The new password for your account %s has been sent to %s. You are now able to login with your new password.');
INSERT INTO usebb_language VALUES ('SendpwdNotActivated', 'English', 'The new password for your account %s has been sent to %s, together with information on how to reactivate your account.');
INSERT INTO usebb_language VALUES ('ForumIndex', 'English', 'Forum Index');
INSERT INTO usebb_language VALUES ('MissingFields', 'English', 'The following fields were missing or incorrect: %s.');
INSERT INTO usebb_language VALUES ('TermsOfUseContent', 'English', '-Put your terms and conditions in here.-');
INSERT INTO usebb_language VALUES ('TermsOfUse', 'English', 'Terms Of Use');
INSERT INTO usebb_language VALUES ('RegistrationActivationEmailSubject', 'English', 'Account Activation');
INSERT INTO usebb_language VALUES ('NeedToBeLoggedIn', 'English', 'You need to be logged in in order to do this. Click the \'Log In\' link to log in or \'Register\' to create a new account.');
INSERT INTO usebb_language VALUES ('WrongEmail', 'English', 'Sorry, but %s isn\'t the correct e-mail address for your account %s. If you can\'t remember your e-mail address, please contact the board admin.');
INSERT INTO usebb_language VALUES ('NeedToBeLoggedInGlobal', 'English', 'This board requires that you log in in order to view it. Click the \'Log In\' link to log in or \'Register\' to create a new account.');
INSERT INTO usebb_language VALUES ('Note', 'English', 'Note');
INSERT INTO usebb_language VALUES ('Hidden', 'English', 'Hidden');
INSERT INTO usebb_language VALUES ('ACP', 'English', 'Admin Control Panel');
INSERT INTO usebb_language VALUES ('SendMessage', 'English', 'Send a message');
INSERT INTO usebb_language VALUES ('NoViewableForums', 'English', 'You don\'t have permission to view any forums with this user level. If you are not logged in, do so. If you are logged in, you probably shouldn\'t be here.');
INSERT INTO usebb_language VALUES ('Rank', 'English', 'Rank');
INSERT INTO usebb_language VALUES ('Location', 'English', 'Location');
INSERT INTO usebb_language VALUES ('Website', 'English', 'Website');
INSERT INTO usebb_language VALUES ('Occupation', 'English', 'Occupation');
INSERT INTO usebb_language VALUES ('Interests', 'English', 'Interests');
INSERT INTO usebb_language VALUES ('MSNM', 'English', 'MSN Messenger');
INSERT INTO usebb_language VALUES ('YahooM', 'English', 'Yahoo Messenger');
INSERT INTO usebb_language VALUES ('AIM', 'English', 'AIM');
INSERT INTO usebb_language VALUES ('ICQ', 'English', 'ICQ');
INSERT INTO usebb_language VALUES ('Jabber', 'English', 'Jabber');
INSERT INTO usebb_language VALUES ('BannedIP', 'English', 'Your IP address %s has been banned from this board.');
INSERT INTO usebb_language VALUES ('Avatar', 'English', 'Avatar');
INSERT INTO usebb_language VALUES ('BannedUser', 'English', 'Banned Account');
INSERT INTO usebb_language VALUES ('BannedUserExplain', 'English', 'Your account %s has been banned from this board. The reason is:');
INSERT INTO usebb_language VALUES ('BannedUsername', 'English', 'The username %s has been banned on this board. Please choose another one.');
INSERT INTO usebb_language VALUES ('BannedEmail', 'English', 'The e-mail address %s has been banned on this board. Please choose another one.');
INSERT INTO usebb_language VALUES ('PostsPerDay', 'English', 'Posts per day');
INSERT INTO usebb_language VALUES ('BoardClosedOnlyAdmins', 'English', 'Only administrators can log in when the board is closed.');
INSERT INTO usebb_language VALUES ('NoPosts', 'English', 'No Posts');
INSERT INTO usebb_language VALUES ('AuthorDate', 'English', 'By %s on %s');
INSERT INTO usebb_language VALUES ('Re', 'English', 'Re:');
INSERT INTO usebb_language VALUES ('MailForm', 'English', 'Send an e-mail to %s');
INSERT INTO usebb_language VALUES ('SendEmail', 'English', 'Send a message to %s');
INSERT INTO usebb_language VALUES ('NoMails', 'English', 'This user has chosen not to receive any e-mails.');
INSERT INTO usebb_language VALUES ('UserEmailBody', 'English', 'Hello,\n\nThis is the forum software of [board_name] speaking. The user [username] has sent this message to you via our board. The message body follows.\n\n[board_name]\n[board_link]\n\n-----\n\n[body]');
INSERT INTO usebb_language VALUES ('EmailSent', 'English', 'Your e-mail to %s has been sent succesfully!');
INSERT INTO usebb_language VALUES ('To', 'English', 'To');
INSERT INTO usebb_language VALUES ('From', 'English', 'From');
INSERT INTO usebb_language VALUES ('Subject', 'English', 'Subject');
INSERT INTO usebb_language VALUES ('Body', 'English', 'Body');
INSERT INTO usebb_language VALUES ('Send', 'English', 'Send');
INSERT INTO usebb_language VALUES ('EditProfile', 'English', 'Edit Profile');
INSERT INTO usebb_language VALUES ('EditOptions', 'English', 'Edit Options');
INSERT INTO usebb_language VALUES ('EditPasswd', 'English', 'Edit Password');
INSERT INTO usebb_language VALUES ('PanelHome', 'English', 'Panel Home');
INSERT INTO usebb_language VALUES ('NewEmailNotActivated', 'English', 'Because you have changed your e-mail address for your account %s, you need to reactivate it. An e-mail has been sent to %s with instructions on how to do this.');
INSERT INTO usebb_language VALUES ('Required', 'English', 'Required');
INSERT INTO usebb_language VALUES ('ViewProfile', 'English', 'View Profile');
INSERT INTO usebb_language VALUES ('NewEmailActivationEmailBody', 'English', 'Hello,\n\nThis is the forum software of [board_name] speaking. You have just altered the e-mail address of your account [account_name], but these has not been reactivated yet. Please click the link below to reactivate your account:\n\n[activate_link]\n\nor copy-n-paste it to your browser. Then you can log in using this username and password:\n\nUsername: [account_name]\nPassword: [password]\n\nIf you forget your password, you can request a new one via the link on the login form.\n\n[board_name]\n[board_link]');
INSERT INTO usebb_language VALUES ('NewEmailActivationEmailSubject', 'English', 'Account Reactivation');
INSERT INTO usebb_language VALUES ('Signature', 'English', 'Signature');
INSERT INTO usebb_language VALUES ('SessionInfo', 'English', 'Session Information');
INSERT INTO usebb_language VALUES ('SessionID', 'English', 'Session ID');
INSERT INTO usebb_language VALUES ('IPAddress', 'English', 'IP address');
INSERT INTO usebb_language VALUES ('Started', 'English', 'Started');
INSERT INTO usebb_language VALUES ('Updated', 'English', 'Updated');
INSERT INTO usebb_language VALUES ('Pages', 'English', 'Pages');
INSERT INTO usebb_language VALUES ('AutoLogin', 'English', 'Auto login');
INSERT INTO usebb_language VALUES ('Enabled', 'English', 'Enabled');
INSERT INTO usebb_language VALUES ('Disabled', 'English', 'Disabled');
INSERT INTO usebb_language VALUES ('Enable', 'English', 'Enable');
INSERT INTO usebb_language VALUES ('Disable', 'English', 'Disable');
INSERT INTO usebb_language VALUES ('AutoLoginSet', 'English', 'The auto login cookie has now been set.');
INSERT INTO usebb_language VALUES ('AutoLoginUnset', 'English', 'The auto login cookie has now been unset.');
INSERT INTO usebb_language VALUES ('RegistrationEmailBody', 'English', 'Hello,\r\n\r\nThis is the forum software of [board_name] speaking. You have just registered an account named [account_name]. You can log in using this username and password:\r\n\r\nUsername: [account_name]\r\nPassword: [password]\r\n\r\nIf you forget your password, you can request a new one via the link on the login form. Thank you for registering!\r\n\r\n[board_name]\r\n[board_link]');
INSERT INTO usebb_language VALUES ('RegistrationEmailSubject', 'English', 'Account Registration');
INSERT INTO usebb_language VALUES ('PublicEmail', 'English', 'Public e-mail address');
INSERT INTO usebb_language VALUES ('PublicLastLogin', 'English', 'Public last login time');
INSERT INTO usebb_language VALUES ('DateFormat', 'English', 'Date format');
INSERT INTO usebb_language VALUES ('Again', 'English', 'Again');
INSERT INTO usebb_language VALUES ('NewPassword', 'English', 'New password');
INSERT INTO usebb_language VALUES ('NewPasswordAgain', 'English', 'New password (again)');
INSERT INTO usebb_language VALUES ('PasswordEdited', 'English', 'Your password has been edited succesfully.');

# --------------------------------------------------------

#
# Table structure for table `usebb_posts`
#

CREATE TABLE usebb_posts (
  id int(11) NOT NULL auto_increment,
  topic_id int(11) NOT NULL default '0',
  poster_id int(11) NOT NULL default '0',
  poster_guest varchar(255) NOT NULL default '',
  poster_ip_addr varchar(23) NOT NULL default '',
  content text NOT NULL,
  post_time int(10) NOT NULL default '0',
  post_edit_time int(10) NOT NULL default '0',
  post_edit_by varchar(255) NOT NULL default '',
  enable_bbcode int(1) NOT NULL default '0',
  enable_smilies int(1) NOT NULL default '0',
  enable_sig int(1) NOT NULL default '0',
  enable_html int(1) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table `usebb_posts`
#


# --------------------------------------------------------

#
# Table structure for table `usebb_sessions`
#

CREATE TABLE usebb_sessions (
  sess_id varchar(32) NOT NULL default '',
  user_id int(11) NOT NULL default '0',
  ip_addr varchar(23) NOT NULL default '',
  started int(10) NOT NULL default '0',
  updated int(10) NOT NULL default '0',
  location varchar(255) NOT NULL default '',
  pages int(11) NOT NULL default '0',
  PRIMARY KEY  (sess_id)
) TYPE=MyISAM;

#
# Dumping data for table `usebb_sessions`
#


# --------------------------------------------------------

#
# Table structure for table `usebb_templates`
#

CREATE TABLE usebb_templates (
  name varchar(255) NOT NULL default '',
  template varchar(255) NOT NULL default '',
  content text NOT NULL
) TYPE=MyISAM;

#
# Dumping data for table `usebb_templates`
#

INSERT INTO usebb_templates VALUES ('normal_header', 'default', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\r\n<html>\r\n<head>\r\n  <title>{board_name}: {page_title}</title>\r\n  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />\r\n  <link rel="stylesheet" type="text/css" href="{css_url}" />\r\n</head>\r\n<body>\r\n  <div align="center">\r\n  <div class="main">\r\n  \r\n  <table class="header">\r\n  <tbody>\r\n    <tr>\r\n      <td class="logo"><a href="{link_home}"><img src="{img_dir}usebb.gif" alt="UseBB" title="Home" /></a></td>\r\n      <td class="namebox" nowrap="nowrap"><div class="title">{board_name}</div><div class="descr">{board_descr}</div></td>\r\n    </tr>\r\n  </tbody>\r\n  </table>\r\n  \r\n  <div class="menu">\r\n    <a href="{link_home}" accesskey="h">{home}</a><a href="{link_reg_panel}">{reg_panel}</a><a href="{link_faq}">{faq}</a><a href="{link_search}">{search}</a><a href="{link_active}">{active}</a><a href="{link_log_inout}">{log_inout}</a>  </div>');
INSERT INTO usebb_templates VALUES ('normal_footer', 'default', '  <div class="linkbar">\r\n    {link_bar}\r\n  </div>\r\n  <div class="copyright">\r\n<!--\r\nWe request not to remove the following copyright notice including the link to the UseBB Home Page.\r\nThis shows your respect to everyone involved in UseBB\'s development and promotes the use of UseBB.\r\nIf you don\'t want to show the Copyright notice, just leave the Powered by UseBB line. If you\r\ncompletely alter or remove the notice, support at our community forums will be affected.\r\n-->\r\n    {copyright}\r\n  </div>\r\n  \r\n  </div>\r\n  </div>\r\n</body>\r\n\r\n</html>');
INSERT INTO usebb_templates VALUES ('forumlist_header', 'default', '  <table class="maintable">\r\n  <tbody>\r\n    <tr class="tablehead">\r\n      <th colspan="2">{forum}</th>\r\n      <th width="1%">{topics}</th>\r\n      <th width="1%">{posts}</th>\r\n      <th>{latest_post}</th>\r\n    </tr>');
INSERT INTO usebb_templates VALUES ('css', 'default', 'body {\n  margin-top: 20px;\n  margin-left: 20px;\n  margin-right: 20px;\n  background-color: #FFFFFF;\n  }\nbody, td, th {\n  font-family: verdana, sans-serif;\n  font-size: 10pt;\n  color: #000000;\n  }\nimg {\n  border: 0px;\n  }\nform {\n  margin: 0px;\n  }\n.small {\n  font-size: 8pt;\n  }\na:link, a:active, a:visited {\n  color: #336699;\n  text-decoration: none;\n  }\na:hover {\n  color: #7F0000;\n  text-decoration: underline;\n  }\na.administrator:link, a.administrator:active, a.administrator:visited {\n  color: red;\n  }\na.moderator:link, a.moderator:active, a.moderator:visited {\n  color: blue;\n  }\ninput, select, textarea {\n  border: 1px solid silver;\n  background-color: #FFFFFF;\n  font-size: 10pt;\n  }\ntextarea {\n  font-family: verdana, sans-serif;\n  width: 100%;\n  }\n.main {\n  width: 750px;\n  }\n.header {\n  border-collapse: collapse;\n  width: 100%;\n  margin-bottom: 10px;\n  }\n.header td {\n  padding: 0px;\n  vertical-align: bottom;\n  }\n.header td.logo {\n  text-align: left;\n  width: 100%;\n  }\n.header td.namebox {\n  text-align: right;\n  }\n.header td.namebox .title {\n  font-size: 16pt;\n  font-weight: bold;\n  letter-spacing: 1px;\n  color: #336699;\n  border-bottom: 2px solid #ebd6ad;\n  }\n.header td.namebox .descr {\n  font-style: italic;\n  padding-top: 2px;\n  }\n.menu {\n  border: 1px solid #336699;\n  background-image: url({img_dir}menubg.gif);\n  background-repeat: repeat-x;\n  background-color: #E5E5E5;\n  text-align: left;\n  padding-top: 4px;\n  padding-bottom: 4px;\n  margin-bottom: 20px;\n  font-size: 9pt;\n  }\n.menu a {\n  padding-left: 10px;\n  padding-right: 10px;\n  padding-top: 4px;\n  padding-bottom: 4px;\n  border-right: 1px solid #336699;\n  }\n.menu a:hover {\n  background-image: url({img_dir}menubg2.gif);\n  background-repeat: repeat-x;\n  background-color: #FFFFFF;\n  text-decoration: none;\n  border-right: 1px solid #336699;\n  }\n.maintable {\n  border-collapse: collapse;\n  border-left: 1px solid #336699;\n  border-right: 1px solid #336699;\n  border-bottom: 2px solid #336699;\n  width: 100%;\n  margin-bottom: 20px;\n  }\n.maintable th {\n  color: #EBD6AD;\n  font-weight: bold;\n  background-color: #336699;\n  background-image: url({img_dir}tableheadbg.gif);\n  background-position: top;\n  background-repeat: repeat-x;\n  text-align: center;\n  padding: 6px;\n  padding-top: 3px;\n  padding-bottom: 3px;\n  border-left: 1px solid #336699;\n  border-top: 1px solid #336699;\n  }\n.maintable td {\n  background-color: #EFEFEF;\n  padding: 6px;\n  text-align: left;\n  border-left: 1px solid #336699;\n  border-top: 1px solid #336699;\n  vertical-align: middle;\n  }\n.maintable td.forumcat {\n  font-weight: bold;\n  letter-spacing: 1px;\n  background-image: url({img_dir}menubg.gif);\n  background-repeat: repeat-x;\n  background-color: #E5E5E5;\n  }\n.maintable td.td1 {\n  background-color: #EFEFEF;\n  }\n.maintable td.td2 {\n  background-color: #E5E5E5;\n  }\n.linkbar {\n  color: #323232;\n  margin-bottom: 2px;\n  font-size: 8pt;\n  }\n.copyright {\n  color: #323232;\n  margin-bottom: 20px;\n  font-size: 8pt;\n  }');
INSERT INTO usebb_templates VALUES ('panel_menu', 'default', '  <table class="maintable">\r\n  <tbody>\r\n    <tr>\r\n      <td width="25%" class="td2"><div align="center">{view_profile}</div></td>\r\n      <td width="25%" class="td2"><div align="center">{panel_profile}</div></td>\r\n      <td width="25%" class="td2"><div align="center">{panel_options}</div></td>\r\n      <td width="25%" class="td2"><div align="center">{panel_passwd}</div></td>\r\n    </tr>\r\n  </tbody>\r\n  </table>');
INSERT INTO usebb_templates VALUES ('forumlist_footer', 'default', '  </tbody>\r\n  </table>');
INSERT INTO usebb_templates VALUES ('forumlist_forum', 'default', '    <tr>\n      <td class="td1" width="1"><img src="{img_dir}{forum_icon}" alt="{forum_status}" /></td>\n      <td class="td1">{forum_name}<br /><div class="small">{forum_descr}</div></td>\n      <td class="td2" width="1%"><div align="center">{total_topics}</div></td>\n      <td class="td2" width="1%"><div align="center">{total_posts}</div></td>\n      <td class="td1" nowrap="nowrap">{latest_post}<div class="small">{author_date}</div></td>\n    </tr>');
INSERT INTO usebb_templates VALUES ('profile', 'default', '  <table class="maintable">\r\n  <tbody>\r\n    <tr class="tablehead">\r\n      <th colspan="2">{title}</th>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{username}</td><td class="td1">{username_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{userid}</td><td class="td1">{userid_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{level}</td><td class="td1">{level_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{rank}</td><td class="td1">{rank_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{avatar}</td><td class="td1">{avatar_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{regdate}</td><td class="td1">{regdate_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{posts}</td><td class="td1">{posts_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{postsperday}</td><td class="td1">{postsperday_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{lastlogin}</td><td class="td1">{lastlogin_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{location}</td><td class="td1">{location_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{website}</td><td class="td1">{website_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{occupation}</td><td class="td1">{occupation_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{interests}</td><td class="td1">{interests_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{signature}</td><td class="td1">{signature_v}</td>\r\n    </tr>\r\n    <tr class="tablehead">\r\n      <th colspan="2">{contact_info}</th>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{email}</td><td class="td1">{email_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{msnm}</td><td class="td1">{msnm_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{yahoom}</td><td class="td1">{yahoom_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{aim}</td><td class="td1">{aim_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{icq}</td><td class="td1">{icq_v} {icq_status}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{jabber}</td><td class="td1">{jabber_v}</td>\r\n    </tr>\r\n  </tbody>\r\n  </table>');
INSERT INTO usebb_templates VALUES ('register_form', 'default', '  {form_begin}\r\n  <table class="maintable">\r\n  <tbody>\r\n    <tr class="tablehead">\r\n      <th colspan="2">{register_form}</th>\r\n    </tr>\r\n  <tr>\r\n    <td width="25%" class="td2">{user}</td>\r\n    <td class="td1">{user_input}</td>\r\n  </tr>\r\n  <tr>\r\n    <td width="25%" class="td2">{email}</td>\r\n    <td class="td1">{email_input}</td>\r\n  </tr>\r\n  <tr>\r\n    <td colspan="2" class="td1"><div class="small">{everything_required}</div></td>\r\n  </tr>\r\n  <tr>\r\n    <td colspan="2" class="td2"><div align="center">{submit_button}&nbsp;{reset_button}</div></td>\r\n  </tr>\r\n  </tbody>\r\n  </table>\r\n  {form_end}');
INSERT INTO usebb_templates VALUES ('login_form', 'default', '  {form_begin}\r\n  <table class="maintable">\r\n  <tbody>\r\n    <tr class="tablehead">\r\n      <th colspan="2">{login}</th>\r\n    </tr>\r\n  <tr>\r\n    <td width="25%" class="td2">{user}</td>\r\n    <td class="td1">{user_input}<br /><div class="small">{link_reg}</div></td>\r\n  </tr>\r\n  <tr>\r\n    <td width="25%" class="td2">{password}</td>\r\n    <td class="td1">{password_input}<br /><div class="small">{link_sendpwd}</div></td>\r\n  </tr>\r\n  <tr>\r\n    <td width="25%" class="td2">{remember}</td>\r\n    <td class="td1">{remember_input}</td>\r\n  </tr>\r\n  <tr>\r\n    <td colspan="2" class="td2"><div align="center">{submit_button}&nbsp;{reset_button}</div></td>\r\n  </tr>\r\n  </tbody>\r\n  </table>\r\n  {form_end}');
INSERT INTO usebb_templates VALUES ('msgbox', 'default', '  <table class="maintable">\r\n  <tbody>\r\n    <tr class="tablehead">\r\n      <th>{box_title}</th>\r\n    </tr>\r\n    <tr>\r\n      <td><p align="center">{content}</p></td>\r\n    </tr>\r\n  </tbody>\r\n  </table>');
INSERT INTO usebb_templates VALUES ('sendpwd_form', 'default', '  {form_begin}\r\n  <table class="maintable">\r\n  <tbody>\r\n    <tr class="tablehead">\r\n      <th colspan="2">{sendpwd}</th>\r\n    </tr>\r\n  <tr>\r\n    <td width="25%" class="td2">{user}</td>\r\n    <td class="td1">{user_input}</td>\r\n  </tr>\r\n  <tr>\r\n    <td width="25%" class="td2">{email}</td>\r\n    <td class="td1">{email_input}</td>\r\n  </tr>\r\n  <tr>\r\n    <td colspan="2" class="td1"><div class="small">{everything_required}</div></td>\r\n  </tr>\r\n  <tr>\r\n    <td colspan="2" class="td2"><div align="center">{submit_button}&nbsp;{reset_button}</div></td>\r\n  </tr>\r\n  </tbody>\r\n  </table>\r\n  {form_end}');
INSERT INTO usebb_templates VALUES ('forumlist_cat', 'default', '  <tr>\r\n    <td colspan="5" class="forumcat">{cat_name}</td>\r\n  </tr>');
INSERT INTO usebb_templates VALUES ('forumlist_stats', 'default', '  <table class="maintable">\r\n  <tbody>\r\n    <tr class="tablehead">\r\n      <th>{stats_title}</th>\r\n    </tr>\r\n    <tr>\r\n      <td>{small_stats}</td>\r\n    </tr>\r\n  <tr>\r\n    <td>\r\n      {users_online}<br />\r\n      <div class="small">{members}</div>\r\n    </td>\r\n  </tr>\r\n  </tbody>\r\n  </table>');
INSERT INTO usebb_templates VALUES ('edit_profile', 'default', '  {form_begin}\r\n  <table class="maintable">\r\n  <tbody>\r\n    <tr class="tablehead">\r\n      <th colspan="2">{edit_profile}</th>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{email}</td><td class="td1">{email_input}&nbsp;<span class="small">{required}</span></td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{avatar}</td><td class="td1">{avatar_input}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{location}</td><td class="td1">{location_input}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{website}</td><td class="td1">{website_input}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{occupation}</td><td class="td1">{occupation_input}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{interests}</td><td class="td1">{interests_input}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{signature}</td><td class="td1">{signature_input}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{msnm}</td><td class="td1">{msnm_input}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{yahoom}</td><td class="td1">{yahoom_input}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{aim}</td><td class="td1">{aim_input}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{icq}</td><td class="td1">{icq_input}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{jabber}</td><td class="td1">{jabber_input}</td>\r\n    </tr>\r\n    <tr>\r\n      <td colspan="2" class="td2"><div align="center">{submit_button}&nbsp;{reset_button}</div></td>\r\n    </tr>\r\n  </tbody>\r\n  </table>\r\n  {form_end}');
INSERT INTO usebb_templates VALUES ('mail_form', 'default', '  {form_begin}\n  <table class="maintable">\n  <tbody>\n    <tr class="tablehead">\n      <th colspan="2">{sendemail}</th>\n    </tr>\n  <tr>\n    <td width="25%" class="td2">{from}</td>\n    <td class="td1">{from_v}</td>\n  </tr>\n  <tr>\n    <td width="25%" class="td2">{to}</td>\n    <td class="td1">{to_v}</td>\n  </tr>\n  <tr>\n    <td width="25%" class="td2">{subject}</td>\n    <td class="td1">{subject_input}</td>\n  </tr>\n  <tr>\n    <td class="td1" colspan="2">{body_input}</td>\n  </tr>\n  <tr>\n    <td colspan="2" class="td1"><div class="small">{everything_required}</div></td>\n  </tr>\n  <tr>\n    <td colspan="2" class="td2"><div align="center">{submit_button}&nbsp;{reset_button}</div></td>\n  </tr>\n  </tbody>\n  </table>\n  {form_end}');
INSERT INTO usebb_templates VALUES ('panel_sess_info', 'default', '  <table class="maintable">\r\n  <tbody>\r\n    <tr class="tablehead">\r\n      <th colspan="2">{title}</th>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{sess_id}</td><td class="td1">{sess_id_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{ip_addr}</td><td class="td1">{ip_addr_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{updated}</td><td class="td1">{updated_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{pages}</td><td class="td1">{pages_v}</td>\r\n    </tr>\r\n    <tr>\r\n      <td width="25%" class="td2">{al}</td><td class="td1">{al_v}</td>\r\n    </tr>\r\n  </tbody>\r\n  </table>');
INSERT INTO usebb_templates VALUES ('edit_options', 'default', '  {form_begin}\r\n  <table class="maintable">\r\n  <tbody>\r\n    <tr class="tablehead">\r\n      <th colspan="2">{edit_options}</th>\r\n    </tr>\r\n  <tr>\r\n    <td width="25%" class="td2">{email_show}</td>\r\n    <td class="td1">{email_show_input}</td>\r\n  </tr>\r\n  <tr>\r\n    <td width="25%" class="td2">{last_login_show}</td>\r\n    <td class="td1">{last_login_show_input}</td>\r\n  </tr>\r\n  <tr>\r\n    <td width="25%" class="td2">{date_format}</td>\r\n    <td class="td1">{date_format_input}</td>\r\n  </tr>\r\n  <tr>\r\n    <td colspan="2" class="td2"><div align="center">{submit_button}&nbsp;{reset_button}</div></td>\r\n  </tr>\r\n  </tbody>\r\n  </table>\r\n  {form_end}');
INSERT INTO usebb_templates VALUES ('editpwd_form', 'default', '  {form_begin}\r\n  <table class="maintable">\r\n  <tbody>\r\n    <tr class="tablehead">\r\n      <th colspan="2">{edit_pwd}</th>\r\n    </tr>\r\n  <tr>\r\n    <td width="25%" class="td2">{current_passwd}</td>\r\n    <td class="td1">{current_passwd_input}</td>\r\n  </tr>\r\n  <tr>\r\n    <td width="25%" class="td2">{new_passwd}</td>\r\n    <td class="td1">{new_passwd1_input}</td>\r\n  </tr>\r\n  <tr>\r\n    <td width="25%" class="td2">{new_passwd_again}</td>\r\n    <td class="td1">{new_passwd2_input}</td>\r\n  </tr>\r\n  <tr>\r\n    <td colspan="2" class="td1"><div class="small">{everything_required}</div></td>\r\n  </tr>\r\n  <tr>\r\n    <td colspan="2" class="td2"><div align="center">{submit_button}&nbsp;{reset_button}</div></td>\r\n  </tr>\r\n  </tbody>\r\n  </table>\r\n  {form_end}');

# --------------------------------------------------------

#
# Table structure for table `usebb_topics`
#

CREATE TABLE usebb_topics (
  id int(11) NOT NULL auto_increment,
  forum_id int(11) NOT NULL default '0',
  topic_title varchar(255) NOT NULL default '',
  first_post_id int(11) NOT NULL default '0',
  last_post_id int(11) NOT NULL default '0',
  count_replies int(11) NOT NULL default '0',
  count_views int(11) NOT NULL default '0',
  status_locked int(1) NOT NULL default '0',
  status_sticky int(1) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table `usebb_topics`
#


# --------------------------------------------------------

#
# Table structure for table `usebb_users`
#

CREATE TABLE usebb_users (
  id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  email_show int(1) NOT NULL default '0',
  passwd varchar(32) NOT NULL default '',
  regdate int(10) NOT NULL default '0',
  level int(1) NOT NULL default '0',
  rank varchar(255) NOT NULL default '',
  active int(1) NOT NULL default '0',
  active_key varchar(32) NOT NULL default '',
  banned int(1) NOT NULL default '0',
  banned_reason text NOT NULL,
  last_login int(10) NOT NULL default '0',
  last_login_show int(1) NOT NULL default '0',
  posts int(11) NOT NULL default '0',
  template varchar(255) NOT NULL default '',
  language varchar(255) NOT NULL default '',
  date_format varchar(255) NOT NULL default '',
  avatar_type int(1) NOT NULL default '0',
  avatar_remote varchar(255) NOT NULL default '',
  signature text NOT NULL,
  location varchar(255) NOT NULL default '',
  website varchar(255) NOT NULL default '',
  occupation varchar(255) NOT NULL default '',
  interests varchar(255) NOT NULL default '',
  msnm varchar(255) NOT NULL default '',
  yahoom varchar(255) NOT NULL default '',
  aim varchar(255) NOT NULL default '',
  icq varchar(255) NOT NULL default '',
  jabber varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table `usebb_users`
#

