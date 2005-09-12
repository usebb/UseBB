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

if ( $functions->get_config('enable_acp_modules') ) {
	
	$_GET['do'] = ( !empty($_GET['do']) ) ? $_GET['do'] : 'index';
	
	if ( $_GET['do'] == 'index' ) {
		
		$content = '<p>'.sprintf($lang['ModulesInfo'], '<a href="http://www.usebb.net/">www.usebb.net</a>').'</p>';
		$content .= '<ul id="adminfunctionsmenu">';
			$content .= '<li><a href="'.$functions->make_url('admin.php', array('act' => 'modules', 'do' => 'upload')).'">'.$lang['ModulesUpload'].'</a></li> ';
		$content .= '</ul>';
		
		if ( count($admin_functions->acp_modules) ) {
			
			$content .= '<table id="adminmodulestable"><tr><th>'.$lang['ModulesLongName'].'</th><th>'.$lang['ModulesShortName'].'</th><th>'.$lang['ModulesCategory'].'</th><th>'.$lang['ModulesFilename'].'</th><th class="action">'.$lang['Delete'].'</th></tr>';
			foreach ( $admin_functions->acp_modules as $module ) {
				
				$content .= '<tr><td><a href="'.$functions->make_url('admin.php', array('act' => 'mod_'.$module['short_name'])).'"><em>'.$module['long_name'].'</em></a></td><td><code>(mod_)'.$module['short_name'].'</code></td><td>'.$lang['Category-'.$module['acp_category']].'</td><td><code>'.$module['filename'].'</code></td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'modules', 'do' => 'delete', 'name' => $module['short_name'])).'">'.$lang['Delete'].'</a></td></tr>';
				
			}
			$content .= '</table>';
			
		} else {
			
			$content .= '<p>'.$lang['ModulesNoneAvailable'].'</p>';
			
		}
		
	} elseif ( $_GET['do'] == 'upload' ) {
		
		$content .= '<h2>'.$lang['ModulesUpload'].'</h2>';
		
		$modules_dir = ROOT_PATH.'sources/modules/';
		if ( file_exists($modules_dir) && is_writable($modules_dir) ) {
			
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' && array_key_exists('acp_module', $_FILES) && is_uploaded_file($_FILES['acp_module']['tmp_name']) ) {
				
				$acp_module = $_FILES['acp_module'];
				
				if ( in_array($acp_module['name'], $admin_functions->acp_modules_files) ) {
					
					$content .= '<p><strong>'.sprintf($lang['ModulesUploadDuplicateModule'], '<code>'.$acp_module['name'].'</code>').'</strong></p>';
					
				} elseif ( $admin_functions->check_module($acp_module['name'], $acp_module['tmp_name']) ) {
					
					if ( copy($acp_module['tmp_name'], dirname($_SERVER['PATH_TRANSLATED']).'/sources/modules/'.$acp_module['name']) )
						$functions->redirect('admin.php', array('act' => 'modules'));
					else
						$content .= '<p><strong>'.sprintf($lang['ModulesUploadFailed'], '<code>'.$acp_module['name'].'</code>').'</strong></p>';
					
				} else {
					
					$content .= '<p><strong>'.sprintf($lang['ModulesUploadNoValidModule'], '<code>'.$acp_module['name'].'</code>').'</strong></p>';
					
				}
				
			} else {
				
				$content .= '<p>'.$lang['ModulesUploadInfo'].'</p>';
				
			}
			
			$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'modules', 'do' => 'upload')).'" method="post" enctype="multipart/form-data"><p><input type="file" name="acp_module" size="25" /> <input type="submit" value="'.$lang['Upload'].'" /></p></form>';
			
		} else {
		
			$content .= '<p>'.sprintf($lang['ModulesUploadDisabled'], '<code>sources/modules/</code>').'</p>';
		
		}
		
	}
	
} else {
	
	$content .= '<p>'.$lang['ModulesDisabled'].'</p>';
	
}

$admin_functions->create_body('modules', $content);

?>
