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
// Initialize a new template holder array
//
$templates = array();

//
// Define configuration variables of this template set
//
$templates['config'] = array(
	'item_delimiter'							=> '&middot;',
	'locationbar_item_delimiter'			=> '&raquo;',
	'open_nonewposts_icon'					=> 'open_nonewposts.gif',
	'open_newposts_icon'						=> 'open_newposts.gif',
	'closed_nonewposts_icon'				=> 'closed_nonewposts.gif',
	'closed_newposts_icon'					=> 'closed_newposts.gif',
	'forumlist_topic_rtrim_completion'	=> '...',
	'forumlist_topic_rtrim_length'		=> '20',
	'new_topic_button'						=> 'newtopic.gif',
	'reply_button'								=> 'reply.gif',
	'quote_button'								=> 'quote.gif',
	'edit_button'								=> 'edit.gif',
	'delete_button'							=> 'delete.gif',
	'sig_format'								=> '<hr /><small>%s</small>',
	'quote_format'								=> '<blockquote><b>%s</b><hr />%s</blockquote>',
	'code_format'								=> '<pre>%s</pre>',
	'textarea_rows'							=> '10',
	'textarea_cols'							=> '60',
	'quick_reply_textarea_rows'			=> '5',
	'quick_reply_textarea_cols'			=> '60',
);

//
// Globally needed templates
//

$templates['normal_header'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>{board_name}: {page_title}</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="{css_url}" />
</head>
<body>
	<div align="center">
	<div class="main">
	
	<table class="header">
		<tr>
			<td class="logo"><a href="{link_home}"><img src="{img_dir}usebb.png" alt="UseBB" title="{home}" /></a></td>
			<td class="namebox" nowrap="nowrap"><div class="title">{board_name}</div><div class="descr">{board_descr}</div></td>
		</tr>
	</table>
	
	<div class="menu">
		<a href="{link_home}" accesskey="h">{home}</a><a href="{link_reg_panel}">{reg_panel}</a><a href="{link_faq}">{faq}</a><a href="{link_search}">{search}</a><a href="{link_active}">{active}</a><a href="{link_log_inout}">{log_inout}</a>
	</div>
';

$templates['normal_footer'] = '
	<div class="linkbar">
		{link_bar}
	</div>
	<div class="banners">
		<a href="http://www.usebb.net"><img src="{img_dir}powered-by-usebb.png" alt="Powered by UseBB" /></a>
		<a href="http://validator.w3.org/check/referer"><img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0!" /></a>
		<a href="http://jigsaw.w3.org/css-validator/check/referer"><img src="http://jigsaw.w3.org/css-validator/images/vcss" alt="Valid CSS!" /></a>
		<a href="http://www.php.net"><img src="{img_dir}php-power-white.gif" alt="Powered by PHP" /></a>
		<a href="http://www.mysql.com"><img src="{img_dir}powered-by-mysql-88x31.png" alt="Powered by MySQL" /></a>
	</div>
	<div class="copyright">
		<!--
			We request not to remove the following copyright notice including the link to the UseBB Home Page.
			This shows your respect to everyone involved in UseBB\'s development and promotes the use of UseBB.
			If you don\'t want to show the Copyright notice, just leave the Powered by UseBB line. If you
			completely alter or remove the notice, support at our community forums or IRC channel will be affected.
		-->
		Powered by <a href="http://www.usebb.net">UseBB</a> {usebb_version} - Copyright &copy; 2003-2004 UseBB Team
	</div>
	
	</div>
	</div>
</body>
</html>
';

$templates['css'] = '
body {
	margin: 0px;
	padding: 0px;
	background-color: #EFEFEF;
}
body, td, th {
	font-family: verdana, sans-serif;
	font-size: 10pt;
	color: #000000;
}
img {
	border: 0px;
}
form {
	margin: 0px;
}
label {
	cursor: pointer;
}
hr {
	border: 0px solid silver;
	border-top-width: 1px;
	height: 0px;
}
small {
	font-size: 8pt;
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
	color: red;
}
a.moderator:link, a.moderator:active, a.moderator:visited {
	color: blue;
}
input, select, textarea {
	border: 1px solid silver;
	background-color: #FFFFFF;
	font-size: 10pt;
}
textarea {
	font-family: verdana, sans-serif;
	font-size: 8pt;
}
pre {
	display: block;
	margin: 0px;
	padding: 5px;
	font-size: 8pt;
	font-family: monospace;
	color: #7F0000;
	background-color: #FFFFFF;
	border: 1px solid #CDCDCD;
	overflow: auto;
}
blockquote {
	display: block;
	margin: 0px;
	padding: 10px;
	font-size: 8pt;
	color: #333333;
	background-color: #FFFFFF;
	border: 1px solid #BFBFBF;
	overflow: auto;
}
.main {
	width: 750px;
	padding: 20px;
	margin-top: 20px;
	margin-bottom: 20px;
	background-color: #FFFFFF;
	border-left: 1px solid #E5E5E5;
	border-right: 1px solid #E5E5E5;
	border-top: 5px solid #E5E5E5;
	border-bottom: 5px solid #E5E5E5;
}
.header {
	border-collapse: collapse;
	width: 100%;
	margin-bottom: 10px;
}
.header td {
	padding: 0px;
	vertical-align: bottom;
}
.header td.logo {
	text-align: left;
	width: 100%;
}
.header td.namebox {
	text-align: right;
}
.header td.namebox .title {
	font-family: "trebuchet ms", sans-serif;
	font-size: 16pt;
	font-weight: bold;
	letter-spacing: 1px;
	color: #336699;
	border-bottom: 2px solid #ebd6ad;
}
.header td.namebox .descr {
	font-style: italic;
	padding-top: 2px;
}
.menu {
	border: 1px solid #336699;
	background-image: url({img_dir}menubg.gif);
	background-repeat: repeat-x;
	background-color: #E5E5E5;
	text-align: left;
	padding-top: 4px;
	padding-bottom: 4px;
	margin-bottom: 20px;
	font-size: 8pt;
}
.menu a, .menu a:visited {
	padding-left: 10px;
	padding-right: 10px;
	padding-top: 4px;
	padding-bottom: 4px;
	text-decoration: none;
	border-right: 1px solid #336699;
}
.menu a:hover {
	background-image: url({img_dir}menubg2.gif);
	background-repeat: repeat-x;
	background-color: #FFFFFF;
	text-decoration: none;
	border-right: 1px solid #336699;
}
.locationbar {
	text-align: left;
	font-size: 8pt;
	font-style: italic;
	color: #333333;
	margin-bottom: 20px;
	padding: 3px;
	background-color: #EFEFEF;
	border: 1px solid #BFBFBF;
}
.locationbar a {
	font-style: normal;
}
.maintable, .msgbox, .confirmform {
	border-collapse: collapse;
	border-left: 1px solid #336699;
	border-right: 1px solid #336699;
	border-bottom: 2px solid #336699;
	margin-bottom: 20px;
}
.maintable th, .msgbox th, .confirmform th {
	color: #EBD6AD;
	font-weight: bold;
	font-size: 8pt;
	background-color: #336699;
	background-image: url({img_dir}tableheadbg.gif);
	background-position: top;
	background-repeat: repeat-x;
	text-align: left;
	padding: 6px;
	padding-top: 3px;
	padding-bottom: 3px;
	border-left: 1px solid #336699;
	border-top: 1px solid #336699;
}
.maintable td, .msgbox td, .confirmform td, td.msg {
	background-color: #EFEFEF;
	padding: 6px;
	text-align: left;
	border-left: 1px solid #336699;
	border-top: 1px solid #336699;
	vertical-align: middle;
}
.maintable {
	width: 100%;
}
.maintable td.forumcat {
	font-weight: bold;
	background-image: url({img_dir}menubg.gif);
	background-repeat: repeat-x;
	background-color: #E5E5E5;
}
.maintable td.toolbar {
	background-image: url({img_dir}menubg.gif);
	background-repeat: repeat-x;
	background-color: #E5E5E5;
}
.maintable td.toolbar img {
	margin-left: 5px;
}
.maintable td.td1 {
	background-color: #EFEFEF;
}
.maintable td.td2 {
	background-color: #E5E5E5;
}
.maintable tr.post {
	border-left: 1px solid #336699;
}
.maintable tr.post td {
	vertical-align: top;
	border-left: 0px;
}
.msgbox td, .confirmform td.content, td.msg {
	padding: 18px;
	padding-left: 36px;
	padding-right: 36px;
}
.confirmform td.buttons {
	background-color: #E5E5E5;
}
.avatar {
	margin-top: 10px;
}
.avatar img {
	margin-bottom: 10px;
}
.posterinfo {
	font-size: 8pt;
	color: #3F3F3F;
}
.postlinks {
	float: right;
	font-size: 8pt;
}
.postcontent {
	padding-top: 6px;
	padding-bottom: 6px;
	overflow: auto;
}
.panelmenu {
	border-collapse: collapse;
	border-left: 1px solid #336699;
	border-right: 1px solid #336699;
	border-bottom: 1px solid #336699;
	margin-bottom: 20px;
}
.panelmenu td {
	background-color: #E5E5E5;
	padding: 5px;
	padding-left: 15px;
	padding-right: 15px;
	text-align: center;
	border-left: 1px solid #336699;
	border-top: 1px solid #336699;
	vertical-align: middle;
	font-size: 8pt;
}
.linkbar {
	color: #323232;
	margin-bottom: 20px;
	font-size: 8pt;
}
.banners {
	text-align: center;
}
.copyright {
	margin-top: 2px;
	color: #323232;
	font-size: 8pt;
}
';

$templates['location_bar'] = '
	<div class="locationbar">
		&bull; {location_bar}
	</div>
';

$templates['msgbox'] = '
	<table class="msgbox">
		<tr class="tablehead">
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
		<tr class="tablehead">
			<th>{title}</th>
		</tr>
		<tr>
			<td class="content">{content}</td>
		</tr>
		<tr>
			<td class="buttons"><div align="center">{submit_button}&nbsp;{cancel_button}</div></td>
		</tr>
	</table>
	{form_end}
';

?>
