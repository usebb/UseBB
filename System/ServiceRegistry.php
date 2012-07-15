<?php

namespace UseBB\System;

use UseBB\System\Database\Connection as Database;
use UseBB\System\Plugins\Registry as Plugins;
use UseBB\Utils\Input\Service as Input;
use UseBB\Utils\Text\StringOperations;
use UseBB\System\Navigation\Registry as Navigation;
use UseBB\Utils\Config\Registry as Config;
use UseBB\Utils\Config\Defaults as ConfigDefaults;
use UseBB\Utils\Config\AutoConfigured as ConfigAuto;
use UseBB\System\Context\CLI;
use UseBB\System\Context\HTTP;
use UseBB\System\ModuleManagement\Registry as Modules;
use UseBB\Utils\Translation\Service as Translation;
use UseBB\System\Session\Session;
use UseBB\Utils\Events\Log;
use UseBB\Utils\Mail\Sender as Mail;

/**
 * Services registry.
 *
 * Instantiates and keeps all instances of services.
 *
 * \author Dietrich Moerman
 */
class ServiceRegistry {
	private $services = array();
	private $envName;
	private $dbConfig;

	/**
	 * Constructor.
	 *
	 * \param $envName Environment name
	 * \param $dbConfig Database configuration array
	 */
	public function __construct($envName, array $dbConfig) {
		if (defined("USEBB_UNIT_TESTS")) {
			$envName = "testing";
		} elseif (!in_array($envName, array("production", "development"))) {
			throw new \InvalidArgumentException("Unknown environment name.");
		}
		
		if (!isset($dbConfig[$envName]) || !is_array($dbConfig[$envName])) {
			throw new UseBBException("Missing database configuration.");
		}
		
		$this->envName = $envName;
		$this->dbConfig = $dbConfig[$envName];
	}
	
	/**
	 * Get the environment name.
	 * 
	 * \returns Environment name
	 */
	public function getEnvironmentName() {
		return $this->envName;
	}
	
	/**
	 * Explicitly set a service to a given instance.
	 * 
	 * This method is often used in unit tests and can be useful for
	 * mocking objects.
	 * 
	 * \param $name Service name
	 * \param $instance Instance
	 */
	public function setServiceInstance($name, $instance) {
		$this->services[$name] = $instance;
	}

	/**
	 * Get a service.
	 *
	 * \param $name Service name
	 * \returns Service instance
	 * 
	 * \exception ServiceNotFoundException When service not found
	 */
	public function get($name) {
		if (!isset($this->services[$name])) {
			switch ($name) {
				case "database":
					$service = new Database($this->dbConfig);
					break;
				case "plugins":
					$service = new Plugins();
					break;
				case "input":
					$service = new Input();
					break;
				case "string":
					$service = new StringOperations();
					break;
				case "navigation":
					$service = new Navigation($this);
					break;
				case "config":
					$service = new Config($this, 
						new ConfigDefaults($this, 
							new ConfigAuto($this)));
					break;
				case "context":
					$service = $this->contextFactory();
					break;
				case "info":
					$service = new Info($this);
					break;
				case "modules":
					$service = new Modules($this);
					break;
				case "translation":
					$service = new Translation($this);
					break;
				case "session":
					$service = new Session($this);
					break;
				case "log":
					$service = new Log($this);
					break;
				case "mail":
					$service = new Mail($this);
					break;
				default:
					throw new ServiceNotFoundException($name);
			}

			$this->services[$name] = $service;
		}

		return $this->services[$name];
	}
	
	/**
	 * Get a suitable instance of a context.
	 * 
	 * \returns AbstractContext instance
	 * 
	 * \exception UseBBException When no suitable context found
	 */
	private function contextFactory() {
		if (!empty($_SERVER["SHELL"])) {
			return new CLI($this);
		}
		
		if (!empty($_SERVER["REQUEST_METHOD"])) {
			return new HTTP($this);
		}
		
		throw new UseBBException("No suitable context found.");
	}
}
