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
// Initialize a new faq holder array
//
$faq = array();

//
// Define headings and questions
//

$faq[] = array('--', 'User Accounts');
$faq[] = array('Do I need to register?', 'Registration may be required to post in this forum, this depends on whether the board administrator(s) have enabled guest posting or not (can be per forum). Generally, registering is a good idea as it gives you many additional features.');
$faq[] = array('What are the benifits of registering?', 'First of all, you get a personal account with your nickname which will be only available to you. You also get a personal profile where you can give some additional information about yourself if you like and you can adjust the forum to your wishes via the Edit Options feature, which may also include choosing a language and template setting. A very interesting feature is the possibility to subscribe to topics.');
$faq[] = array('What do I need to register an account?', 'To register, you need to choose a nickname (username) which will be the key to your account. If you like, you can also use your real name instead of your nickname. Note you can\'t change your username by yourself after registering, although you can change your displayed name at any time while your username stays the same. You also need to have a working e-mail address (as it may be required to activate your account) and you have to choose a password (which you can change afterwards).');
$faq[] = array('What if I forget my login info or my e-mail address doesn\'t work anymore?', 'You can always request a new password via the link on the login form. If your e-mail address doesn\'t work anymore or if you have forgotten it, contact an administrator. He/she may give you the e-mail address you registered with or adjust your account to your new and working e-mail address.');

$faq[] = array('--', 'UseBB Issues');
$faq[] = array('Who made this forum?', 'This bulletin board, called <em>UseBB</em>, is developed by the UseBB Team. UseBB is Open Source software released under the GPL license. You can download UseBB for free from <a href="http://www.usebb.net">www.usebb.net</a>. Note the administrator(s) of this forum/website may have added additional functionality.');
$faq[] = array('Are the creators of UseBB responsible for this forum?', 'No, this board is maintained by the website\'s webmaster(s). We are not responsible for (loss of) any content on this forum.');
$faq[] = array('I have a complaint about this forum. To whom should I direct?', 'If you have a complaint about the forum software itself, not the content, you are welcome to <a href="http://www.usebb.net">tell the UseBB Team</a>. For any other inquiries, please contact the administrator(s) of this forum/website.');

?>
