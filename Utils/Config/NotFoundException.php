<?php

namespace UseBB\Utils\Config;

use UseBB\System\UseBBException;

/**
 * Configuration value not found exception.
 * 
 * \author Dietrich Moerman
 */
class NotFoundException extends UseBBException {
	private $module;
	private $key;
	
	/**
	 * Constructor.
	 * 
	 * \param $module Module name
	 * \param $key Key
	 */
	public function __construct($module, $key) {
		parent::__construct(sprintf("Config value for '%s' at module '%s' " . 
			"not found.", $key, $module));

		$this->module = $module;
		$this->key = $key;
	}

	/**
	 * Get module name.
	 * 
	 * \returns Module name
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * Get key.
	 * 
	 * \returns Key
	 */
	public function getKey() {
		return $this->key;
	}
}
