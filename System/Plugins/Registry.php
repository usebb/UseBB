<?php

namespace UseBB\System\Plugins;

/**
 * Global plugin callback registry.
 *
 * Registers all callbacks for all classes and hooks.
 *
 * \author Dietrich Moerman
 */
class Registry {
	/**
	 * Normal priority for callbacks.
	 */
	const PRIORITY_NORMAL = 1;
	/**
	 * High priority for callbacks.
	 */
	const PRIORITY_HIGH = 2;

	private $registry = array();

	/**
	 * Register a callback (plugin) for a specific class and hook.
	 *
	 * The callback will be passed an instance of Context, upon where keyed
	 * arguments and the current result value or value collecting array can be 
	 * retrieved.
	 *
	 * Depending on the caller's implementation, its behaviour may differ 
	 * depending on the data returned. See 
	 * <a href="hooks.html">hook documentation</a> or developer's manual for 
	 * specific or more info.
	 *
	 * Example plugin disabling errors for PHP notices:
	 *
	 * \code
	 * $plugins->register("Core", "error", 
	 * 	function($c) { 
	 * 		if ($c->get("number") == E_NOTICE) {
	 * 			// Core.error hook stops execution 
	 *			// when any callback returns FALSE.
	 * 			return FALSE;
	 * 		}
	 * 	});
	 * \endcode
	 *
	 * Instead of using \c return, it is also possible to use the result 
	 * setters of Context. It is also the only way of returning multiple 
	 * results to collect (using Context::saveMultipleResults).
	 *
	 * Callbacks with high priority will be executed before all normal ones.
	 *
	 * \param $className Class name
	 * \param $hook Hook name
	 * \param $callback Callback to execute
	 * \param $priority \c PRIORITY_NORMAL or \c PRIORITY_HIGH
	 * 
	 * \exception \InvalidArgumentException When not passed a callback
	 * \exception \InvalidArgumentException When priority not understood
	 */
	public function register($className, $hook, $callback, 
		$priority = self::PRIORITY_NORMAL) {
		if (!is_callable($callback)) {
			throw new \InvalidArgumentException("No callback passed.");
		}
		
		if (!isset($this->registry[$className])) {
			$this->registry[$className] = array();
		}

		if (!isset($this->registry[$className][$hook])) {
			$this->registry[$className][$hook] = array();
		}

		$list = &$this->registry[$className][$hook];

		switch ($priority) {
		case self::PRIORITY_NORMAL:
			$list[] = $callback;
			break;
		case self::PRIORITY_HIGH:
			array_unshift($list, $callback);
			break;
		default:
			throw new \InvalidArgumentException("Given priority not understood.");
		}
	}

	/**
	 * Get the registry for a specific class.
	 *
	 * \attention Classes should extend PluginRunningClass and use 
	 * PluginRunningClass::runPlugins() and related instead of manually
	 * creating a registry and using ClassRegistry::run().
	 *
	 * \param $className Class name
	 * \returns ClassRegistry instance
	 */
	public function getRegistry($className) {
		if (!isset($this->registry[$className])) {
			$this->registry[$className] = array();
		}

		return new ClassRegistry($className, $this->registry[$className]);
	}
}
