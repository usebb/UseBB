<?php

namespace UseBB\System;

/**
 * Abstract module class.
 * 
 * All modules' main classes inherit from this class. It sets the necessary
 * interface and default methods.
 * 
 * \author Dietrich Moerman
 */
abstract class AbstractModule extends ServiceAccessor {
	/**
	 * Run this module for HTTP.
	 */
	public function runForHTTP() {}
	
	/**
	 * Run this module for CLI.
	 */
	public function runForCLI() {}
	
	/**
	 * Get the schema manager for this module.
	 * 
	 * \returns AbstractSchemaManager instance
	 */
	public function getSchemaManager() {
		return NULL;
	}
	
	/**
	 * Called when the module gets enabled.
	 */
	public function enable() {}
	
	/**
	 * Called when the module gets disabled.
	 */
	public function disable() {}
	
	/**
	 * Called when the module gets updated.
	 * 
	 * \param $oldVersion Old version
	 */
	public function update($oldVersion) {}
}
