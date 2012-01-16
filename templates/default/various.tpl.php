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
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
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
// Various templates
//

$templates['forum_stats_box'] = '
	<table class="maintable">
		<tr>
			<th colspan="2">{board_name} {l_Statistics}</th>
		</tr>
		<tr>
			<td rowspan="3" class="icon"><img src="{img_dir}stats.gif" alt="{l_Statistics}" /></td>
			<td class="stats-cell">{small_stats}<div>{newest_member}</div></td>
		</tr>
		<tr>
			<td class="online">&raquo; {l_WhosOnline}</td>
		</tr>
		<tr>
			<td class="stats-cell"><div class="detailed-list-link">{detailed_list_link}</div>{users_online}<div>{members_online}</div></td>
		</tr>
	</table>
';

$templates['login_form'] = '
	{form_begin}
	<table class="maintable thinner-table">
		<tr>
			<th colspan="2">{l_LogIn}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Username}</td><td>{user_input}<div class="moreinfo">{link_reg}</div></td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Password}</td><td>{password_input}<div class="moreinfo">{link_sendpwd}</div></td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_RememberMe}</td><td>{remember_input}</td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}</td>
		</tr>
	</table>
	{form_end}
';

$templates['mail_form'] = '
	{form_begin}
	<table class="maintable thinner-table">
		<tr>
			<th colspan="2">{l_SendMessage}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_From}</td><td>{from_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_To}</td><td>{to_v}<div class="bcc-input">{bcc_input}</div></td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Subject}</td><td>{subject_input}</td>
		</tr>
		<tr>
			<td colspan="2">{body_input}</td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}</td>
		</tr>
	</table>
	{form_end}
';

$templates['mail_form_guest'] = '
	{form_begin}
	<table class="maintable thinner-table">
		<tr>
			<th colspan="2">{l_SendMessage}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Name}</td><td>{name_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Email}</td><td>{email_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_To}</td><td>{to_v}<div class="bcc-input">{bcc_input}</div></td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Subject}</td><td>{subject_input}</td>
		</tr>
		<tr>
			<td colspan="2">{body_input}</td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}</td>
		</tr>
	</table>
	{form_end}
';

$templates['move_topic_form'] = '
	{form_begin}
	<table class="maintable thinner-table">
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
				<ul id="bbcode-controls"><li>{bbcode_controls}</li></ul>
				{content_input}
				<ul id="smiley-controls"><li>{smiley_controls}</li></ul>
				{potential_spammer_notice}
			</td>
		</tr>
		<tr>
			<td><strong>{l_Options}</strong><div id="post-options">{options_input}</div></td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">
				{submit_button}&nbsp;{preview_button}&nbsp;{reset_button}
				<div class="postformshortcut">{l_PostFormShortcut}</div>
			</td>
		</tr>
	</table>
	{form_end}
';

$templates['preview'] = '
	<div class="preview">
		<h3>{l_Preview}</h3>
		<div class="preview">{post_content}</div>
	</div>
';

$templates['profile'] = '
	<table class="maintable">
		<tr>
			<th colspan="3">{title}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Username}</td><td>{username_v}</td><td rowspan="4" class="avatar-field">{avatar_v}</td>
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
			<td class="fieldtitle">{l_Registered}</td><td colspan="2">{regdate_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Posts}</td><td colspan="2">{posts_v} ({postsperday_v} {l_PostsPerDay}) &middot; {searchposts}</td>
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
			<td class="fieldtitle">{l_ICQ}</td><td colspan="2">{icq_v}</td>
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
	<table class="maintable thinner-table">
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
			<td colspan="2"><ul><li>{l_UsernameInfo}</li><li>{passwd_info}</li><li>{l_EverythingRequired}</li></ul></td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}</td>
		</tr>
	</table>
	{form_end}
';

$templates['sendpwd_form'] = '
	{form_begin}
	<table class="maintable thinner-table">
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
			<td colspan="2" class="formcontrols">{submit_button}</td>
		</tr>
	</table>
	{form_end}
';

$templates['anti_spam_question'] = '
	{form_begin}
	<table class="maintable" id="spamquestion">
		<tr>
			<th colspan="2">{l_AntiSpamQuestion}</th>
		</tr>
		<tr>
			<td colspan="2">{l_AntiSpamQuestionInfo}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Question}</td><td>{question}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Answer}</td><td>{answer_input}</td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}</td>
		</tr>
	</table>
	{form_end}
';

?>
