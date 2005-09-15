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

/**
 * MySQLi database driver
 *
 * Contains the db class for MySQLi handling.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2005 UseBB Team
 * @package	UseBB
 * @subpackage Core
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

if ( !extension_loaded('mysqli') )
	trigger_error('Unable to load module for database server "mysqli": PHP mysqli extension not available!');

@ini_set('mysqli.trace_mode', '0');

/**
 * MySQLi database driver
 *
 * Performs database handling for MySQLi.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2005 UseBB Team
 * @package	UseBB
 * @subpackage Core
 */
class db {
	
	/**#@+
	 * @access private
	 */
	var $connection;
	var $queries = array();
	/**#@-*/
	
	/**
	 * Make a connection to the MySQL server
	 *
	 * @param array $config Database configuration
	 */
	function connect($config) {
		
		//
		// Connect to server
		//
		$this->connection = @mysqli_connect($config['server'], $config['username'], $config['passwd']) or trigger_error('SQL: '.mysqli_error());
		
		//
		// Select database
		//
		@mysqli_select_db($config['dbname'], $this->connection) or trigger_error('SQL: '.mysqli_error());
		
	}
	
	/**
	 * Execute database queries
	 *
	 * @param string $query SQL query
	 * @param bool $return_error Return error instead of giving general error
	 * @returns mixed SQL result resource or SQL error (only when $return_error is true)
	 */
	function query($query, $return_error=false) {
		
		global $functions;
		
		$this->queries[] = preg_replace('#\s+#', ' ', $query);
		$result = @mysqli_query($query, $this->connection) or $error = mysqli_error();
		if ( isset($error) ) {
			
			if ( $return_error ) 
				return $error;
			else
				trigger_error('SQL: '.$error);
			
		}
		return $result;
		
	}
	
	/**
	 * Fetch query results
	 *
	 * @param resource $result SQL query resource
	 * @returns array Array containing one result
	 */
	function fetch_result($result) {
		
		return mysqli_fetch_array($result, MYSQL_ASSOC);
		
	}
	
	/**
	 * Count row number
	 *
	 * @param resource $result SQL query resource
	 * @returns int Number of result rows
	 */
	function num_rows($result) {
		
		return mysqli_num_rows($result);
		
	}
	
	/**
	 * Last inserted ID
	 *
	 * @returns int Last inserted auto increment ID
	 */
	function last_id() {
		
		return mysqli_insert_id($this->connection);
		
	}
	
	/**
	 * Get used queries array
	 *
	 * @returns array Array containing executed queries
	 */
	function get_used_queries() {
		
		return $this->queries;
		
	}
	
	/**
	 * Get server version info
	 *
	 * @returns array Array containing database driver info and server version
	 */
	function get_server_info() {
		
		return array(
			'MySQL (mysqli)',
			mysqli_get_server_info($this->connection)
		);
		
	}
	
	/**
	 * Disconnect the database connection
	 */
	function disconnect() {
		
		@mysqli_close($this->connection);
		
	}
	
}

?>
