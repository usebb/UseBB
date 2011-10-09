<?php

namespace UseBB\System\Database;

use Doctrine\DBAL\DriverManager;

/**
 * Database connection.
 *
 * Represents a connection to the database.
 *
 * \author Dietrich Moerman
 */
class Connection {
	private $type;
	private $connection;
	private $prefix;

	/**
	 * Constructor.
	 * 
	 * \param $dbConfig Database configuration array
	 * 
	 * \exception \InvalidArgumentException When connection info is missing
	 * \exception \InvalidArgumentException On unknown database type
	 */
	public function __construct(array $dbConfig) {
		if (!isset($dbConfig["type"])) {
			throw new \InvalidArgumentException("Missing database connection info.");
		}
		
		$this->type = $dbConfig["type"];
		$this->prefix = !empty($dbConfig["prefix"])
			? $dbConfig["prefix"]
			: "";
		
		$driverMap = array(
			"mysql"      => "pdo_mysql",
			"sqlite"     => "pdo_sqlite",
			"postgresql" => "pdo_pgsql",
			"oracle"     => "oci8", // pdo_oci gives problems with DBAL
			"mssql"      => "pdo_sqlsrv"
		);

		if (!isset($driverMap[$this->type])) {
			throw new \InvalidArgumentException("Unknown database type.");
		}
		
		$params = array(
			"driver"  => $driverMap[$this->type],
			"charset" => "utf8"
		);
		unset($dbConfig["type"], $dbConfig["prefix"]);
		$params = array_merge($params, $dbConfig);

		$this->connection = DriverManager::getConnection($params);
		$this->connection->setCharset("utf8");
	}

	/**
	 * Get the table prefix.
	 * 
	 * \returns Table prefix
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 * Get the database's schema.
	 * 
	 * \returns Schema instance
	 */
	public function getSchema() {
		return new Schema($this->type, $this->connection, $this->prefix);
	}

	/**
	 * Begin a database transaction.
	 */
	public function beginTransaction() {
		$this->connection->beginTransaction();
	}

	/**
	 * Commit the transaction.
	 */
	public function commit() {
		$this->connection->commit();
	}

	/**
	 * Rollback the transaction.
	 */
	public function rollback() {
		$this->connection->rollback();
	}

	/**
	 * Create a new query.
	 * 
	 * \returns Query instance
	 */
	public function newQuery() {
		return new Query($this->connection->createQueryBuilder(), 
			$this->prefix);
	}
	
	/**
	 * Convert DateTime instances to strings.
	 * 
	 * \param $data Array
	 */
	private function setDateTimes(array &$data) {
		$new = array();
		
		foreach ($data as $k => $v) {
			if ($v instanceof \DateTime) {
				$v = $v->format("Y-m-d H:i:s");
			}
			
			$new[$k] = $v;
		}
		
		$data = $new;
	}
	
	/**
	 * Insert a row into a table.
	 * 
	 * Example:
	 * 
	 * \code
	 * $db->insert("test", array(
	 * 	"foo" => "bar"
	 * ));
	 * \endcode
	 * 
	 * \param $tableName Table name
	 * \param $data Key/value pairs
	 * \param $hasDateTime Has DateTime instances
	 * \returns Number of affected rows
	 */
	public function insert($tableName, array $data, $hasDateTime = FALSE) {
		if ($hasDateTime) {
			$this->setDateTimes($data);
		}
		
		return $this->connection->insert($this->prefix . $tableName, $data);
	}
	
	/**
	 * Update data in a database table.
	 * 
	 * \param $tableName Table name
	 * \param $data Key/value pairs
	 * \param $identifier Key/value pairs as update criteria
	 * \param $hasDateTime Has DateTime instances
	 * \returns Number of affected rows
	 */
	public function update($tableName, array $data, array $identifier, 
		$hasDateTime = FALSE) {
		if ($hasDateTime) {
			$this->setDateTimes($data);
		}
		
		return $this->connection->update($this->prefix . $tableName, $data, 
			$identifier);
	}
	
	/**
	 * Delete data from a database table.
	 * 
	 * \param $tableName Table name
	 * \param $identifier Key/value pairs as update criteria
	 * \returns Number of affected rows
	 */
	public function delete($tableName, array $identifier) {
		return $this->connection->delete($this->prefix . $tableName, 
			$identifier);
	}
	
	/**
	 * Close the database connection.
	 */
	public function close() {
		$this->connection->close();
	}
}
