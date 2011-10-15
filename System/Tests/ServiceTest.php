<?php

namespace UseBB\System\Tests;

use UseBB\System\ServiceAccessor;
use UseBB\System\ServiceRegistry;

class ServiceTestClass extends ServiceAccessor {
	public function test($name) {
		return $this->getService($name);
	}
}

class ServiceTest extends \PHPUnit_Framework_TestCase {
	protected $services;

	public function setUp() {
		$this->services = new ServiceRegistry($GLOBALS["dbConfig"]);
	}

	public function testInstances() {
		$this->assertInstanceOf("UseBB\System\Database\\Connection", 
			$this->services->get("database"));
		$this->assertInstanceOf("UseBB\System\Plugins\Registry", 
			$this->services->get("plugins"));
		$this->assertInstanceOf("UseBB\Utils\Input\Service", 
			$this->services->get("input"));
		$this->assertInstanceOf("UseBB\Utils\Text\StringOperations", 
			$this->services->get("string"));
		$this->assertInstanceOf("UseBB\System\Navigation\\Registry", 
			$this->services->get("navigation"));
		$this->assertInstanceOf("UseBB\Utils\Config\Registry", 
			$this->services->get("config"));
		$this->assertInstanceOf("UseBB\System\Context\AbstractContext", 
			$this->services->get("context"));
		$this->assertInstanceOf("UseBB\System\Info", 
			$this->services->get("info"));
		$this->assertInstanceOf("UseBB\System\ModuleManagement\Registry", 
			$this->services->get("modules"));
		$this->assertInstanceOf("UseBB\Utils\Translation\Service", 
			$this->services->get("translation"));
		$this->assertInstanceOf("UseBB\System\Session\Session", 
			$this->services->get("session"));
		$this->assertInstanceOf("UseBB\Utils\Events\Log", 
			$this->services->get("log"));
		$this->assertInstanceOf("UseBB\Utils\Mail\Sender", 
			$this->services->get("mail"));
	}

	public function testTheClass() {
		$testClass = new ServiceTestClass($this->services);
		$this->assertInstanceOf("UseBB\System\Plugins\\Registry", 
			$testClass->test("plugins"));
	}
	
	public function testSetInstances() {
		$this->services->setServiceInstance("foo", $this);
		$this->assertEquals($this, $this->services->get("foo"));
	}
}
