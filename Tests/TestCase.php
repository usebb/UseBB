<?php

namespace UseBB\Tests;

use UseBB\System\ServiceRegistry;

class TestCase extends \PHPUnit_Framework_TestCase {
	protected $services;
	
	protected function newServices($envName = "testing") {
		$this->services = new ServiceRegistry($envName, $this->getDatabaseConfig());
	}
	
	protected function getServices() {
		return $this->services;
	}
	
	protected function getService($name) {
		return $this->services->get($name);
	}
	
	protected function setService($name, $object) {
		$this->services->setServiceInstance($name, $object);
	}
	
	protected function getDatabaseConfig() {
		return $GLOBALS["dbConfig"];
	}
	
	protected function getMockWithoutConstructor($class, array $methods = array()) {
		$mock = $this->getMockBuilder($class)->disableOriginalConstructor();
		
		if (count($methods) > 0) {
			$mock->setMethods($methods);
		}
		
		return $mock->getMock();
	}
	
	protected function getOutput($closure) {
		ob_start();
		$closure();
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
	
	protected function expectOutputSubstring($string) {
		$this->expectOutputRegex("#" . preg_quote($string, "#") . "#");
	}
}
