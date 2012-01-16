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
// FAQ templates
//

$templates['contents_header'] = '
	<table class="maintable">
		<tr>
			<th>{l_FAQ}</th>
		</tr>
		<tr>
			<td id="faq-contents">
';

$templates['contents_cat_header'] = '
				<h3>{cat_name}</h3>
				<ul>
';

$templates['contents_question'] = '
					<li>{question_entry}</li>
';

$templates['contents_cat_footer'] = '
				</ul>
';

$templates['contents_footer'] = '
			</td>
		</tr>
	</table>
';

$templates['question'] = '
	<table class="maintable">
		<tr>
			<th>{question_title}</th>
		</tr>
		<tr>
			<td id="question">{question_answer}</td>
		</tr>
	</table>
';

?>
