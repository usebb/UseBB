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
 * Database connection factory.
 *
 * @package UseBB
 * @subpackage db
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */
class UseBB_ConnectionFactory
{
	/**
	 * Instantiate a new database connection.
	 *
	 * @param string $dsn DSN (data source name)
	 * @param string $userName Username
	 * @param string $password Password
	 * @param string $tablePrefix Table prefix
	 * @returns UseBB_Connection Database connection instance
	 */
	public static function newConnection($dsn, $userName, $password, $tablePrefix)
	{
		// Find the driver to use.
		$driver = substr($dsn, 0, strpos($dsn, ':'));
		
		switch ( $driver )
		{
			case 'mysql':
				$db = new UseBB_MySQLConnection($dsn, $userName, $password, $tablePrefix);
				break;
			default:
				throw new UseBB_Exception('Database driver ' . $driver . ' not available.');
		}
		
		return $db;
	}
}
