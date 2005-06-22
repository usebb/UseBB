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

$content = '';

if ( !empty($_REQUEST['ip']) && preg_match('#^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$#', $_REQUEST['ip']) ) {
	
	$hostname = @gethostbyaddr($_REQUEST['ip']);
	
	if ( !empty($hostname) && $_REQUEST['ip'] != $hostname )
		$content .= '<p>'.sprintf($lang['ACPIPLookupResult'], '<em>'.$_REQUEST['ip'].'</em>', '<em>'.$hostname.'</em>').'</p>';
	else
		$content .= '<p>'.sprintf($lang['ACPIPLookupNotFound'], '<em>'.$_REQUEST['ip'].'</em>').'</p>';
	
}

$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'iplookup')).'" method="post"><p>'.$lang['IPAddress'].': <input type="text" name="ip" id="ip" size="15" maxlength="15" /> <input type="submit" value="'.$lang['OK'].'" /></p></form>';

$admin_functions->create_body('iplookup', $content);
$template->set_js_onload("set_focus('ip')");

?>
