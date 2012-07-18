<?php

namespace UseBB\Tests;

use UseBB\Core;
use UseBB\System\UseBBException;

class CoreTest extends TestCase {
	protected $core;
	protected $context;
	protected $modules;
	protected $config;

	protected function setUp() {
		$this->core = new Core("development", $this->getDatabaseConfig());
		$this->context = $this->getMockWithoutConstructor(
			"UseBB\System\Context\AbstractContext");
		$this->core->getServiceRegistry()
			->setServiceInstance("context", $this->context);
	}
	
	private function setUpForRequest() {
		$this->modules = $this->getMockWithoutConstructor(
			"UseBB\System\ModuleManagement\Registry");
		$this->core->getServiceRegistry()
			->setServiceInstance("modules", $this->modules);
		
		$this->config = $this->getMockWithoutConstructor(
			"UseBB\Utils\Config\Registry");
		$this->core->getServiceRegistry()
			->setServiceInstance("config", $this->config);
	}
	
	public function testHandleRequestTesting() {
		$this->setUpForRequest();
		
		// Call without skipping env check - testing env prohibits handling requests
		$this->modules->expects($this->never())->method("runModules");
		$this->context->expects($this->never())->method("handleRequest");
		$this->config->expects($this->never())->method("save");
		$this->core->handleRequest();
	}
	
	public function testHandleRequestOther() {
		$this->setUpForRequest();
		
		// Call with skipping env check
		$this->modules->expects($this->once())->method("runModules");
		$this->context->expects($this->once())->method("handleRequest");
		$this->config->expects($this->once())->method("save");
		$this->core->handleRequest(FALSE);
	}
	
	public function testErrors() {
		$this->context->expects($this->once())->method("handleError")->with(
			$this->equalTo(E_NOTICE),
			$this->matchesRegularExpression("#undefined.*foo#i"),
			$this->equalTo(__FILE__),
			$this->greaterThan(__LINE__),
			$this->anything());
		$foo += 1;
	}
	
	public function testExceptions() {
		// Cannot throw an exception here myself, so doing manual call.
		$e = new UseBBException("bar");
		$this->context->expects($this->once())->method("handleException")->with(
			$this->equalTo($e));
		$this->core->handleException($e);
	}
}
