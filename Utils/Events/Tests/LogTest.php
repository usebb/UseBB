<?php

namespace UseBB\Utils\Events\Tests;

use UseBB\System\ServiceAccessor;
use UseBB\System\ServiceRegistry;
use UseBB\Utils\SchemaManagement\SystemSchema;
use UseBB\Utils\Events\Event;

// TODO: test object type and ID when ORM is done.

class TestClass extends ServiceAccessor {
	public function testLog() {
		return $this->log(Event::LEVEL_ERROR, "This is a @test.", 
			array("@test" => "test event"));
	}
}

class LogTest extends \PHPUnit_Framework_TestCase {
	private $services;
	private $sSchema;
	private $log;
	private $config;
	
	protected function setUp() {
		$this->services = new ServiceRegistry($GLOBALS["dbConfig"]);
		$this->services->setServiceInstance("context", 
			new \UseBB\System\Context\HTTP($this->services));
		$this->sSchema = new SystemSchema($this->services);
		$this->sSchema->install();
		$this->log = $this->services->get("log");
		$this->config = $this->services->get("config")->forModule("system");
	}
	
	protected function tearDown() {
		$this->sSchema->uninstall();
	}
	
	public function testLog() {
		$this->assertEquals("enabled", $this->config->get("logMode"));
		$this->assertEquals(array(), $this->config->get("logExcludedModules"));
		
		$event = $this->log->logEvent(Event::LEVEL_ERROR, "system", "Foo", 
			"This is a @test.", array("@test" => "test event"));
		
		$this->assertInstanceOf("UseBB\Utils\Events\Event", $event);
		$this->assertEquals(Event::LEVEL_ERROR, $event->getLevel());
		$this->assertEquals("system", $event->getModule());
		$this->assertEquals("Foo", $event->getClassName());
		$this->assertEquals("This is a @test.", $event->getRawMessage());
		$this->assertEquals("This is a test event.", $event->getMessage());
		
		// TODO: redundant with ORM tests
		$query = $this->services->get("database")->newQuery();
		$stmt = $query->select("e.id, e.message")->from("events", "e")
			->where("e.class = :class")->setParameter(":class", "Foo")
			->execute();
		$this->assertEquals(1, $stmt->rowCount());
		
		$result = $stmt->fetch();
		$this->assertEquals(1, $result["id"]);
		$this->assertEquals("This is a @test.", $result["message"]);
	}
	
	public function testClass() {
		$this->assertEquals("enabled", $this->config->get("logMode"));
		$this->assertEquals(array(), $this->config->get("logExcludedModules"));
		
		$class = new TestClass($this->services);
		$event = $class->testLog();
		
		$this->assertInstanceOf("UseBB\Utils\Events\Event", $event);
		$this->assertEquals(Event::LEVEL_ERROR, $event->getLevel());
		$this->assertEquals("system", $event->getModule());
		$this->assertEquals("Utils\Events\Tests\TestClass", 
			$event->getClassName());
		$this->assertEquals("This is a @test.", $event->getRawMessage());
		$this->assertEquals("This is a test event.", $event->getMessage());
		
		// TODO: redundant with ORM tests
		$query = $this->services->get("database")->newQuery();
		$stmt = $query->select("e.id, e.message")->from("events", "e")
			->where("e.class = :class")
			->setParameter(":class", "Utils\Events\Tests\TestClass")
			->execute();
		$this->assertEquals(1, $stmt->rowCount());
		
		$result = $stmt->fetch();
		$this->assertEquals(1, $result["id"]);
		$this->assertEquals("This is a @test.", $result["message"]);
	}
	
	public function testExclude() {
		$this->config->set("logExcludedModules", array("bar"));
		
		$this->assertEquals("enabled", $this->config->get("logMode"));
		$this->assertEquals(array("bar"), 
			$this->config->get("logExcludedModules"));
		
		$event = $this->log->logEvent(Event::LEVEL_ERROR, "bar", "Foo", 
			"This is a @test.", array("@test" => "test event"));
		
		$this->assertFalse($event);
		
		$event = $this->log->logEvent(Event::LEVEL_ERROR, "baz", "Foo", 
			"This is a @test.", array("@test" => "test event"));
		
		$this->assertInstanceOf("UseBB\Utils\Events\Event", $event);
	}
	
	public function testInclude() {
		$this->config->set("logMode", "enabledForSome");
		$this->config->set("logIncludedModules", array("bar"));
		
		$this->assertEquals("enabledForSome", $this->config->get("logMode"));
		$this->assertEquals(array("bar"), 
			$this->config->get("logIncludedModules"));
		
		$event = $this->log->logEvent(Event::LEVEL_ERROR, "bar", "Foo", 
			"This is a @test.", array("@test" => "test event"));
		
		$this->assertInstanceOf("UseBB\Utils\Events\Event", $event);
		
		$event = $this->log->logEvent(Event::LEVEL_ERROR, "baz", "Foo", 
			"This is a @test.", array("@test" => "test event"));
		
		$this->assertFalse($event);
	}
	
	public function testDisable() {
		$this->config->set("logMode", "disabled");
		
		$this->assertEquals("disabled", $this->config->get("logMode"));
		
		$event = $this->log->logEvent(Event::LEVEL_ERROR, "system", "Foo", 
			"This is a @test.", array("@test" => "test event"));
		
		$this->assertFalse($event);
	}
}
