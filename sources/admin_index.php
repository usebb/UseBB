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

$content = '<p>'.$lang['ACPIndexWelcome'].'</p>';

if ( !ini_get('allow_url_fopen') ) {
	
	$content .= '<p>'.sprintf($lang['ACPIndexVersionCheckFailed'], '<a href="http://www.usebb.net/">www.usebb.net</a>').'</p>';
	
} else {
	
	if ( !isset($_SESSION['latest_version']) ) {
		
		$fp = fopen('http://usebb.sourceforge.net/latest_version', 'r');
		$_SESSION['latest_version'] = trim(@fread($fp, 16));
		@fclose($fp);
		
	}
	
	$content .= '<p>'.sprintf($lang['ACPIndexVersionCheck'], USEBB_VERSION, $_SESSION['latest_version']).' ';
	
	if ( version_compare(USEBB_VERSION, $_SESSION['latest_version']) === -1 )
		$content .= '<strong>'.sprintf($lang['ACPIndexNeedUpdate'], '<a href="http://www.usebb.net/downloads/">www.usebb.net/downloads</a>').'</strong></p>';
	elseif ( version_compare(USEBB_VERSION, $_SESSION['latest_version']) === 1 )
		$content .= $lang['ACPIndexBewareDevVersions'].'</p>';
	else
		$content .= $lang['ACPIndexLatestVersion'].'</p>';
	
}

$admin_functions->create_body('index', $content);

?>
