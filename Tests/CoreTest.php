<?php

namespace UseBB\Tests;

use UseBB\Core;

class CoreTest extends \PHPUnit_Framework_TestCase {
	protected $core;
	protected $context;

	protected function setUp() {
		$this->core = new Core($GLOBALS["dbConfig"]);
		$this->context = $this->getMockBuilder(
			"UseBB\System\Context\AbstractContext")
			->disableOriginalConstructor()->getMock();
		$this->core->getServiceRegistry()
			->setServiceInstance("context", $this->context);
	}
	
	public function testHandleRequest() {
		$this->context->expects($this->once())->method("handleRequest");
		
		$modules = $this->getMockBuilder(
			"UseBB\System\ModuleManagement\Registry")
			->disableOriginalConstructor()->getMock();
		$modules->expects($this->once())->method("runModules");
		$this->core->getServiceRegistry()
			->setServiceInstance("modules", $modules);
		
		$config = $this->getMockBuilder(
			"UseBB\Utils\Config\Registry")
			->disableOriginalConstructor()->getMock();
		$config->expects($this->once())->method("save");
		$this->core->getServiceRegistry()
			->setServiceInstance("config", $config);
		
		$this->core->handleRequest();
	}

	public function testErrors() {
		$this->context->expects($this->once())->method("handleError");

		$foo += 1;
	}
}
