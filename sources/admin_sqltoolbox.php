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
 * ACP SQL toolbox
 *
 * Gives an interface to execute SQL queries and basic database maintenance.
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

//
// Function used for array_map().
//
function sqltoolbox_resultval_clean($string) {

	return unhtml(stripslashes(trim($string)));

}

if ( isset($_POST['warned']) )
	$_SESSION['sqltoolbox_warned'] = true;

if ( !isset($_SESSION['sqltoolbox_warned']) ) {
	
	$content = '<h2>'.$lang['SQLToolboxWarningTitle'].'</h2><p><strong>'.$lang['SQLToolboxWarningContent'].'</strong></p>';
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'sqltoolbox')).'" method="post">';
	$content .= '<p class="submit"><input type="submit" name="warned" value="'.$lang['OK'].'" /></p></form>';
	
} else {
	
	$content = '<h2>'.$lang['SQLToolboxExecuteQuery'].'</h2><p>'.$lang['SQLToolboxExecuteQueryInfo'].'</p>';
	$result_content = '';
	
	if ( !empty($_POST['query']) && $functions->verify_form() ) {
		
		$result = $db->query(stripslashes($_POST['query']), true);
		
		if ( is_resource($result) || is_object($result) ) {

			$result_content .= '<div id="resultset"><table id="adminregulartable">';
			$columns_printed = false;
			$num = 0;

			while ( $out = $db->fetch_result($result) ) {

				if ( !$columns_printed ) {
					
					$columns = array_map('sqltoolbox_resultval_clean', array_keys($out));
					$result_content .= "\n".'<tr><th>#</th><th>'. implode('</th><th>', $columns) .'</th></tr>';
					$columns_printed = true;

				}

				$num++;

				$values = array_map('sqltoolbox_resultval_clean', $out);
				$result_content .= "\n".'<tr><td>('.$num.')</td><td>'. implode('</td><td>', $values) .'</td></tr>';

			}

			$result_content .= '</table></div>';
			
		} elseif ( $result === true ) {
			
			$result_content .= '<p>'.$lang['SQLToolboxExecutedSuccessfully'].'</p>';
			
		} else {
			
			$result_content .= '<p><strong>'.unhtml($result).'.</strong></p>';
			
		}
		
	} else {
		
		$_POST['query'] = '';
		
	}
	
	$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'sqltoolbox')).'" method="post">';
	$content .= '<ul id="adminsqlbuttons">';
	foreach ( $functions->get_usebb_tables() as $table )
		$content .= '<li><a href="javascript:insert_table(\''.$table.'\')">'.$table.'</a></li> ';
	$content .= '</ul>';
	$content .= '<p><textarea name="query" id="tags-txtarea" rows="5" cols="50">'.unhtml(stripslashes($_POST['query'])).'</textarea></p>';
	$content .= '<p class="submit"><input type="submit" value="'.$lang['SQLToolboxExecute'].'" />'.$admin_functions->form_token().' <input type="reset" value="'.$lang['Reset'].'" /></p></form>';

	$content .= $result_content;
	
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
