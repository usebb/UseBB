<?php

namespace UseBB\Tests;

use UseBB\Core;

class CoreTest extends \PHPUnit_Framework_TestCase {
	protected $core;

	protected function setUp() {
		$this->core = new Core($GLOBALS["dbConfig"]);
		
	}

	public function testErrors() {
		$context = $this->getMock("UseBB\System\Context\AbstractContext");
		$context->expects($this->once())->method("handleError");
		$this->core->getServiceRegistry()->setServiceInstance("context", $context);

		$foo += 1;
	}
}
