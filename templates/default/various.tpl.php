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
			<th colspan="2">{l_Statistics}</th>
		</tr>
		<tr>
			<td rowspan="3" class="icon"><img src="{img_dir}stats.gif" alt="{l_Statistics}" /></td>
			<td>{small_stats}<br />{newest_member}</td>
		</tr>
		<tr>
			<td class="online">&raquo; {users_online} {detailed_list_link}</td>
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
			<th colspan="2">{l_LogIn}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Username}</td><td>{user_input}<br />{link_reg}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Password}</td><td>{password_input}<br />{link_sendpwd}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_RememberMe}</td><td>{remember_input}</td>
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
			<td class="fieldtitle">{l_From}</td><td>{from_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_To}</td><td>{to_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Subject}</td><td>{subject_input}</td>
		</tr>
		<tr>
			<td colspan="2">{body_input}</td>
		</tr>
		<tr>
			<td colspan="2">{l_EverythingRequired}</td>
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
			<th colspan="2">{l_MoveTopic}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Topic}</td><td>{topic_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_OldForum}</td><td>{old_forum_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_NewForum}</td><td>{new_forum_input}</td>
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
			<td class="fieldtitle">{l_Username}</td><td>{username_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Subject}</td><td>{subject_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Content}</td>
			<td rowspan="2">
				<div id="bbcode-controls">{bbcode_controls}</div>
				<div id="smiley-controls">{smiley_controls}</div>
				{content_input}
			</td>
		</tr>
		<tr>
			<td><strong>{l_Options}</strong><div id="post-options">{options_input}</div></td>
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
			<th colspan="3">{title}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Username}</td><td>{username_v}</td><td rowspan="5" class="minimal">{avatar_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_RealName}</td><td>{real_name_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Level}</td><td>{level_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Rank}</td><td>{rank_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Registered}</td><td>{regdate_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Posts}</td><td colspan="2">{posts_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_PostsPerDay}</td><td colspan="2">{postsperday_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_LastLogin}</td><td colspan="2">{lastlogin_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Age}</td><td colspan="2">{age_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Location}</td><td colspan="2">{location_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Website}</td><td colspan="2">{website_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Occupation}</td><td colspan="2">{occupation_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Interests}</td><td colspan="2">{interests_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Signature}</td><td colspan="2">{signature_v}</td>
		</tr>
		<tr>
			<th colspan="3">{l_ContactInfo}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Email}</td><td colspan="2">{email_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_MSNM}</td><td colspan="2">{msnm_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_YahooM}</td><td colspan="2">{yahoom_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_AIM}</td><td colspan="2">{aim_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_ICQ}</td><td colspan="2">{icq_v} {icq_status}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Jabber}</td><td colspan="2">{jabber_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Skype}</td><td colspan="2">{skype_v}</td>
		</tr>
	</table>
';

$templates['register_form'] = '
	{form_begin}
	<table class="maintable">
		<tr>
			<th colspan="2">{l_Register}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Username}</td><td>{user_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Email}</td><td>{email_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Password}</td><td>{passwd1_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_PasswordAgain}</td><td>{passwd2_input}</td>
		</tr>
		<tr>
			<td colspan="2">{l_UsernameInfo} {passwd_info} {l_EverythingRequired}</td>
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
			<th colspan="2">{l_SendPassword}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Username}</td><td>{user_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Email}</td><td>{email_input}</td>
		</tr>
		<tr>
			<td colspan="2">{l_EverythingRequired}</td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}&nbsp;{reset_button}</td>
		</tr>
	</table>
	{form_end}
';

?>
