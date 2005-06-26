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

$content = '<p>'.$lang['IndexWelcome'].'</p>
<h2>'.$lang['IndexLinks'].'</h2>
<ul>
	<li><a href="http://www.usebb.net/">UseBB Homepage</a></li>
	<li><a href="http://www.usebb.net/support/">UseBB Support</a></li>
	<li><a href="http://www.usebb.net/community/">UseBB Community</a></li>
	<li><a href="http://www.usebb.net/dev/">UseBB Development</a></li>
</ul>
<p>Copyright &copy; 2003-2005 UseBB Team</p>';

$admin_functions->create_body('index', $content);

?>
