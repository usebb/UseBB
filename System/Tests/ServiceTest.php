<?php

namespace UseBB\System\Tests;

use UseBB\Tests\TestCase;
use UseBB\System\ServiceRegistry;
use UseBB\System\ServiceAccessor;
use UseBB\System\ServiceNotFoundException;

class ServiceTestClass extends ServiceAccessor {
	public function test($name) {
		return $this->getService($name);
	}
}

class ServiceTest extends TestCase {
	public function setUp() {
		$this->newServices();
	}
	
	public function instancesProvider() {
		return array(
			array("database",    "UseBB\System\Database\Connection"),
			array("plugins",     "UseBB\System\Plugins\Registry"),
			array("input",       "UseBB\Utils\Input\Service"),
			array("string",      "UseBB\Utils\Text\StringOperations"),
			array("navigation",  "UseBB\System\Navigation\Registry"),
			array("config",      "UseBB\Utils\Config\Registry"),
			array("context",     "UseBB\System\Context\AbstractContext"),
			array("info",        "UseBB\System\Info"),
			array("modules",     "UseBB\System\ModuleManagement\Registry"),
			array("translation", "UseBB\Utils\Translation\Service"),
			array("session",     "UseBB\System\Session\Session"),
			array("log",         "UseBB\Utils\Events\Log"),
			array("mail",        "UseBB\Utils\Mail\Sender"),
			array("primitives",  "UseBB\Utils\PrimitiveFunctions\Service"),
		);
	}

	/**
	 * @dataProvider instancesProvider
	 */
	public function testInstances($name, $class) {
		$this->assertInstanceOf($class, $this->getServices()->get($name));
	}
	
	/**
	 * @expectedException UseBB\System\ServiceNotFoundException
	 */
	public function testUnexisting() {
		$this->getServices()->get("foobar");
	}

	public function testTheClass() {
		$testClass = new ServiceTestClass($this->getServices());
		$this->assertInstanceOf("UseBB\System\Plugins\\Registry", 
			$testClass->test("plugins"));
	}
	
	public function testSetInstances() {
		$this->getServices()->setServiceInstance("foo", $this);
		$this->assertEquals($this, $this->getServices()->get("foo"));
	}
	
	public function testContextFactoryCLI() {
		$_SERVER["SHELL"] = "foo";
		$this->assertInstanceOf("UseBB\System\Context\CLI", 
			$this->getServices()->get("context"));
	}
	
	public function testContextFactoryHTTP() {
		unset($_SERVER["SHELL"]);
		$_SERVER["REQUEST_METHOD"] = "foo";
		$this->assertInstanceOf("UseBB\System\Context\HTTP", 
			$this->getServices()->get("context"));
	}
	
	/**
	 * @expectedException UseBB\System\UseBBException
	 * @expectedExceptionMessage No suitable context found.
	 */
	public function testContextFactoryError() {
		unset($_SERVER["SHELL"]);
		$this->getServices()->get("context");
	}
	
	/**
	 * @expectedException UseBB\System\UseBBException
	 * @expectedExceptionMessage Missing database configuration.
	 */
	public function testWrongDbConfig() {
		new ServiceRegistry("production", array());
	}
	
	public function testEnvironmentNames() {
		$this->getServices()->setEnvironmentName("production", FALSE);
		$this->assertEquals("production", 
			$this->getServices()->getEnvironmentName());
		
		$this->getServices()->setEnvironmentName("development", FALSE);
		$this->assertEquals("development", 
			$this->getServices()->getEnvironmentName());
		
		$this->getServices()->setEnvironmentName("testing", FALSE);
		$this->assertEquals("testing", 
			$this->getServices()->getEnvironmentName());
		
		$context = $this->getMockWithoutConstructor(
			"UseBB\System\Context\AbstractContext");
		$context->expects($this->once())->method("getForcedEnvironmentName")
			->will($this->returnValue("development"));
		$this->setService("context", $context);
		
		$this->getServices()->setEnvironmentName("production", FALSE);
		$this->assertEquals("development", 
			$this->getServices()->getEnvironmentName());
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Unknown environment name 'foobar'.
	 */
	public function testWrongEnvironmentName() {
		$this->getServices()->setEnvironmentName("foobar", FALSE);
	}
}
