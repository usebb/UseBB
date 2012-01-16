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
// Search templates
//

$templates['search_form'] = '
	{form_begin}
	<table class="maintable">
		<tr>
			<th colspan="2">{l_Search}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_SearchKeywords}</td><td>{keywords_input}<div class="moreinfo">{keywords_explain}</div></td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_SearchMode}</td><td>{mode_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_SearchAuthor}</td><td>{author_input} {exact_match_input} {include_guests_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_SearchForums}</td><td>{forums_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_SortBy}</td><td>{sort_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_ShowResultsAs}</td><td>{show_mode_input}</td>
		</tr>
		<tr>
			<td class="formcontrols" colspan="2">{submit_button} {reset_button}</td>
		</tr>
	</table>
	{form_end}
';

$templates['results_header'] = '
	<p id="pagelinksothertop">{page_links}</p>
	<table class="maintable">
		<tr>
			<td colspan="7" class="forumcat">{l_SearchKeywords}: <em>{keywords}</em><div>{l_SearchMode}: {mode}</div></td>
		</tr>
		<tr>
			<th class="icon"></th>
			<th>{l_Forum}</th>
			<th>{l_Topic}</th>
			<th class="count">{l_Replies}</th>
			<th class="count">{l_Views}</th>
			<th class="lastpostinfo">{l_LatestPost}</th>
		</tr>
';

$templates['results_topic'] = '
		<tr>
			<td class="icon"><img src="{img_dir}{topic_icon}" alt="{topic_status}" /></td>
			<td class="atforum">{forum}</td>
			<td><div class="topicname">{topic_name}</div><div class="topicpagelinks">{topic_page_links}</div><div class="author">&mdash; {author}</div></td>
			<td class="count">{replies}</td>
			<td class="count">{views}</td>
			<td class="lastpostinfo">{by_author} <a href="{last_post_url}">&gt;&gt;</a><div>{on_date}</div></td>
		</tr>
';

$templates['results_footer'] = '
	</table>
	<p id="pagelinksotherbottom">{page_links}</p>
';

$templates['results_posts_header'] = '
	<p id="pagelinksothertop">{page_links}</p>
	<table class="maintable">
		<tr>
			<td colspan="2" class="forumcat">{l_SearchKeywords}: <em>{keywords}</em><div>{l_SearchMode}: {mode}</div></td>
		</tr>
';

$templates['results_posts_post'] = '
		<tr class="results-posts-post-info">
			<td>
				<div class="results-posts-post-date">{post_date}</div>
				<div><strong>{topic_title}</strong> ({forum})</div>
			</td>
		</tr>
		<tr class="results-posts-post-content">
			<td>
				<div><strong>{poster_name}</strong>: <em>{post_content}</em></div>
			</td>
		</tr>
';

$templates['results_posts_footer'] = '
	</table>
	<p id="pagelinksotherbottom">{page_links}</p>
';

?>
