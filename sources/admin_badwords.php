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
 * ACP bad words management
 *
 * Set up bad words that are censored on the forum.
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

if ( $functions->get_config('enable_badwords_filter') ) {
	
	if ( !empty($_POST['word']) && $functions->verify_form() ) {
		
		$db->query("DELETE FROM ".TABLE_PREFIX."badwords WHERE word = '".$_POST['word']."'");
		
		if ( !empty($_POST['replacement']) )
			$db->query("INSERT INTO ".TABLE_PREFIX."badwords VALUES('".$_POST['word']."', '".$_POST['replacement']."')");
		else
			$db->query("INSERT INTO ".TABLE_PREFIX."badwords VALUES('".$_POST['word']."', '')");
		
		$functions->redirect('admin.php', array('act' => 'badwords'));
				
	} elseif ( !empty($_GET['do']) && $_GET['do'] == 'delete' && !empty($_GET['word']) && $functions->verify_url() ) {
		
		$db->query("DELETE FROM ".TABLE_PREFIX."badwords WHERE word = '".$_GET['word']."'");
		$functions->redirect('admin.php', array('act' => 'badwords'));
		
	} else {
		
		$content = '<p>'.$lang['BadwordsInfo'].'</p>';
		
		$result = $db->query("SELECT word, replacement FROM ".TABLE_PREFIX."badwords ORDER BY word ASC");
		$badwords = array();
		while ( $badword = $db->fetch_result($result) )
			$badwords[] = $badword;
		
		$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'badwords')).'" method="post">';
		$content .= '<table id="adminregulartable">';
		$content .= '<tr><th>'.$lang['BadwordsAddBadwordWord'].'</th><th>'.$lang['BadwordsAddBadwordReplacement'].'</th><th class="action">'.$lang['Action'].'</th></tr>';
		$content .= '<tr><td><input type="text" name="word" size="20" maxlength="255" /></td><td><input type="text" name="replacement" size="20" maxlength="255" /></td><td class="action"><input type="submit" value="'.$lang['Add'].'" />'.$admin_functions->form_token().'</td></tr>';
		
		if ( !count($badwords) ) {
			
			$content .= '<tr><td colspan="3">'.$lang['BadwordsNoBadwordsExist'].'</td></tr>';
			
		} else {
			
			foreach ( $badwords as $badword )
				$content .= '<tr><td>'.unhtml(stripslashes($badword['word'])).'</td><td>'.unhtml(stripslashes($badword['replacement'])).'</td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'badwords', 'do' => 'delete', 'word' => stripslashes($badword['word'])), true, true, false, true).'">'.$lang['Delete'].'</a></td></tr>';
			
		}
		
		$content .= '</table></form>';
		
	}
	
} else {
	
	$content = '<h2>'.$lang['BadwordsDisabled'].'</h2>';
	$content .= '<p>'.$lang['BadwordsDisabledInfo'].'</p>';
	
}

$admin_functions->create_body('badwords', $content);

?>
