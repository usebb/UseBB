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
		<tr>
			<th colspan="2">{stats_title}</th>
		</tr>
		<tr>
			<td rowspan="3" class="icon"><img src="{img_dir}stats.gif" alt="{stats_title}" /></td>
			<td>{small_stats}<br />{newest_member}</td>
		</tr>
		<tr>
			<td class="forumcat">&raquo; {users_online} {detailed_list_link}</td>
		</tr>
		<tr>
			<td>{members_online}</td>
		</tr>
	</table>
';

$templates['login_form'] = '
	{form_begin}
	<table class="maintable">
		<tr>
			<th colspan="2">{login}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{user}</td><td>{user_input}<br />{link_reg}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{password}</td><td>{password_input}<br />{link_sendpwd}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{remember}</td><td>{remember_input}</td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}&nbsp;{reset_button}</td>
		</tr>
	</table>
	{form_end}
';

$templates['mail_form'] = '
	{form_begin}
	<table class="maintable">
		<tr>
			<th colspan="2">{sendemail}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{from}</td><td>{from_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{to}</td><td>{to_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{subject}</td><td>{subject_input}</td>
		</tr>
		<tr>
			<td colspan="2">{body_input}</td>
		</tr>
		<tr>
			<td colspan="2">{everything_required}</td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}&nbsp;{reset_button}</td>
		</tr>
	</table>
	{form_end}
';

$templates['move_topic_form'] = '
	{form_begin}
	<table class="maintable">
		<tr>
			<th colspan="2">{move_topic}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{topic}</td><td>{topic_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{old_forum}</td><td>{old_forum_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{new_forum}</td><td>{new_forum_input}</td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}&nbsp;{cancel_button}</td>
		</tr>
	</table>
	{form_end}
';

$templates['post_form'] = '
	{form_begin}
	<table class="maintable">
		<tr>
			<th colspan="2">{post_title}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{username}</td><td>{username_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{subject}</td><td>{subject_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{content}</td><td>{content_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{options}</td><td>{options_input}</td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}&nbsp;{preview_button}&nbsp;{reset_button}</td>
		</tr>
	</table>
	{form_end}
';

$templates['profile'] = '
	<table class="maintable">
		<tr>
			<th colspan="2">{title}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{username}</td><td>{username_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{userid}</td><td>{userid_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{level}</td><td>{level_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{rank}</td><td>{rank_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{avatar}</td><td>{avatar_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{real_name}</td><td>{real_name_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{regdate}</td><td>{regdate_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{posts}</td><td>{posts_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{postsperday}</td><td>{postsperday_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{lastlogin}</td><td>{lastlogin_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{location}</td><td>{location_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{website}</td><td>{website_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{occupation}</td><td>{occupation_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{interests}</td><td>{interests_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{signature}</td><td>{signature_v}</td>
		</tr>
		<tr>
			<th colspan="2">{contact_info}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{email}</td><td>{email_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{msnm}</td><td>{msnm_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{yahoom}</td><td>{yahoom_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{aim}</td><td>{aim_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{icq}</td><td>{icq_v} {icq_status}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{jabber}</td><td>{jabber_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{skype}</td><td>{skype_v}</td>
		</tr>
	</table>
';

$templates['register_form'] = '
	{form_begin}
	<table class="maintable">
		<tr>
			<th colspan="2">{register_form}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{user}</td><td>{user_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{email}</td><td>{email_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{passwd1}</td><td>{passwd1_input}<br />{passwd_info}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{passwd2}</td><td>{passwd2_input}</td>
		</tr>
		<tr>
			<td colspan="2">{everything_required}</td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}&nbsp;{reset_button}</td>
		</tr>
	</table>
	{form_end}
';

$templates['sendpwd_form'] = '
	{form_begin}
	<table class="maintable">
		<tr>
			<th colspan="2">{sendpwd}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{user}</td><td>{user_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{email}</td><td>{email_input}</td>
		</tr>
		<tr>
			<td colspan="2">{everything_required}</td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}&nbsp;{reset_button}</td>
		</tr>
	</table>
	{form_end}
';

?>
