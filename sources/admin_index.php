<?php

/*
	Copyright (C) 2003-2006 UseBB Team
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
 * ACP index
 *
 * Shows the ACP index with general information.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2006 UseBB Team
 * @package	UseBB
 * @subpackage	ACP
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

$content = '<p>'.$lang['IndexWelcome'].'</p>';

if ( !is_writable(ROOT_PATH.'config.php') ) {
	
	$content .= '<h2>'.$lang['IndexWarning'].'</h2>';
	$content .= '<p>'.sprintf($lang['IndexUnwritableConfig'], '<code>config.php</code>').'</p>';
	
}

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
	<li>'.$lang['IndexUseBBVersion'].': '.USEBB_VERSION.' (<a href="'.$functions->make_url('admin.php', array('act' => 'version')).'">'.$lang['Item-version'].'</a>)</li>
	<li>'.$lang['IndexPHPVersion'].': '.phpversion().'</li>
	<li>'.$lang['IndexSQLServer'].': '.join('/', $db->get_server_info()).'</li>
	<li>'.$lang['IndexHTTPServer'].': '.$_SERVER['SERVER_SOFTWARE'].'</li>
	<li>'.$lang['IndexOS'].': '.( ( array_key_exists('OS', $_ENV) ) ? $_ENV['OS'] : PHP_OS ).'</li>
	<li>'.$lang['IndexServerLoad'].': '.$server_load.'</li>
</ul>

<h2>'.$lang['IndexLinks'].'</h2>
<ul>
	<li><a href="http://www.usebb.net/">UseBB.net</a></li>
	<li><a href="http://www.usebb.net/community/">Support &amp; Community</a></li>
	<li><a href="http://usebb.sourceforge.net/">UseBB Development</a></li>
</ul>
<p id="admincopyright">Copyright &copy; 2003-2006 UseBB Team - Released under the GNU General Public License.</p>';

$admin_functions->create_body('index', $content);

?>
