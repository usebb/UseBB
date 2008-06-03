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
 * Represents a database connection.
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
	private $dsn;
	private $userName;
	private $tablePrefix;
	private $options;
	private $driver;
	private $host;
	private $PDO;
	
	/**
	 * Construct a new connection object.
	 *
	 * $libPath is passed with the default instance only.
	 *
	 * @param string $dsn Data Source Name (DSN)
	 * @param string $userName Username
	 * @param string $password Password
	 * @param string $tablePrefix Table prefix
	 * @param string $libPath Root UseBB library path
	 */
	public function __construct($dsn, $userName, $password, $tablePrefix, $libPath = NULL)
	{
		// Find the driver used.
		$driver = strtolower(substr_replace($dsn, '', strpos($dsn, ':')));
		
		// Unsupported driver.
		if ( !in_array($driver, self::getAvailableDrivers()) )
		{
			throw new UseBB_Exception('Database driver ' . $driver . ' not available.');
		}
		
		// Find the hostname in the DSN.
		preg_match('#(?:host(?:name)?|datasource)=([^; ]+)#i', $dsn, $host);
		
		$this->dsn = $dsn;
		$this->userName = $userName;
		$this->tablePrefix = $tablePrefix;
		$this->driver = $driver;
		$this->host = $host[1];
		
		// UseBB_Statement does not get loaded automatically using autoload...
		// So, load it once with the default connection.
		if ( $libPath !== NULL )
		{
			require_once $libPath . '/db/Statement.php';
		}
		
		// Create the PDO instance, setting default attributes.
		$this->PDO = new PDO($dsn, $userName, $password, array
		(
			PDO::ATTR_CASE => PDO::CASE_LOWER,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
			PDO::ATTR_STATEMENT_CLASS => array('UseBB_Statement'),
		));
	}
	
	/**
	 * Get available database drivers.
	 *
	 * @returns array Drivers
	 */
	public static function getAvailableDrivers()
	{
		return PDO::getAvailableDrivers();
	}
	
	/**
	 * Execute a database query.
	 *
	 * Tables surrounded with curly braces automatically get the table prefix.
	 * Parameters are equal to PDO's named parameters.
	 *
	 * @param string $query Query
	 * @param array $parameters Query parameters
	 * @returns UseBB_Statement Statement object
	 */
	public function query($query, array $parameters = array())
	{
		// Apply table prefix
		$query = preg_replace('#\{([a-z_]+)\}#', $this->tablePrefix . '$1', $query);
		
		$statement = $this->PDO->prepare($query);
		
		foreach ( $parameters as $key => $value )
		{
			$statement->bindValue($key, $value);
		}
		
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		$statement->execute();
		
		return $statement;
	}
}
