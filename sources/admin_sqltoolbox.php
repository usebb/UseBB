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
 * ACP SQL toolbox
 *
 * Gives an interface to execute SQL queries and basic database maintenance.
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

if ( isset($_POST['warned']) )
	$_SESSION['sqltoolbox_warned'] = true;

if ( !isset($_SESSION['sqltoolbox_warned']) ) {
	
	$content = '<h2>'.$lang['SQLToolboxWarningTitle'].'</h2><p><strong>'.$lang['SQLToolboxWarningContent'].'</strong></p>';
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'sqltoolbox')).'" method="post">';
	$content .= '<p class="submit"><input type="submit" name="warned" value="'.$lang['OK'].'" /></p></form>';
	
} else {
	
	$content = '<h2>'.$lang['SQLToolboxExecuteQuery'].'</h2><p>'.$lang['SQLToolboxExecuteQueryInfo'].'</p>';
	
	if ( !empty($_POST['query']) ) {
		
		$result = $db->query(stripslashes($_POST['query']), true);
		
		if ( is_resource($result) ) {
			
			$results = array();
			while ( $out = $db->fetch_result($result) )
				$results[] = $out;
			ob_start();
			print_r($results);
			$results = ob_get_contents();
			ob_end_clean();
			$results = unhtml(stripslashes(trim($results)));
			$content .= '<p><textarea rows="5" cols="50" readonly="readonly" id="resultset">'.$results.'</textarea></p>';
			
		} elseif ( $result === true ) {
			
			$content .= '<p>'.$lang['SQLToolboxExecutedSuccessfully'].'</p>';
			
		} else {
			
			$content .= '<p><strong>'.unhtml($result).'.</strong></p>';
			
		}
		
	} else {
		
		$_POST['query'] = '';
		
	}
	
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'sqltoolbox')).'" method="post">';
	$content .= '<p><textarea name="query" rows="5" cols="50">'.stripslashes($_POST['query']).'</textarea></p>';
	$content .= '<p class="submit"><input type="submit" value="'.$lang['SQLToolboxExecute'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></p></form>';
	
	$content .= '<h2>'.$lang['SQLToolboxMaintenance'].'</h2><p>'.$lang['SQLToolboxMaintenanceInfo'].'</p><ul id="adminfunctionsmenu">';
	
	if ( !empty($_GET['do']) && $_GET['do'] == 'repair' ) {
		
		$db->query("REPAIR TABLE ".join(', ', $functions->get_usebb_tables()));
		$content .= '<li>'.$lang['SQLToolboxRepairTables'].': '.$lang['Done'].'</li> ';
		
	} else {
		
		$content .= '<li><a href="'.$functions->make_url('admin.php', array('act' => 'sqltoolbox', 'do' => 'repair')).'">'.$lang['SQLToolboxRepairTables'].'</a></li> ';
		
	}
	
	if ( !empty($_GET['do']) && $_GET['do'] == 'optimize' ) {
		
		$db->query("OPTIMIZE TABLE ".join(', ', $functions->get_usebb_tables()));
		$content .= '<li>'.$lang['SQLToolboxOptimizeTables'].': '.$lang['Done'].'</li> ';
		
	} else {
		
		$content .= '<li><a href="'.$functions->make_url('admin.php', array('act' => 'sqltoolbox', 'do' => 'optimize')).'">'.$lang['SQLToolboxOptimizeTables'].'</a></li> ';
		
	}
	
	$content .= '</ul><p>'.$lang['SQLToolboxMaintenanceNote'].'</p>';
	
}

$admin_functions->create_body('sqltoolbox', $content);

?>
