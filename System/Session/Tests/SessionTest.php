<?php

namespace UseBB\System\Session\Tests;

use UseBB\Tests\TestCase;
use UseBB\System\Session\NotStartedException;
use UseBB\System\Session\ValueNotFoundException;

class SessionTest extends TestCase {
	protected $context;
	protected $session;
	
	protected function setUp() {
		$this->newServices();
		$this->beginTransaction();
		$this->context = $this->getMock("UseBB\System\Context\HTTP", 
			array("setCookie"), array($this->getServices()));
		$this->setService("context", $this->context);
		$this->getService("config")->set("system", "sessionLifetime", 4);
		$this->session = $this->getService("session");
	}
	
	public function tearDown() {
		$this->rollback();
	}
	
	public function testSessionNotStarted() {
		$count = 0;
		try {
			$this->session->getId();
		} catch (NotStartedException $e) {
			$count++;
		}
		try {
			$this->session->getRequestCount();
		} catch (NotStartedException $e) {
			$count++;
		}
		try {
			$this->session->getStartTime();
		} catch (NotStartedException $e) {
			$count++;
		}
		try {
			$this->session->getUpdateTime();
		} catch (NotStartedException $e) {
			$count++;
		}
		try {
			$this->session->getValue("foo");
		} catch (NotStartedException $e) {
			$count++;
		}
		try {
			$this->session->setValue("foo", "bar");
		} catch (NotStartedException $e) {
			$count++;
		}
		try {
			$this->session->deleteValue("foo");
		} catch (NotStartedException $e) {
			$count++;
		}
		$this->assertEquals(7, $count);
		$this->assertNull($this->session->save());
	}
	
	public function testSessionContinuationSystemNotInstalled() {
		$this->getService("config")->set("system", "installed", FALSE);
		$this->assertNull($this->session->startOrContinue());
		$this->assertNull($this->session->save());
	}
	
	public function testSessionContinuation() {
		$_SERVER["REMOTE_ADDR"] = "192.168.1.1";
		
		$this->session->startOrContinue();
		$this->assertEquals(1, $this->session->getRequestCount());
		$this->assertInstanceOf("\DateTime", $this->session->getStartTime());
		$this->assertInstanceOf("\DateTime", $this->session->getUpdateTime());
		$this->assertEquals($this->session->getStartTime(), 
			$this->session->getUpdateTime());
		
		$this->session->save();
		$sessId = $this->session->getId();
		$_COOKIE["sessId"] = $sessId;
		sleep(2);
		
		$this->session->startOrContinue();
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
		
		$this->session->startOrContinue();
		$this->assertFalse($this->session->hasValue("foo"));
		
		$this->session->setValue("foo", "bar");
		$this->session->save();
		$_COOKIE["sessId"] = $this->session->getId();
		
		$this->session->startOrContinue();
		$this->assertTrue($this->session->hasValue("foo"));
		$this->assertEquals("bar", $this->session->getValue("foo"));
		
		$this->session->deleteValue("foo");
		$this->session->save();
		
		$this->session->startOrContinue();
		$this->session->getValue("foo");
	}
	
	public function testDifferentSessionId() {
		$_SERVER["REMOTE_ADDR"] = "192.168.1.1";
		
		$this->session->startOrContinue();
		$this->session->save();
		$id1 = $this->session->getId();
		
		$_COOKIE["sessId"] = "abc";
		
		$this->session->startOrContinue();
		$id2 = $this->session->getId();
		
		$this->assertNotEquals($id1, $id2);
		$this->assertNotEquals("abc", $id2);
	}
	
	public function testDifferentIP() {
		$_SERVER["REMOTE_ADDR"] = "192.168.1.1";
		
		$this->session->startOrContinue();
		$this->session->save();
		$id1 = $this->session->getId();
		
		$_COOKIE["sessId"] = $id1;
		$_SERVER["REMOTE_ADDR"] = "192.168.1.2";
		
		$this->session->startOrContinue();
		$id2 = $this->session->getId();
		
		$this->assertNotEquals($id1, $id2);
	}
	
	public function testTimeout() {
		$_SERVER["REMOTE_ADDR"] = "192.168.1.1";
		
		$this->session->startOrContinue();
		$this->session->save();
		$id1 = $this->session->getId();
		
		$_COOKIE["sessId"] = $id1;
		sleep(5);
		
		$this->session->startOrContinue();
		$id2 = $this->session->getId();
		
		$this->assertNotEquals($id1, $id2);
	}
	
	public function testExceptions() {
		$e = new ValueNotFoundException("foo");
		$this->assertEquals("foo", $e->getKey());
	}
}
