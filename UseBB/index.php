<?php

/*
	This file is part of UseBB.

	UseBB is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	UseBB is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with UseBB.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * UseBB configuration and caller page.
 *
 * @package UseBB
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */

// Define paths to libraries
define('USEBB_LIB_ROOT', realpath('.') . '/lib');
define('USEBB_LIB_USEBB', USEBB_LIB_ROOT . '/UseBB');
define('USEBB_LIB_PHPUTF8', USEBB_LIB_ROOT . '/utf8');

// Define database connection info
define('USEBB_DB_DSN', 'mysql:host=localhost;dbname=usebb');
define('USEBB_DB_USERNAME', 'usebb');
define('USEBB_DB_PASSWORD', 'usebb');
define('USEBB_DB_TABLE_PREFIX', 'usebb2_');

// Call the init script
require USEBB_LIB_USEBB . '/init.php';
