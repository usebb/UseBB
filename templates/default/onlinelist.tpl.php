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
// Online list templates
//

$templates['onlinelist_header'] = '
	<p id="pagelinkstop">{page_links}</p>
	<table class="maintable">
		<tr>
			<th>{username}</th>
			<th>{location}</th>
			<th>{latest_update}</th>
		</tr>
';

$templates['onlinelist_user'] = '
		<tr>
			<td>{username}</td>
			<td>{location}</td>
			<td class="minimal">{latest_update}</td>
		</tr>
';

$templates['onlinelist_footer'] = '
	</table>
	<p id="pagelinksbottom">{page_links}</p>
	<div id="bottomfix"></div>
';

?>
