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
	<p id="panelmenu">{panel_home} &middot; {view_profile} &middot; {panel_profile} &middot; {panel_options} &middot; {panel_passwd}</p>
';

$templates['panel_sess_info'] = '
	<table class="maintable">
		<tr>
			<th colspan="2">{title}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{sess_id}</td><td>{sess_id_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{ip_addr}</td><td>{ip_addr_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{started}</td><td>{started_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{updated}</td><td>{updated_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{total_time}</td><td>{total_time_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{pages}</td><td>{pages_v}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{al}</td><td>{al_status} <small>({al_change})</small></td>
		</tr>
	</table>
';

$templates['edit_profile'] = '
	{form_begin}
	<table class="maintable">
		<tr>
			<th colspan="2">{edit_profile}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{email} <small>({required})</small></td><td>{email_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{avatar}</td><td>{avatar_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{location}</td><td>{location_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{website}</td><td>{website_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{occupation}</td><td>{occupation_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{interests}</td><td>{interests_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{signature}</td><td>{signature_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{msnm}</td><td>{msnm_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{yahoom}</td><td>{yahoom_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{aim}</td><td>{aim_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{icq}</td><td>{icq_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{jabber}</td><td>{jabber_input}</td>
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
			<th colspan="2">{edit_options}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{language}</td><td>{language_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{template}</td><td>{template_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{email_show}</td><td>{email_show_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{last_login_show}</td><td>{last_login_show_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{hide_from_online_list}</td><td>{hide_from_online_list_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{date_format}</td><td>{date_format_input}<br /><small>{date_format_help}</small></td>
		</tr>
		<tr>
			<td class="fieldtitle">{timezone}</td><td>{timezone_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{dst}</td><td>{dst_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{quickreply}</td><td>{quickreply_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{return_to_topic}</td><td>{return_to_topic_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{target_blank}</td><td>{target_blank_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{hide_avatars}</td><td>{hide_avatars_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{hide_signatures}</td><td>{hide_signatures_input}</td>
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
			<th colspan="2">{edit_pwd}</th>
		</tr>
		<tr>
			<td class="fieldtitle">{current_passwd}</td><td>{current_passwd_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{new_passwd}</td><td>{new_passwd1_input}</td>
		</tr>
		<tr>
			<td class="fieldtitle">{new_passwd_again}</td><td>{new_passwd2_input}</td>
		</tr>
		<tr>
			<td colspan="2"><small>{everything_required}</small></td>
		</tr>
		<tr>
			<td colspan="2" class="formcontrols">{submit_button}&nbsp;{reset_button}</td>
		</tr>
	</table>
	{form_end}
';

?>
