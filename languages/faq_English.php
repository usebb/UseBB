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
$faq[] = array('--', 'UseBB Issues');
$faq[] = array('Who made this forum?', 'This bulletin board, called <em>UseBB</em>, is developed by the UseBB Team. UseBB is Open Source software released under the GPL license. You can download UseBB for free from <a href="http://www.usebb.net">www.usebb.net</a>. Note the administrator(s) of this forum/website may have added additional functionality.');
$faq[] = array('I have a complaint about this forum. To whom should I direct?', 'If you have a complaint about the forum software itself, not the content, you are welcome to <a href="http://www.usebb.net">tell the UseBB Team</a>. For any other inquiries, please contact the administrator(s) of this forum/website.');
