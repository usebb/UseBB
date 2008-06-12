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
 * Database connection.
 *
 * @package UseBB
 * @subpackage db
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */
abstract class UseBB_Connection
{
	protected $dsn;
	protected $userName;
	protected $tablePrefix;
	protected $host;
	protected $PDO;
	
	/**
	 * Class constructor.
	 *
	 * @param string $dsn DSN (data source name)
	 * @param string $userName Username
	 * @param string $password Password
	 * @param string $tablePrefix Table prefix
	 * @param array $options PDO options
	 */
	public function __construct($dsn, $userName, $password, $tablePrefix, array $options)
	{
		// Find the hostname in the DSN.
		preg_match('#(?:host(?:name)?|datasource)=([^; ]+)#i', $dsn, $host);
		
		$this->dsn = $dsn;
		$this->userName = $userName;
		$this->tablePrefix = $tablePrefix;
		$this->host = $host[1];
		
		// Standard options set by default.
		$_options = array
		(
			// Column names are lower-case.
			PDO::ATTR_CASE => PDO::CASE_LOWER,
			// Raise exception on error.
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			// Translate empty strings to NULL.
			PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
		);
		
		// Add the passed per-driver necessary options.
		// We can't use array_merge(), since this resets numeric keys.
		foreach ( $options as $key => $val )
		{
			$_options[$key] = $val;
		}
		
		// Create the PDO instance, setting default attributes.
		$this->PDO = new PDO($dsn, $userName, $password, $_options);
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
	 * @return PDOStatement Statement
	 */
	public function query($query, array $parameters = array())
	{
		// Apply table prefix.
		if ( strpos($query, '{') !== FALSE )
		{
			$query = preg_replace('#\{([a-z_]+)\}#', $this->tablePrefix . '$1', $query);
		}
		
		// Prepare this statement.
		$statement = $this->PDO->prepare($query);
		
		// Bind all the parameters by value.
		foreach ( $parameters as $key => $value )
		{
			if ( is_int($value) )
			{
				$statement->bindValue($key, $value, PDO::PARAM_INT);
			}
			else
			{
				$statement->bindValue($key, $value);
			}
		}
		
		// Execute.
		$statement->execute();
		
		return $statement;
	}
}
