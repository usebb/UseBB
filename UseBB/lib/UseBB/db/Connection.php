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
 * Manages and represents database connections.
 *
 * @package UseBB
 * @subpackage db
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */
final class UseBB_Connection
{
	const DEFAULT_NAME = 'default';
	
	private static $objects = array();
	
	private $name;
	private $type;
	private $host;
	private $tablePrefix;
	private $connection;
	
	/**
	 * Construct a new connection object
	 *
	 * @param string $name Connection name
	 * @param string $dsn Data Source Name (DSN)
	 * @param string $userName Username
	 * @param string $password Password
	 * @param string $tablePrefix Table prefix
	 * @param array $options PDO options array
	 */
	private function __construct($name, $dsn, $userName, $password, $tablePrefix, array $options)
	{
		preg_match('#(?:host(?:name)?|datasource)=([^; ]+)#i', $dsn, $host);
		
		$this->name = $name;
		$this->type = strtolower(substr_replace($dsn, '', strpos($dsn, ':')));
		$this->host = $host[1];
		$this->tablePrefix = $tablePrefix;
		$this->connection = new PDO($dsn, $userName, $password, $options);
	}
	
	/**
	 * Open a new named database connection
	 *
	 * @param string $name Connection name
	 * @param string $dsn Data Source Name (DSN)
	 * @param string $userName Username
	 * @param string $password Password
	 * @param string $tablePrefix Table prefix
	 * @param array $options PDO options array
	 * @returns UseBB_Connection Database connection
	 */
	public static function open($name, $dsn, $userName, $password = NULL, $tablePrefix = NULL, array $options = array())
	{
		if ( array_key_exists($name, self::$objects) )
		{
			throw new UseBB_Exception('A database object "' . $name . '" is already registered.');
		}
		
		self::$objects[$name] = $object = new UseBB_Connection($name, $dsn, $userName, $password, $tablePrefix, $options);
		
		return $object;
	}
	
	/**
	 * Get the instance of a named connection
	 *
	 * @param string $name Connection name (when empty, default will be used)
	 * @returns UseBB_Connection Database connection
	 */
	public static function getInstance($name = NULL)
	{
		if ( $name === NULL )
		{
			$name = self::DEFAULT_NAME;
		}
		
		if ( !array_key_exists($name, self::$objects) )
		{
			throw new UseBB_Exception('There is no database object "' . $name . '" registered.');
		}
		
		return self::$objects[$name];
	}
	
	/**
	 * Close a named database connection
	 *
	 * Note: when you have a reference to the connection object, you'll need
	 * to unset it for the connection to actually close.
	 *
	 * @param string $name Connection name (when empty, default will be used)
	 */
	public static function close($name = NULL)
	{
		if ( $name === NULL )
		{
			$name = self::DEFAULT_NAME;
		}
		
		if ( !array_key_exists($name, self::$objects) )
		{
			throw new UseBB_Exception('There is no database object "' . $name . '" registered.');
		}
		
		unset(self::$objects[$name]);
	}
	
	/**
	 * Close all open database connections
	 *
	 * Note: when you have a reference to a connection object, you'll need
	 * to unset it for the connection to actually close.
	 */
	public static function closeAll()
	{
		self::$objects = array();
	}
	
	public function __toString()
	{
		$host = ( !empty($this->host) ) ? $this->host : 'unknown host';
		
		$types = array
		(
			'mssql' => 'Microsoft SQL Server',
			'sybase' => 'Sybase',
			'dblib' => 'DBLIB',
			'firebird' => 'Firebird',
			'ibm' => 'IBM',
			'informix' => 'Informix',
			'mysql' => 'MySQL',
			'oci' => 'Oracle',
			'odbc' => 'ODBC',
			'pgsql' => 'PostgreSQL',
			'sqlite' => 'SQLite',
		);
		
		return 'Database connection "' . $this->name . '" at ' . $this->host . ' (' . $types[$this->type] . ')';
	}
}
