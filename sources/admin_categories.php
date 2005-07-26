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

$result = $db->query("SELECT id, name, sort_id FROM ".TABLE_PREFIX."cats ORDER BY sort_id ASC, name");
$cats = array();
$filled_in = true;
while ( $catinfo = $db->fetch_result($result) ) {
	
	$cats[$catinfo['id']] = $catinfo;
	if ( !isset($_POST['sort_id-'.$catinfo['id']]) || !valid_int($_POST['sort_id-'.$catinfo['id']]) )
		$filled_in = false;
	
}

$_GET['do'] = ( !empty($_GET['do']) ) ? $_GET['do'] : 'index';

if ( $_GET['do'] == 'index' ) {
	
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
				
				foreach ( $cats as $cat ) {
					
					$db->query("UPDATE ".TABLE_PREFIX."cats SET sort_id = ".$_POST['sort_id-'.$cat['id']]." WHERE id = ".$cat['id']);
					$cats[$cat['id']]['sort_id'] = $_POST['sort_id-'.$cat['id']];
					
				}
				
				$content .= '<p>'.$lang['CategoriesSortChangesApplied'].'</p>';
				
			} else {
				
				$content .= '<p><strong>'.$lang['CategoriesMissingFields'].'</strong></p>';
				
			}
			
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
	
} elseif ( $_GET['do'] == 'delete' && !empty($_GET['id']) && valid_int($_GET['id']) && array_key_exists($_GET['id'], $cats) ) {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		if ( !empty($_POST['delete']) ) {
			
			if ( !empty($_POST['move_contents']) && valid_int($_POST['move_contents']) && array_key_exists($_POST['move_contents'], $cats) )
				$db->query("UPDATE ".TABLE_PREFIX."forums SET cat_id = ".$_POST['move_contents']." WHERE cat_id = ".$_GET['id']);
			else
				$admin_functions->delete_forums('cat_id = '.$_GET['id']);
			$db->query("DELETE FROM ".TABLE_PREFIX."cats WHERE id = ".$_GET['id']);
			
		}
		
		header('Location: '.$functions->get_config('board_url').$functions->make_url('admin.php', array('act' => 'categories')));
		
	} else {
		
		$content = '<h2>'.$lang['CategoriesConfirmCatDelete'].'</h2>';
		$content .= '<p><strong>'.sprintf($lang['CategoriesConfirmCatDeleteContent'], '<em>'.unhtml(stripslashes($cats[$_GET['id']]['name'])).'</em>').'</strong></p>';
		$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'categories', 'do' => 'delete', 'id' => $_GET['id'])).'" method="post">';
		if ( count($cats) >= 2 ) {
			
			$category_select = '<select name="move_contents"><option value="">'.$lang['CategoriesDeleteContents'].'</option>';
			foreach ( $cats as $cat ) {
				
				if ( $cat['id'] != $_GET['id'] )
					$category_select .= '<option value="'.$cat['id'].'">'.unhtml(stripslashes($cat['name'])).'</option>';
				
			}
			$category_select .= '</select>';
			
			$content .= '<p>'.sprintf($lang['CategoriesMoveContents'], $category_select).'</p>';
			
		}
		$content .= '<p><input type="submit" name="delete" value="'.$lang['Delete'].'" /> <input type="submit" value="'.$lang['Cancel'].'" /></p>';
		$content .= '</form>';
		
	}
	
}

$admin_functions->create_body('categories', $content);

?>
