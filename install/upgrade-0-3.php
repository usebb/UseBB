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

define('INCLUDED', true);
include('../config.php');

class functions {
	
	function usebb_die($errno, $error, $file, $line) {
		
		global $connerror;
		$connerror = $error;
		
	}
	
}

$functions = new functions;

if ( intval($_POST['step']) > 1 ) {
	
	include('../sources/db_'.$dbs['type'].'.php');
	$db = new db;
	$db->connect($dbs);
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>UseBB Upgrade 0.3.x</title>
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
	}
	h2 {
		color: #336699;
		font-size: 12pt;
		font-weight: bold;
	}
	address {
		color: #333333;
		margin: 10px 0px 0px 0px;
	}
</style>
</head>
<body>
<p id="logo"><img src="../templates/default/gfx/usebb.png" alt="" /></p>
<h1>Upgrade 0.3.x</h1>
<div id="wrap">
<?php

function to_step($step) {
	
	return '<form action="'.$_SERVER['PHP_SELF'].'" method="post"><p><input type="hidden" name="step" value="'.$step.'" /><input type="submit" value="Continue to step '.$step.'" /></p></form>';
	
}

if ( empty($_POST['step']) ) {
	
	echo '<h2>Welcome</h2>';
	echo '<p>Welcome to the UseBB upgrade 0.3.x wizard. This wizard will help you upgrade UseBB <strong>0.3.x</strong> to version <strong>0.4</strong>.</p>';
	echo to_step(1);
	
} elseif ( intval($_POST['step']) === 1 ) {
	
	echo '<h2>Step 1</h2>';
	
	if ( !function_exists('version_compare') ) {
		
		echo '<p>We\'re sorry. UseBB does not work on the PHP version running on this server (PHP '.phpversion().'). You need at least <strong>4.1.0</strong>. Get a recent version from <a href="http://www.php.net/downloads.php">PHP.net</a>.</p>';
		
	} else {
		
		echo '<p>First, upload UseBB 0.4 to the same location as 0.3.x, overwriting the old files. Then edit the configuration values in <code>config.php</code>. Make sure the database settings match with those for your host. If in doubt, please contact your web host for information regarding accessing databases.</p>';
		echo '<strong>Tip:</strong> if you already use MySQL 4.1, it might be interesting to set <code>$dbs[\'type\']</code> to <code>\'mysqli\'</code>. If you don\'t know which version you are running, leave the default value.';
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
		
		echo 'The database settings are OK!';
		echo to_step(3);
		
	}
	
} elseif ( intval($_POST['step']) === 3 ) {
	
	echo '<h2>Step 3</h2>';
	
	$queries = array(
		"CREATE TABLE usebb_subscriptions ( topic_id int(11) NOT NULL default '0', user_id int(11) NOT NULL default '0' ) TYPE=MyISAM;"
	);
	
	$error = false;
	foreach ( $queries as $query ) {
		
		if ( !($db->query($query)) ) {
			
			$error = true;
			break;
			
		}
		
	}
	
	if ( $error ) {
		
		echo '<p>An error occured while executing the SQL queries.</p>';
		echo to_step(3);
		
	} else {
		
		echo '<p>All SQL queries have been executed. Please delete the directory <code>install/</code> for security reasons. You can now go to <a href="'.$conf['board_url'].'">your UseBB board</a> and continue using it.</p>';
		echo '<p><strong>Note:</strong> if the above URL leads you to an error page without UseBB, your <code>$conf[\'board_url\']</code> value in <code>config.php</code> isn\'t correct! Please adjust it <strong>first</strong>!</p>';
		echo '<p><strong>Tip:</strong> you might want to use <a href="http://www.usebb.net/docs/index.php/Administration_without_ACP">this manual</a> to further set up your forum.</p>';
		echo '<p>Thanks for choosing UseBB! We wish you a lot of fun with your board!</p>';
		
	}
	
}

?>
</div>
<address>Copyright &copy; 2003-2005 UseBB Team</address>
</body>
</html>