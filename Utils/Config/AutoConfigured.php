<?php

namespace UseBB\Utils\Config;

use UseBB\System\ModuleManagement\ModuleNotFoundException;

/**
 * Auto-configured config settings.
 * 
 * Figures out unset settings from the environment, other settings, etc.
 * 
 * \author Dietrich Moerman
 */
class AutoConfigured extends AbstractRegistry {
	private $cache = array();
	
	/**
	 * Get a system auto-configured value.
	 * 
	 * \param $key Key
	 * \returns Value
	 */
	private function getSystemValue($key) {
		$value = NULL;
		
		switch ($key) {
			
		}
		
		return $value;
	}
	
	/**
	 * Get the value for a module and key.
	 * 
	 * \param $module Module
	 * \param $key Key
	 * \returns Value
	 * 
	 * \exception NotFoundException When value not found
	 */
	private function getValue($module, $key) {
		if ($module == "system") {
			return $this->getSystemValue($key);
		}
		
		try {
			$inst = $this->getService("modules")->getModuleInfo($module)
				->getModule();
		} catch (ModuleNotFoundException $e) {
			return NULL;
		}
		
		return $inst->getAutoConfigured($key);
	}
	
	protected function _get($module, $key) {
		if (!isset($this->cache[$module])) {
			$this->cache[$module] = array();
		}
		
		if (isset($this->cache[$module][$key])) {
			return $this->cache[$module][$key];
		}
		
		$value = $this->getValue($module, $key);
		
		if ($value === NULL) {
			throw new NotFoundException($module, $key);
		}
		
		$this->cache[$module][$key] = $value;
		
		return $value;
	}
}
