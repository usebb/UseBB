<?php

namespace UseBB\Utils\Config;

use UseBB\System\ServiceAccessor;
use UseBB\System\ServiceRegistry;

/**
 * Abstract configuration registry.
 * 
 * Implements the chaining of registries.
 * 
 * \author Dietrich Moerman
 */
abstract class AbstractRegistry extends ServiceAccessor {
	private $parent;
	
	/**
	 * Constructor.
	 * 
	 * \param $services ServiceRegistry instance
	 * \param $parent AbstractRegistry instance or \c NULL
	 */
	public function __construct(ServiceRegistry $services, 
		AbstractRegistry $parent = NULL) {
		parent::__construct($services);
		
		$this->parent = $parent;
	}
	
	/**
	 * Get a value.
	 * 
	 * If the value is not found but a parent is set, the parent will be used.
	 * 
	 * \param $module Module name
	 * \param $key Key
	 * \returns Value
	 * 
	 * \exception NotFoundException When value not found (in this and parent(s))
	 */
	public function get($module, $key) {
		$value = NULL;
		
		try {
			$value = $this->_get($module, $key);
		} catch (NotFoundException $e) {
			if ($this->parent !== NULL) {
				return $this->parent->get($module, $key);
			}
			
			throw $e;
		}
		
		return $value;
	}
	
	/**
	 * Get a value in current registry.
	 * 
	 * \param $module Module name
	 * \param $key Key
	 * \returns Value
	 * 
	 * \exception NotFoundException When value not found
	 */
	abstract protected function _get($module, $key);
}
