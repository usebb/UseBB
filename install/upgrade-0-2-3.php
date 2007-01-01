<?php

/*
	Copyright (C) 2003-2007 UseBB Team
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
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('INCLUDED', true);
include('../config.php');

class functions {
	
	function usebb_die($errno, $error, $file, $line) {
		
		global $connerror;
		
		//
		// Don't show various errors on PHP5
		//
		if ( intval(substr(phpversion(), 0, 1)) > 4 ) {
			
			$ignore_warnings = array(
				'var: Deprecated. Please use the public/private/protected modifiers',
				'Trying to get property of non-object',
			);
			if ( in_array($error, $ignore_warnings) )
				return;
			
		}
		
		$connerror = $error;
		
	}
	
	function get_config($setting) {
		
		global $conf;
		
		if ( isset($conf[$setting]) )
			return $conf[$setting];
		else
			return '';
		
	}
	
}

$functions = new functions;

function error_handler($errno, $error, $file, $line) {
	
	global $functions;
	$functions->usebb_die($errno, $error, $file, $line);
	
}
set_error_handler('error_handler');

if ( !empty($_POST['step']) && intval($_POST['step']) > 1 ) {
	
	include('../sources/db_'.$dbs['type'].'.php');
	$db = new db;
	$db->connect($dbs);
	
}

function to_step($step) {
	
	return '<form action="'.$_SERVER['PHP_SELF'].'" method="post"><p><input type="hidden" name="step" value="'.$step.'" /><input type="submit" value="' . ( ( $_POST['step'] == $step ) ? 'Retry step '.$step : 'Continue to step '.$step ) . '" /></p></form>';
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>UseBB Upgrade 0.2.3</title>
<style type="text/css">
	body {
		font-family: sans-serif;
		text-align: center;
		font-size: 10pt;
	}
	#logo {
		margin-bottom: 0px;
	}
	h1 {
		color: #336699;
		font-size: 18pt;
		font-weight: bold;
		margin-top: 0px;
	}
	#wrap {
		width: 600px;
		margin: 0px auto 0px auto;
		background-color: #EFEFEF;
		border: 1px solid silver;
		padding: 10px;
		text-align: left;
	}
	h2 {
		color: #336699;
		font-size: 12pt;
		font-weight: bold;
		text-align: center;
	}
	form {
		text-align: center;
	}
	address {
		color: #333333;
		margin: 10px 0px 0px 0px;
	}
</style>
</head>
<body>
<p id="logo"><img src="../templates/default/gfx/usebb.png" alt="" /></p>
<h1>Upgrade 0.2.3</h1>
<div id="wrap">
<?php

if ( empty($_POST['step']) ) {
	
	echo '<h2>Welcome</h2>';
	echo '<p>Welcome to the UseBB upgrade 0.2.3 wizard. This wizard will help you upgrade UseBB <strong>0.2.3(a)</strong> to version <strong>0.3</strong>.</p>';
	echo to_step(1);
	
} elseif ( intval($_POST['step']) === 1 ) {
	
	echo '<h2>Step 1</h2>';
	
	if ( !function_exists('version_compare') ) {
		
		echo '<p>We\'re sorry. UseBB does not work on the PHP version running on this server (PHP '.phpversion().'). You need at least <strong>4.1.0</strong>. Get a recent version from <a href="http://www.php.net/downloads.php">PHP.net</a>.</p>';
		
	} else {
		
		echo '<p>First, upload UseBB 0.3 to the same location as 0.2.3, overwriting the old files. Then edit the database configuration values in <code>config.php</code>. Make sure the database settings match with those for your host. If in doubt, please contact your web host for information regarding accessing databases.</p>';
		echo '<p><strong>Tip:</strong> if you already use MySQL 4.1, it might be interesting to set <code>$dbs[\'type\']</code> to <code>\'mysqli\'</code>. If you don\'t know which version you are running, leave the default value.</p>';
		echo '<p><strong>Another tip:</strong> you might want to check <a href="http://usebb.sourceforge.net/docs/doku.php?id=configuration:config.php_guide">this document</a> out to change config.php.</p>';
		echo to_step(2);
		
	}
	
} elseif ( intval($_POST['step']) === 2 ) {
	
	echo '<h2>Step 2</h2>';
	if ( !empty($connerror) ) {
		
		echo '<p>An error was encountered while trying to access the database. The error was:</p>';
		echo '<code>'.$connerror.'</code>';
		echo '<p>Please check your database settings in <code>config.php</code>!</p>';
		echo to_step(2);
		
	} else {
		
		echo '<p>The database settings are OK!</p>';
		echo to_step(3);
		
	}
	
} elseif ( intval($_POST['step']) === 3 ) {
	
	echo '<h2>Step 3</h2>';
	
	$queries = array(
		"ALTER TABLE `".$dbs['prefix']."users` RENAME `".$dbs['prefix']."members`",
		"ALTER TABLE `".$dbs['prefix']."members` ADD `last_pageview` INT( 10 ) NOT NULL AFTER `last_login_show` , ADD `hide_from_online_list` INT( 1 ) NOT NULL AFTER `last_pageview`",
		"ALTER TABLE `".$dbs['prefix']."members` ADD `target_blank` INT( 1 ) NOT NULL AFTER `return_to_topic_after_posting` , ADD `hide_avatars` INT( 1 ) NOT NULL AFTER `target_blank` , ADD `hide_userinfo` INT( 1 ) NOT NULL AFTER `hide_avatars` , ADD `hide_signatures` INT( 1 ) NOT NULL AFTER `hide_userinfo`"
	);
	
	$error = false;
	foreach ( $queries as $query ) {
		
		if ( !($db->query($query)) ) {
			
			$error = true;
			break;
			
		}
		
	}
	
	$db->disconnect();
	
	if ( $error ) {
		
		echo '<p>An error occured while executing the SQL queries.</p>';
		echo to_step(3);
		
	} else {
		
		echo '<p>All SQL queries have been executed. Your board has now been updated to version 0.3. If the latest release is newer, you might need to run other wizards too. See the UPGRADE document. Otherwise, please delete the directory <code>install/</code> for security reasons. You can now go to <a href="../">your UseBB board</a> and continue using it.</p>';
		echo '<p><strong>Tip:</strong> you might want to use <a href="http://usebb.sourceforge.net/docs/doku.php?id=configuration:administration_without_acp">this manual</a> to further set up your forum.</p>';
		echo '<p>Thanks for choosing UseBB! We wish you a lot of fun with your board!</p>';
		
	}
	
}

?>
</div>
<address>Copyright &copy; 2003-2007 UseBB Team</address>
</body>
</html>