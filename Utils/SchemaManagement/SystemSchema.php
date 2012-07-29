<?php

namespace UseBB\Utils\SchemaManagement;

use UseBB\System\ServiceRegistry;

/**
 * System database schema manager.
 * 
 * Manage the database schema of the system, used in the (web) installer 
 * and unit tests.
 * 
 * \author Dietrich Moerman
 */
class SystemSchema extends AbstractSchemaManager {
	protected $schemaVersion = 1;
	
	public function install() {
		$schema = $this->getService("database")->getSchema();
		
		// config
		
		$config = $schema->createTable("config");
		$config->addColumn("module", "string");
		$config->addColumn("ckey", "string");
		$config->addColumn("ctype", "string", array(
			"length" => 20
		));
		$config->addColumn("value", "text");
		$config->setPrimaryKey(array("module", "ckey"));
		$config->addIndex(array("module"));
		
		// modules
		
		$modules = $schema->createTable("modules");
		$modules->addColumn("name", "string");
		$modules->addColumn("enabled", "boolean");
		$modules->addColumn("version", "string");
		$modules->setPrimaryKey(array("name"));
		
		// sessions
		
		$sessions = $schema->createTable("sessions");
		$sessions->addColumn("id", "string", array(
			"length" => 32
		));
		$sessions->addColumn("ip_addr", "string", array(
			"length" => 39
		));
		$sessions->addColumn("user_id", "integer", array(
			"unsigned" => TRUE
		));
		$sessions->addColumn("started", "datetime");
		$sessions->addColumn("updated", "datetime");
		$sessions->addColumn("requests", "integer", array(
			"unsigned" => TRUE
		));
		$sessions->addColumn("browser", "string");
		$sessions->addColumn("language", "string", array(
			"length" => 5
		));
		$sessions->addColumn("data", "text");
		$sessions->setPrimaryKey(array("id"));
		$sessions->addIndex(array("id", "ip_addr"));
		
		// events
		
		$events = $schema->createTable("events");
		$events->addColumn("id", "integer", array(
			"unsigned" => TRUE,
			"autoincrement" => TRUE
		));
		$events->addColumn("level", "smallint", array(
			"unsigned" => TRUE,
		));
		$events->addColumn("module", "string");
		$events->addColumn("class", "string");
		$events->addColumn("user_id", "integer", array(
			"unsigned" => TRUE
		));
		$events->addColumn("object_type", "string");
		$events->addColumn("object_id", "integer", array(
			"unsigned" => TRUE
		));
		$events->addColumn("date", "datetime");
		$events->addColumn("message", "text");
		$events->addColumn("message_args", "text");
		$events->setPrimaryKey(array("id"));
		
		// done
		
		$schema->commitChanges();
		
		$this->getService("config")->set("system", "installed", TRUE);
		$this->setSchemaVersion();
	}
	
	public function uninstall() {
		$schema = $this->getService("database")->getSchema();
		
		$schema->dropTable("config");
		$schema->dropTable("modules");
		$schema->dropTable("sessions");
		$schema->dropTable("events");
		
		$schema->commitChanges();
	}
}
