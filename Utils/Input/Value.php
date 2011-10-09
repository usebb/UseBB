<?php

namespace UseBB\Utils\Input;

/**
 * %Input value class.
 *
 * Represents a single input value from one of the input sources.
 * Includes ability to typecast and validate values.
 *
 * \author Dietrich Moerman
 */
class Value {
	private $key;
	private $method;
	private $validation;
	private $value;
	private $type = "string";

	/**
	 * Constructor.
	 *
	 * \attention Use Service::key() instead.
	 *
	 * \param $key %Input key
	 * \param $method Method (GET, POST or COOKIE)
	 */
	public function __construct($key, $method) {
		$this->key = $key;
		$this->method = $method;
	}

	/**
	 * Set the value's type.
	 *
	 * \param $type Any of PHP's builtin types (e.g. <tt>int</tt>, <tt>bool</tt>, etc)
	 * \returns This instance
	 */
	public function type($type) {
		$this->type = $type;
		$this->value = NULL;

		return $this;
	}

	/**
	 * Set the value's validation.
	 *
	 * Example <a href="http://php.net/manual/en/functions.anonymous.php">anonymous function</a>
	 * (recommended):
	 * \code
	 * function($k, $v) {
	 * 	return $v > 0;
	 * };
	 * \endcode
	 *
	 * \param $validation Callback (function name, anonymous function, etc)
	 * \returns This instance
	 * 
	 * \exception \InvalidArgumentException If validation is not a callback
	 */
	public function validate($validation) {
		if (!is_callable($validation)) {
			throw new \InvalidArgumentException("No callback passed.");
		}
		
		$this->validation = $validation;
		$this->value = NULL;

		return $this;
	}

	/**
	 * Check if the key has a suitable value set.
	 *
	 * This will typecast the value and apply validation first.
	 *
	 * \returns Boolean
	 */
	public function hasValue() {
		try {
			$this->findValue();
		} catch (\Exception $e) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Get the suitable value.
	 *
	 * This will typecast the value and apply validation first.
	 * If a default is given, no exceptions are thrown but the default is returned.
	 *
	 * \param $default Default value (optional)
	 * \returns %Value
	 * 
	 * \exception \InvalidArgumentException If method does not exist
	 * \exception NotFoundException If key was not found
	 * \exception WrongTypeException If cannot cast value to type
	 * \exception ValueInvalidException If value is invalid
	 */
	public function getValue($default = NULL) {
		$value = NULL;
		
		try {
			$value = $this->findValue();
		} catch (\Exception $e) {
			$exc = $e;
		}

		if ($value === NULL) {
			if ($default !== NULL) {
				return $default;
			} else {
				throw $exc;
			}
		}

		return $value;
	}

	/**
	 * Find and set a value for the key.
	 *
	 * \exception \InvalidArgumentException If method does not exist
	 * \exception InputValueNotFoundException If key was not found
	 */
	private function findValue() {
		if ($this->value !== NULL) {
			return $this->value;
		}

		switch ($this->method) {
			case "GET":
				$source = &$_GET;
				break;
			case "POST":
				$source = &$_POST;
				break;
			case "COOKIE":
				$source = &$_COOKIE;
				break;
			default:
				throw new \InvalidArgumentException(
					sprintf("No input method '%s' exists.", $this->method));
		}

		if (!isset($source[$this->key])) {
			throw new NotFoundException($this->key, $this->type, $this->method);
		}

		$this->value = $this->processValue($source[$this->key]);

		return $this->value;
	}

	/**
	 * Process the value to be of the right type and valid.
	 *
	 * \param $value Value
	 * \returns Valid value
	 * 
	 * \exception InputValueWrongTypeException If cannot cast value to type
	 * \exception InputValueInvalidException If value is invalid
	 */
	private function processValue($value) {
		if ($this->type != "string") {
			if (!settype($value, $this->type)) {
				throw new WrongTypeException($this->key, $this->type, 
					$this->method);
			}
		}

		if ($this->validation !== NULL) {
			$validation = $this->validation;

			if (!$validation($this->key, $value)) {
				throw new ValueInvalidException($this->key, $this->type, 
					$this->method);
			}
		}

		return $value;
	}
}
