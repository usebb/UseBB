<?php

namespace UseBB\System\Plugins;

use UseBB\System\ServiceAccessor;
use UseBB\System\ServiceRegistry;

/**
 * Parent class for all classes supporting plugins.
 *
 * Adds methods for running plugins.
 *
 * \author Dietrich Moerman
 */
abstract class PluginRunningClass extends ServiceAccessor {
	private $className;
	private $plugins;
	
	public function __construct(ServiceRegistry $services) {
		parent::__construct($services);

		// Get the class name with namespace except "UseBB\"
		$this->className = substr(get_class($this), 6);
	}

	/**
	 * Run plugins (callbacks).
	 *
	 * The argument key-value pairs and eventual result will be passed to the 
	 * callbacks using an instance of Context. If \c $collect is \c TRUE, all 
	 * previous results will be collected in an array. The final or collected 
	 * result(s) will be the final result of this method call.
	 *
	 * When a reducer callback is passed, collected results will be reduced to 
	 * a new single value. A reducer takes as arguments the new result being 
	 * built and a new value to be added. Example:
	 *
	 * \code
	 * function($xs, $x) {
	 * 	return $xs * $x;
	 * }
	 * \endcode
	 *
	 * \note When developing core/modules, please describe hooks in the source 
	 * code in Docblocks using <tt>\\hook{...} ...</tt>
	 *
	 * \param $hook Hook name
	 * \param $args Argument key-value pairs
	 * \param $result Default/start result
	 * \param $collect Boolean whether to collect results (instead of passing)
	 * \param $reducer Callback to reduce collected results
	 * \returns Return value(s)
	 * \exception \InvalidArgumentException When reducer is not a callback
	 */ 
	 protected function runPlugins($hook, array $args = array(), $result = NULL, 
		$collect = FALSE, $reducer = NULL) {
		// Lazily load registry for this class.
		if ($this->plugins === NULL) {
			$this->plugins = $this->getService("plugins")
				->getRegistry($this->className);
		}
		
		return $this->plugins->run($hook, $args, $result, $collect, $reducer);
	}

	/**
	 * Run plugins and collect all results, calculating the boolean \c AND.
	 *
	 * Example hook ending the method execution when any result is \c FALSE:
	 * \code
	 * if ($this->runPluginsCollectAnd("foo", array("bar" => $bar)) === FALSE) {
	 * 	return;
	 * }
	 * \endcode
	 *
	 * \param $hook Hook name
	 * \param $args Argument key-value pairs
	 * \param $result Default/start result
	 * \returns Boolean return value
	 */ 
	protected function runPluginsCollectAnd($hook, array $args = array(), 
		$result = NULL) {
		return $this->runPlugins($hook, $args, $result, TRUE, 
			function($x, $y) { 
				return $x && $y; 
			});
	}

	/**
	 * Run plugins and collect all results, calculating the boolean \c OR.
	 *
	 * \param $hook Hook name
	 * \param $args Argument key-value pairs
	 * \param $result Default/start result
	 * \returns Boolean return value
	 */ 
	protected function runPluginsCollectOr($hook, array $args = array(), 
		$result = NULL) {
		return $this->runPlugins($hook, $args, $result, TRUE, 
			function($x, $y) { 
				return $x || $y; 
			});
	}

	/**
	 * Run plugins and collect all results, calculating the numeric sum.
	 *
	 * \param $hook Hook name
	 * \param $args Argument key-value pairs
	 * \param $result Default/start result
	 * \returns Numeric return value
	 */ 
	protected function runPluginsCollectNum($hook, array $args = array(), 
		$result = NULL) {
		return $this->runPlugins($hook, $args, $result, TRUE, 
			function($x, $y) { 
				return $x + $y; 
			});
	}
}
