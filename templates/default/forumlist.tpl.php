<?php

/*
	Copyright (C) 2003-2004 UseBB Team
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
// Forum list templates
//

$templates['forumlist_header'] = '
	<table class="maintable">
		<tr class="tablehead">
			<th></th>
			<th>{forum}</th>
			<th>{topics}</th>
			<th>{posts}</th>
			<th nowrap="nowrap">{latest_post}</th>
		</tr>
';

$templates['forumlist_cat'] = '
		<tr>
			<td colspan="5" class="forumcat"><small>&raquo;</small> {cat_name}</td>
		</tr>
';

$templates['forumlist_forum'] = '
		<tr>
			<td class="td2"><img src="{img_dir}{forum_icon}" alt="{forum_status}" /></td>
			<td class="td1" width="100%">{forum_name}<br /><small>{forum_descr}</small></td>
			<td class="td2"><div align="center">{total_topics}</div></td>
			<td class="td2"><div align="center">{total_posts}</div></td>
			<td class="td1" nowrap="nowrap"><small>{latest_post}<br />{author_date}</small></td>
		</tr>
';

$templates['forumlist_footer'] = '
	</table>
';

$templates['forumlist_stats'] = '
	<table class="maintable">
		<tr class="tablehead">
			<th colspan="2">{stats_title}</th>
		</tr>
		<tr>
			<td rowspan="2" class="td2"><img src="{img_dir}stats.gif" alt="{stats_title}" /></td>
			<td width="100%">{small_stats}<br />{newest_member}</td>
		</tr>
		<tr>
			<td>{users_online}<br /><small>{members_online}</small></td>
		</tr>
	</table>
';

?>
