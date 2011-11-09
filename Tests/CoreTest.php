<?php

namespace UseBB\Tests;

use UseBB\Core;

class CoreTest extends \PHPUnit_Framework_TestCase {
	protected $core;

	protected function setUp() {
		$this->core = new Core($GLOBALS["dbConfig"]);
		
	}

	public function testErrors() {
		$context = $this->getMockBuilder("UseBB\System\Context\AbstractContext")
			->disableOriginalConstructor()->getMock();
		$context->expects($this->once())->method("handleError");
		$this->core->getServiceRegistry()->setServiceInstance("context", $context);

		$foo += 1;
	}
}
