<?php

namespace UseBB\Utils\Mail\Tests;

use UseBB\Utils\Mail\Sender;
use UseBB\System\ServiceRegistry;
use UseBB\Utils\SchemaManagement\SystemSchema;

class MailTest extends \PHPUnit_Framework_TestCase {
	private $services;
	private $sSchema;
	private $mail;
	
	protected function setUp() {
		$this->services = new ServiceRegistry($GLOBALS["dbConfig"]);
		$this->sSchema = new SystemSchema($this->services);
		$this->sSchema->install();
		$this->mail = $this->services->get("mail");
	}
	
	protected function tearDown() {
		$this->sSchema->uninstall();
	}
	
	public function testMail() {
		$params = $this->mail->send("ik", "ik@hier.net", 
			array("jij@daar.net" => "jij"), "test", "foobar", array(), 
			TRUE);
		
		$this->assertEquals("=?utf-8?B?amlq?= <jij@daar.net>", $params[0]);
		$this->assertEquals("=?utf-8?B?dGVzdA==?=", $params[1]);
		$this->assertEquals("foobar", $params[2]);
		
		$headers = explode("\n", $params[3]);
		$this->assertContains("MIME-Version: 1.0", $headers);
		$this->assertContains("Content-Type: text/plain; charset=UTF-8", 
			$headers);
		$this->assertContains("Content-Transfer-Encoding: 8bit", $headers);
		$this->assertContains("From: =?utf-8?B?aWs=?= <ik@hier.net>", $headers);
	}
	
	public function testEmptyFromName() {
		$params = $this->mail->send(NULL, "ik@hier.net", 
			array("jij@daar.net" => "jij"), "test", "foobar", array(), 
			TRUE);
		
		$this->assertEquals("=?utf-8?B?amlq?= <jij@daar.net>", $params[0]);
		$this->assertEquals("=?utf-8?B?dGVzdA==?=", $params[1]);
		$this->assertEquals("foobar", $params[2]);
		
		$headers = explode("\n", $params[3]);
		$this->assertContains("From: ik@hier.net", $headers);
	}
	
	public function testMultiTo() {
		$params = $this->mail->send("ik", "ik@hier.net", array(
			"jij@daar.net" => "jij",
			"foo@bar.net" => "baz"
		), "test", "foobar", array(), TRUE);
		
		$this->assertEquals("=?utf-8?B?amlq?= <jij@daar.net>, "
			. "=?utf-8?B?YmF6?= <foo@bar.net>", $params[0]);
		$this->assertEquals("=?utf-8?B?dGVzdA==?=", $params[1]);
		$this->assertEquals("foobar", $params[2]);
		
		$headers = explode("\n", $params[3]);
		$this->assertContains("From: =?utf-8?B?aWs=?= <ik@hier.net>", $headers);
	}
	
	public function testCC() {
		$params = $this->mail->send("ik", "ik@hier.net", 
			array("jij@daar.net" => "jij"), "test", "foobar", array(
				"cc" => array(
					"foo@bar.net" => "baz",
					"some@thing.com" => "some thing"
				)
			), TRUE);
		
		$this->assertEquals("=?utf-8?B?amlq?= <jij@daar.net>", $params[0]);
		$this->assertEquals("=?utf-8?B?dGVzdA==?=", $params[1]);
		$this->assertEquals("foobar", $params[2]);
		
		$headers = explode("\n", $params[3]);
		$this->assertContains("From: =?utf-8?B?aWs=?= <ik@hier.net>", $headers);
		$this->assertContains("Cc: =?utf-8?B?YmF6?= <foo@bar.net>, "
			."=?utf-8?B?c29tZSB0aGluZw==?= <some@thing.com>", $headers);
	}
	
	public function testBCC() {
		$params = $this->mail->send("ik", "ik@hier.net", 
			array("jij@daar.net" => "jij"), "test", "foobar", array(
				"bcc" => array(
					"foo@bar.net" => "baz",
					"some@thing.com" => "some thing"
				)
			), TRUE);
		
		$this->assertEquals("=?utf-8?B?amlq?= <jij@daar.net>", $params[0]);
		$this->assertEquals("=?utf-8?B?dGVzdA==?=", $params[1]);
		$this->assertEquals("foobar", $params[2]);
		
		$headers = explode("\n", $params[3]);
		$this->assertContains("From: =?utf-8?B?aWs=?= <ik@hier.net>", $headers);
		$this->assertContains("Bcc: =?utf-8?B?YmF6?= <foo@bar.net>, "
			."=?utf-8?B?c29tZSB0aGluZw==?= <some@thing.com>", $headers);
	}
	
	public function testSenderParameter() {
		$this->services->get("config")
			->set("system", "mailEnableSenderParameter", TRUE);
		
		$params = $this->mail->send("ik", "ik@hier.net", 
			array("jij@daar.net" => "jij"), "test", "foobar", array(), 
			TRUE);
		
		$this->assertEquals("=?utf-8?B?amlq?= <jij@daar.net>", $params[0]);
		$this->assertEquals("=?utf-8?B?dGVzdA==?=", $params[1]);
		$this->assertEquals("foobar", $params[2]);
		
		$headers = explode("\n", $params[3]);
		$this->assertContains("From: =?utf-8?B?aWs=?= <ik@hier.net>", $headers);
		
		$opts = explode(" ", $params[4]);
		$this->assertContains("-fik@hier.net", $opts);
	}
}
