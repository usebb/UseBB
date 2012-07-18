<?php

namespace UseBB\Tests;

use UseBB\System\ServiceRegistry;

class TestCase extends \PHPUnit_Framework_TestCase {
	protected $services;
	
	protected function newServices() {
		$this->services = new ServiceRegistry($this->getDatabaseConfig());
	}
	
	protected function getServices() {
		return $this->services;
	}
	
	protected function setService($name, $object) {
		$this->services->setServiceInstance($name, $object);
	}
	
	protected function getDatabaseConfig() {
		return $GLOBALS["dbConfig"];
	}
	
	protected function getMockWithoutConstructor($class) {
		return $this->getMockBuilder($class)
			->disableOriginalConstructor()->getMock();
	}
}
