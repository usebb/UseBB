<?php

/*
	Copyright (C) 2003-2011 UseBB Team
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
 * Common include file
 *
 * Does all kinds of stuff to initiate the board.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2011 UseBB Team
 * @package	UseBB
 * @subpackage Core
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

//
// Timer for checking parsetime
//
$timer_begin = explode(' ', microtime());
define('TIMER_BEGIN', (float)$timer_begin[1] + (float)$timer_begin[0]);

//
// Check PHP version
//
if ( !function_exists('version_compare') || version_compare(PHP_VERSION, '4.3.0', '<') )
	die('<h1>Unsupported PHP version</h1><p>UseBB 1 does not work on PHP '.PHP_VERSION.'. You need at least PHP <strong>4.3.0</strong>. Please install a recent PHP 4 or 5 release or contact your hosting provider.</p>');

//
// Production environment switch
//
// When TRUE:
//  * PHP notices are not shown to the user
//  * install/ directory must be removed to run
//  * debug level 2 is reset to level 1
//
// This is deliberately not made a config setting.
//
// Set to FALSE only if you are testing the package, developing it or performing modifications.
// In most other cases, such as live forums, this should be set to TRUE.
//
define('USEBB_IS_PROD_ENV', FALSE);

//
// Error reporting levels
//
$error_level = E_ALL ^ E_NOTICE;
if ( defined('E_DEPRECATED') )
	$error_level ^= E_DEPRECATED;
define('USEBB_PROD_ERROR_LEVEL', $error_level);

$error_level |= E_NOTICE;
if ( defined('E_DEPRECATED') )
	$error_level |= E_DEPRECATED;
define('USEBB_DEV_ERROR_LEVEL', $error_level);

error_reporting(USEBB_IS_PROD_ENV ? USEBB_PROD_ERROR_LEVEL : USEBB_DEV_ERROR_LEVEL);

//
// Setting some PHP config values
//
// magic_quotes_runtime is no longer used since 1.0.12
//
ini_set('magic_quotes_runtime', '0');
ini_set('display_errors', '1');
ini_set('session.use_trans_sid', '0');

//
// Disallow requests that contain some _XYZ global variables
//
$request_keys = array_keys($_REQUEST);
if ( in_array('_GET', $request_keys) || in_array('_POST', $request_keys) || in_array('_COOKIE', $request_keys) || in_array('_FILES', $request_keys) || in_array('_SERVER', $request_keys) || in_array('_ENV', $request_keys) || in_array('_REQUEST', $request_keys) )
	die('Disallowed request variable found. Exited.');

//
// Unset global variables
// Upto here, all created variables can be removed
//
if ( ini_get('register_globals') ) {
	
	foreach ( $_REQUEST as $var_name => $null )
		unset($$var_name);
	
	unset($null);
	
}

//
// Make $_SERVER['PHP_SELF'] safe
//
$_SERVER['PHP_SELF'] = str_replace(array('<', '>'), array('%3C', '%3E'), $_SERVER['PHP_SELF']);

//
// Fix unavailable $_SERVER['REQUEST_URI'] on IIS
//
if ( empty($_SERVER['REQUEST_URI']) ) {
	
	$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'];
	$_SERVER['REQUEST_URI'] .= ( !empty($_SERVER['QUERY_STRING']) ) ? '?'.$_SERVER['QUERY_STRING'] : '';
	
}

//
// Fix unavailable $_SERVER['HTTP_HOST']
//
if ( empty($_SERVER['HTTP_HOST']) ) {
	
	$_SERVER['HTTP_HOST'] = ( !empty($_SERVER['SERVER_NAME']) ) ? $_SERVER['SERVER_NAME'] : $_SERVER['SERVER_ADDR'];
	$_SERVER['HTTP_HOST'] .= ( !empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80 ) ? ':'.$_SERVER['SERVER_PORT'] : '';
	
}

//
// Fix some undefined values
//
foreach ( array('HTTP_USER_AGENT', 'SERVER_SOFTWARE') as $key )
	$_SERVER[$key] = ( !empty($_SERVER[$key]) ) ? $_SERVER[$key] : '';

//
// Without this, PHP 5.1 might drop a notice
// UseBB uses its own timezone handling where needed
//
if ( function_exists('date_default_timezone_set') )
	date_default_timezone_set('UTC');

//
// Include functions.php
//
require(ROOT_PATH.'sources/functions.php');
$functions = new functions;

//
// Check slashes and trim get, post and cookie variables
//
$magic_quotes = get_magic_quotes_gpc();
array_walk($_GET, 'usebb_clean_input_value', $magic_quotes);
array_walk($_POST, 'usebb_clean_input_value', $magic_quotes);
array_walk($_COOKIE, 'usebb_clean_input_value', $magic_quotes);
array_walk($_REQUEST, 'usebb_clean_input_value', $magic_quotes);

//
// Set the error handler
//
set_error_handler(array(&$functions, 'usebb_die'));

//
// Check for install/ on production environment.
//
if ( USEBB_IS_PROD_ENV && !defined('IS_INSTALLER') && file_exists(ROOT_PATH.'install') )
	trigger_error('The directory \'install\' must be removed after installation to access this forum.', E_USER_ERROR);

//
// Include config.php
//
$config_file = ROOT_PATH.'config.php';
if ( file_exists($config_file) )
	require($config_file);
else
	trigger_error('config.php does not exist!', E_USER_ERROR);

//
// Define some constants
//
// NOTE!
// We don't allow non-alphanumeric characters anymore for usernames
// in order to avoid problems with different encodings used on the board.
// One can however set a publicly displayed name, eventually with non-alphanumeric
// characters.
//
// UPDATE (1.0.12)
// ASCII symbols are allowed for passwords - but may not contain HTML entities ofcourse.
//
/**
 * Current UseBB version.
 */
define('USEBB_VERSION', '1.0.13');
/**
 * Regular expression for matching usernames.
 */
define('USER_PREG', '#^[a-z0-9_\- ]+$#i');
/**
 * Regular expression for matching e-mail addresses.
 */
define('EMAIL_PREG', '#^[a-z0-9&\-_\.\+]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+$#i');
/**
 * Regular expression for matching passwords.
 */
define('PWD_PREG', '#^[[:alnum:][:punct:]]+$#');
/**
 * Regular expression for matching URL's.
 */
define('WEB_PREG', '#^[\w]+?://[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)]*?$#i');
/**
 * Regular expression for matching image URL's.
 */
define('IMG_PREG', '#^[\w]+?://[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)]*?$#i');
/**
 * Level for admins.
 */
define('LEVEL_ADMIN', 3);
/**
 * Level for moderators.
 */
define('LEVEL_MOD', 2);
/**
 * Level for members.
 */
define('LEVEL_MEMBER', 1);
/**
 * Level for guests.
 */
define('LEVEL_GUEST', 0);
/**
 * SQL table prefix
 */
define('TABLE_PREFIX', $dbs['prefix']);
/**
 * On Windows
 */
define('ON_WINDOWS', ( DIRECTORY_SEPARATOR == '\\' ));
/**
 * Disable anti-spam
 */
define('ANTI_SPAM_DISABLE', 0);
/**
 * Anti-spam math question mode
 */
define('ANTI_SPAM_MATH', 1);
/**
 * Anti-spam custom question mode
 */
define('ANTI_SPAM_CUSTOM', 2);
/**
 * 403 header
 */
define('HEADER_403', $_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
/**
 * 404 header
 */
define('HEADER_404', $_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
/**
 * Debug disabled
 */
define('DEBUG_DISABLED', 0);
/**
 * Simple debug
 */
define('DEBUG_SIMPLE', 1);
/**
 * Extended debug
 */
define('DEBUG_EXTENDED', 2);

//
// If logging hidden errors is enabled, reset error reporting to dev environment.
// usebb_die will now catch these, log them and further ignore them.
//
if ( $functions->get_config('error_log_log_hidden') && USEBB_IS_PROD_ENV )
	error_reporting(USEBB_DEV_ERROR_LEVEL);

//
// Include all other necessary files
//
require(ROOT_PATH.'sources/template.php');
$template = new template;
require(ROOT_PATH.'sources/session.php');
$session = new session;

//
// Load the database class
//
$db_class_file = ROOT_PATH.'sources/db_'.$dbs['type'].'.php';

if ( !file_exists($db_class_file) || !is_readable($db_class_file) )
	trigger_error('Unable to load module for database server "'.$dbs['type'].'"!', E_USER_ERROR);

require($db_class_file);
$db = new db;

//
// Connect to DB
//
$db->connect($dbs);

//
// Start/continue session
//
$session->start();

?>
