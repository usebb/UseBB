<?php

namespace UseBB\System\Context\Tests;

use UseBB\System\Context\HTTP;

class ContextTest extends \PHPUnit_Framework_TestCase {
	protected $context;
	
	protected function setUp() {
		$services = $this->getMockBuilder("UseBB\System\ServiceRegistry")
			->disableOriginalConstructor()->getMock();
		$this->context = new HTTP($services);
	}
	
	public function testEscape() {
		$this->assertEquals("&lt;a&gt;", 
			$this->context->escapeString("<a>"));
		$this->assertEquals('&quot;b&quot;', 
			$this->context->escapeString('"b"'));
		$this->assertEquals("a^üPÑç&amp;", 
			$this->context->escapeString("a^üPÑç&"));
	}
	
	public function testStringArgs() {
		$this->assertEquals("Hello <em>you&gt;</em>.",
			$this->context->applyArgumentsToString(
			"Hello %name.", array(
				"%name" => "you>"
			)));
		$this->assertEquals("Hello you&gt;.",
			$this->context->applyArgumentsToString(
			"Hello @name.", array(
				"@name" => "you>"
			)));
		$this->assertEquals("Hello you>.",
			$this->context->applyArgumentsToString(
			"Hello !name.", array(
				"!name" => "you>"
			)));
	}
	
	public function acceptLanguageProvider() {
		return array(
			array("nl", "nl-be,en-gb;q=0.7,en-us;q=0.3", array("en", "nl")),
			array("en", "en-gb,nl-be;q=0.7,en-us;q=0.3", array("en", "nl")),
			array("en", "nl-be,en-gb;q=0.7,en-us;q=0.3", array("en")),
			array("fr", "nl-be,fr-fr;q=0.7", array("en", "fr")),
			array("en", "nl-be,fr-fr;q=0.7", array("en")),
		);
	}
	
	/**
	 * @dataProvider acceptLanguageProvider
	 */
	public function testAcceptLanguage($lang, $list, $available) {
		$_SERVER["HTTP_ACCEPT_LANGUAGE"] = $lang;
		$this->assertEquals($lang, $this->context->getLanguage($available));
	}
	
	public function testIP() {
		$_SERVER["REMOTE_ADDR"] = "192.168.3.4";
		$this->assertEquals($_SERVER["REMOTE_ADDR"], 
			$this->context->getIPAddress());
	}
	
	public function testBrowser() {
		$_SERVER["HTTP_USER_AGENT"] = "Chrome";
		$this->assertEquals($_SERVER["HTTP_USER_AGENT"], 
			$this->context->getBrowser());
	}
}
