<?php

/*
	Copyright (C) 2003-2012 UseBB Team
	http://www.usebb.net
	
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
 * Stop Forum Spam API request
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @copyright	Copyright (C) 2003-2012 UseBB Team
 * @package	UseBB
 */

$usebb_module_info = array(
	'short_name' => 'sfs_request',
	'long_name' => 'SFS Request',
	'acp_category' => 'security',
	'min_version' => '1.0.13'
);

if ( defined('RUN_MODULE') ) {
	
	class usebb_module {

		function do_request($email, &$content) {

			global $functions;
			
			$result = $functions->sfs_api_request($email);

			$content .= '<h2>Result</h2>';

			if ( $result === false ) {

				$content .= '<p>No results found for '.unhtml($email).'.</p>';

				return;

			}

			$content .= '<p><strong>'.unhtml($email).' is present in the database!</strong></p>';

			$more = array();

			if ( isset($result['frequency']) )
				$more[] = 'Frequency: '.$result['frequency'];

			if ( isset($result['lastseen']) )
				$more[] = 'Last seen: '.$functions->make_date($result['lastseen'], "Y-m-d h:i:s a");

			if ( count($more) )
				$content .= '<ul><li>'.implode('</li><li>', $more).'</li></ul>';

		}
		
		function run_module() {

			global $functions, $template;

			$content = '<p>With this module you can look for an email address in <a href="http://www.stopforumspam.com/">Stop Forum Spam</a>\'s database.</p>';

			if ( !empty($_POST['email']) && preg_match(EMAIL_PREG, $_POST['email']) )
				$this->do_request($_POST['email'], $content);

			$content .= '<h2>New request</h2>'
				.'<form action="'.$functions->make_url('admin.php', array('act' => 'mod_sfs_request')).'" method="post">'
				.'<p>Enter an email address to test: <input type="text" name="email" id="email" size="30" /></p>'
				.'<p class="submit"><input type="submit" value="Make request" /></p>'
				.'</form>';
			$template->set_js_onload("set_focus('email')");
			
			return $content;

		}		
	}
	
	$usebb_module = new usebb_module;
	
}

?>
