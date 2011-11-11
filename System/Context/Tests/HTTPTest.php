<?php

namespace UseBB\System\Context\Tests;

use UseBB\Tests\TestCase;
use UseBB\System\Context\HTTP;

class ContextTest extends TestCase {
	protected $context;
	
	protected function setUp() {
		$this->newServices();
		$this->context = new HTTP($this->getServices());
	}
	
	public function testHandleRequest() {
		$session = $this->getMockBuilder("UseBB\System\Session\Session")
			->disableOriginalConstructor()->getMock();
		$this->setService("session", $session);
		
		$navigation = $this->getMockBuilder("UseBB\System\Navigation\Registry")
			->disableOriginalConstructor()->getMock();
		$this->setService("navigation", $navigation);
		
		$session->expects($this->once())->method("startOrContinue");
		$navigation->expects($this->once())->method("handleRequest")->with(
			$this->arrayHasKey("foo"));
		$session->expects($this->once())->method("save");
		
		$_GET["foo"] = "bar";
		$this->context->handleRequest();
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
	
	public function escapeProvider() {
		return array(
			array("<a>", "&lt;a&gt;"),
			array('"b"', "&quot;b&quot;"),
			array("a^üPÑç&", "a^üPÑç&amp;")
		);
	}
	
	/**
	 * @dataProvider escapeProvider
	 */
	public function testEscape($string, $expected) {
		$this->assertEquals($expected, 
			$this->context->escapeString($string));
	}
	
	public function stringArgsProvider() {
		return array(
			array("Hello %name.", array(
				"%name" => "you>"
			), "Hello <em>you&gt;</em>."),
			array("Hello @name.", array(
				"@name" => "you>"
			), "Hello you&gt;."),
			array("Hello !name.", array(
				"!name" => "you>"
			), "Hello you>."),
		);
	}
	
	/**
	 * @dataProvider stringArgsProvider
	 */
	public function testStringArgs($string, $args, $expected) {
		$this->assertEquals($expected,
			$this->context->applyArgumentsToString($string, $args));
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
	
	public function secureHTTPProvider() {
		return array(
			array(FALSE, NULL, FALSE),
			array(TRUE, FALSE, FALSE),
			array(TRUE, TRUE, TRUE),
			array(TRUE, 0, FALSE),
			array(TRUE, 1, TRUE),
			array(TRUE, "off", FALSE),
			array(TRUE, "on", TRUE),
		);
	}
	
	/**
	 * @dataProvider secureHTTPProvider
	 */
	public function testSecureHTTP($set, $val, $expected) {
		if ($set) {
			$_SERVER["HTTPS"] = $val;
		}
		
		$this->assertEquals($expected, $this->context->isSecureHTTP());
	}
	
	public function generateLinkProvider() {
		return array(
			array(array(), "./"),
			array(array("foo" => "bar"), "./?foo=bar"),
			array(array("foo" => "baz", "bar" => TRUE), "./?foo=baz&bar=1"),
			array(array("foo" => "&baz", "bar" => "=="), 
				"./?foo=%26baz&bar=%3D%3D")
		);
	}
	
	/**
	 * @dataProvider generateLinkProvider
	 */
	public function testGenerateLink($params, $expected) {
		$this->assertEquals($expected, $this->context->generateLink($params));
	}
	
	public function testHandleError() {
		$this->expectOutputRegex("#Some error\..*foo\.php.*5#");
		
		$this->context->handleError(E_NOTICE, "Some error.", 
			"foo.php", 5, array());
	}
	
	public function testHandleException() {
		$this->expectOutputRegex("#Exception.*foo#");
		
		$this->context->handleException(new \Exception("foo"));
	}
}
