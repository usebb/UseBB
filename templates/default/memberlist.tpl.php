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

$templates['memberlist_header'] = '
	<p id="pagelinksothertop">{page_links}</p>
	<table class="maintable">
		<tr>
			<th>{l_Username}</th>
			<th>{l_RealName}</th>
			<th>{l_Level}</th>
			<th>{l_Rank}</th>
			<th>{l_Registered}</th>
			<th>{l_Posts}</th>
		</tr>
';

$templates['memberlist_user'] = '
		<tr>
			<td>{username}</td>
			<td>{real_name}</td>
			<td class="minimal">{level}</td>
			<td class="minimal">{rank}</td>
			<td>{registered}</td>
			<td class="count">{posts}</td>
		</tr>
';

$templates['memberlist_footer'] = '
	</table>
	<p id="pagelinksotherbottom">{page_links}</p>
';

?>
