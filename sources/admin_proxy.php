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
 * ACP proxy ban management
 *
 * Enable or disable open proxy banning.
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

if ( !$functions->get_config('enable_ip_bans') ) {
	
	$content = '<h2>'.$lang['ProxyIPBansDisabled'].'</h2>';
	$content .= '<p>'.$lang['ProxyIPBansDisabledInfo'].'</p>';
	
} elseif ( !function_exists('checkdnsrr') ) {
	
	$content = '<h2>'.$lang['ProxyNotAvailable'].'</h2>';
	$content .= '<p>'.sprintf($lang['ProxyNotAvailableInfo'], '<code>checkdnsrr()</code>').'</p>';
	
} else {
	
	$dnsbl_servers = array(
		'dsbl_list'			=> array('DSBL list', 'http://dsbl.org/'),
		'dsbl_unconfirmed'	=> array('DSBL unconfirmed', 'http://dsbl.org/'),
		'sorbs_all'			=> array('SORBS aggregate zone', 'http://www.sorbs.net/'),
		'sorbs_http'		=> array('SORBS open HTTP proxies', 'http://www.sorbs.net/'),
		'sorbs_socks'		=> array('SORBS open SOCKS proxies', 'http://www.sorbs.net/'),
		'socks_misc'		=> array('SORBS open misc. proxies', 'http://www.sorbs.net/'),
		'spamcop'			=> array('SpamCop Blocking List', 'http://www.spamcop.net/'),
		'cbl'				=> array('Composite Blocking List (CBL)', 'http://cbl.abuseat.org/'),
		'blitzed'			=> array('Blitzed OPML (BOPM)', 'http://opm.blitzed.org/'),
		'njabl_combined'	=> array('NJABL combined', 'http://www.njabl.org/'),
		'tornevall'			=> array('TornevallNET OPM', 'http://opm.tornevall.org/'),
		'spamhaus_sbl'		=> array('Spamhaus SBL', 'http://www.spamhaus.org/sbl/index.lasso'),
		'spamhaus_xbl'		=> array('Spamhaus XBL (CBL+BOPM+NJABL)', 'http://www.spamhaus.org/xbl/index.lasso'),
		'spamhaus_sbl_xbl'	=> array('Spamhaus SBL+XBL', 'http://www.spamhaus.org/xbl/index.lasso'),
		'ahbl'				=> array('Abusive Hosts Blocking List (AHBL)', 'http://www.ahbl.org/'),
	);
	asort($dnsbl_servers);
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		
		$new_settings = array(
			'enable_open_proxy_ban' => ( !empty($_POST['enable_open_proxy_ban']) ) ? 1 : 0,
			'open_proxy_ban_min_hits' => ( !empty($_POST['open_proxy_ban_min_hits']) && valid_int($_POST['open_proxy_ban_min_hits']) && intval($_POST['open_proxy_ban_min_hits']) >= 1 ) ? intval($_POST['open_proxy_ban_min_hits']) : 1,
			'open_proxy_ban_recheck_minutes' => ( !empty($_POST['open_proxy_ban_recheck_minutes']) && valid_int($_POST['open_proxy_ban_recheck_minutes']) ) ? intval($_POST['open_proxy_ban_recheck_minutes']) : 0,
			'enable_open_proxy_ban_wildcard' => ( !empty($_POST['enable_open_proxy_ban_wildcard']) ) ? 1 : 0,
			'open_proxy_ban_whitelist' => ( !empty($_POST['open_proxy_ban_whitelist']) ) ? preg_split("#[\r\n]+#", $_POST['open_proxy_ban_whitelist']) : array(),
		);
		
		foreach ( $dnsbl_servers as $key => $val )
			$new_settings['enable_open_proxy_ban_'.$key] = ( !empty($_POST['enable_open_proxy_ban_'.$key]) ) ? 1 : 0;
		
		$admin_functions->set_config($new_settings);
		
		$content = '<p>'.$lang['ProxySettingsSaved'].'</p>';
		
	} else {
		
		$enable_open_proxy_ban_checked = ( $functions->get_config('enable_open_proxy_ban') ) ? ' checked="checked"' : '';
		$enable_open_proxy_ban_wildcard_checked = ( $functions->get_config('enable_open_proxy_ban_wildcard') ) ? ' checked="checked"' : '';
		$open_proxy_ban_min_hits = intval($functions->get_config('open_proxy_ban_min_hits'));
		$open_proxy_ban_recheck_minutes = intval($functions->get_config('open_proxy_ban_recheck_minutes'));
		$open_proxy_ban_whitelist = $functions->get_config('open_proxy_ban_whitelist');
		$open_proxy_ban_whitelist = unhtml(join("\n", $open_proxy_ban_whitelist));
		
		$content = '<p>'.$lang['ProxyGeneralInfo'].'</p>';
		$content .= '<form action="'.$functions->make_url('admin.php', array('act' => 'proxy')).'" method="post">';
		$content .= '<fieldset><legend><label><input type="checkbox" name="enable_open_proxy_ban" value="1"'.$enable_open_proxy_ban_checked.' /> '.$lang['ProxyEnableOpenProxyBan'].'</label></legend>';
			$content .= '<h3>'.$lang['ProxyServers'].'</h3><ul id="optionslist">';
			foreach ( $dnsbl_servers as $key => $val ) {
				
				$checked = ( $functions->get_config('enable_open_proxy_ban_'.$key) ) ? ' checked="checked"' : '';
				$content .= '<li><label><input type="checkbox" name="enable_open_proxy_ban_'.$key.'" value="1"'.$checked.' /> '.$val[0].'</label> <a href="'.$val[1].'">(?)</a></li>';
				
			}
			$content .= '</ul>';
			$content .= '<p><label>'.sprintf($lang['ProxyMinPositiveHits'], '<input type="text" name="open_proxy_ban_min_hits" size="2" maxlength="2" value="'.$open_proxy_ban_min_hits.'" />').'</label></p>';
			$content .= '<p><label>'.sprintf($lang['ProxyRecheckMinutes'], '<input type="text" name="open_proxy_ban_recheck_minutes" size="2" maxlength="2" value="'.$open_proxy_ban_recheck_minutes.'" />').'</label></p>';
			$content .= '<p><label><input type="checkbox" name="enable_open_proxy_ban_wildcard" value="1"'.$enable_open_proxy_ban_wildcard_checked.' /> '.sprintf($lang['ProxyEnableOpenProxyBanWildcard'], '<code>1.2.3.*</code>').'</label></p>';
			$content .= '<h3>'.$lang['ProxyWhitelist'].'</h3><p>'.$lang['ProxyWhitelistInfo'].'</p>';
			$content .= '<div><textarea name="open_proxy_ban_whitelist" rows="5" cols="50">'.$open_proxy_ban_whitelist.'</textarea></div>';
		$content .= '</fieldset>';
		$content .= '<ul>';
			$content .= '<li>'.$lang['ProxyUnwantedBansInfo'].'</li>';
			$content .= '<li>'.$lang['ProxySlownessInfo'].'</li>';
			$content .= '<li>'.$lang['ProxyAggregatesInfo'].'</li>';
			$content .= '<li>'.$lang['ProxyHighTrafficInfo'].'</li>';
		$content .= '</ul>';
		$content .= '<p class="submit"><input type="submit" value="'.$lang['Save'].'" /> <input type="reset" value="'.$lang['Reset'].'" /></p>';
		$content .= '</form>';
		
	}
	
}

$admin_functions->create_body('proxy', $content);

?>
