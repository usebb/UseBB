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
// Stats templates
//

$templates['general_stats'] = '
	<table class="maintable">
		<tr>
			<th colspan="4">{l_GeneralStats}</th>
		</tr>
		<tr>
			<td class="statstitle">{l_Posts}</td><td>{count_posts}</td>
			<td class="statstitle">{l_PostsPerDay}</td><td>{posts_per_day}</td>
		</tr>
		<tr>
			<td class="statstitle">{l_Topics}</td><td>{count_topics}</td>
			<td class="statstitle">{l_TopicsPerDay}</td><td>{topics_per_day}</td>
		</tr>
		<tr>
			<td class="statstitle">{l_Members}</td><td>{count_members}</td>
			<td class="statstitle">{l_MembersPerDay}</td><td>{members_per_day}</td>
		</tr>
		<tr>
			<td class="statstitle">{l_Categories}</td><td>{count_cats}</td>
			<td class="statstitle">{l_Forums}</td><td>{count_forums}</td>
		</tr>
		<tr>
			<td class="statstitle">{l_BoardStarted}</td><td>{board_started}</td>
			<td class="statstitle">{l_BoardAge}</td><td>{board_days} {l_Days}</td>
		</tr>
		<tr>
			<td class="statstitle">{l_NewestMember}</td><td>{newest_member}</td>
			<td class="statstitle">{l_Registered}</td><td>{regdate_newest_member}</td>
		</tr>
		<tr>
			<td class="statstitle">{l_PostsPerMember}</td><td>{posts_per_member}</td>
			<td class="statstitle">{l_PostsPerForum}</td><td>{posts_per_forum}</td>
		</tr>
		<tr>
			<td class="statstitle">{l_TopicsPerMember}</td><td>{topics_per_member}</td>
			<td class="statstitle">{l_TopicsPerForum}</td><td>{topics_per_forum}</td>
		</tr>
	</table>
';

$templates['most_active_members_header'] = '
	<table class="statslayout">
		<tr>
			<td>
				<table class="maintable">
					<tr>
						<th colspan="3">{l_MostActiveMembers}</th>
					</tr>
';

$templates['most_active_members_member'] = '
					<tr>
						<td class="statstitle">{rank}</td><td>{username}</td><td class="statstitle">{posts}</td>
					</tr>
';

$templates['most_active_members_footer'] = '
				</table>
			</td>
';

$templates['most_active_forums_header'] = '
			<td class="last">
				<table class="maintable">
					<tr>
						<th colspan="3">{l_MostActiveForums}</th>
					</tr>
';

$templates['most_active_forums_forum'] = '
					<tr>
						<td class="statstitle">{rank}</td><td>{forum}</td><td class="statstitle">{posts}</td>
					</tr>
';

$templates['most_active_forums_footer'] = '
				</table>
			</td>
		</tr>
	</table>
';

$templates['most_active_topics_header'] = '
	<table class="statslayout">
		<tr>
			<td>
				<table class="maintable">
					<tr>
						<th colspan="3">{l_MostActiveTopics}</th>
					</tr>
';

$templates['most_active_topics_topic'] = '
					<tr>
						<td class="statstitle">{rank}</td><td>{title}</td><td class="statstitle">{replies}</td>
					</tr>
';

$templates['most_active_topics_footer'] = '
				</table>
			</td>
';

$templates['most_viewed_topics_header'] = '
			<td class="last">
				<table class="maintable">
					<tr>
						<th colspan="3">{l_MostViewedTopics}</th>
					</tr>
';

$templates['most_viewed_topics_topic'] = '
					<tr>
						<td class="statstitle">{rank}</td><td>{title}</td><td class="statstitle">{views}</td>
					</tr>
';

$templates['most_viewed_topics_footer'] = '
				</table>
			</td>
		</tr>
	</table>
';

?>
