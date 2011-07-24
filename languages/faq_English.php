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
// Initialize a new faq holder array
//
$faq = array();

//
// Define headings and questions
//

$faq[] = array('--', 'User Accounts');
$faq[] = array('Do I need to register?', 'Registration may be required to post in this forum, this depends on whether the board administrator(s) have enabled guest posting or not (can be per forum). Generally, registering is a good idea as it gives you many additional features.');
$faq[] = array('What are the benefits of registering?', 'First of all, you get a personal account with your nickname which will be only available to you. You also get a personal profile where you can give some additional information about yourself if you like and you can adjust the forum to your wishes via the Edit Options feature, which may also include choosing a language and template setting. A very interesting feature is the possibility to subscribe to topics.');
$faq[] = array('What do I need to register an account?', 'To register, you need to choose a nickname (username) which will be the key to your account. If you like, you can also use your real name instead of your nickname. Note you can\'t change your username by yourself after registering, although you can change your displayed name at any time while your username stays the same. You also need to have a working e-mail address (as it may be required to activate your account) and you have to choose a password (which you can change afterwards).');
$faq[] = array('I didn\'t receive the activation e-mail!', 'You may be able to log in without activating. If not, try requesting a new password. If this doesn\'t work, contact the administrator.');
$faq[] = array('What if I forget my login info or my e-mail address doesn\'t work anymore?', 'You can always request a new password via the link on the login form. If your e-mail address doesn\'t work anymore or if you have forgotten it, contact an administrator. He/she may give you the e-mail address you registered with or adjust your account to your new and working e-mail address.');
$faq[] = array('What are moderators and administrators?', 'A moderator is a person which checks one or more forums for inaccurate posts. He/she may also help the users in his/her forum. An administrator is a moderator on all boards, also he/she can assign moderators and change all configuration options of the forum.');
$faq[] = array('How do I become a moderator or administrator?', 'Normally, you can\'t, but try asking the administrator if you are interested.');
$faq[] = array('Can I change my rank?', 'No, only administrators can give users a custom rank.');

$faq[] = array('--', 'Subscriptions');
$faq[] = array('How do I subscribe to topics?', 'On the bottom of each topic, you\'ll find a link to subscribe yourself to it. You can only subscribe to topics if you are logged in.');
$faq[] = array('How do I unsubscribe from topics?', 'You can unsubscribe by clicking the unsubscribe link or via the subscription overview in your panel.');

$faq[] = array('--', 'Your Profile');
$faq[] = array('How can I change my username?', 'You can not change your username (login name) yourself. Only the board administrator is allowed to do that. You can however change your displayed name at any time.');
$faq[] = array('What\'s the difference between Username, Displayed name and Real name?', 'Your username, which you use to log in onto the forums, is constant, you can\'t change it. Your displayed name (which is upon registering equal to your username) is the name displayed publicly. You can always change this name. Note you will still have to log in with your constant username. Your real name, which is optional, is only shown on your profile page.');
$faq[] = array('My language is missing in the drop down box!', 'Ask the administrator of this board whether he/she wants to add a translation for your language.');
$faq[] = array('Why can\'t I select another template?', 'Probably, no other templates are available on this board.');

$faq[] = array('--', 'Topics and Posts');
$faq[] = array('What are topics and posts?', 'A topic is a group of messages (posts) in a certain forum. If you click the name of a forum on the forum index, you can see an overview of topics in that forum.');
$faq[] = array('What are sticky topics?', 'Sticky topics are designed to always stay on top of the topic overview. Generally, normal users can not create sticky topics, but moderators and administrators can.');
$faq[] = array('Why can\'t I post new topics or replies?', 'If no link appears to create new topics or posts, you are not allowed to make new topics or posts in that forum. This may either be a setting of the forum or the topic may have been closed by a moderator. In doubt, contact the administrator.');
$faq[] = array('What are BBCode and smilies?', 'BBCode are tags which you can use to add certain elements to your post. Try some and preview your post to see the effect. Smilies are used to express emotions in your posts.');
$faq[] = array('My post count does not increase when posting!', 'In certain forums, post count increasing may have been disabled. This is common for testing or general chat forums.');
$faq[] = array('Why do I have to wait a certain amount of time between posting?', 'Flood protection is used to ommit spamming or flooding the server with posting requests. Please wait for a moment and resubmit the post.');
$faq[] = array('Why do links in my signature and/or posts remain unclickable?', 'The forum might add a &quot;potential spammer&quot; status to new accounts, which results in less rights for posting links on the forum. Mostly, the status is removed after some posting activity. Otherwise or if this is a problem, please contact the administrator.');

$faq[] = array('--', 'Various');
$faq[] = array('I don\'t receive any e-mails from this board!', 'Make sure the e-mails are not blocked by anti spam software. If not, the e-mail address set up in your profile may not work anymore.');
$faq[] = array('What\'s the RSS feed?', 'RSS feeds are used to fetch recent information from a website or a forum to view in a news/RSS reader or some browsers.');
$faq[] = array('Where in my profile can I place my Google Talk account?', 'Google Talk is an IM network using the Jabber/XMPP protocol. You can place your account name (completely, including the domain name) in the Jabber/XMPP profile field.');

$faq[] = array('--', 'UseBB Issues');
$faq[] = array('Who made this forum? What is UseBB?', 'This bulletin board, called <em>UseBB</em>, is developed by the UseBB Project. UseBB is Open Source software released under the GPL. You can download UseBB for free from <a href="http://www.usebb.net">www.usebb.net</a>. Note the administrator(s) of this forum/website may have added additional functionality.');
$faq[] = array('Are the creators of UseBB responsible for this forum?', 'No, this board is maintained by the website\'s webmaster(s). The UseBB Project can not be held responsible for this forum in any way.');
$faq[] = array('I have a complaint about this forum. To whom should I direct?', 'If you have a complaint about the forum software itself, not the content, you are welcome to <a href="http://www.usebb.net">tell the UseBB Project</a>. For any other inquiries, please contact the administrator(s) of this forum/website.');

?>
