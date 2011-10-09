<?php

namespace UseBB\System\Plugins;

/**
 * Plugin context (call arguments and results).
 *
 * Contains arguments and result(s) upon running a plugin hook. In a callback, 
 * you can also set one or multiple results instead of returning one result 
 * using \c return.
 *
 * \attention As a developer, there is no need to instantiate this class 
 * yourself.
 *
 * \author Dietrich Moerman
 */
class Context {
	private $arguments = array();
	private $result;
	private $collect;

	/**
	 * Constructor
	 *
	 * \param $arguments Key-value pairs of arguments
	 * \param $result Result value
	 * \param $collect Whether results are collected
	 */
	public function __construct(array $arguments, $result, $collect) {
		$this->arguments = $arguments;
		$this->result = $result;
		$this->collect = $collect;
	}

	/**
	 * Get plugin argument by key.
	 *
	 * \param $key Argument key
	 * \param $default Optional default value (backwards compatibility with new parameters)
	 * \returns Argument value
	 */
	public function get($key, $default = NULL) {
		if (!isset($this->arguments[$key])) {
			return $default;
		}

		return $this->arguments[$key];
	}

	/**
	 * Get all arguments.
	 *
	 * \returns Original argument key-value pairs
	 */
	public function getAll() {
		return $this->arguments;
	}

	/**
	 * Whether multiple results are saved (collected).
	 *
	 * \returns Boolean
	 */
	public function collectsResults() {
		return $this->collect;
	}

	/**
	 * Get result(s).
	 *
	 * \returns Result(s)
	 */
	public function getResult() {
		return $this->result;
	}

	/**
	 * Get result(s).
	 *
	 * \returns Result(s)
	 */
	public function getResults() {
		return $this->result;
	}

	/**
	 * Save result.
	 *
	 * \param $result Result value
	 */
	public function saveResult($result) {
		if ($result === NULL) {
			return;
		}

		$this->result = $this->collect 
			? array_merge($this->result, array($result)) 
			: $result;
	}

	/**
	 * Save multiple results.
	 *
	 * \param $results Array with results
	 */
	public function saveMultipleResults(array $results) {
		if (count($results) === 0) {
			return;
		}

		// Collecting disabled - this method is actually useless.
		if (!$this->collect) {
			$this->result = $results[0];

			return;
		}

		$this->result = array_merge($this->result, $results);
	}
}
