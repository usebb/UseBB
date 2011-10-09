<?php

namespace UseBB\System\ModuleManagement;

use UseBB\System\UseBBException;

/**
 * Unmet dependencies exception.
 *
 * Thrown when dependencies are unmet.
 *
 * \author Dietrich Moerman
 */
class UnmetDependenciesException extends UseBBException {
	private $name;
	
	/**
	 * Constructor.
	 *
	 * \param $name Name
	 */
	public function __construct($name) {
		parent::__construct(sprintf("Module '%s' has unmet dependencies.", 
			$name));

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
