<?php

namespace UseBB\System;

/**
 * Service not found exception.
 *
 * Thrown when a service (name) is not found.
 *
 * \author Dietrich Moerman
 */
class ServiceNotFoundException extends UseBBException {
	private $name;
	
	/**
	 * Constructor.
	 *
	 * \param $name Name
	 */
	public function __construct($name) {
		parent::__construct(sprintf("Service '%s' not found.", $name));

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
