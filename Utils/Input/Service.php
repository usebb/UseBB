<?php

namespace UseBB\Utils\Input;

/**
 * Data input service.
 *
 * Provides access to input values.
 *
 * Example of getting an input value (\c $input is an Input instance):
 * \code
 * $id = $input->key("id")->type("int")->validate(
 * 	function($k, $v) {
 * 		return $v > 0;
 * 	});
 * if ($id->hasValue()) {
 * 	$id = $id->getValue();
 * } else {
 * 	// ...
 * }
 * \endcode
 *
 * \author Dietrich Moerman
 */
class Service {
	private $method = "GET";

	/**
	 * Get an input value by key.
	 *
	 * \param $key Key
	 * \param $method Method (GET, POST or COOKIE). When NULL, default method will be used.
	 * \returns Value instance
	 */
	public function key($key, $method = NULL) {
		return new Value($key, $method !== NULL ? $method : $this->method);
	}

	/**
	 * Set default method.
	 *
	 * \param $method New default method
	 */
	public function setMethod($method) {
		$this->method = $method;
	}
}
