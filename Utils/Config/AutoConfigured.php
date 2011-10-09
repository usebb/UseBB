<?php

namespace UseBB\Utils\Config;

/**
 * Auto-configured config settings.
 * 
 * Figures out unset settings from the environment, other settings, etc.
 * 
 * \author Dietrich Moerman
 */
class AutoConfigured extends AbstractRegistry {
	private $cache = array();
	
	protected function _get($module, $key) {
		if (!isset($this->cache[$module])) {
			$this->cache[$module] = array();
		}
		
		if (isset($this->cache[$module][$key])) {
			return $this->cache[$module][$key];
		}
		
		$sKey = $module . "__" . $key;
		$value = NULL;
		
		switch ($sKey) {
			case "system__foo":
				$value = "bar";
				break;
			default:
				throw new NotFoundException($module, $key);
		}
		
		$this->cache[$module][$key] = $value;
		
		return $value;
	}
}
