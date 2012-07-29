<?php

namespace UseBB\System\Context\Tests;

use UseBB\Tests\TestCase;
use UseBB\System\Context\HTTP;

class ContextTest extends TestCase {
	protected $context;
	protected $prim;
	
	protected function setUp() {
		$this->newServices();
		$this->prim = $this->getMock(
			"UseBB\Utils\PrimitiveFunctions\Service", array("setcookie"));
		$this->setService("primitives", $this->prim);
		$this->context = new HTTP($this->getServices());
		$this->beginTransaction();
	}
	
	protected function tearDown() {
		$this->rollback();
	}
	
	public function testName() {
		$this->assertEquals("HTTP", $this->context->getName());
	}
	
	public function testHandleRequest() {
		$session = $this->getMockWithoutConstructor(
			"UseBB\System\Session\Session");
		$this->setService("session", $session);
		
		$navigation = $this->getMockWithoutConstructor(
			"UseBB\System\Navigation\Registry");
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
			array("en", "nl-be,fr-fr;q=0.7", array()),
			array("en", "nl-be,fr-fr;q=0.7", array("en")),
			array("fr", "nl-be,fr;q=0.7", array("en", "fr")),
			array("en", "", array("fr", "en")),
			array("en", "", array()),
		);
	}
	
	/**
	 * @dataProvider acceptLanguageProvider
	 * @covers UseBB\System\Context\HTTP::getLanguage
	 * @covers UseBB\System\Context\HTTP::getLanguagePriorities
	 * @covers UseBB\System\Context\HTTP::getLanguageMoreShortCodes
	 */
	public function testAcceptLanguage($lang, $list, $available) {
		$_SERVER["HTTP_ACCEPT_LANGUAGE"] = $list;
		
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
	
	public function cookieTestProvider() {
		return array(
			array("foo", "bar", NULL, NULL, 0, TRUE),
		);
	}
	
	/**
	 * @dataProvider cookieTestProvider
	 */
	public function testCookieSetting($name, $value, $days, $httpOnly, $expectExpire, $expectHttpOnly) {
		$config = $this->getService("config")->forModule("system");
		$this->prim->expects($this->once())->method("setcookie")->with(
			$this->equalTo($name),
			$this->equalTo($value),
			$this->equalTo($expectExpire),
			$this->equalTo($config->get("cookiePath")),
			$this->equalTo($config->get("cookieDomain")),
			$this->equalTo($this->context->isSecureHTTP()),
			$this->equalTo($expectHttpOnly));
		
		$this->context->setCookie($name, $value, $days, $httpOnly);
	}
	
	/**
	 * @dataProvider cookieTestProvider
	 */
	public function testCookieDeleting($name, $value, $days, $httpOnly, $expectExpire, $expectHttpOnly) {
		$config = $this->getService("config")->forModule("system");
		$this->prim->expects($this->once())->method("setcookie")->with(
			$this->equalTo($name),
			$this->equalTo(""),
			$this->lessThan(0),
			$this->equalTo($config->get("cookiePath")),
			$this->equalTo($config->get("cookieDomain")),
			$this->equalTo($this->context->isSecureHTTP()),
			$this->equalTo(FALSE));
		
		$this->context->deleteCookie($name);
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
	
	/**
	 * @covers UseBB\System\Context\HTTP::handleError
	 * @covers UseBB\System\Context\HTTP::cleanPath
	 */
	public function testHandleError() {
		$context = $this->context;
		$msg = $this->getOutput(function() use ($context) {
			$context->handleError(E_NOTICE, "Some error.", 
				USEBB_ROOT_PATH . "/foo.php", 5, array());
		});
		$this->assertRegExp("#Some error.*foo\.php\(5\)#", $msg);
		$this->assertTrue(strpos($msg, USEBB_ROOT_PATH) === FALSE, 
			"Not contain full path.");
		// TODO test logging
	}
	
	public function testHandleErrorPlugin() {
		$this->getService("plugins")->register("System\Context\HTTP", 
			"error", function() {
				return FALSE;
			});
		$this->assertNull($this->context->handleError(E_NOTICE, "Some error.", 
			USEBB_ROOT_PATH . "/foo.php", 5, array()));
	}
	
	/**
	 * @covers UseBB\System\Context\HTTP::handleException
	 * @covers UseBB\System\Context\HTTP::cleanPath
	 */
	public function testHandleException() {
		$context = $this->context;
		$msg = $this->getOutput(function() use ($context) {
			$context->handleException(new \Exception("foo"));
		});
		$this->assertRegExp("#Exception.*foo#", $msg);
		$this->assertTrue(strpos($msg, USEBB_ROOT_PATH) === FALSE, 
			"Not contain full path.");
		// TODO test logging
	}
	
	public function testHandleExceptionPlugin() {
		$this->getService("plugins")->register("System\Context\HTTP", 
			"exception", function() {
				return FALSE;
			});
		$this->assertNull($this->context->handleException(new \Exception("foo")));
	}
	
	public function testForcedEnvironment() {
		$this->assertNull($this->context->getForcedEnvironmentName());
	}
}
