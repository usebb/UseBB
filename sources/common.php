<?php

/*
	Copyright (C) 2003-2004 UseBB Team
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
$timer['begin'] = explode(' ', microtime());
$timer['begin'] = (float)$timer['begin'][1] + (float)$timer['begin'][0];

//
// Disable notices of uninitialized
// variables and the magic quotes runtime
//
#error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL);
set_magic_quotes_runtime(0);

//
// Include all necessary files
//
require(ROOT_PATH.'sources/functions.php');
require(ROOT_PATH.'config.php');
require(ROOT_PATH.'sources/session.php');
require(ROOT_PATH.'sources/template.php');

//
// Define some constants
//
define('TABLE_PREFIX', $dbs['prefix']);
define('USEBB_VERSION', '0.2.1');
define('USER_PREG', '/^[a-z0-9\.\-\+\[\]_ ]+$/is');
define('EMAIL_PREG', '/^[a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+$/is');
define('PWD_PREG', '/^[^\'\"\s\\]+$/is');
define('WEB_PREG', '/^[\w]+?://[^ \"\n\r\t<]*?$/is');
define('IMG_PREG', '/^[\w]+?://[^ \"\n\r\t<]*?\.(gif|png|jpe?g)$/is');

//
// Create objects
//
$functions = new functions;
$session = new session;
$template = new template;

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

$db_class_file = ROOT_PATH.'sources/db_'.$dbs['type'].'.php';
if ( !file_exists($db_class_file) || !is_readable($db_class_file) )
	$functions->usebb_die('SQL', 'Unable to load module for database server "'.$dbs['type'].'"!', __FILE__, __LINE__);
else
	require($db_class_file);

//
// Create objects
//
$db = new db;

//
// Add slashes to get, post and cookie variables if magic
// quotes gpc is off. This is necessary for security reasons.
//
if ( !get_magic_quotes_gpc() ) {
	
	$_GET = $functions->slashes_to_global($_GET); // slashes to get vars
	$_POST = $functions->slashes_to_global($_POST); // slashes to post vars
	$_COOKIE = $functions->slashes_to_global($_COOKIE); // slashes to cookie vars
	
}
	
//
// Trim get, post and cookie variables
//
$_GET = $functions->trim_global($_GET); // trim get vars
$_POST = $functions->trim_global($_POST); // trim post vars
$_COOKIE = $functions->trim_global($_COOKIE); // trim cookie vars

//
// Connect to DB
//
$db->connect($dbs);

//
// Start/continue session
//
$session->start();

?>
