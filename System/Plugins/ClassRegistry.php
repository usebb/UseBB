<?php

namespace UseBB\System\Plugins;

/**
 * Plugin registry for a specific class.
 *
 * Contains plugin callbacks for all hooks in a specific class, and
 * ables the class to run them at the right places.
 *
 * \attention Classes should extend PluginRunningClass and use 
 * PluginRunningClass::runPlugins() and related instead of manually
 * creating a registry and using ClassRegistry::run().
 *
 * \author Dietrich Moerman
 */
class ClassRegistry {
	private $className;
	private $registry;
	
	/**
	 * Constructor.
	 * 
	 * \param $className Class name
	 * \param $registry Registry array
	 */
	public function __construct($className, array &$registry) {
		$this->className = $className;
		$this->registry = &$registry;
	}

	/**
	 * Run all the callbacks for a specific hook.
	 *
	 * \param $hook Hook name
	 * \param $args Argument key-value pairs
	 * \param $result Default/start result
	 * \param $collect Boolean whether to collect results (instead of passing)
	 * \param $reducer Callback to reduce collected results
	 * \returns Return value(s)
	 * \exception \InvalidArgumentException When reducer is not a callback
	 */
	public function run($hook, array $args, $result, $collect, $reducer) {
		if (!isset($this->registry[$hook])) {
			return $result;
		}

		// Collect in array - one element or empty.
		if ($collect) {
			$result = (array) $result;
		}

		// Plugin context.
		$context = new Context($args, $result, $collect);

		foreach ($this->registry[$hook] as $callback) {
			$context->saveResult(call_user_func($callback, $context));
		}

		return $this->processResults($context->getResult(), $collect, $reducer);
	}

	/**
	 * Process results
	 *
	 * \param $results (Collected) results
	 * \param $collect Boolean whether results are collected
	 * \param $reducer Callback to reduce collected results
	 * \returns Return value(s)
	 * 
	 * \exception \InvalidArgumentException When reducer is not a callback
	 */
	private function processResults($results, $collect, $reducer) {
		// Do not reduce.
		if (!$collect || $reducer === NULL) {
			return $results;
		}

		if (!is_callable($reducer)) {
			throw new \InvalidArgumentException("No callable reducer passed.");
		}

		// No combining for single value.
		if (count($results) === 1) {
			return $results[0];
		}

		// array_reduce needs first element, is NULL otherwise.
		// NULL casts to FALSE and messes up boolean combining.
		$first = array_shift($results);

		return array_reduce($results, $reducer, $first);
	}
}
