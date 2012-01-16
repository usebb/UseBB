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
 * ACP bans management
 *
 * Ban usernames, email and IP addresses
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

$modes = array('username', 'email', 'ip_addr');
$_GET['show'] = ( !empty($_GET['show']) && in_array($_GET['show'], $modes) ) ? $_GET['show'] : $modes[0];

if ( !empty($_GET['delete']) && valid_int($_GET['delete']) && $functions->verify_url() ) {
	
	$db->query("DELETE FROM ".TABLE_PREFIX."bans WHERE id= ".$_GET['delete']);
	$functions->redirect('admin.php', array('act' => 'bans', 'show' => $_GET['show']));
	
} else {
	
	$content = '<p>'.$lang['BansInfo'].'</p>';
	
	$content .= '<ul id="adminfunctionsmenu">';
	foreach ( $modes as $mode ) {
		
		if ( $mode == $_GET['show'] )
			$content .= '<li>'.$lang['Bans-'.$mode].'</li> ';
		else
			$content .= '<li><a href="'.$functions->make_url('admin.php', array('act' => 'bans', 'show' => $mode)).'">'.$lang['Bans-'.$mode].'</a></li> ';
		
	}
	$content .= '</ul>';
	
	if ( $_GET['show'] == 'username' ) {
		
		if ( !empty($_POST['name']) && $functions->verify_form() ) {
			
			$db->query("DELETE FROM ".TABLE_PREFIX."bans WHERE name = '".$_POST['name']."'");
			$db->query("INSERT INTO ".TABLE_PREFIX."bans VALUES(NULL, '".$_POST['name']."', '', '')");
			
			$functions->redirect('admin.php', array('act' => 'bans', 'show' => $_GET['show']));
					
		} else {
			
			$result = $db->query("SELECT id, name FROM ".TABLE_PREFIX."bans WHERE name <> '' ORDER BY name ASC");
			$bans = array();
			while ( $ban = $db->fetch_result($result) )
				$bans[] = $ban;
			
			$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'bans', 'show' => $_GET['show'])).'" method="post">';
			$content .= '<table id="adminregulartable">';
			$content .= '<tr><th>'.$lang['BansUsername'].'</th><th class="action">'.$lang['Action'].'</th></tr>';
			$content .= '<tr><td><input type="text" name="name" size="30" maxlength="255" /></td><td class="action"><input type="submit" value="'.$lang['Add'].'" />'.$admin_functions->form_token().'</td></tr>';
			
			if ( !count($bans) ) {
				
				$content .= '<tr><td colspan="3">'.$lang['BansNoBansExist'].'</td></tr>';
				
			} else {
				
				foreach ( $bans as $ban )
					$content .= '<tr><td>'.unhtml(stripslashes($ban['name'])).'</td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'bans', 'show' => $_GET['show'], 'delete' => $ban['id']), true, true, false, true).'">'.$lang['Delete'].'</a></td></tr>';
				
			}
			
			$content .= '</table></form>';
			
		}
		
	} elseif ( $_GET['show'] == 'email' ) {
		
		if ( !empty($_POST['email']) && $functions->verify_form() ) {
			
			$db->query("DELETE FROM ".TABLE_PREFIX."bans WHERE email = '".$_POST['email']."'");
			$db->query("INSERT INTO ".TABLE_PREFIX."bans VALUES(NULL, '', '".$_POST['email']."', '')");
			
			$functions->redirect('admin.php', array('act' => 'bans', 'show' => $_GET['show']));
					
		} else {
			
			$result = $db->query("SELECT id, email FROM ".TABLE_PREFIX."bans WHERE email <> '' ORDER BY email ASC");
			$bans = array();
			while ( $ban = $db->fetch_result($result) )
				$bans[] = $ban;
			
			$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'bans', 'show' => $_GET['show'])).'" method="post">';
			$content .= '<table id="adminregulartable">';
			$content .= '<tr><th>'.$lang['BansEmail'].'</th><th class="action">'.$lang['Action'].'</th></tr>';
			$content .= '<tr><td><input type="text" name="email" size="30" maxlength="255" /></td><td class="action"><input type="submit" value="'.$lang['Add'].'" />'.$admin_functions->form_token().'</td></tr>';
			
			if ( !count($bans) ) {
				
				$content .= '<tr><td colspan="3">'.$lang['BansNoBansExist'].'</td></tr>';
				
			} else {
				
				foreach ( $bans as $ban )
					$content .= '<tr><td>'.unhtml(stripslashes($ban['email'])).'</td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'bans', 'show' => $_GET['show'], 'delete' => $ban['id']), true, true, false, true).'">'.$lang['Delete'].'</a></td></tr>';
				
			}
			
			$content .= '</table></form>';
			
		}
		
	} elseif ( $_GET['show'] == 'ip_addr' ) {
		
		if ( !$functions->get_config('enable_ip_bans') ) {
			
			$content .= '<p>'.$lang['BansIPBansDisabledInfo'].'</p>';
			
		} else {
			
			if ( !empty($_POST['ip_addr']) && $functions->verify_form() ) {
				
				$db->query("DELETE FROM ".TABLE_PREFIX."bans WHERE ip_addr = '".$_POST['ip_addr']."'");
				$db->query("INSERT INTO ".TABLE_PREFIX."bans VALUES(NULL, '', '', '".$_POST['ip_addr']."')");
				
				$functions->redirect('admin.php', array('act' => 'bans', 'show' => $_GET['show']));
						
			} else {
				
				$result = $db->query("SELECT id, ip_addr FROM ".TABLE_PREFIX."bans WHERE ip_addr <> '' ORDER BY ip_addr ASC");
				$bans = array();
				while ( $ban = $db->fetch_result($result) )
					$bans[] = $ban;
				
				$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'bans', 'show' => $_GET['show'])).'" method="post">';
				$content .= '<table id="adminregulartable">';
				$content .= '<tr><th>'.$lang['BansIp_addr'].'</th><th class="action">'.$lang['Action'].'</th></tr>';
				$content .= '<tr><td><input type="text" name="ip_addr" size="30" maxlength="255" /></td><td class="action"><input type="submit" value="'.$lang['Add'].'" />'.$admin_functions->form_token().'</td></tr>';
				
				if ( !count($bans) ) {
					
					$content .= '<tr><td colspan="3">'.$lang['BansNoBansExist'].'</td></tr>';
					
				} else {
					
					foreach ( $bans as $ban )
						$content .= '<tr><td>'.unhtml(stripslashes($ban['ip_addr'])).'</td><td class="action"><a href="'.$functions->make_url('admin.php', array('act' => 'bans', 'show' => $_GET['show'], 'delete' => $ban['id']), true, true, false, true).'">'.$lang['Delete'].'</a></td></tr>';
					
				}
				
				$content .= '</table></form>';
				
			}
			
		}
		
	}
	
}

$admin_functions->create_body('bans', $content);

?>
