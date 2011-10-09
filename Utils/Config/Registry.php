<?php

namespace UseBB\Utils\Config;

use UseBB\System\Database\Connection;

/**
 * Configuration registry for the system and modules.
 * 
 * Settings are grouped by module and can have various internal PHP types.
 * Upon saving, settings are saved in the database.
 * 
 * \author Dietrich Moerman
 */
class Registry extends AbstractRegistry {
	private $cache = array();
	private $new = array();
	private $updated = array();
	
	/**
	 * Load a module's settings in the cache.
	 * 
	 * \param $module Module name
	 */
	private function loadConfigForModule($module) {
		$this->cache[$module] = array();
		
		try {
			$stmt = $this->getService("database")->newQuery()
				->select("c.ckey", "c.ctype", "c.value")->from("config", "c")
				->where("c.module = :module")->setParameter(":module", $module)
				->execute();
			$all = $stmt->fetchAll();
			
			foreach ($all as $one) {
				$this->cache[$module][$one["ckey"]] = 
					$this->toNativeValue($one["value"], $one["ctype"]);
			}
			
			$stmt->closeCursor();
		} catch (\PDOException $e) {
			// Do nothing.
		}
	}
	
	/**
	 * Convert to native value.
	 * 
	 * \param $value Value
	 * \param $type Type name
	 * \returns Value in native type
	 */
	private function toNativeValue($value, $type) {
		if ($type == "array") {
			return $this->unserialize($value);
		}
		
		settype($value, $type);
		
		return $value;
	}
	
	/**
	 * Convert to database value.
	 * 
	 * \param $value Value
	 * \returns Array with value and type
	 */
	private function toDatabaseValue($value) {
		$type = gettype($value);
		
		if ($type == "array") {
			return array($this->serialize($value), $type);
		}
		
		return array((string) $value, $type);
	}
	
	/**
	 * Register a setting change.
	 * 
	 * \param $module Module name
	 * \param $key Key
	 */
	private function registerChange($module, $key) {
		$item = array($module, $key);
		
		if (!isset($this->cache[$module][$key])) {
			$this->new[] = $item;
		} elseif (!in_array($item, $this->new)) {
			$this->updated[] = $item;
		}
	}
	
	/**
	 * Set a value.
	 * 
	 * \param $module Module name
	 * \param $key Key
	 * \param $value Value
	 */
	public function set($module, $key, $value) {
		if (!isset($this->cache[$module])) {
			$this->loadConfigForModule($module);
		}
		
		$this->registerChange($module, $key);
		$this->cache[$module][$key] = $value;
	}
	
	protected function _get($module, $key) {
		if (!isset($this->cache[$module])) {
			$this->loadConfigForModule($module);
		}
		
		if (!isset($this->cache[$module][$key])) {
			throw new NotFoundException($module, $key);
		}
		
		return $this->cache[$module][$key];
	}
	
	/**
	 * Delete a value.
	 * 
	 * \note The module/key combination will still return a value whenever
	 * a default was set in the system/module.
	 * 
	 * \param $module Module name
	 * \param $key Key
	 */
	public function delete($module, $key) {
		if (!isset($this->cache[$module])) {
			$this->loadConfigForModule($module);
		}
		
		if (!isset($this->cache[$module][$key])) {
			// No exception.
			return;
		}
		
		unset($this->cache[$module][$key]);
		
		$item = array($module, $key);
		$filter = function($current) use($item) {
			return $item[0] != $current[0] || $item[1] != $current[1];
		};
		
		if (in_array($item, $this->new)) {
			$this->new = array_filter($this->new, $filter);
			
			return;
		}
		
		if (in_array($item, $this->updated)) {
			$this->updated = array_filter($this->updated, $filter);
		}
		
		$this->getService("database")->delete("config", array(
			"module" => $module,
			"ckey"   => $key
		));
	}
	
	/**
	 * Refresh configuration from database.
	 */
	public function refresh() {
		$this->cache = array();
	}
	
	/**
	 * Save new settings.
	 * 
	 * \param $db Connection
	 */
	private function saveNew(Connection $db) {
		foreach ($this->new as $newItem) {
			list($module, $key) = $newItem;
			list($value, $type) = 
				$this->toDatabaseValue($this->cache[$module][$key]);
			
			$db->insert("config", array(
				"module" => $module,
				"ckey"   => $key,
				"ctype"  => $type,
				"value"  => $value
			));
		}
		
		$this->new = array();
	}
	
	/**
	 * Save updated settings.
	 * 
	 * \param $db Connection
	 */
	private function saveUpdated(Connection $db) {
		foreach ($this->updated as $updatedItem) {
			list($module, $key) = $updatedItem;
			list($value, $type) = 
				$this->toDatabaseValue($this->cache[$module][$key]);
			
			$db->update("config", array(
				"ctype"  => $type,
				"value"  => $value
			), array(
				"module" => $module,
				"ckey"   => $key
			));
		}
		
		$this->updated = array();
	}
	
	/**
	 * Save configuration in database.
	 */
	public function save() {
		if (count($this->new) + count($this->updated) == 0) {
			$this->refresh();
			
			return;
		}
		
		$db = $this->getService("database");
		
		$this->saveNew($db);
		$this->saveUpdated($db);
		$this->refresh();
	}
	
	/**
	 * Get a registry for a specific module.
	 * 
	 * \param $module Module name
	 * \returns ModuleRegistry instance
	 */
	public function forModule($module) {
		return new ModuleRegistry($module, $this);
	}
}
