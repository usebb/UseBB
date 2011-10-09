<?php

namespace UseBB\System\Session;

use UseBB\System\UseBBException;

/**
 * Value not found exception.
 * 
 * Thrown when a value for a session variable has not been found.
 * 
 * \author Dietrich Moerman
 */
class ValueNotFoundException extends UseBBException {
	private $key;
	
	/**
	 * Constructor.
	 * 
	 * \param $key Key
	 */
	public function __construct($key) {
		parent::__construct(sprintf("Session value for %s not found.", $key));

		$this->key = $key;
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
