-- $Header$

-- 
-- Table structure for table `usebb_badwords`
-- 

CREATE TABLE usebb_badwords (
  word varchar(255) NOT NULL default '',
  replacement varchar(255) NOT NULL default '',
  PRIMARY KEY  (word)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `usebb_bans`
-- 

CREATE TABLE usebb_bans (
  id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  ip_addr varchar(23) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `usebb_cats`
-- 

CREATE TABLE usebb_cats (
  id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  sort_id int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `usebb_forums`
-- 

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
  auth varchar(10) NOT NULL default '0011222223',
  auto_lock int(11) NOT NULL default '0',
  increase_post_count int(1) NOT NULL default '1',
  hide_mods_list int(1) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `usebb_members`
-- 

CREATE TABLE usebb_members (
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
  last_pageview int(10) NOT NULL default '0',
  hide_from_online_list int(1) NOT NULL default '0',
  posts int(11) NOT NULL default '0',
  template varchar(255) NOT NULL default '',
  language varchar(255) NOT NULL default '',
  date_format varchar(255) NOT NULL default '',
  timezone float NOT NULL default '0',
  dst int(1) NOT NULL default '0',
  enable_quickreply int(1) NOT NULL default '0',
  return_to_topic_after_posting int(1) NOT NULL default '0',
  target_blank int(1) NOT NULL default '0',
  hide_avatars int(1) NOT NULL default '0',
  hide_userinfo int(1) NOT NULL default '0',
  hide_signatures int(1) NOT NULL default '0',
  auto_subscribe_topic int(1) NOT NULL default '0',
  auto_subscribe_reply int(1) NOT NULL default '0',
  avatar_type int(1) NOT NULL default '0',
  avatar_remote varchar(255) NOT NULL default '',
  displayed_name varchar(255) NOT NULL default '',
  real_name varchar(255) NOT NULL default '',
  signature text NOT NULL,
  birthday int(8) NOT NULL default '0',
  location varchar(255) NOT NULL default '',
  website varchar(255) NOT NULL default '',
  occupation varchar(255) NOT NULL default '',
  interests varchar(255) NOT NULL default '',
  msnm varchar(255) NOT NULL default '',
  yahoom varchar(255) NOT NULL default '',
  aim varchar(255) NOT NULL default '',
  icq varchar(255) NOT NULL default '',
  jabber varchar(255) NOT NULL default '',
  skype varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `usebb_moderators`
-- 

CREATE TABLE usebb_moderators (
  forum_id int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `usebb_posts`
-- 

CREATE TABLE usebb_posts (
  id int(11) NOT NULL auto_increment,
  topic_id int(11) NOT NULL default '0',
  poster_id int(11) NOT NULL default '0',
  poster_guest varchar(255) NOT NULL default '',
  poster_ip_addr varchar(23) NOT NULL default '',
  content text NOT NULL,
  post_time int(10) NOT NULL default '0',
  post_edit_time int(10) NOT NULL default '0',
  post_edit_by int(11) NOT NULL default '0',
  enable_bbcode int(1) NOT NULL default '1',
  enable_smilies int(1) NOT NULL default '1',
  enable_sig int(1) NOT NULL default '1',
  enable_html int(1) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `usebb_searches`
-- 

CREATE TABLE usebb_searches (
  sess_id varchar(32) NOT NULL default '',
  results text NOT NULL,
  PRIMARY KEY  (sess_id)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `usebb_sessions`
-- 

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

-- --------------------------------------------------------

-- 
-- Table structure for table `usebb_stats`
-- 

CREATE TABLE usebb_stats (
  name varchar(255) NOT NULL default '',
  content text NOT NULL,
  PRIMARY KEY  (name)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `usebb_subscriptions`
-- 

CREATE TABLE usebb_subscriptions (
  topic_id int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `usebb_topics`
-- 

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
