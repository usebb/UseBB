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
 * MySQL database connection.
 *
 * @package UseBB
 * @subpackage db
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */
class UseBB_MySQLConnection extends UseBB_Connection
{
	/**
	 * Class constructor.
	 *
	 * @param string $dsn DSN (data source name)
	 * @param string $userName Username
	 * @param string $password Password
	 * @param string $tablePrefix Table prefix
	 */
	public function __construct($dsn, $userName, $password, $tablePrefix)
	{
		parent::__construct($dsn, $userName, $password, $tablePrefix, array
		(
			// Not sure if this is necessary.
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
			// MySQL does not cache prepared statements, so use direct ones.
			// http://dev.mysql.com/doc/refman/5.0/en/query-cache.html
			PDO::MYSQL_ATTR_DIRECT_QUERY => TRUE,
		));
	}
	
	/**
	 * Query the database and create a new statement object.
	 *
	 * The query should be marked up in several ways:
	 *  - UseBB table names without prefix enclosed in curly braces;
	 *  - parameters as PDO alike named parameters (:param).
	 *
	 * Parameters is an associative array of parameter names and values.
	 *
	 * @param string $query Query string
	 * @param array $parameters Parameters
	 * @return UseBB_MySQLStatement Statement
	 */
	public function query($query, array $parameters = array())
	{
		return new UseBB_MySQLStatement($this->PDO, parent::query($query, $parameters));
	}
}
