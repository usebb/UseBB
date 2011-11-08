<?php

namespace UseBB\Utils\Events;

use UseBB\System\ServiceAccessor;

/**
 * %Event log.
 * 
 * Logs events in the system, with useful information.
 * 
 * \author Dietrich Moerman
 */
class Log extends ServiceAccessor {
	/**
	 * %Log an event.
	 * 
	 * Logging events is controlled by the settings \c logMode, 
	 * \c logExcludedModules and \c logIncludedModules in \c system.
	 * 
	 * \param $level Level (see Event class constants)
	 * \param $module Module name
	 * \param $className Class name
	 * \param $message Message
	 * \param $args Message arguments
	 * \param $objectType Object type
	 * \param $objectId Object ID
	 * \returns Event instance or \c FALSE
	 */
	public function logEvent($level, $module, $className, $message, 
		array $args = array(), $objectType = "", $objectId = 0) {
		if (!$this->systemIsInstalled()) {
			return;
		}

		$config = $this->getService("config")->forModule("system");
		$mode = $config->get("logMode");
		$doLog = FALSE;
		
		switch ($mode) {
			case "enabled":
				$excluded = $config->get("logExcludedModules");
				$doLog = !in_array($module, $excluded);
				break;
			case "enabledForSome":
				$included = $config->get("logIncludedModules");
				$doLog = in_array($module, $included);
				break;
			case "disabled":
			default:
				$doLog = FALSE;
		}
		
		if (!$doLog) {
			return FALSE;
		}
		
		$event = new Event($this->getServiceRegistry(), $level, $module, 
			$className, $message, $args, $objectType, $objectId);
		$event->save();
		
		return $event;
	}
}
