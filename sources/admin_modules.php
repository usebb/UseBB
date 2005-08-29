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

$content = '<p>'.sprintf($lang['ModulesInfo'], '<a href="http://www.usebb.net/">www.usebb.net</a>').'</p>';
$content .= '<h2>'.$lang['ModulesOverview'].'</h2>';

if ( $functions->get_config('enable_acp_modules') ) {
	
	if ( count($admin_functions->acp_modules) ) {
		
		$content .= '<table id="adminmodulestable"><tr><th>'.$lang['ModulesLongName'].'</th><th>'.$lang['ModulesShortName'].'</th><th>'.$lang['ModulesCategory'].'</th><th>'.$lang['ModulesFilename'].'</th></tr>';
		foreach ( $admin_functions->acp_modules as $module ) {
			
			$content .= '<tr><td><a href="'.$functions->make_url('admin.php', array('act' => 'mod_'.$module['short_name'])).'"><em>'.$module['long_name'].'</em></a></td><td><code>(mod_)'.$module['short_name'].'</code></td><td>'.$lang['Category-'.$module['acp_category']].'</td><td><code>'.$module['filename'].'</code></td></tr>';
			
		}
		$content .= '</table>';
		
	} else {
		
		$content .= '<p>'.$lang['ModulesNoneAvailable'].'</p>';
		
	}
	
	$content .= '<h2>'.$lang['ModulesHowToUse'].'</h2>';
	$content .= '<p>'.sprintf($lang['ModulesInstall'], '<code>modules/</code>', '<code>sources/</code>').'</p>';
	$content .= '<p>'.sprintf($lang['ModulesUninstall'], '<code>modules/</code>').'</p>';
	$content .= '<p>'.sprintf($lang['ModulesCreateOwn'], '<a href="http://usebb.sourceforge.net/">UseBB Development</a>').'</p>';
	
} else {
	
	$content .= '<p>'.$lang['ModulesDisabled'].'</p>';
	
}

$admin_functions->create_body('modules', $content);

?>
