<?php

/*
	Copyright (C) 2003-2012 UseBB Team
	http://www.usebb.net
	
	$Id$
	
	This file is part of UseBB.
	
	UseBB is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	UseBB is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
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
// Member list templates
//

$templates['header'] = '
	<table class="maintable">
		<tr>
			<th>{l_Username} / {l_RealName}</th>
			<th>{l_Rank}</th>
			<th>{l_Email}</th>
		</tr>
';

$templates['cat_header'] = '
		<tr>
			<td colspan="3" class="forumcat">&raquo; {level}</td>
		</tr>
';

$templates['user'] = '
		<tr>
			<td>{username}<div class="jump-in-data">{real_name}</div></td>
			<td>{rank}</td>
			<td>{email}</td>
		</tr>
';

$templates['cat_footer'] = '
';

$templates['footer'] = '
	</table>
';

?>
