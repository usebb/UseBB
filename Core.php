<?php

namespace UseBB;

use UseBB\System\Plugins\PluginRunningClass;
use UseBB\System\ServiceRegistry;

/**
 * System bootstrap and request redirector.
 *
 * This class is instantiated first and creates the service registry. It also
 * assigns error and exception handlers, and is responsible for redirecting requests.
 *
 * \author Dietrich Moerman
 */
class Core extends PluginRunningClass {
	/**
	 * Constructor.
	 *
	 * \param $envName Environment name
	 * \param $dbConfig Database configuration array
	 */
	public function __construct($envName, array $dbConfig) {
		parent::__construct(new ServiceRegistry($envName, $dbConfig));

		set_error_handler    (array($this, "handleError"));
		set_exception_handler(array($this, "handleException"));
	}

	/**
	 * Handle the current request.
	 */
	public function handleRequest($force = FALSE) {
		if (!$force && defined("USEBB_UNIT_TESTS")) {
			return;
		}
		
		$this->getService("modules")->runModules();
		$this->getService("context")->handleRequest();
		$this->getService("config")->save();
	}

	/**
	 * PHP error handler.
	 *
	 * \attention Do not call this method yourself.
	 *
	 * \param $number Error type
	 * \param $string Description
	 * \param $file File
	 * \param $line Line number
	 * \param $context Context
	 */
	public function handleError($number, $string, $file, $line, array $context) {
		$this->getService("context")
			->handleError($number, $string, $file, $line, $context);
	}

	/**
	 * Uncaught exception handler.
	 *
	 * \attention Do not call this method yourself.
	 *
	 * \param $e Exception
	 */
	public function handleException(\Exception $e) {
		$this->getService("context")->handleException($e);
	}
}
