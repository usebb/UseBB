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
// Initialize a new template holder array
//
$templates = array();

//
// Define configuration variables of this template set
//
$templates['config'] = array(
	'item_delimiter'					=> '&middot;',
	'locationbar_item_delimiter'		=> '&raquo;',
	'open_nonewposts_icon'				=> 'open_nonewposts.gif',
	'open_newposts_icon'				=> 'open_newposts.gif',
	'closed_nonewposts_icon'			=> 'closed_nonewposts.gif',
	'closed_newposts_icon'				=> 'closed_newposts.gif',
	'forumlist_topic_rtrim_completion'	=> '...',
	'forumlist_topic_rtrim_length'		=> '20',
	'new_topic_button'					=> 'newtopic.gif',
	'reply_button'						=> 'reply.gif',
	'quote_button'						=> 'quote.gif',
	'edit_button'						=> 'edit.gif',
	'delete_button'						=> 'delete.gif',
	'sig_format'						=> '<div class="signature">_______________<br />%s</div>',
	'quote_format'						=> '<blockquote class="quote"><div class="title">%s</div><div class="content">%s</div></blockquote>',
	'code_format'						=> '<pre class="code">%s</pre>',
	'post_editinfo_format'				=> '<div class="editinfo">&laquo;%s&raquo;</div>',
	'textarea_rows'						=> '10',
	'textarea_cols'						=> '60',
	'quick_reply_textarea_rows'			=> '5',
	'quick_reply_textarea_cols'			=> '60',
	'debug_info_small'					=> '<div id="debug-info-small">%s</div>',
	'debug_info_large'					=> '<div id="debug-info-large">%s</div>',
);

//
// Globally needed templates
//

$templates['normal_header'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="{text_direction}">
<head>
	<title>{board_name}: {page_title}</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset={character_encoding}" />
	<link rel="stylesheet" type="text/css" href="{css_url}" />
	<link rel="shortcut icon" href="{img_dir}usebb.ico" />
</head>
<body>
	<div id="pagebox-bg">
	<div id="shadow-left">
	<div id="shadow-right">
	
	<p id="logo"><a href="{link_home}"><img src="{img_dir}usebb.png" alt="UseBB" title="{home}" /></a></p>
	<h1 id="boardname"><span id="line">{board_name}</span></h1>
	<h2 id="boarddescr">{board_descr}</h2>
	
	<div id="topmenu"><ul>
		<li><a href="{link_home}">{home}</a></li><li><a href="{link_reg_panel}">{reg_panel}</a></li><li><a href="{link_faq}">{faq}</a></li><li><a href="{link_search}">{search}</a></li><li><a href="{link_active}">{active}</a></li><li><a href="{link_log_inout}">{log_inout}</a></li>
	</ul></div>
	<div id="topmenu-shadow"></div>
';

$templates['normal_footer'] = '
	<p id="linkbar">
		{link_bar}
	</p>
	{debug_info_small}
	<address id="bottom">
		<!--
			We request not to remove the following copyright notice including the link to the UseBB Home Page.
			This shows your respect to everyone involved in UseBB\'s development and promotes the use of UseBB.
			If you don\'t want to show the Copyright notice, just leave the Powered by UseBB line. If you
			completely alter or remove the notice, support at our community forums or IRC channel will be affected.
		-->
		Powered by <a href="http://www.usebb.net">UseBB</a> {usebb_version} &middot; Copyright &copy; 2003-2005 UseBB Team
	</address>
	<div id="bottom-shadow"></div>
	{debug_info_large}
	</div>
	</div>
	</div>
</body>
</html>
';

$templates['css'] = '
* {
	margin: 0px;
	padding: 0px;
}
html, body {
	min-height: 100%;
	font-size: 8pt;
	font-family: verdana, sans-serif;
	text-align: center;
	background-image: url({img_dir}body_bg.png);
	background-color: #CCCCCC;
	cursor: default;
}
#pagebox-bg {
	width: 785px;
	background-image: url({img_dir}pagebox_bg.png);
	background-repeat: repeat-x;
	background-color: #FFFFFF;
	margin: 0px auto 0px auto;
}
#shadow-left {
	background-image: url({img_dir}pageshadow_left.png);
	background-repeat: repeat-y;
	background-position: left;
}
#shadow-right {
	padding: 20px 30px 15px 30px;
	background-image: url({img_dir}pageshadow_right.png);
	background-repeat: repeat-y;
	background-position: right;
}
a img {
	border: 0px;
}
label {
	cursor: pointer;
}
a:link, a:active, a:visited {
	color: #336699;
	text-decoration: underline;
}
a:hover {
	color: #7F0000;
	text-decoration: none;
}
a.administrator:link, a.administrator:active, a.administrator:visited {
	color: #BF0000 !important;
}
a.moderator:link, a.moderator:active, a.moderator:visited {
	color: #0000BF !important;
}
input, select, textarea {
	font-size: 8pt !important;
}
textarea {
	width: 99%;
}
input[type="submit"], input[type="reset"], input[type="button"] {
	padding: 0px 10px 0px 10px;
	cursor: pointer;
}
optgroup {
	padding: 1px 1px 5px 1px;
}
optgroup option {
	padding: 1px 1px 1px 5px;
}
pre.code {
	display: block;
	margin: 0px 25px 0px 25px;
	padding: 5px;
	font-family: monospace;
	color: #7F0000;
	background-color: #FFFFFF;
	border: 1px solid #BFBFBF;
	overflow: auto;
	font-size: 8pt;
}
blockquote.quote {
	margin: 0px 25px 0px 25px;
	font-size: 8pt;
}
blockquote.quote .title {
	font-weight: bold;
	padding: 3px;
}
blockquote.quote .content {
	padding: 10px;
	color: #333333;
	background-image: url({img_dir}quote_bg.png);
	background-repeat: no-repeat;
	background-position: top right;
	background-color: #FFFFFF;
	border: 1px solid #BFBFBF;
	overflow: auto;
}
strong {
	font-weight: bold;
}
em, em.underline em, em em.underline {
	font-style: italic;
}
em.underline {
	text-decoration: underline;
	font-style: normal;
}
#logo {
	float: left;
}
h1#boardname {
	text-align: right;
	font-size: 13pt;
	font-weight: bold;
	color: #336699;
	height: 30px !important;
	line-height: 30px !important;
}
h1#boardname #line {
	padding: 0px 0px 2px 0px;
	border-bottom: 2px solid #EBD6AD;
}
h2#boarddescr {
	text-align: right;
	font-size: 10pt;
	font-weight: normal;
	font-style: italic;
	color: #7F7F7F;
	height: 30px !important;
	line-height: 30px !important;
}
#topmenu * {
	text-align: left;
}
#topmenu {
	float: left;
	width: 723px;
	background-image: url({img_dir}topmenu_bg.png);
	background-repeat: repeat-x;
	background-color: #E8E8E8;
	border: 1px solid #336699;
	padding: 3px 0px 3px 0px;
	margin: 4px 0px 0px 0px;
}
#topmenu ul {
	list-style: none;
}
#topmenu ul li {
	display: inline;
}
#topmenu ul li a:link, #topmenu ul li a:visited, #topmenu ul li a:hover {
	text-decoration: none;
	padding: 3px 7px 3px 7px;
	border-right: 1px solid #336699;
}
#topmenu ul li#logout a:link, #topmenu ul li#logout a:visited, #topmenu ul li#logout a:hover {
	border-right: 0px;
	border-left: 1px solid #336699;
}
#topmenu ul li a:link, #topmenu ul li a:visited {
	color: #336699;
}
#topmenu ul li a:active, #topmenu ul li a:hover {
	color: #7F0000;
	background-image: url({img_dir}topmenu_bg_reverse.png);
	background-repeat: repeat-x;
	background-color: #FFFFFF;
}
#topmenu-shadow {
	clear: both;
	height: 5px;
	background-image: url({img_dir}topmenu_shadow.png);
	background-repeat: repeat-x;
	background-color: #FFFFFF;
	line-height: 100%;
	overflow: hidden;
	margin: 0px 0px 15px 0px;
	width: 100%; /* Be nice to Safari */
}
p.locationbar {
	clear: both;
	text-align: left;
	font-style: italic;
	color: #333333;
	margin: -10px 0px 10px 0px;
	padding: 3px;
}
p.locationbar a {
	font-style: normal;
}

/* Global styles */

table.maintable, table.msgbox, table.confirmform {
	clear: both;
	border-collapse: collapse;
	border-left: 1px solid silver;
	border-right: 1px solid silver;
	border-bottom: 1px solid silver;
	margin: 0px 0px 20px 0px;
	width: 100%;
}
table.maintable th, table.msgbox th, table.confirmform th {
	color: #EBD6AD;
	font-weight: bold;
	background-color: #336699;
	background-image: url({img_dir}tableheadbg.gif);
	background-position: top;
	background-repeat: repeat-x;
	text-align: left;
	padding: 4px 6px 4px 6px;
	border-left: 1px solid silver;
	border-top: 1px solid silver;
}
table.maintable td, table.msgbox td, table.confirmform td, td.msg {
	background-color: #EFEFEF;
	padding: 6px;
	text-align: left;
	border-left: 1px solid silver;
	border-top: 1px solid silver;
	vertical-align: middle;
}
table.maintable td a:visited {
	color: #555555;
}
table.msgbox td, table.confirmform td.msg, td.msg {
	padding: 18px 36px 18px 36px !important;
	font-size: 10pt;
}
table.maintable td.fieldtitle {
	background-color: #E8E8E8;
	width: 25%;
	font-weight: bold;
}
td.formcontrols {
	background-color: #E8E8E8 !important;
	text-align: center !important;
	padding: 6px !important;
}
table.maintable td.count {
	background-color: #E8E8E8;
	width: 1%;
	text-align: center;
	font-weight: bold;
	color: #333333;
}
table.maintable td.lastpostinfo {
	width: 195px;
}
table.maintable td.icon {
	background-color: #E8E8E8;
	width: 1px;
}

/* Forumlist styles */

table.maintable td.forumcat {
	font-weight: bold;
	background-image: url({img_dir}topmenu_bg.png);
	background-repeat: repeat-x;
	background-color: #E8E8E8;
	color: #333333;
}
table.maintable td .forumname {
	font-weight: bold;
}
table.maintable td .forumdescr {
	margin: 3px 0px 0px 3px;
}
table.maintable td .forummods {
	margin: 3px 0px 0px 3px;
	color: #444444;
}

/* Topiclist styles */

#forumname {
	font-size: 11pt;
	font-weight: bold;
	text-align: left;
}
#forumname a:link, #forumname a:hover, #forumname a:active, #forumname a:visited {
	text-decoration: none;
}
#forummods {
	text-align: left;
	color: #444444;
	margin: 0px 0px 6px 0px;
}
#toolbartop {
	float: right;
	text-align: right;
	margin: 0px 0px 6px 0px;
}
#pagelinkstop {
	float: left;
	line-height: 23px;
	font-weight: bold;
	margin: 0px 0px 6px 0px;
}
.topicpagelinks {
	display: block;
	text-align: right;
	color: #444444;
}
#toolbarbottom {
	float: right;
	text-align: right;
	margin: -14px 0px 0px 0px;
}
#pagelinksbottom {
	float: left;
	line-height: 23px;
	font-weight: bold;
	margin: -14px 0px 0px 0px;
}
#bottomfix {
	height: 20px;
}
table.maintable td.author {
	text-align: center;
	width: 1%;
	white-space: nowrap;
}

/* Topic styles */

table.maintable tr.tr1 td {
	background-color: #EFEFEF;
}
table.maintable tr.tr2 td {
	background-color: #E8E8E8;
}
table.maintable td.postername {
	text-align: center;
	font-size: 10pt;
	font-weight: bold;
	width: 135px;
}
table.maintable td.postinfo {
	vertical-align: middle;
}
table.maintable td.postinfo .postdate {
	margin: 2px 0px 0px 0px;
	color: #444444;
}
table.maintable td.postinfo .postlinks {
	float: right;
}
table.maintable td.posterinfo {
	vertical-align: top;
	text-align: center;
	width: 135px;
}
table.maintable td.posterinfo .avatar {
	margin-top: 10px;
}
table.maintable td.posterinfo .avatar img {
	margin-bottom: 10px;
}
table.maintable td.posterinfo .field {
	color: #444444;
}
table.maintable td.postcontent {
	vertical-align: top;
}
table.maintable td.postcontent .post {
	overflow: auto;
	font-size: 10pt;
}
table.maintable td.postcontent .signature {
	color: #444444;
	font-size: 8pt;
}
table.maintable td.postcontent .editinfo {
	margin: 5px 0px 0px 0px;
	border-top: 1px solid silver;
	padding: 5px 0px 0px 0px;
	color: #444444;
	font-size: 8pt;
	font-style: italic;
}
table.maintable td.postcontent .poster-ip-addr {
	color: #666666;
	font-size: 7pt;
	text-align: right;
}
table.maintable tr.postseperator td {
	font-size: 0pt;
	background-color: #D8D8D8;
	padding: 0px;
	height: 4px;
}
#actionlinks {
	color: #323232;
	text-align: left;
	padding: 3px;
	margin-bottom: 10px;
	margin-top: -10px;
	font-size: 7pt;
}

/* FAQ styles */

table.maintable td.faqheading {
	font-weight: bold;
	background-image: url({img_dir}topmenu_bg.png);
	background-repeat: repeat-x;
	background-color: #E8E8E8;
}
table.maintable td.faqquestion .questiontitle {
	font-weight: bold;
	font-style: italic;
}
table.maintable td.faqquestion .questionanswer {
	margin: 0px 0px 0px 20px;
}

/* Active topics styles */

table.maintable td.atforum {
	font-size: 7pt;
	width: 1%;
}
table.maintable td.attopic {
}

/* */

table.maintable td.minimal {
	background-color: #E8E8E8;
	width: 1%;
	white-space: nowrap;
	text-align: center;
}
p#panelmenu {
	color: #323232;
	text-align: center;
	padding: 3px;
	margin-bottom: 10px;
	margin-top: -10px;
}
p#linkbar {
	color: #323232;
	text-align: center;
	margin-bottom: 20px;
}
#bottom {
	clear: both;
	float: left;
	width: 705px;
	padding: 3px 9px 3px 9px;
	background-image: url({img_dir}topmenu_bg.png);
	background-repeat: repeat-x;
	background-color: #E8E8E8;
	border: 1px solid #336699;
	font-style: normal;
	text-align: right;
	color: #333333;
}
#bottom a:link, #bottom a:hover, #bottom a:active, #bottom a:visited {
	text-decoration: none;
}
#bottom-shadow {
	clear: both;
	height: 5px;
	background-image: url({img_dir}topmenu_shadow.png);
	background-repeat: repeat-x;
	background-color: #FFFFFF;
	line-height: 100%;
	overflow: hidden;
	width: 100%; /* Be nice to Safari */
}

#debug-info-small {
	margin: 0px auto 20px auto;
	padding: 3px;
	text-align: center;
	background-color: #E8E8E8;
	border: 1px solid silver;
	color: #333333;
	font-size: 7pt;
}

#debug-info-large {
	margin: 20px 0px 0px 0px;
	font-size: 7pt;
	text-align: center;
	color: #333333;
}
#debug-info-large select, #debug-info-large textarea {
	color: #333333;
	font-size: 7pt !important;
	margin: 3px 0px 0px 0px;
}
';

$templates['location_bar'] = '
	<p class="locationbar">
		&bull; {location_bar}
	</p>
';

$templates['msgbox'] = '
	<table class="msgbox">
		<tr>
			<th>{box_title}</th>
		</tr>
		<tr>
			<td>{content}</td>
		</tr>
	</table>
';

$templates['confirm_form'] = '
	{form_begin}
	<table class="confirmform">
		<tr>
			<th>{title}</th>
		</tr>
		<tr>
			<td class="msg">{content}</td>
		</tr>
		<tr>
			<td class="formcontrols">{submit_button}&nbsp;{cancel_button}</td>
		</tr>
	</table>
	{form_end}
';

?>
