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
 * ACP DNSBL ban management
 *
 * Enable or disable DNSBL powered banning.
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

if ( !$functions->get_config('enable_ip_bans') ) {
	
	$content = '<h2>'.$lang['DNSBLIPBansDisabled'].'</h2>';
	$content .= '<p>'.$lang['DNSBLIPBansDisabledInfo'].'</p>';
	
} elseif ( !$functions->get_config('enable_dnsbl_powered_banning') ) {
	
	$content = '<h2>'.$lang['DNSBLDisabled'].'</h2>';
	$content .= '<p>'.$lang['DNSBLDisabledInfo'].'</p>';
	
} else {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' && $functions->verify_form() ) {
		
		$new_settings = array(
			'dnsbl_powered_banning_globally' => ( !empty($_POST['dnsbl_powered_banning_globally']) ) ? 1 : 0,
			'dnsbl_powered_banning_min_hits' => ( !empty($_POST['dnsbl_powered_banning_min_hits']) && valid_int($_POST['dnsbl_powered_banning_min_hits']) && intval($_POST['dnsbl_powered_banning_min_hits']) >= 1 ) ? intval($_POST['dnsbl_powered_banning_min_hits']) : 1,
			'dnsbl_powered_banning_recheck_minutes' => ( !empty($_POST['dnsbl_powered_banning_recheck_minutes']) && valid_int($_POST['dnsbl_powered_banning_recheck_minutes']) ) ? intval($_POST['dnsbl_powered_banning_recheck_minutes']) : 0,
			'dnsbl_powered_banning_servers' => ( !empty($_POST['dnsbl_powered_banning_servers']) ) ? preg_split("#[\r\n]+#", $_POST['dnsbl_powered_banning_servers']) : array(),
			'dnsbl_powered_banning_whitelist' => ( !empty($_POST['dnsbl_powered_banning_whitelist']) ) ? preg_split("#[\r\n]+#", $_POST['dnsbl_powered_banning_whitelist']) : array(),
		);
		
		$admin_functions->set_config($new_settings);
		
		$content = '<p>'.$lang['DNSBLSettingsSaved'].'</p>';
		
	} else {
		
		$dnsbl_powered_banning_globally_checked = ( $functions->get_config('dnsbl_powered_banning_globally') ) ? ' checked="checked"' : '';
		$dnsbl_powered_banning_min_hits = intval($functions->get_config('dnsbl_powered_banning_min_hits'));
		$dnsbl_powered_banning_recheck_minutes = intval($functions->get_config('dnsbl_powered_banning_recheck_minutes'));
		$dnsbl_powered_banning_servers = $functions->get_config('dnsbl_powered_banning_servers');
		$dnsbl_powered_banning_servers = unhtml(join("\n", $dnsbl_powered_banning_servers));
		$dnsbl_powered_banning_whitelist = $functions->get_config('dnsbl_powered_banning_whitelist');
		$dnsbl_powered_banning_whitelist = unhtml(join("\n", $dnsbl_powered_banning_whitelist));
		
		$content = '<p>'.$lang['DNSBLGeneralInfo'].'</p>';
		$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'dnsbl')).'" method="post">';
		$content .= '<h2>'.$lang['DNSBLServers'].'</h2><p>'.$lang['DNSBLServersInfo'].'</p>';
		$content .= '<p><textarea name="dnsbl_powered_banning_servers" rows="5" cols="50">'.$dnsbl_powered_banning_servers.'</textarea></p>';
		$content .= '<p><label>'.sprintf($lang['DNSBLMinPositiveHits'], '<input type="text" name="dnsbl_powered_banning_min_hits" size="2" maxlength="2" value="'.$dnsbl_powered_banning_min_hits.'" />').'</label></p>';
		$content .= '<p><label>'.sprintf($lang['DNSBLRecheckMinutes'], '<input type="text" name="dnsbl_powered_banning_recheck_minutes" size="2" maxlength="2" value="'.$dnsbl_powered_banning_recheck_minutes.'" />').'</label></p>';
		$content .= '<h2>'.$lang['DNSBLWhitelist'].'</h2><p>'.$lang['DNSBLWhitelistInfo'].'</p>';
		$content .= '<p><textarea name="dnsbl_powered_banning_whitelist" rows="5" cols="50">'.$dnsbl_powered_banning_whitelist.'</textarea></p>';
		$content .= '<p><label><input type="checkbox" name="dnsbl_powered_banning_globally" value="1"'.$dnsbl_powered_banning_globally_checked.' /> '.$lang['DNSBLGlobally'].'</label></p>';
		$content .= '<p class="submit"><input type="submit" value="'.$lang['Save'].'" />'.$admin_functions->form_token().' <input type="reset" value="'.$lang['Reset'].'" /></p>';
		$content .= '</form>';
		
	}
	
}

$admin_functions->create_body('dnsbl', $content);

?>
