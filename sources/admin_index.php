<?php

/*
	Copyright (C) 2003-2012 UseBB Team
	http://www.usebb.net
	
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
 * ACP index
 *
 * Shows the ACP index with general information.
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

$content = '<p>'.$lang['IndexWelcome'].'</p>';

$warnings = array();
if ( !empty($lang['character_encoding']) && strtolower($lang['character_encoding']) == 'utf-8' )
	$warnings[] = sprintf($lang['IndexMultibyteUsage'], strtoupper($lang['character_encoding']));
if ( !USEBB_IS_PROD_ENV )
	$warnings[] = $lang['IndexDevelopmentEnvironment'];
if ( count($warnings) )
	$content .= '<h2>'.$lang['IndexWarning'].'</h2>' . '<ul><li>'.join('</li><li>', $warnings).'</li></ul>';

$content .= '<h2><a href="'.$functions->make_url('admin.php', array('act' => 'activate_members')).'">'.$lang['IndexUnactiveMembers'].'</a></h2>';
$result = $db->query("SELECT COUNT(id) as count FROM ".TABLE_PREFIX."members WHERE active = 0 AND active_key = ''");
$out = $db->fetch_result($result);
switch ( $out['count'] ) {
	
	case 0:
		$content .= '<p>'.$lang['IndexNoUnactiveMembers'].'</p>';
		break;
	case 1:
		$content .= '<p><strong>'.$lang['IndexOneUnactiveMember'].'</strong></p>';
		break;
	default:
		$content .= '<p><strong>'.sprintf($lang['IndexMoreUnactiveMembers'], $out['count']).'</strong></p>';
	
}

if ( ( $server_load = $functions->get_server_load('all') ) == true )
	$server_load = sprintf('%.2f, %.2f, %.2f', $server_load[0], $server_load[1], $server_load[2]);
else	
	$server_load = $lang['Unknown'];

$content .= '<h2>'.$lang['IndexSystemInfo'].'</h2>
<ul>
	<li>'.$lang['IndexUseBBVersion'].': '.USEBB_VERSION.' &ndash; <a href="'.$functions->make_url('admin.php', array('act' => 'version')).'">'.$lang['Item-version'].'</a></li>
	<li>'.$lang['IndexPHPVersion'].': '.PHP_VERSION.'</li>
	<li>'.$lang['IndexSQLServer'].': '.join('/', $db->get_server_info()).'</li>
	<li>'.$lang['IndexHTTPServer'].': '.$_SERVER['SERVER_SOFTWARE'].'</li>
	<li>'.$lang['IndexOS'].': '.( ( array_key_exists('OS', $_ENV) ) ? $_ENV['OS'] : PHP_OS ).'</li>
	<li>'.$lang['IndexServerLoad'].': '.$server_load.'</li>
</ul>

<h2>'.$lang['IndexLinks'].'</h2>
<ul>
	<li><a href="http://www.usebb.net/">UseBB.net</a></li>
	<li><a href="http://www.usebb.net/community/">Support Forum</a></li>
</ul>
<p id="admincopyright">By the UseBB Project and contributors &mdash; Released under the GNU GPLv2</p>';

$admin_functions->create_body('index', $content);

?>
