<?php

namespace UseBB\System\ModuleManagement;

use UseBB\System\UseBBException;

/**
 * Module not found exception.
 *
 * Thrown when a module (name) is not found.
 *
 * \author Dietrich Moerman
 */
class ModuleNotFoundException extends UseBBException {
	private $name;
	
	/**
	 * Constructor.
	 *
	 * \param $name Name
	 */
	public function __construct($name) {
		parent::__construct(sprintf("Module '%s' not found.", $name));

		$this->name = $name;
	}

	/**
	 * Get the name.
	 *
	 * \returns Name
	 */
	public function getName() {
		return $this->name;
	}
}
