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

$result = $db->query("SELECT id, name, sort_id FROM ".TABLE_PREFIX."cats ORDER BY sort_id ASC, id");
$cats = array();
$filled_in = true;
while ( $catinfo = $db->fetch_result($result) ) {
	
	$cats[$catinfo['id']] = $catinfo;
	if ( !isset($_POST['sort_id-'.$catinfo['id']]) || !valid_int($_POST['sort_id-'.$catinfo['id']]) )
		$filled_in = false;
	
}

$_GET['do'] = ( !empty($_GET['do']) ) ? $_GET['do'] : 'index';

if ( $_GET['do'] == 'index' ) {
	
	if ( $filled_in ) {
		
		$biggest_sort_id = 0;
		foreach ( $cats as $cat ) {
			
			$db->query("UPDATE ".TABLE_PREFIX."cats SET sort_id = ".$_POST['sort_id-'.$cat['id']]." WHERE id = ".$cat['id']);
			
			if ( $_POST['sort_id-'.$cat['id']] > $biggest_sort_id )
				$biggest_sort_id = $_POST['sort_id-'.$cat['id']];
			
		}
		
		if ( !empty($_POST['new_cat_name']) ) {
			
			$sort_id = ( isset($_POST['new_sort_id']) && valid_int($_POST['new_sort_id']) ) ? $_POST['new_sort_id'] : $biggest_sort_id+1;
			$db->query("INSERT INTO ".TABLE_PREFIX."cats VALUES (NULL, '".$_POST['new_cat_name']."', ".$sort_id.")");
			
			$content = '<p>'.$lang['CategoriesSortChangesNewCatApplied'].'</p>';
			
		} else {
			
			$content = '<p>'.$lang['CategoriesSortChangesApplied'].'</p>';
			
		}
		
	} else {
		
		$content = ( $_SERVER['REQUEST_METHOD'] == 'POST' ) ? '<p>'.$lang['CategoriesMissingFields'].'</p>' : '<p>'.$lang['CategoriesInfo'].'</p>';
		
		$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'categories')).'" method="post">';
		$content .= '<table id="admincatstable"><tr><th>'.$lang['CategoriesCatName'].'</th><th>'.$lang['CategoriesEdit'].'</th><th>'.$lang['CategoriesDelete'].'</th><th>'.$lang['CategoriesSortID'].'</th></tr>';
		
		if ( count($cats) ) {
			
			$i = 1;
			foreach ( $cats as $cat ) {
				
				$content .= '<tr><td><em>'.unhtml(stripslashes($cat['name'])).'</em></td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'categories', 'do' => 'edit', 'id' => $cat['id'])).'">'.$lang['CategoriesEdit'].'</a></td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'categories', 'do' => 'delete', 'id' => $cat['id'])).'">'.$lang['CategoriesDelete'].'</a></td><td><input type="text" name="sort_id-'.$cat['id'].'" value="'.$cat['sort_id'].'" size="3" maxlength="11" tabindex="'.$i.'" /></td></tr>';
				$i++;
				
			}
			
		}
		
		$content .= '<tr><td><input type="text" name="new_cat_name" size="30" maxlength="255" /></td><td class="action"></td><td class="action"></td><td><input type="text" name="new_sort_id" size="3" maxlength="11" /></td></tr>';
		$content .= '<tr><td colspan="4" class="submit"><input type="submit" value="'.$lang['Save'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></td></tr></table></form>';
		
	}
	
} elseif ( $_GET['do'] == 'delete' && !empty($_GET['id']) && valid_int($_GET['id']) && array_key_exists($_GET['id'], $cats) ) {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		if ( !empty($_POST['delete']) ) {
			
			$admin_functions->delete_forums('cat_id = '.$_GET['id']);
			$db->query("DELETE FROM ".TABLE_PREFIX."cats WHERE id = ".$_GET['id']);
			
		}
		
		header('Location: '.$functions->get_config('board_url').$functions->make_url('admin.php', array('act' => 'categories')));
		
	} else {
		
		$content = '<h2>'.$lang['CategoriesConfirmCatDelete'].'</h2><p><strong>'.sprintf($lang['CategoriesConfirmCatDeleteContent'], '<em>'.unhtml(stripslashes($cats[$_GET['id']]['name'])).'</em>').'</strong></p><form action="'.$functions->make_url('admin.php', array('act' => 'categories', 'do' => 'delete', 'id' => $_GET['id'])).'" method="post"><p><input type="submit" name="delete" value="'.$lang['Delete'].'" /> <input type="submit" value="'.$lang['Cancel'].'" /></p></form>';
		
	}
	
}

$admin_functions->create_body('categories', $content);

?>
