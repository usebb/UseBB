<?php

namespace UseBB\Utils\Input;

use UseBB\System\UseBBException;

/**
 * %Input value not found exception.
 *
 * Thrown when an input value (key) is not found in the method.
 *
 * \author Dietrich Moerman
 */
class NotFoundException extends UseBBException {
	private $key;
	private $type;
	private $method;
	
	/**
	 * Constructor.
	 *
	 * \param $key Key
	 * \param $type Type
	 * \param $method Method
	 */
	public function __construct($key, $type, $method) {
		parent::__construct(sprintf("%s input value '%s' (%s) not found.", 
			$method, $key, $type));

		$this->key = $key;
		$this->type = $type;
		$this->method = $method;
	}

	/**
	 * Get the key.
	 *
	 * \returns Key
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * Get the type.
	 *
	 * \returns Type
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Get the method.
	 *
	 * \returns Method
	 */
	public function getMethod() {
		return $this->method;
	}
}
