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

$_POST['do'] = ( !empty($_POST['do']) ) ? $_POST['do'] : 'index';

if ( $_POST['do'] == 'index' ) {
	
	$result = $db->query("SELECT id, name, sort_id FROM ".TABLE_PREFIX."cats ORDER BY sort_id ASC, id");
	$cats = array();
	while ( $catinfo = $db->fetch_result($result) )
		$cats[] = $catinfo;
	
	$filled_in = true;
	foreach ( $cats as $cat ) {
		
		if ( !isset($_POST['sort_id-'.$cat['id']]) || !valid_int($_POST['sort_id-'.$cat['id']]) )
			$filled_in = false;
		
	}
	
	if ( $filled_in ) {
		
		foreach ( $cats as $cat ) {
			
			
			
		}
		
	} else {
		
		$content = '<p>'.$lang['CategoriesInfo'].'</p><table id="admincatstable"><tr><th>'.$lang['CategoriesCatName'].'</th><th>'.$lang['CategoriesEdit'].'</th><th>'.$lang['CategoriesDelete'].'</th><th>'.$lang['CategoriesSortID'].'</th></tr>';
		
		if ( count($cats) ) {
			
			foreach ( $cats as $cat ) {
				
				$content .= '<tr><td>'.unhtml(stripslashes($cat['name'])).'</td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'categories', 'do' => 'edit', 'id' => $cat['id'])).'">'.$lang['CategoriesEdit'].'</a></td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'categories', 'do' => 'delete', 'id' => $cat['id'])).'">'.$lang['CategoriesDelete'].'</a></td><td><input type="text" name="sort_id-'.$cat['id'].'" value="'.$cat['sort_id'].'" size="3" maxlength="11" /></td></tr>';
				
			}
			
		}
		
		$content .= '<tr><td><input type="text" name="new_cat_name" size="30" maxlength="255" /></td><td class="action"></td><td class="action"></td><td><input type="text" name="new_sort_id" size="3" maxlength="11" /></td></tr>';
		$content .= '<tr><td colspan="4" class="submit"><input type="submit" value="'.$lang['Send'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></td></tr></table>';
		
	}
	
}

$admin_functions->create_body('categories', $content);

?>
