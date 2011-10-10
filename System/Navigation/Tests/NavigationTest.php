<?php

namespace UseBB\System\Navigation\Tests;

use UseBB\System\AbstractController;
use UseBB\System\ServiceRegistry;

class TestController extends AbstractController {
	public function handleRequest() {
		echo "Handling request.";
	}
}

class TestController2 extends AbstractController {
	public function handleFooRequest() {
		echo "Handling named request.";
	}
}

class NavigationTest extends \PHPUnit_Extensions_OutputTestCase {
	protected $navigation;
	
	protected function setUp() {
		$services = new ServiceRegistry($GLOBALS["dbConfig"]);
		$services->setForcedContext("UseBB\System\Context\HTTP");
		$this->navigation = $services->get("navigation");
	}
	
	public function testRegistry() {
		$this->expectOutputString("Handling request.");
		
		$this->navigation->register("foo", 
			"UseBB\System\Navigation\Tests\TestController");
		$this->navigation->register("some", "unexisting");
		
		// Keep the "." - it is just a value in the current request.
		$this->navigation->handleRequest(array("test" => ".", "foo" => "bar"));
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
		
		$this->assertEquals("./?do=some&id=5&foo=bar%C3%B9", 
			$this->navigation->getLink("SomeController", NULL, 
			array("id" => 5)));
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
		
		$this->assertEquals("./?do=some&act=edit&id=5", 
			$this->navigation->getLink("SomeController", "edit", 
			array("id" => 5)));
	}
}
