<?php

/*
    Copyright (C) 2003-2004 UseBB Team
	http://usebb.sourceforge.net
	
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
set_error_handler('usebb_die'); // set the UseBB error handler

require(ROOT_PATH.'config.php');
require(ROOT_PATH.'sources/session.php');
require(ROOT_PATH.'sources/template.php');

$file = ROOT_PATH.'sources/db_'.$dbs['type'].'.php';
if ( !file_exists($file) || !is_readable($file) )
	usebb_die('SQL', 'Unable to load module for database server "'.$dbs['type'].'"!', __FILE__, __LINE__);
else
	require($file);

//
// Add slashes to get, post and cookie variables if magic
// quotes gpc is off. This is necessary for security reasons.
//
if ( !get_magic_quotes_gpc() ) {
	
	$_GET = usebb_slashes_to_global($_GET); // slashes to get vars
	$_POST = usebb_slashes_to_global($_POST); // slashes to post vars
	$_COOKIE = usebb_slashes_to_global($_COOKIE); // slashes to cookie vars
	
}
	
//
// Trim get, post and cookie variables
//
$_GET = usebb_trim_global($_GET); // trim get vars
$_POST = usebb_trim_global($_POST); // trim post vars
$_COOKIE = usebb_trim_global($_COOKIE); // trim cookie vars

//
// Define some constants
//
define('TABLE_PREFIX', $dbs['prefix']);
define('USEBB_VERSION', '0.1');
define('USER_PREG', '/^[a-z0-9\.\-\+\[\]_ ]+$/is');
define('EMAIL_PREG', '/^[a-z0-9\.\-_]+@[a-z0-9\-]+(\.[a-z0-9\-]+)*\.[a-z]+$/is');
define('PWD_PREG', '/^[^\'\"\s]+$/is');

//
// Create objects
//
$session = new session;
$template = new template;
$db = new db;

//
// Connect to DB
//
$db->connect($dbs);

//
// Get configuration variables
//
if ( !($result = $db->query("SELECT name, content FROM ".TABLE_PREFIX."config")) )
	usebb_die('SQL', 'Unable to get forum configuration!', __FILE__, __LINE__);
while ( $out = $db->fetch_result($result) )
	$config[$out['name']] = $out['content'];

//
// Start/continue session
//
$session->start($config['session_name'].'_sid');

?>
