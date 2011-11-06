<?php

namespace UseBB\Tests;

use UseBB\Core;
use UseBB\Utils\SchemaManagement\SystemSchema;

class CoreTest extends \PHPUnit_Framework_TestCase {
	protected $core;
	protected $plugins;
	protected $systemSchema;

	protected function setUp() {
		$this->core = new Core($GLOBALS["dbConfig"], FALSE);
		$this->systemSchema = new SystemSchema($this->core->getServiceRegistry());
		$this->systemSchema->install();
		$this->plugins = $this->core->getServiceRegistry()->get("plugins");
	}
	
	protected function tearDown() {
		
		$this->systemSchema->uninstall();
	}

	public function testErrors() {
		$this->expectOutputRegex("#Error.+Undefined#");

		$foo += 1;
	}

	public function testErrorsWithPlugin() {
		$this->expectOutputString("foo");

		$this->plugins->register("System\Context\CLI", "error", 
			function($c) { 
				if ($c->get("number") == E_NOTICE) {
					echo "foo";
					return FALSE;
				}
			});

		$foo += 1;
	}

	/**
	 * Disabled this test for PHPUnit 3.6 ("You must not expect the generic exception class").
	 * Keeping it in place also results in "Call to a member function uninstall() on a non-object"
	 * in tearDown(). It is pretty useless after all.
	 *
	 * @ expectedException \Exception
	 */
	public function _testExceptions() {
		// No way of testing this?
		// PHPUnit catches all exceptions - does not reach exception handler.
		//$this->expectOutputRegex("#Exception#");

		throw new \Exception();
	}
}
