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

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

if ( !extension_loaded('pdo_mysql') )
	trigger_error('Unable to load module for database server "pdo-mysql": PHP pdo_mysql extension not available!');

//
// Create the MySQL handlers
//
class db {
	
	//
	// Variables in this class
	//
	var $connection;
	var $queries = array();
	
	//
	// Make a connection to the MySQL server
	//
	function connect($config) {
		
		try {
			
			$this->connection = new PDO('mysql:dbname='.$config['dbname'].';host='.$config['server'], $config['username'], $config['passwd']);
			
		} catch ( PDOException $e ) {
			
			trigger_error('SQL: '.$e->getMessage());
			
		}
		
	}
	
	//
	// Execute database queries
	//
	function query($query, $return_error=false) {
		
		$this->queries[] = preg_replace('#\s+#', ' ', $query);
		$query = $this->connection->prepare($query);
		$query->execute();
		return $query;
		
	}
	
	//
	// Fetch query results
	//
	function fetch_result($result) {
		
		return $result->fetch(PDO_FETCH_ASSOC);
		
	}
	
	//
	// Count row number
	//
	function num_rows($result) {
		
		return $result->rowCount();
		
	}
	
	//
	// Last ID
	//
	function last_id() {
		
		return $this->connection->lastInsertId();
		
	}
	
	//
	// Get used queries array
	//
	function get_used_queries() {
		
		return $this->queries;
		
	}
	
	//
	// Get server version info
	//
	function get_server_info() {
		
		return array(
			'MySQL (pdo-mysql)',
			$this->connection->getAttribute(PDO_ATTR_SERVER_VERSION)
		);
		
	}
	
	//
	// Disconnect the database connection
	//
	function disconnect() {
		
		unset($this->connection);
		
	}
	
}

?>
