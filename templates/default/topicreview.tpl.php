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
// Topic review templates
//

$templates['topicreview_header'] = '
	<table class="maintable">
		<tr>
			<td colspan="2" class="forumcat">&raquo; {l_TopicReview}</td>
		</tr>
		<tr>
			<th>{l_Author}</th>
			<th>{l_Post}</th>
		</tr>
';

$templates['topicreview_post'] = '
		<tr class="tr{colornum}">
			<td class="postername">
				<div class="posternamecontainer">{poster_name}</div>
			</td>
			<td class="postcontent" rowspan="2">
				<div class="post">{post_content}</div>
			</td>
		</tr>
		<tr class="tr{colornum}">
			<td class="posterinfo">
				<div class="field">{post_date}</div>
			</td>
		</tr>
';

$templates['topicreview_footer'] = '
		<tr>
			<td class="formcontrols" colspan="2">
				{view_more_posts}
			</td>
		</tr>
	</table>
';

?>
