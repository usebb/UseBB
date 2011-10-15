<?php

namespace UseBB\Utils\Config;

use UseBB\Utils\File\InfoFile;

/**
 * Config default values.
 * 
 * Load default values per module and return them to the config system.
 * 
 * \author Dietrich Moerman
 */
class Defaults extends AbstractRegistry {
	private $cache = array();
	
	/**
	 * Read the defaults from the file system.
	 * 
	 * \param $module Module name
	 */
	private function readConfigForModule($module) {
		$dir = $module == "system"
			? USEBB_ROOT_PATH . "/includes/"
			: USEBB_ROOT_PATH . "/Modules/" . $module . "/";
		$file = new InfoFile($dir . "configDefaults.php");
		
		return $file->getInfo();
	}
	
	/**
	 * Set the defaults in the cache.
	 * 
	 * \param $module Module name
	 */
	private function loadConfigForModule($module) {
		$this->cache[$module] = $this->readConfigForModule($module);
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
}
