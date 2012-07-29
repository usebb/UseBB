<?php

namespace UseBB\System\Navigation\Tests;

use UseBB\Tests\TestCase;
use UseBB\System\AbstractController;
use UseBB\System\Navigation\DuplicateRequestException;
use UseBB\System\Navigation\NoRegisteredRequestException;

class TestController extends AbstractController {
	public function doIt() {
		echo "Handling request.";
	}
}

class TestController2 extends AbstractController {
	public function doFoo() {
		echo "Handling named request.";
	}
}

class NavigationTest extends TestCase {
	protected $context;
	protected $navigation;
	
	protected function setUp() {
		$this->newServices();
		$this->context = $this->getMockWithoutConstructor(
			"UseBB\System\Context\AbstractContext");
		$this->setService("context", $this->context);
		$this->navigation = $this->getService("navigation");
	}
	
	public function testRegistry() {
		$this->expectOutputString("Handling request.");
		
		$this->navigation->register("foo", 
			"UseBB\System\Navigation\Tests\TestController");
		$this->navigation->register("some", "unexisting");
		
		// Keep the "." - it is just a value in the current request.
		$this->navigation->handleRequest(array("test" => ".", "foo" => "bar"));
	}
	
	/**
	 * @expectedException UseBB\System\Navigation\NoControllerFoundException
	 */
	public function testRegistry2() {
		$this->expectOutputString("Handling request.");
		
		$this->navigation->register(array("first" => 1, "second" => 2), 
			"UseBB\System\Navigation\Tests\TestController");
		$this->navigation->handleRequest(array("first" => 1, "second" => 2));
		
		$this->navigation->register(array("foo" => "a", "bar" => "b"), 
			"UseBB\System\Navigation\Tests\TestController");
		$this->navigation->handleRequest(array("foo" => "d", "bar" => "b"));
	}
	
	public function testSubRequestMatch() {
		$this->expectOutputString("Handling request.");
		
		$this->navigation->register(array("foo", "test"), 
			"UseBB\System\Navigation\Tests\TestController");
		$this->navigation->register(array("test"), "unexisting");
		$this->navigation->handleRequest(array("test" => ".", "foo" => "."));
	}
	
	public function testSubRequestMatch2() {
		$this->expectOutputString("Handling request.");
		
		// Different order added as previous.
		$this->navigation->register(array("test"), "unexisting");
		$this->navigation->register(array("foo", "test"), 
			"UseBB\System\Navigation\Tests\TestController");
		$this->navigation->handleRequest(array("test" => ".", "foo" => "."));
	}
	
	/**
	 * @expectedException UseBB\System\Navigation\NoControllerFoundException
	 */
	public function testException() {
		$this->navigation->register("foo", "unexisting");
		$this->navigation->register("bar", "unexisting");
		$this->navigation->handleRequest(array("hm" => ".", "some" => "."));
	}
	
	/**
	 * @expectedException UseBB\System\Navigation\NoControllerFoundException
	 */
	public function testException2() {
		$this->navigation->register(array("hm", "foo", "some"), "unexisting");
		$this->navigation->handleRequest(array("hm" => ".", "some" => "."));
	}
	
	/**
	 * @expectedException UseBB\System\Navigation\DuplicateRequestException
	 */
	public function testDuplicate() {
		$this->navigation->register(array("foo", "bar" => "baz"), "unexisting");
		$this->navigation->register(array("bar" => "baz", "foo"), "second");
	}
	
	/**
	 * @expectedException UseBB\System\Navigation\DuplicateRequestException
	 */
	public function testDuplicate2() {
		$this->navigation->register(array("foo" => "a", "bar" => "b"), 
			"unexisting");
		$this->navigation->register(array("bar" => "b", "foo" => "a"), 
			"second");
	}
	
	/**
	 * @expectedException UseBB\System\Navigation\DuplicateRequestException
	 */
	public function testDuplicate3() {
		$this->navigation->register(array("foo", "bar"), 
			"unexisting");
		$this->navigation->register(array("bar", "foo"), 
			"second");
	}

	/**
	 * FIXME
	 * @expectedException UseBB\System\Navigation\DuplicateRequestException
	 */
	public function __testDuplicateWithParams() {
		$this->navigation->register(array("foo" => "@foo", "bar"), 
			"unexisting");
		$this->navigation->register(array("foo" => "@baz", "bar"), 
			"same");
	}
	
	public function testNamed() {
		$this->expectOutputString("Handling named request.");
		
		$this->navigation->register("foo", 
			"UseBB\System\Navigation\Tests\TestController2", "foo");
		$this->navigation->handleRequest(array("foo" => "bar"));
	}
	
	public function testParameters() {
		$this->expectOutputString("Handling request.");
		
		$this->navigation->register(array(
			"test", 
			"foo" => "baz"
		), "unexisting");
		$this->navigation->register(array(
			"test", 
			"foo" => "@anything"
		), "UseBB\System\Navigation\Tests\TestController");
		
		$this->navigation->handleRequest(array(
			"test" => ".", 
			"foo" => "bar"
		));
	}
	
	public function testLink() {
		$this->navigation->register(array(
			"do" => "some",
			"id" => "@id",
			"foo" => "barù"
		), "SomeController");
		
		$expectedArgs = array(
			"do" => "some",
			"id" => 5,
			"foo" => "barù"
		);
		$this->context->expects($this->once())->method("generateLink")->with(
			$this->equalTo($expectedArgs));
		$this->navigation->getLink("SomeController", NULL, array("id" => 5));
	}
	
	public function testLinkWithCName() {
		$this->navigation->register(array(
			"do" => "some",
			"id" => "@id"
		), "SomeController");
		$this->navigation->register(array(
			"do" => "some",
			"act" => "edit",
			"id" => "@id"
		), "SomeController", "edit");
		
		$expectedArgs = array(
			"do" => "some",
			"act" => "edit",
			"id" => 5
		);
		$this->context->expects($this->once())->method("generateLink")->with(
			$this->equalTo($expectedArgs));
		$this->navigation->getLink("SomeController", "edit", array("id" => 5));
	}
	
	/**
	 * @expectedException UseBB\System\Navigation\NoRegisteredRequestException
	 */
	public function testLinkUnexisting() {
		$this->navigation->getLink("SomeController", NULL, array("id" => 5));
	}
	
	public function testExceptions() {
		$r = array("foo" => "bar", "bar", "baz" => 1);
		$e = new DuplicateRequestException($r);
		
		$this->assertEquals("More than one handler is being set for request " .
			"'(foo => bar, bar, baz => 1)'.", $e->getMessage());
		$this->assertEquals($r, $e->getRequest());
		
		$e = new NoRegisteredRequestException("Foo", "bar");
		$this->assertEquals("Foo", $e->getController());
		$this->assertEquals("bar", $e->getName());
	}
}
