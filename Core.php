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
	 * \param $dbConfig Database configuration array
	 * \param $handleRequest Whether to handle the request
	 */
	public function __construct(array $dbConfig, $handleRequest = TRUE) {
		parent::__construct(new ServiceRegistry($dbConfig));

		$this->setHandlers();
		
		if (!$handleRequest) {
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

	/**
	 * Set error and exception handlers.
	 */
	private function setHandlers() {
		set_error_handler    (array($this, "handleError"));
		set_exception_handler(array($this, "handleException"));
	}
}
