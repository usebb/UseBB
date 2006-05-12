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
 * ACP bad words management
 *
 * Set up bad words that are censored on the forum.
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

if ( $functions->get_config('enable_badwords_filter') ) {
	
	if ( !empty($_GET['do']) && $_GET['do'] == 'add' ) {
		
		$content = '<h2>'.$lang['BadwordsAddBadword'].'</h2>'; 
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			if ( !empty($_POST['word']) ) {
				
				$db->query("DELETE FROM ".TABLE_PREFIX."badwords WHERE word = '".$_POST['word']."'");
				
				if ( !empty($_POST['replacement']) )
					$db->query("INSERT INTO ".TABLE_PREFIX."badwords VALUES('".$_POST['word']."', '".$_POST['replacement']."')");
				else
					$db->query("INSERT INTO ".TABLE_PREFIX."badwords VALUES('".$_POST['word']."', '')");
				
				$functions->redirect('admin.php', array('act' => 'badwords'));
				
			} else {
				
				$content .= '<p><strong>'.$lang['BadwordsWordMissing'].'</strong></p>';
				
			}
			
		}
		
		$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'badwords', 'do' => 'add')).'" method="post">';
		$content .= '<table id="adminregulartable">';
			$content .= '<tr><td class="fieldtitle">'.$lang['BadwordsAddBadwordWord'].'</td><td><input type="text" name="word" id="word" size="20" maxlength="255" /><div class="moreinfo">'.$lang['BadwordsAddBadwordWordInfo'].'</div></td></tr>';
			$content .= '<tr><td class="fieldtitle">'.$lang['BadwordsAddBadwordReplacement'].'</td><td><input type="text" name="replacement" size="20" maxlength="255" /><div class="moreinfo">'.$lang['BadwordsAddBadwordReplacementInfo'].'</div></td></tr>';
		$content .= '<tr><td colspan="2" class="submit"><input type="submit" value="'.$lang['Add'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></td></tr></table></form>';
		
		$template->set_js_onload("set_focus('word')");
		
	} elseif ( !empty($_GET['do']) && $_GET['do'] == 'delete' && !empty($_GET['word']) ) {
		
		$db->query("DELETE FROM ".TABLE_PREFIX."badwords WHERE word = '".$_GET['word']."'");
		$functions->redirect('admin.php', array('act' => 'badwords'));
		
	} else {
		
		$content = '<p>'.$lang['BadwordsInfo'].'</p>';
		$content .= '<ul id="adminfunctionsmenu"><li><a href="'.$functions->make_url('admin.php', array('act' => 'badwords', 'do' => 'add')).'">'.$lang['BadwordsAddBadword'].'</a></li></ul>';
		
		$result = $db->query("SELECT word, replacement FROM ".TABLE_PREFIX."badwords ORDER BY word ASC");
		$badwords = array();
		while ( $badword = $db->fetch_result($result) )
			$badwords[] = $badword;
		
		if ( !count($badwords) ) {
			
			$content .= '<p>'.$lang['BadwordsNoBadwordsExist'].'</p>';
			
		} else {
			
			$content .= '<table id="adminregulartable">';
			$content .= '<tr><th>'.$lang['BadwordsAddBadwordWord'].'</th><th>'.$lang['BadwordsAddBadwordReplacement'].'</th><th class="action">'.$lang['Delete'].'</th></tr>';
			foreach ( $badwords as $badword )
				$content .= '<tr><td>'.unhtml(stripslashes($badword['word'])).'</td><td>'.unhtml(stripslashes($badword['replacement'])).'</td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'badwords', 'do' => 'delete', 'word' => stripslashes($badword['word']))).'">'.$lang['Delete'].'</a></td></tr>';
			$content .= '</table>';
			
		}
		
	}
	
} else {
	
	$content = '<p>'.$lang['BadwordsDisabled'].'</p>';
	
}

$admin_functions->create_body('badwords', $content);

?>
