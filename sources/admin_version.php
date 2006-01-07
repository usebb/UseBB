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

/**
 * ACP version check
 *
 * Gives an interface to check for the latest UseBB version.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2005 UseBB Team
 * @package	UseBB
 * @subpackage	ACP
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

if ( !@ini_get('allow_url_fopen') ) {
	
	$content = '<p>'.sprintf($lang['VersionFailed'], '<code>allow_url_fopen</code>', '<a href="http://www.usebb.net/">www.usebb.net</a>').'</p>';
	
} else {
	
	if ( !isset($_SESSION['latest_version']) ) {
		
		$fp = fopen('http://usebb.sourceforge.net/latest_version', 'r');
		$_SESSION['latest_version'] = trim(@fread($fp, 16));
		@fclose($fp);
		
	}
	
	switch ( version_compare(USEBB_VERSION, $_SESSION['latest_version']) ) {
		
		case -1:
			$content = '<h2>'.$lang['VersionNeedUpdateTitle'].'</h2>';
			$content .= '<p><strong>'.sprintf($lang['VersionNeedUpdate'], USEBB_VERSION, $_SESSION['latest_version'], '<a href="http://www.usebb.net/downloads/">www.usebb.net/downloads</a>').'</strong></p>';
			break;
		case 1:
			$content = '<h2>'.$lang['VersionBewareDevVersionsTitle'].'</h2>';
			$content .= '<p>'.sprintf($lang['VersionBewareDevVersions'], USEBB_VERSION, $_SESSION['latest_version']).'</p>';
			break;
		default:
			$content = '<h2>'.$lang['VersionLatestVersionTitle'].'</h2>';
			$content .= '<p>'.sprintf($lang['VersionLatestVersion'], USEBB_VERSION).'</p>';
		
	}
	
}

$admin_functions->create_body('version', $content);

?>
