<?php

namespace UseBB\Utils\Config;

/**
 * Configuration registry for specific module.
 * 
 * \attention You should not instantiate this class yourself but use
 * Registry::forModule() instead.
 * 
 * \author Dietrich Moerman
 */
class ModuleRegistry {
	private $module;
	private $root;
	
	/**
	 * Constructor.
	 * 
	 * \param $module Module name
	 * \param $root Root Registry
	 */
	public function __construct($module, Registry $root) {
		$this->module = $module;
		$this->root = $root;
	}
	
	/**
	 * Get root registry.
	 * 
	 * \returns Root Registry
	 */
	public function getRoot() {
		return $this->root;
	}
	
	/**
	 * Set a value.
	 * 
	 * \param $key Key
	 * \param $value Value
	 */
	public function set($key, $value) {
		$this->root->set($this->module, $key, $value);
	}
	
	/**
	 * Get a value.
	 * 
	 * \param $key Key
	 * \returns Value
	 */
	public function get($key) {
		return $this->root->get($this->module, $key);
	}
	
	/**
	 * Delete a value.
	 * 
	 * \note The module/key combination will still return a value whenever
	 * a default was set in the system/module.
	 * 
	 * \param $key Key
	 */
	public function delete($key) {
		return $this->root->delete($this->module, $key);
	}
	
	/**
	 * Refresh configuration from database.
	 */
	public function refresh() {
		$this->root->refresh();
	}
	
	/**
	 * Save configuration in database.
	 */
	public function save() {
		$this->root->save();
	}
}
