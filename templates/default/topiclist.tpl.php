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
// Topic list templates
//

$templates['header'] = '
	<h3 id="forumname">{forum_name}</h3>
	<p id="forummods">{forum_moderators}</p>
	<p id="toolbartop">{new_topic_link}</p>
	<p id="pagelinkstop">{page_links}</p>
	<table class="maintable">
		<tr>
			<th class="icon"></th>
			<th>{l_Topic}</th>
			<th class="author">{l_Author}</th>
			<th class="count">{l_Replies}</th>
			<th class="count">{l_Views}</th>
			<th class="lastpostinfo">{l_LatestPost}</th>
		</tr>
';

$templates['notopics'] = '
		<tr>
			<td class="msg" colspan="6">
				{l_NoTopics}
			</td>
		</tr>
';

$templates['topic'] = '
		<tr>
			<td class="icon"><img src="{img_dir}{topic_icon}" alt="{topic_status}" /></td>
			<td>{topic_name}<div class="topicpagelinks">{topic_page_links}</div></td>
			<td class="author">{author}</td>
			<td class="count">{replies}</td>
			<td class="count">{views}</td>
			<td class="lastpostinfo">{by_author} <a href="{last_post_url}">&gt;&gt;</a><br />{on_date}</td>
		</tr>
';

$templates['footer'] = '
	</table>
	<p id="toolbarbottom">{new_topic_link}</p>
	<p id="pagelinksbottom">{page_links}</p>
	<div id="bottomfix"></div>
';

?>
