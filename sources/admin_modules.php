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
 * ACP module management
 *
 * Gives an interface to manage ACP modules on the board.
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

if ( $functions->get_config('enable_acp_modules') ) {
	
	$_GET['do'] = ( !empty($_GET['do']) ) ? $_GET['do'] : 'index';
	$modules_dir = ROOT_PATH.'sources/modules/';
	
	if ( $_GET['do'] == 'index' ) {
		
		$content = '<p>'.sprintf($lang['ModulesInfo'], '<a href="http://www.usebb.net/">www.usebb.net</a>').'</p>';
		$content .= '<ul id="adminfunctionsmenu">';
			$content .= '<li><a href="'.$functions->make_url('admin.php', array('act' => 'modules', 'do' => 'upload')).'">'.$lang['ModulesUpload'].'</a></li> ';
		$content .= '</ul>';
		
		if ( count($admin_functions->acp_modules) ) {
			
			$content .= '<table id="adminregulartable"><tr><th>'.$lang['ModulesCategory'].' - '.$lang['ModulesLongName'].'</th><th>'.$lang['ModulesShortName'].' - '.$lang['ModulesFilename'].'</th><th class="action">'.$lang['Delete'].'</th></tr>';
			foreach ( $admin_functions->acp_modules as $module ) {

				$min_version = ( !empty($module['min_version']) && version_compare(USEBB_VERSION, $module['min_version'], '<') ) ? '<br /><strong>'.sprintf($lang['RunningACPModuleMinVersion'], $module['min_version']).'</strong>' : '';

				$delete_link = ( is_writable($modules_dir.$module['filename']) ) ? '<a href="'.$functions->make_url('admin.php', array('act' => 'modules', 'do' => 'delete', 'name' => $module['short_name'])).'">'.$lang['Delete'].'</a>' : $lang['ModulesDeleteNotPermitted'];
				
				$content .= '<tr><td>'.$lang['Category-'.$module['acp_category']].'<br />&middot; <a href="'.$functions->make_url('admin.php', array('act' => 'mod_'.$module['short_name'])).'">'.$module['long_name'].'</a>'.$min_version.'</td><td><code>'.$module['short_name'].'<br />'.$module['filename'].'</code></td><td class="action">'.$delete_link.'</td></tr>';
				
			}
			$content .= '</table>';
			
		} else {
			
			$content .= '<p>'.$lang['ModulesNoneAvailable'].'</p>';
			
		}
		
	} elseif ( $_GET['do'] == 'upload' ) {
		
		$content = '<h2>'.$lang['ModulesUpload'].'</h2>';
		
		if ( file_exists($modules_dir) && is_writable($modules_dir) ) {
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' && array_key_exists('acp_module', $_FILES) && is_uploaded_file($_FILES['acp_module']['tmp_name']) && $functions->verify_form() ) {
				
				$acp_module = $_FILES['acp_module'];
				
				if ( in_array($acp_module['name'], $admin_functions->acp_modules_files) ) {
					
					$content .= '<p><strong>'.sprintf($lang['ModulesUploadDuplicateModule'], '<code>'.$acp_module['name'].'</code>').'</strong></p>';
					
				} elseif ( $admin_functions->check_module($acp_module['name'], $acp_module['tmp_name']) ) {
					
					if ( copy($acp_module['tmp_name'], join(DIRECTORY_SEPARATOR, array(dirname(__FILE__), 'modules', $acp_module['name']))) )
						$functions->redirect('admin.php', array('act' => 'modules'));
					else
						$content .= '<p><strong>'.sprintf($lang['ModulesUploadFailed'], '<code>'.$acp_module['name'].'</code>').'</strong></p>';
					
				} else {
					
					$content .= '<p><strong>'.sprintf($lang['ModulesUploadNoValidModule'], '<code>'.$acp_module['name'].'</code>').'</strong></p>';
					
				}
				
			} else {
				
				$content .= '<p>'.$lang['ModulesUploadInfo'].'</p>';
				
			}
			
			$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'modules', 'do' => 'upload')).'" method="post" enctype="multipart/form-data">';
			$content .= '<p><input type="file" name="acp_module" size="25" /> <input type="submit" value="'.$lang['Upload'].'" />'.$admin_functions->form_token().'</p></form>';
			
		} else {
		
			$content .= '<p>'.sprintf($lang['ModulesUploadDisabled'], '<code>sources/modules/</code>').'</p>';
		
		}
		
	} elseif ( $_GET['do'] == 'delete' && !empty($_GET['name']) && array_key_exists($_GET['name'], $admin_functions->acp_modules) && is_writable($modules_dir.$admin_functions->acp_modules[$_GET['name']]['filename']) ) {
		
		$acp_module = $admin_functions->acp_modules[$_GET['name']];
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			if ( !empty($_POST['delete']) && $functions->verify_form(false) )
				unlink($modules_dir.$acp_module['filename']);
			
			$functions->redirect('admin.php', array('act' => 'modules'));
			
		} else {
			
			$content = '<h2>'.$lang['ModulesConfirmModuleDelete'].'</h2>';
			$content .= '<p>'.sprintf($lang['ModulesConfirmModuleDeleteInfo'], '<em>'.$acp_module['long_name'].'</em>', '<code>'.$acp_module['short_name'].'</code>').'</p>';
			$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'modules', 'do' => 'delete', 'name' => $_GET['name'])).'" method="post">';
			$content .= '<p class="submit"><input type="submit" name="delete" value="'.$lang['Delete'].'" />'.$admin_functions->form_token().' <input type="submit" value="'.$lang['Cancel'].'" /></p>';
			$content .= '</form>';
			
		}
		
	}
	
} else {
	
	$content = '<h2>'.$lang['ModulesDisabled'].'</h2>';
	$content .= '<p>'.$lang['ModulesDisabledInfo'].'</p>';
	
}

$admin_functions->create_body('modules', $content);

?>
