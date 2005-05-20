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

//
// Security measures
//
error_reporting(E_ALL);
set_magic_quotes_runtime(1);
@ini_set('display_errors', '1');
@ini_set('mysql.trace_mode', '0');

//
// Check PHP version by checking the presence of version_compare()
// (available since PHP 4.1.0)
//
if ( !function_exists('version_compare') )
	die('<h1>Warning!</h1><p>UseBB does not work on PHP '.phpversion().'. You need at least <strong>4.1.0</strong>. Get a recent version from <a href="http://www.php.net/downloads.php">PHP.net</a>.</p>');

//
// Timer for checking parsetime
//
$timer['begin'] = explode(' ', microtime());
$timer['begin'] = (float)$timer['begin'][1] + (float)$timer['begin'][0];

//
// Unregister globals for more security.
//
if ( @ini_get('register_globals') ) {
	
	foreach ( $_REQUEST as $var_name => $null )
		unset($$var_name);
	
}

//
// Include all necessary files
//
require(ROOT_PATH.'sources/functions.php');
require(ROOT_PATH.'config.php');
require(ROOT_PATH.'sources/session.php');
require(ROOT_PATH.'sources/template.php');
	
//
// Add slashes and trim get, post and cookie variables
//
$_GET = slash_trim_global($_GET);
$_POST = slash_trim_global($_POST);
$_COOKIE = slash_trim_global($_COOKIE);

//
// Define some constants
//
// NOTE!
// We don't allow non-alphanumeric characters anymore for usernames and passwords
// in order to avoid problems with different encodings used on the board.
// One can however set a publicly displayed name, eventually with non-alphanumeric
// characters.
//
define('TABLE_PREFIX', $dbs['prefix']);
define('USEBB_VERSION', '0.5.1-CVS');
define('USER_PREG', '#^[A-Za-z0-9_-]+$#');
define('EMAIL_PREG', '#^[a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+$#');
define('PWD_PREG', '#^[A-Za-z0-9]+$#');
define('WEB_PREG', '#^[\w]+?://[^ \"\n\r\t<]*?$#i');
define('IMG_PREG', '#^[\w]+?://[^ \"\n\r\t<]*?\.(gif|png|jpe?g)$#i');

//
// Create objects
//
$functions = new functions;
$session = new session;
$template = new template;

//
// Load the database class
//
$db_class_file = ROOT_PATH.'sources/db_'.$dbs['type'].'.php';
if ( !file_exists($db_class_file) || !is_readable($db_class_file) )
	trigger_error('Unable to load module for database server "'.$dbs['type'].'"!');
else
	require($db_class_file);

//
// Create objects
//
$db = new db;

//
// Set the UseBB error handler
//
function error_handler($errno, $error, $file, $line) {
	
	//
	// We use this workaround to make the error handler work
	// on < PHP 4.3.0. These older versions do not accept an 
	// array containing a link to a function inside a class.
	//
	
	global $functions;
	$functions->usebb_die($errno, $error, $file, $line);
	
}
set_error_handler('error_handler');

//
// Activate gzip compression if needed, BEFORE doing a session_start()
//
if ( !defined('IS_XML') && ( !defined('IS_CSS') || stristr($_SERVER['HTTP_USER_AGENT'], 'Gecko') ) && ( $functions->get_config('output_compression') === 2 || $functions->get_config('output_compression') === 3 ) && !ini_get('zlib.output_compression') )
	ob_start('ob_gzhandler');

//
// Connect to DB
//
$db->connect($dbs);

//
// Start/continue session
//
$session->start();

?>
