<?php

namespace UseBB\System\Database;

use Doctrine\DBAL\Connection as DBALConnection;

/**
 * Database schema handling.
 *
 * Create, delete and modify database (table) schemas, to install and update
 * the core system and modules.
 * 
 * This class wraps around the basic schema getting and updating process of
 * Doctrine DBAL and gives access to table operations, taking into account
 * the table prefix set.
 * 
 * \attention You should not instantiate this class yourself, but use
 * Connection::getSchema().
 *
 * \author Dietrich Moerman
 */
class Schema {
	private $type;
	private $connection;
	private $prefix;
	private $tableOptions;
	private $manager;
	private $from;
	private $to;

	/**
	 * Constructor.
	 * 
	 * \param $type Database type
	 * \param $connection Doctrine DBAL connection
	 * \param $prefix Table prefix
	 */
	public function __construct($type, DBALConnection $connection, $prefix) {
		$this->type = $type;
		$this->connection = $connection;
		$this->prefix = $prefix;
		$this->manager = $connection->getSchemaManager();
		
		$this->makeTableOptions();
		$this->readSchema();
	}
	
	/**
	 * Create table creation options.
	 * 
	 * \TODO Choose between MyISAM and InnoDB
	 */
	private function makeTableOptions() {
		$opts = array();
		
		if ($this->type == "mysql") {
			$opts["charset"] = "utf8";
			$opts["collate"] = "utf8_unicode_ci";
			$opts["engine"] = "myisam";
		}
		
		$this->tableOptions = $opts;
	}
	
	/**
	 * (Re)read the database schema and copy.
	 */
	private function readSchema() {
		$this->from = $this->manager->createSchema();
		$this->to = clone $this->from;
	}
	
	/**
	 * Create a table.
	 * 
	 * Example creating a table "test" with two columns:
	 * 
	 * \code
	 * $table = $schema->createTable("test");
	 * $table->addColumn("id", "integer", array(
	 * 	"unsigned" => TRUE,
	 * 	"autoincrement" => TRUE
	 * ));
	 * $table->addColumn("foo", "text");
	 * $table->setPrimaryKey(array("id"));
	 * 
	 * $schema->commitChanges();
	 * \endcode
	 * 
	 * As the class takes the prefix into account, the created table might
	 * be \c usebb_test.
	 * 
	 * \param $name Table name
	 * \returns Doctrine DBAL Table instance
	 */
	public function createTable($name) {
		$table = $this->to->createTable($this->prefix . $name);;
		
		foreach ($this->tableOptions as $k => $v) {
			$table->addOption($k, $v);
		}
		
		return $table;
	}
	
	/**
	 * Get a table.
	 * 
	 * \param $name Table name
	 * \returns Doctrine DBAL Table instance
	 */
	public function getTable($name) {
		return $this->to->getTable($this->prefix . $name);
	}
	
	/**
	 * Rename a table.
	 * 
	 * \param $oldName Old table name
	 * \param $newName New table name
	 */
	public function renameTable($oldName, $newName) {
		$this->to->renameTable($this->prefix . $oldName, 
			$this->prefix . $newName);
	}
	
	/**
	 * Drop a table.
	 * 
	 * \param $name Table name
	 */
	public function dropTable($name) {
		$this->to->dropTable($this->prefix . $name);
	}
	
	/**
	 * Commit schema changes.
	 */
	public function commitChanges() {
		$queries = $this->from->getMigrateToSql($this->to,
			$this->connection->getDatabasePlatform());
		
		foreach ($queries as $query) {
			$this->connection->query($query);
		}
		
		$this->readSchema();
	}
	
	/**
	 * Rollback any changes made.
	 */
	public function rollback() {
		$this->to = clone $this->from;
	}
	
	/**
	 * Refresh the schema, discarding all changes.
	 */
	public function refresh() {
		$this->readSchema();
	}
}
