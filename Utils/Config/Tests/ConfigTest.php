<?php

namespace UseBB\Utils\Config\Tests;

use UseBB\System\ServiceRegistry;
use UseBB\Utils\SchemaManagement\SystemSchema;
use UseBB\Utils\Config\NotFoundException;

class ConfigTest extends \PHPUnit_Framework_TestCase {
	protected $db;
	protected $systemSchema;
	protected $conf;
	
	protected function setUp() {
		$services = new ServiceRegistry($GLOBALS["dbConfig"]);
		
		$this->db = $services->get("database");
		$this->systemSchema = new SystemSchema($services);
		$this->systemSchema->install();
		$this->conf = $services->get("config");
	}
	
	protected function tearDown() {
		$this->systemSchema->uninstall();
		$this->db->close();
	}
	
	public function testSetAndGetInput() {
		return array(
			array("system", "foo", "bar", "string"),
			array("system", "bar", 2.3, "float"),
			array("system", "baz", array("one", "two"), "array"),
			array("moo", "grass", TRUE, "bool"),
		);
	}
	
	/**
	 * @dataProvider testSetAndGetInput
	 */
	public function testSetAndGet($module, $key, $expected, $type) {
		$this->conf->set($module, $key, $expected);
		$actual = $this->conf->get($module, $key);
		$this->assertInternalType($type, $actual);
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 * @expectedException UseBB\Utils\Config\NotFoundException
	 * @expectedExceptionMessage Config value for bar at module foo not found.
	 */
	public function testUnexisting() {
		$this->conf->get("foo", "bar");
	}
	
	/**
	 * @dataProvider testSetAndGetInput
	 */
	public function testSetAndGetWithSave($module, $key, $expected, $type) {
		$this->conf->set($module, $key, $expected);
		$this->conf->save();
		$actual = $this->conf->get($module, $key);
		$this->assertInternalType($type, $actual);
		$this->assertEquals($expected, $actual);
	}
	
	public function testChanging() {
		$this->conf->set("system", "foo", "bar");
		$this->conf->set("system", "foo", "baz");
		$foo = $this->conf->get("system", "foo");
		$this->assertEquals("baz", $foo);
	}
	
	public function testChangingWithSave() {
		$this->conf->set("system", "foo", "bar");
		$this->conf->save();
		$this->conf->set("system", "foo", "baz");
		$foo = $this->conf->get("system", "foo");
		$this->assertEquals("baz", $foo);
	}
	
	public function testChangingWithSaveTwice() {
		$this->conf->set("system", "foo", "bar");
		$this->conf->save();
		$this->conf->set("system", "foo", "baz");
		$this->conf->save();
		$foo = $this->conf->get("system", "foo");
		$this->assertEquals("baz", $foo);
	}
	
	public function testModuleRegistry() {
		$core = $this->conf->forModule("system");
		$core->set("foo", "bar");
		$foo = $core->get("foo");
		$this->assertEquals("bar", $foo);
		
		$core->save();
		$foo = $core->get("foo");
		$this->assertEquals("bar", $foo);
		
		$core->set("foo", "baz");
		$core->save();
		$foo = $core->get("foo");
		$this->assertEquals("baz", $foo);
	}
	
	public function testDefaults() {
		$enabled = $this->conf->get("system", "enabled");
		$this->assertInternalType("bool", $enabled);
		$this->assertEquals(TRUE, $enabled);
	}
	
	public function testDelete() {
		$this->conf->set("something", "hello", 6);
		$this->conf->save();
		
		$this->assertEquals(6, $this->conf->get("something", "hello"));
		
		$this->conf->delete("something", "hello");
		$good = FALSE;
		try {
			$this->conf->get("something", "hello");
		} catch (NotFoundException $e) {
			$good = TRUE;
		}
		$this->assertTrue($good);
		$this->conf->save();
	}
	
	public function testDeleteNew() {
		$this->conf->set("something", "hm", 6);
		
		$this->assertEquals(6, $this->conf->get("something", "hm"));
		
		$this->conf->delete("something", "hm");
		$good = FALSE;
		try {
			$this->conf->get("something", "hm");
		} catch (NotFoundException $e) {
			$good = TRUE;
		}
		$this->assertTrue($good);
		$this->conf->save();
	}
	
	public function testDeleteUpdated() {
		$this->conf->set("something", "hello", 6);
		$this->conf->save();
		$this->conf->set("something", "hello", 16);
		
		$this->assertEquals(16, $this->conf->get("something", "hello"));
		
		$this->conf->delete("something", "hello");
		$good = FALSE;
		try {
			$this->conf->get("something", "hello");
		} catch (NotFoundException $e) {
			$good = TRUE;
		}
		$this->assertTrue($good);
		$this->conf->save();
	}
}
