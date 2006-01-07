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
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with UseBB; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA	02111-1307	USA
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
';

$templates['cat_header'] = '
	<table class="maintable">
		<tr>
			<td colspan="4" class="forumcat">&raquo; {level}</td>
		</tr>
		<tr>
			<th>{l_Username}</th>
			<th>{l_RealName}</th>
			<th>{l_Rank}</th>
			<th>{l_Email}</th>
		</tr>
';

$templates['user'] = '
		<tr>
			<td>{username}</td>
			<td>{real_name}</td>
			<td>{rank}</td>
			<td>{email}</td>
		</tr>
';

$templates['cat_footer'] = '
	</table>
';

$templates['footer'] = '
';

?>
