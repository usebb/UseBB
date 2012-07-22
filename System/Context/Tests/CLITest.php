<?php

namespace UseBB\System\Context\Tests;

use UseBB\Tests\TestCase;
use UseBB\System\Context\CLI;

class CLITest extends TestCase {
	protected $context;
	
	protected function setUp() {
		$this->newServices();
		$this->context = new CLI($this->getServices());
	}
	
	public function testName() {
		$this->assertEquals("CLI", $this->context->getName());
	}
	
	public function testHandleRequest() {
		$this->expectOutputSubstring(
			"UseBB version " . $this->getService("info")->getUseBBVersion());
		
		$this->context->handleRequest();
	}
	
	public function testLanguage() {
		$this->assertEquals("en", $this->context->getLanguage(array()));
	}
	
	public function testEscape() {
		$this->assertEquals("<a>", $this->context->escapeString("<a>"));
	}
	
	public function testStringArgs() {
		$this->assertEquals("Hello %name.",
			$this->context->applyArgumentsToString(
			"Hello %name.", array(
				"%name" => "you>"
			)));
	}
	
	public function testGenerateLink() {
		$this->assertEquals("", $this->context->generateLink(array()));
	}
	
	public function testHandleError() {
		$this->expectOutputRegex("#Some error\..*foo\.php.*5#");
		
		$this->context->handleError(E_NOTICE, "Some error.", 
			"foo.php", 5, array());
	}
	
	public function testHandleException() {
		$this->expectOutputRegex("#Exception.*foo#s");
		
		$this->context->handleException(new \Exception("foo"));
	}
	
	public function testForcedEnvironment() {
		$this->assertNull($this->context->getForcedEnvironmentName());
	}
}
