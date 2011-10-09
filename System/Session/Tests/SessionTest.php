<?php

namespace UseBB\System\Session\Tests;

use UseBB\System\ServiceRegistry;
use UseBB\Utils\SchemaManagement\SystemSchema;

class SessionTest extends \PHPUnit_Framework_TestCase {
	protected static $systemSchema;
	protected $session;
	
	public static function setUpBeforeClass() {
		$services = new ServiceRegistry($GLOBALS["dbConfig"]);
		self::$systemSchema = new SystemSchema($services);
		self::$systemSchema->install();
	}
	
	protected function setUp() {
		$services = new ServiceRegistry($GLOBALS["dbConfig"]);
		$services->setForcedContext("UseBB\System\Context\HTTP");
		$services->get("config")->set("system", "sessionLifetime", 4);
		$this->session = $services->get("session");
	}
	
	public static function tearDownAfterClass() {
		self::$systemSchema->uninstall();
	}
	
	public function testSessionContinuation() {
		$_SERVER["REMOTE_ADDR"] = "192.168.1.1";
		
		$this->session->startOrContinue(TRUE);
		$this->assertEquals(1, $this->session->getRequestCount());
		$this->assertInstanceOf("\DateTime", $this->session->getStartTime());
		$this->assertInstanceOf("\DateTime", $this->session->getUpdateTime());
		$this->assertEquals($this->session->getStartTime(), 
			$this->session->getUpdateTime());
		
		$this->session->save();
		$sessId = $this->session->getId();
		$_COOKIE["sessId"] = $sessId;
		sleep(2);
		
		$this->session->startOrContinue(TRUE);
		$this->assertEquals($sessId, $this->session->getId());
		$this->assertEquals(2, $this->session->getRequestCount());
		$this->assertInstanceOf("\DateTime", $this->session->getStartTime());
		$this->assertInstanceOf("\DateTime", $this->session->getUpdateTime());
		$this->assertNotEquals($this->session->getStartTime(), 
			$this->session->getUpdateTime());
	}
	
	/**
	 * @expectedException UseBB\System\Session\ValueNotFoundException
	 */
	public function testSessionData() {
		$_SERVER["REMOTE_ADDR"] = "192.168.1.1";
		
		$this->session->startOrContinue(TRUE);
		$this->assertFalse($this->session->hasValue("foo"));
		
		$this->session->setValue("foo", "bar");
		$this->session->save();
		$_COOKIE["sessId"] = $this->session->getId();
		
		$this->session->startOrContinue(TRUE);
		$this->assertTrue($this->session->hasValue("foo"));
		$this->assertEquals("bar", $this->session->getValue("foo"));
		
		$this->session->deleteValue("foo");
		$this->session->save();
		
		$this->session->startOrContinue(TRUE);
		$this->session->getValue("foo");
	}
	
	public function testDifferentSessionId() {
		$_SERVER["REMOTE_ADDR"] = "192.168.1.1";
		
		$this->session->startOrContinue(TRUE);
		$this->session->save();
		$id1 = $this->session->getId();
		
		$_COOKIE["sessId"] = "abc";
		
		$this->session->startOrContinue(TRUE);
		$id2 = $this->session->getId();
		
		$this->assertNotEquals($id1, $id2);
		$this->assertNotEquals("abc", $id2);
	}
	
	public function testDifferentIP() {
		$_SERVER["REMOTE_ADDR"] = "192.168.1.1";
		
		$this->session->startOrContinue(TRUE);
		$this->session->save();
		$id1 = $this->session->getId();
		
		$_COOKIE["sessId"] = $id1;
		$_SERVER["REMOTE_ADDR"] = "192.168.1.2";
		
		$this->session->startOrContinue(TRUE);
		$id2 = $this->session->getId();
		
		$this->assertNotEquals($id1, $id2);
	}
	
	public function testTimeout() {
		$_SERVER["REMOTE_ADDR"] = "192.168.1.1";
		
		$this->session->startOrContinue(TRUE);
		$this->session->save();
		$id1 = $this->session->getId();
		
		$_COOKIE["sessId"] = $id1;
		sleep(5);
		
		$this->session->startOrContinue(TRUE);
		$id2 = $this->session->getId();
		
		$this->assertNotEquals($id1, $id2);
	}
}
