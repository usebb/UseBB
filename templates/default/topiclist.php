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
// Topic list templates
//

$templates['topiclist_header'] = '
	<table class="maintable">
	 <tr>
		 <td colspan="6" class="toolbar"><div align="right">
			 {new_topic_link}
		 </div></td>
	 </tr>
	 <tr class="tablehead">
		 <th colspan="2" width="100%">{topic}</th>
		 <th>{author}</th>
		 <th>{replies}</th>
		 <th>{views}</th>
		 <th nowrap="nowrap">{latest_post}</th>
	 </tr>
';

$templates['topiclist_notopics'] = '
		<tr>
		 <td class="msg" colspan="6">
			 {notopics}
		 </td>
		</tr>
';

$templates['topiclist_topic'] = '
		<tr>
			<td class="td2" width="1"><img src="{img_dir}{topic_icon}" alt="{topic_status}" /></td>
			<td class="td1" width="100%">{topic_name}</td>
			<td class="td1" nowrap="nowrap"><div align="center"><small>{author}</small></div></td>
			<td class="td2"><div align="center">{replies}</div></td>
			<td class="td2"><div align="center">{views}</div></td>
			<td class="td1" nowrap="nowrap"><small>{author_date}</small></td>
		</tr>
';

$templates['topiclist_footer'] = '
		<tr>
			<td colspan="6" class="toolbar">
				<div align="right">{new_topic_link}</div>
			</td>
		</tr>
		<tr>
			<td colspan="6" class="td2">
				<small>{forum_moderators}</small>
			</td>
		</tr>
	</table>
';

?>
