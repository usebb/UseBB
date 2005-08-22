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

$cats = $admin_functions->get_cats_array();
$filled_in = true;
foreach ( $cats as $cat ) {
	
	if ( !isset($_POST['sort_id-'.$cat['id']]) || !valid_int($_POST['sort_id-'.$cat['id']]) )
		$filled_in = false;
	
}

$_GET['do'] = ( !empty($_GET['do']) ) ? $_GET['do'] : 'index';

if ( in_array($_GET['do'], array('index', 'adjustsortids', 'autosort')) ) {
	
	$content = '<p>'.$lang['CategoriesInfo'].'</p>';
	$content .= '<ul id="adminfunctionsmenu">';
		$content .= '<li><a href="'.$functions->make_url('admin.php', array('act' => 'categories', 'do' => 'add')).'">'.$lang['CategoriesAddNewCat'].'</a></li> ';
		$content .= '<li><a href="'.$functions->make_url('admin.php', array('act' => 'categories', 'do' => 'adjustsortids')).'">'.$lang['CategoriesAdjustSortIDs'].'</a></li> ';
		$content .= '<li><a href="'.$functions->make_url('admin.php', array('act' => 'categories', 'do' => 'autosort')).'">'.$lang['CategoriesSortAutomatically'].'</a></li> ';
	$content .= '</ul>';
	
	if ( !count($cats) ) {
		
		$content .= '<p>'.$lang['CategoriesNoCatsExist'].'</p>';
		
	} else {
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			if ( $filled_in ) {
				
				foreach ( $cats as $cat )
					$db->query("UPDATE ".TABLE_PREFIX."cats SET sort_id = ".$_POST['sort_id-'.$cat['id']]." WHERE id = ".$cat['id']);
				$cats = $admin_functions->get_cats_array();
				$content .= '<p>'.$lang['CategoriesSortChangesApplied'].'</p>';
				
			} else {
				
				$content .= '<p><strong>'.$lang['CategoriesMissingFields'].'</strong></p>';
				
			}
			
		} elseif ( $_GET['do'] == 'adjustsortids' ) {
			
			$cat_sort_id = 1;
			foreach ( $cats as $cat ) {
				
				if ( $cat['sort_id'] != $cat_sort_id )
					$db->query("UPDATE ".TABLE_PREFIX."cats SET sort_id = ".$cat_sort_id." WHERE id = ".$cat['id']);
				$cat_sort_id++;
				
			}
			$cats = $admin_functions->get_cats_array();
			$content .= '<p>'.$lang['CategoriesSortChangesApplied'].'</p>';
			
		} elseif ( $_GET['do'] == 'autosort' ) {
			
			$db->query("UPDATE ".TABLE_PREFIX."cats SET sort_id = 0");
			$cats = $admin_functions->get_cats_array();
			$content .= '<p>'.$lang['CategoriesSortChangesApplied'].'</p>';
			
		}
		
		$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'categories')).'" method="post">';
		$content .= '<table id="admincatstable"><tr><th>'.$lang['CategoriesCatName'].'</th><th class="action">'.$lang['Edit'].'</th><th class="action">'.$lang['Delete'].'</th><th class="action">'.$lang['CategoriesSortID'].'</th></tr>';
		
		$i = 1;
		if ( count($cats) ) {
			
			foreach ( $cats as $cat ) {
				
				$content .= '<tr><td><em>'.unhtml(stripslashes($cat['name'])).'</em></td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'categories', 'do' => 'edit', 'id' => $cat['id'])).'">'.$lang['Edit'].'</a></td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'categories', 'do' => 'delete', 'id' => $cat['id'])).'">'.$lang['Delete'].'</a></td><td class="action"><input type="text" name="sort_id-'.$cat['id'].'" value="'.$cat['sort_id'].'" size="3" maxlength="11" tabindex="'.$i.'" /></td></tr>';
				$i++;
				
			}
			
		}
		
		$content .= '<tr><td colspan="4" class="submit"><input type="submit" value="'.$lang['Save'].'" tabindex="'.$i.'" /> <input type="reset" value="'.$lang['Reset'].'" tabindex="'.($i+1).'" /></td></tr></table></form>';
		
	}
	
} elseif ( $_GET['do'] == 'delete' && !empty($_GET['id']) && array_key_exists($_GET['id'], $cats) ) {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		if ( !empty($_POST['delete']) ) {
			
			if ( !empty($_POST['move_contents']) && array_key_exists($_POST['move_contents'], $cats) )
				$db->query("UPDATE ".TABLE_PREFIX."forums SET cat_id = ".$_POST['move_contents']." WHERE cat_id = ".$_GET['id']);
			else
				$admin_functions->delete_forums('cat_id = '.$_GET['id']);
			$db->query("DELETE FROM ".TABLE_PREFIX."cats WHERE id = ".$_GET['id']);
			
		}
		
		$functions->redirect('admin.php', array('act' => 'categories'));
		
	} else {
		
		$content = '<h2>'.$lang['CategoriesConfirmCatDelete'].'</h2>';
		$content .= '<p><strong>'.sprintf($lang['CategoriesConfirmCatDeleteContent'], '<em>'.unhtml(stripslashes($cats[$_GET['id']]['name'])).'</em>').'</strong></p>';
		$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'categories', 'do' => 'delete', 'id' => $_GET['id'])).'" method="post">';
		if ( count($cats) >= 2 ) {
			
			$category_select = '<select name="move_contents">';
			foreach ( $cats as $cat ) {
				
				if ( $cat['id'] != $_GET['id'] )
					$category_select .= '<option value="'.$cat['id'].'">'.unhtml(stripslashes($cat['name'])).'</option>';
				
			}
			$category_select .= '<option value="" class="strong">-'.$lang['CategoriesDeleteContents'].'-</option></select>';
			
			$content .= '<p>'.sprintf($lang['CategoriesMoveContents'], $category_select).'</p>';
			
		}
		$content .= '<p class="submit"><input type="submit" name="delete" value="'.$lang['Delete'].'" /> <input type="submit" value="'.$lang['Cancel'].'" /></p>';
		$content .= '</form>';
		
	}
	
} elseif ( $_GET['do'] == 'edit' && !empty($_GET['id']) && array_key_exists($_GET['id'], $cats) ) {
	
	$catinfo = $cats[$_GET['id']];
	
	if ( !empty($_POST['name']) ) {
		
		$db->query("UPDATE ".TABLE_PREFIX."cats SET name = '".$_POST['name']."' WHERE id = ".$_GET['id']);
		$functions->redirect('admin.php', array('act' => 'categories'));
		
	} else {
		
		$content = '<h2>'.sprintf($lang['CategoriesEditingCat'], '<em>'.unhtml(stripslashes($catinfo['name'])).'</em>').'</h2>';
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
			$content .= '<p><strong>'.sprintf($lang['MissingFields'], $lang['CategoriesCatName']).'</strong></p>';
		
		$submitted_cat_info = array();
		foreach ( array('name') as $key )
			$submitted_cat_info[$key] = ( isset($_POST[$key]) ) ? $_POST[$key] : $catinfo[$key];
		
		$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'categories', 'do' => 'edit', 'id' => $_GET['id'])).'" method="post">';
		$content .= '<table id="admincatstable">';
		$content .= '<tr><td class="fieldtitle">'.$lang['CategoriesCatName'].'</td><td><input type="text" size="30" name="name" id="name" maxlength="255" value="'.unhtml(stripslashes($submitted_cat_info['name'])).'" /></td></tr>';
		$content .= '<tr><td colspan="2" class="submit"><input type="submit" value="'.$lang['Edit'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></td></tr></table></form>';
		
		$template->set_js_onload("set_focus('name')");
		
	}
	
} elseif ( $_GET['do'] == 'add' ) {
	
	if ( !empty($_POST['name']) ) {
		
		$db->query("INSERT INTO ".TABLE_PREFIX."cats VALUES(NULL, '".$_POST['name']."', 0)");
		$functions->redirect('admin.php', array('act' => 'categories'));
		
	} else {
		
		$content = '<h2>'.$lang['CategoriesAddNewCat'].'</h2>';
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
			$content .= '<p><strong>'.sprintf($lang['MissingFields'], $lang['CategoriesCatName']).'</strong></p>';
		
		$submitted_cat_info = array();
		foreach ( array('name') as $key )
			$submitted_cat_info[$key] = ( isset($_POST[$key]) ) ? $_POST[$key] : '';
		
		$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'categories', 'do' => 'add')).'" method="post">';
		$content .= '<table id="admincatstable">';
		$content .= '<tr><td class="fieldtitle">'.$lang['CategoriesCatName'].'</td><td><input type="text" size="30" name="name" id="name" maxlength="255" value="'.unhtml(stripslashes($submitted_cat_info['name'])).'" /></td></tr>';
		$content .= '<tr><td colspan="2" class="submit"><input type="submit" value="'.$lang['Add'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></td></tr></table></form>';
		
		$template->set_js_onload("set_focus('name')");
		
	}
	
}

$admin_functions->create_body('categories', $content);

?>
