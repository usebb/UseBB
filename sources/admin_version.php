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

/**
 * ACP version check
 *
 * Gives an interface to check for the latest UseBB version.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2012 UseBB Team
 * @package	UseBB
 * @subpackage	ACP
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

$success = false;

if ( !empty($_SESSION['latest_version']) ) {
	
	//
	// Already in session
	//
	$success = true;
	
} else {
	
	$found_version = $admin_functions->read_remote_file('http://usebb.sourceforge.net/latest_version_extended');
	
	//
	// Check for valid version
	//
	if ( !empty($found_version) ) {
		
		$_SESSION['latest_version'] = $found_version;
		$success = true;
		
	}

}

if ( !$success ) {
	
	$content = '<p>'.sprintf($lang['VersionFailed'], '<a href="http://www.usebb.net/">www.usebb.net</a>').'</p>';
	
} else {
	
	$content = '';
	$msg = preg_split("#[\r\n]{2,}#", $_SESSION['latest_version']);
	
	//
	// Version comparing
	//
	if ( preg_match('#^[0-9]+\.[0-9]+#', $msg[0]) ) {
		
		$version = array_shift($msg);

		switch ( version_compare(USEBB_VERSION, $version) ) {
			
			case -1:
				$content .= '<h2>'.$lang['VersionNeedUpdateTitle'].'</h2>';
				$content .= '<p><strong>'.sprintf($lang['VersionNeedUpdate'], USEBB_VERSION, unhtml($version), '<a href="http://www.usebb.net/downloads/">www.usebb.net/downloads</a>').'</strong></p>';
				break;
			case 1:
				$content .= '<h2>'.$lang['VersionBewareDevVersionsTitle'].'</h2>';
				$content .= '<p>'.sprintf($lang['VersionBewareDevVersions'], USEBB_VERSION, unhtml($version)).'</p>';
				break;
			default:
				$content .= '<h2>'.$lang['VersionLatestVersionTitle'].'</h2>';
				$content .= '<p>'.sprintf($lang['VersionLatestVersion'], USEBB_VERSION).'</p>';
			
		}

	}

	//
	// Messages
	//
	foreach ( $msg as $line ) {
		
		// Denote headers with leading =
		if ( substr($line, 0, 1) == '=' ) {
			
			$line = substr($line, 1);
			$content .= '<h2>'.unhtml($line).'</h2>';

			continue;
			
		}

		$content .= '<p>'.nl2br(unhtml($line)).'</p>';

	}
	
}

$admin_functions->create_body('version', $content);

?>
