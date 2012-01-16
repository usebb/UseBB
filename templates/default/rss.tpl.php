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
// RSS templates
//

$templates['header'] = '<?xml version="1.0" encoding="{character_encoding}"?>
<rss version="2.0" xml:lang="{language_code}" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<channel>
		<title>{board_name}</title>
		<link>{board_url}</link>
		<description>{board_descr}</description>
		<image>
			<url>{img_dir}usebb.png</url>
			<title>{board_name}</title>
			<link>{board_url}</link>
		</image>
		<language>{language_code}</language>
		<pubDate>{pubDate}</pubDate>
		<docs>http://www.rssboard.org/rss-specification</docs>
		<generator>UseBB</generator>
		<atom:link href="{link_rss}" rel="self" type="application/rss+xml" />
';

$templates['topic'] = '
		<item>
			<title>{title}</title>
			<description>{description}</description>
			<dc:creator>{author}</dc:creator>
			<link>{link}</link>
			<category domain="{category_domain}">{category}</category>
			<pubDate>{pubDate}</pubDate>
			<guid isPermaLink="true">{guid}</guid>
		</item>
';

$templates['footer'] = '
	</channel>
</rss>
';

?>
