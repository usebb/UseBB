<?php

namespace UseBB\Utils\SchemaManagement;

use UseBB\System\ServiceAccessor;
use UseBB\System\ServiceRegistry;

/**
 * Abstract database schema manager.
 * 
 * Installs, uninstalls and updates the schema of a module or core system.
 * 
 * \author Dietrich Moerman
 */
abstract class AbstractSchemaManager extends ServiceAccessor {
	/**
	 * Module name.
	 */
	protected $module;
	
	/**
	 * Current schema version.
	 */
	protected $schemaVersion = 0;
	
	public function __construct(ServiceRegistry $services) {
		parent::__construct($services);
		
		$className = explode("\\", get_class($this));
		$this->module = $className[1] == "Utils"
			? "system"
			: $className[2];
	}
	
	/**
	 * Install the schema.
	 */
	abstract public function install();
	
	/**
	 * Uninstall the schema.
	 */
	abstract public function uninstall();
	
	/**
	 * Set the schema version in the configuration.
	 * 
	 * Requires the \c $schemaVersion property to be set correctly.
	 * 
	 * \note You must call this method in install() in order to be able to 
	 * make update methods and perform updates for your schema later on.
	 */
	protected function setSchemaVersion() {
		$config = $this->getService("config");
		
		$config->set($this->module, "schemaVersion", $this->schemaVersion);
		$config->save();
	}
	
	/**
	 * Update the schema.
	 * 
	 * This will get the current schema version and run all subsequent 
	 * \c updateToX methods, from \c current + 1 to the latest version.
	 */
	public function update() {
		$current = $this->getService("config")
			->get($this->module, "schemaVersion");
		$schema = $this->getService("database")->getSchema();
		
		for ($i = $current + 1; $i <= $this->schemaVersion; $i++) {
			call_user_func(array($this, "updateTo" . $i), $schema);
		}
		
		$schema->commitChanges();
		$this->setSchemaVersion();
	}
}
