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
// Various templates
//

$templates['forum_stats_box'] = '
	<table class="maintable">
		<tr class="tablehead">
			<th colspan="2">{stats_title}</th>
		</tr>
		<tr>
			<td rowspan="2" class="td2"><img src="{img_dir}stats.gif" alt="{stats_title}" /></td>
			<td width="100%"><small>{small_stats}<br />{newest_member}</small></td>
		</tr>
		<tr>
			<td><small>{users_online}<br />{members_online}</small></td>
		</tr>
	</table>
';

$templates['login_form'] = '
	{form_begin}
	<table class="maintable">
		<tr class="tablehead">
			<th colspan="2">{login}</th>
		</tr>
	<tr>
		<td width="25%" class="td2">{user}</td>
		<td class="td1">{user_input}<br /><small>{link_reg}</small></td>
	</tr>
	<tr>
		<td width="25%" class="td2">{password}</td>
		<td class="td1">{password_input}<br /><small>{link_sendpwd}</small></td>
	</tr>
	<tr>
		<td width="25%" class="td2">{remember}</td>
		<td class="td1">{remember_input}</td>
	</tr>
	<tr>
		<td colspan="2" class="td2"><div align="center">{submit_button}&nbsp;{reset_button}</div></td>
	</tr>
	</table>
	{form_end}
';

$templates['mail_form'] = '
	{form_begin}
	<table class="maintable">
		<tr class="tablehead">
			<th colspan="2">{sendemail}</th>
		</tr>
	<tr>
		<td width="25%" class="td2">{from}</td>
		<td class="td1">{from_v}</td>
	</tr>
	<tr>
		<td width="25%" class="td2">{to}</td>
		<td class="td1">{to_v}</td>
	</tr>
	<tr>
		<td width="25%" class="td2">{subject}</td>
		<td class="td1">{subject_input}</td>
	</tr>
	<tr>
		<td width="25%" class="td2">{body}</td><td class="td1">{body_input}</td>
	</tr>
	<tr>
		<td colspan="2" class="td1"><small>{everything_required}</small></td>
	</tr>
	<tr>
		<td colspan="2" class="td2"><div align="center">{submit_button}&nbsp;{reset_button}</div></td>
	</tr>
	</table>
	{form_end}
';

$templates['move_topic_form'] = '
	{form_begin}
	<table class="maintable">
		<tr class="tablehead">
			<th colspan="2">{move_topic}</th>
		</tr>
		<tr>
			<td width="25%" class="td2">{topic}</td><td class="td1">{topic_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{old_forum}</td><td class="td1">{old_forum_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{new_forum}</td><td class="td1">{new_forum_input}</td>
		</tr>
		<tr>
			<td colspan="2" class="td2"><div align="center">{submit_button}&nbsp;{cancel_button}</div></td>
		</tr>
	</table>
	{form_end}
';

$templates['post_form'] = '
	{form_begin}
	<table class="maintable">
		<tr class="tablehead">
			<th colspan="2">{post_title}</th>
		</tr>
		<tr>
			<td width="25%" class="td2">{username}</td><td class="td1">{username_input}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{subject}</td><td class="td1">{subject_input}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{content}</td><td class="td1">{content_input}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{options}</td><td class="td1"><small>{options_input}</small></td>
		</tr>
		<tr>
			<td colspan="2" class="td2"><div align="center">{submit_button}&nbsp;{reset_button}</div></td>
		</tr>
	</table>
	{form_end}
';

$templates['profile'] = '
	<table class="maintable">
		<tr class="tablehead">
			<th colspan="2">{title}</th>
		</tr>
		<tr>
			<td width="25%" class="td2">{username}</td><td class="td1">{username_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{userid}</td><td class="td1">{userid_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{level}</td><td class="td1">{level_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{rank}</td><td class="td1">{rank_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{avatar}</td><td class="td1">{avatar_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{regdate}</td><td class="td1">{regdate_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{posts}</td><td class="td1">{posts_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{postsperday}</td><td class="td1">{postsperday_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{lastlogin}</td><td class="td1">{lastlogin_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{location}</td><td class="td1">{location_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{website}</td><td class="td1">{website_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{occupation}</td><td class="td1">{occupation_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{interests}</td><td class="td1">{interests_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{signature}</td><td class="td1"><small>{signature_v}</small></td>
		</tr>
		<tr class="tablehead">
			<th colspan="2">{contact_info}</th>
		</tr>
		<tr>
			<td width="25%" class="td2">{email}</td><td class="td1">{email_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{msnm}</td><td class="td1">{msnm_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{yahoom}</td><td class="td1">{yahoom_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{aim}</td><td class="td1">{aim_v}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{icq}</td><td class="td1">{icq_v} {icq_status}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{jabber}</td><td class="td1">{jabber_v}</td>
		</tr>
	</table>
';

$templates['register_form'] = '
	{form_begin}
	<table class="maintable">
		<tr class="tablehead">
			<th colspan="2">{register_form}</th>
		</tr>
		<tr>
			<td width="25%" class="td2">{user}</td>
			<td class="td1">{user_input}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{email}</td>
			<td class="td1">{email_input}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{passwd1}</td>
			<td class="td1">{passwd1_input}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{passwd2}</td>
			<td class="td1">{passwd2_input}</td>
		</tr>
		<tr>
			<td colspan="2" class="td1"><small>{everything_required}</small></td>
		</tr>
		<tr>
			<td colspan="2" class="td2"><div align="center">{submit_button}&nbsp;{reset_button}</div></td>
		</tr>
	</table>
	{form_end}
';

$templates['sendpwd_form'] = '
	{form_begin}
	<table class="maintable">
		<tr class="tablehead">
			<th colspan="2">{sendpwd}</th>
		</tr>
		<tr>
			<td width="25%" class="td2">{user}</td>
			<td class="td1">{user_input}</td>
		</tr>
		<tr>
			<td width="25%" class="td2">{email}</td>
			<td class="td1">{email_input}</td>
		</tr>
		<tr>
			<td colspan="2" class="td1"><small>{everything_required}</small></td>
		</tr>
		<tr>
			<td colspan="2" class="td2"><div align="center">{submit_button}&nbsp;{reset_button}</div></td>
		</tr>
	</table>
	{form_end}
';

?>
