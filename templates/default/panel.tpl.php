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
// Panel templates
//

$templates['panel_menu'] = '
	<ul id="panelmenu"><li id="first">{panel_subscriptions}</li><li>{view_profile}</li><li>{panel_profile}</li><li>{panel_options}</li><li>{panel_passwd}</li></ul>
';

$templates['panel_sess_info'] = '
	<table class="maintable">
		<tr>
			<th colspan="2">{l_SessionInfo}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_SessionID}</td><td>{sess_id_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_IPAddress}</td><td>{ip_addr_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Started}</td><td>{started_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Updated}</td><td>{updated_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_TotalTime}</td><td>{total_time_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Pages}</td><td>{pages_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_AutoLogin}</td><td>{al_status} ({al_change})</td>
		</tr>
	</table>
';

$templates['subscriptions_header'] = '
	{form_begin}
	<table class="maintable">
		<tr>
			<th></th>
			<th>{l_Topic}</th>
			<th>{l_Author}</th>
			<th>{l_LatestPost}</th>
			<th></th>
		</tr>
';

$templates['subscriptions_topic'] = '
		<tr>
			<td class="icon"><img src="{img_dir}{topic_icon}" alt="{topic_status}" /></td>
			<td class="attopic">{topic_name}<div class="topicpagelinks">{topic_page_links}</div></td>
			<td class="author">{author}</td>
			<td class="lastpostinfo">{by_author} <a href="{last_post_url}">&gt;&gt;</a><br />{on_date}</td>
			<td class="minimal">{unsubscribe_check}</td>
		</tr>
';

$templates['subscriptions_footer'] = '
		<tr>
			<td colspan="5" class="formcontrols">{unsubscribe_submit}</td>
		</tr>
	</table>
	{form_end}
';

$templates['edit_profile'] = '
	{form_begin}
	<table class="maintable">
		<tr>
			<th colspan="2">{l_EditProfile}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Username}</td><td>{username}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_DisplayedName} ({l_Required})</td><td>{displayed_name_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_RealName}</td><td>{real_name_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_AvatarURL}</td><td>{avatar_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Location}</td><td>{location_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Website}</td><td>{website_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Occupation}</td><td>{occupation_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Interests}</td><td>{interests_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Signature}</td><td>{signature_input}</td>
		</tr>
		<tr>
			<th colspan="2">{l_ContactInfo}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Email} ({l_Required})</td><td>{email_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_MSNM}</td><td>{msnm_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_YahooM}</td><td>{yahoom_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_AIM}</td><td>{aim_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_ICQ}</td><td>{icq_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Jabber}</td><td>{jabber_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Skype}</td><td>{skype_input}</td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}&nbsp;{reset_button}</td>
		</tr>
	</table>
	{form_end}
';

$templates['edit_options'] = '
	{form_begin}
	<table class="maintable">
		<tr>
			<th colspan="2">{l_EditOptions}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Language}</td><td>{language_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Template}</td><td>{template_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_PublicEmail}</td><td>{email_show_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_PublicLastLogin}</td><td>{last_login_show_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_HideFromOnlineList}</td><td>{hide_from_online_list_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_DateFormat}</td><td>{date_format_input}<br />{date_format_help}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_Timezone}</td><td>{timezone_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_DST}</td><td>{dst_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_QuickReply}</td><td>{quickreply_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_ReturnToTopicAfterPosting}</td><td>{return_to_topic_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_AutoSubscribe}</td><td>{auto_subscribe_topic_input} {auto_subscribe_reply_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_OpenLinksNewWindow}</td><td>{target_blank_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_HideAllAvatars}</td><td>{hide_avatars_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_HideUserinfo}</td><td>{hide_userinfo_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_HideAllSignatures}</td><td>{hide_signatures_input}</td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}&nbsp;{reset_button}</td>
		</tr>
	</table>
	{form_end}
';

$templates['editpwd_form'] = '
	{form_begin}
	<table class="maintable">
		<tr>
			<th colspan="2">{l_EditPasswd}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{l_CurrentPassword}</td><td>{current_passwd_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_NewPassword}</td><td>{new_passwd1_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{l_NewPasswordAgain}</td><td>{new_passwd2_input}</td>
		</tr>
		<tr>
			<td colspan="2">{passwd_info} {l_EverythingRequired}</td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}</td>
		</tr>
	</table>
	{form_end}
';

?>
