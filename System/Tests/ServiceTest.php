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
	protected static $services;
	protected static $testClass;

	public static function setUpBeforeClass() {
		self::$services = new ServiceRegistry($GLOBALS["dbConfig"]);
		self::$testClass = new ServiceTestClass(self::$services);
	}

	public function testInstances() {
		$this->assertInstanceOf("UseBB\System\Database\\Connection", 
			self::$services->get("database"));
		$this->assertInstanceOf("UseBB\System\Plugins\Registry", 
			self::$services->get("plugins"));
		$this->assertInstanceOf("UseBB\Utils\Input\Service", 
			self::$services->get("input"));
		$this->assertInstanceOf("UseBB\Utils\Text\StringOperations", 
			self::$services->get("string"));
		$this->assertInstanceOf("UseBB\System\Navigation\\Registry", 
			self::$services->get("navigation"));
		$this->assertInstanceOf("UseBB\Utils\Config\Registry", 
			self::$services->get("config"));
		$this->assertInstanceOf("UseBB\System\Context\AbstractContext", 
			self::$services->get("context"));
		$this->assertInstanceOf("UseBB\System\Info", 
			self::$services->get("info"));
		$this->assertInstanceOf("UseBB\System\ModuleManagement\Registry", 
			self::$services->get("modules"));
		$this->assertInstanceOf("UseBB\Utils\Translation\Service", 
			self::$services->get("translation"));
		$this->assertInstanceOf("UseBB\System\Session\Session", 
			self::$services->get("session"));
		$this->assertInstanceOf("UseBB\Utils\Events\Log", 
			self::$services->get("log"));
		$this->assertInstanceOf("UseBB\Utils\Mail\Sender", 
			self::$services->get("mail"));
	}

	public function testTheClass() {
		$this->assertInstanceOf("UseBB\System\Plugins\\Registry", 
			self::$testClass->test("plugins"));
	}
}
