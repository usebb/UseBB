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

if ( !extension_loaded('mysql') )
	$functions->usebb_die('General', 'Unable to load module for database server "mysql": PHP mysql extension not available!', __FILE__, __LINE__);

//
// Create the MySQL handlers
//
class db {
	
	//
	// Variables in this class
	//
	var $connection;
	var $queries = array();
	var $results = array();
	var $lastresult;
	
	//
	// Make a connection to the MySQL server
	//
	function connect($config) {
		
		global $functions;
		
		//
		// Connect to server
		//
		if ( !($this->connection = @mysql_connect($config['server'], $config['username'], $config['passwd'])) )
			$functions->usebb_die('General', 'Unable to connect to the database server!', __FILE__, __LINE__);
		//
		// Select database
		//
		if ( !(@mysql_select_db($config['dbname'], $this->connection)) )
			$functions->usebb_die('General', 'Unable to connect to the database!', __FILE__, __LINE__);
		
	}
	
	//
	// Execute database queries
	//
	function query($query) {
		
		$add_query = preg_replace("/\s+/", ' ', $query);
		$this->queries[] = $add_query;
		return @mysql_query($query, $this->connection);
		
	}
	
	//
	// Fetch query results
	//
	function fetch_result($result) {
		
		global $functions;
		
		if ( $functions->get_config('auto_free_sql_results') ) {
			
			$resultnum = intval($result);
			
			if ( !array_key_exists($resultnum, $this->results) ) {
				
				if ( isset($this->lastresult) && $result != $this->lastresult )
					mysql_free_result($this->lastresult);
				$this->lastresult = $result;
				
				$resultset = array();
				while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) )
					$resultset[] = $row;
				$this->results[$resultnum] = $resultset;
				reset($this->results[$resultnum]);
				return current($this->results[$resultnum]);
				
			} else {
				
				return next($this->results[$resultnum]);
				
			}
			
		} else {
			
			return mysql_fetch_array($result, MYSQL_ASSOC);
			
		}
		
	}
	
	//
	// Count row number
	//
	function num_rows($result) {
		
		return mysql_num_rows($result);
		
	}
	
	//
	// Last ID
	//
	function last_id() {
		
		return mysql_insert_id($this->connection);
		
	}
	
	//
	// Get used queries array
	//
	function get_used_queries() {
		
		return $this->queries;
		
	}
	
	//
	// Disconnect the database connection
	//
	function disconnect() {
		
		mysql_close($this->connection);
		
	}
	
}

?>
