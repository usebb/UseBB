<?php

namespace UseBB\Utils\PrimitiveFunctions\Tests;

use UseBB\Tests\TestCase;
use UseBB\Utils\PrimitiveFunctions\Service;

class PrimitivesTest extends TestCase {	
	public function testPrimitives() {
		$service = new Service();
		// array_merge is not really a function that should be called through
		// the service, since it doesn't have side effects.
		$this->assertEquals(array("foo", "bar"),
			$service->array_merge(array("foo"), array("bar")));
	}
	
	public function testMock() {
		// Be sure to pass the mocked function in the array.
		$service = $this->getMock("UseBB\Utils\PrimitiveFunctions\Service",
			array("foo"));
		$service->expects($this->any())->method("foo")
			->will($this->returnValue("barbaz"));
		$this->assertEquals("barbaz", $service->foo());
	}
}
