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
// Timer for checking parsetime
//
$timer = array();
$timer['begin'] = explode(' ', microtime());
$timer['begin'] = (float)$timer['begin'][1] + (float)$timer['begin'][0];

//
// Check PHP version by checking the presence of version_compare()
// (available since PHP 4.1.0)
//
if ( !function_exists('version_compare') )
	die('<h1>Warning!</h1><p>UseBB does not work on PHP '.phpversion().'. You need at least <strong>4.1.0</strong>. Get a recent version from <a href="http://www.php.net/downloads.php">PHP.net</a>.</p>');

//
// Security measures
//
error_reporting(E_ALL);
set_magic_quotes_runtime(1);
@ini_set('display_errors', '1');

if ( @ini_get('register_globals') ) {
	
	foreach ( $_REQUEST as $var_name => $null )
		unset($$var_name);
	
}

//
// Include functions.php
//
require(ROOT_PATH.'sources/functions.php');
$functions = &new functions;
	
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
define('USEBB_VERSION', '0.6-CVS');
define('USER_PREG', '#^[A-Za-z0-9_\-]+$#');
define('EMAIL_PREG', '#^[a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+$#');
define('PWD_PREG', '#^[A-Za-z0-9]+$#');
define('WEB_PREG', '#^[\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?$#i');
define('IMG_PREG', '#^[\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?\.(gif|png|jpe?g)$#i');
define('LEVEL_ADMIN', 3);
define('LEVEL_MOD', 2);
define('LEVEL_MEMBER', 1);
define('LEVEL_GUEST', 0);

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
// Include all other necessary files
//
if ( !file_exists(ROOT_PATH.'config.php') )
	trigger_error('config.php does not exist! Please rename config.php-dist to config.php and make it writable by the webserver (chmod 0777).');
if ( !is_writable(ROOT_PATH.'config.php') )
	trigger_error('config.php is not writable! Please make it writable by the webserver (chmod 0777).');
require(ROOT_PATH.'config.php');
define('TABLE_PREFIX', $dbs['prefix']);
require(ROOT_PATH.'sources/template.php');
$template = &new template;
require(ROOT_PATH.'sources/session.php');
$session = &new session;

//
// Load the database class
//
$db_class_file = ROOT_PATH.'sources/db_'.$dbs['type'].'.php';
if ( !file_exists($db_class_file) || !is_readable($db_class_file) )
	trigger_error('Unable to load module for database server "'.$dbs['type'].'"!');
require($db_class_file);
$db = &new db;

//
// Connect to DB
//
$db->connect($dbs);

//
// Start/continue session
//
$session->start();

?>
