<?php

namespace UseBB\Utils\Input\Tests;

use UseBB\Utils\Input\Service;

class InputTest extends \PHPUnit_Framework_TestCase {
	protected static $input;

	public static function setUpBeforeClass() {
		self::$input = new Service();
	}

	public function testWithoutType() {
		   $_GET["foo"] = "bar1";
		  $_POST["foo"] = "bar2";
		$_COOKIE["foo"] = "bar3";

		$foo = self::$input->key("foo");
		$this->assertInstanceOf("UseBB\Utils\Input\\Value", $foo);
		$this->assertTrue($foo->hasValue());
		$this->assertEquals("bar1", $foo->getValue());

		$foo = self::$input->key("foo", "POST");
		$this->assertTrue($foo->hasValue());
		$this->assertEquals("bar2", $foo->getValue());

		$foo = self::$input->key("foo", "COOKIE");
		$this->assertTrue($foo->hasValue());
		$this->assertEquals("bar3", $foo->getValue());
	}

	public function testFixedMethod() {
		$_POST["foo"] = "bar2";

		self::$input->setMethod("POST");
		$foo = self::$input->key("foo");
		$this->assertTrue($foo->hasValue());
		$this->assertEquals("bar2", $foo->getValue());
		self::$input->setMethod("GET");
	}

	/**
	 * @dataProvider testWithTypeInput
	 */
	public function testWithType($key, $oVal, $type, $rVal) {
		$_GET[$key] = $oVal;

		$var = self::$input->key($key)->type($type);
		$this->assertTrue($var->hasValue());
		$aVal = $var->getValue();
		$this->assertInternalType($type, $aVal);
		$this->assertEquals($rVal, $aVal);
	}

	public function testWithTypeInput() {
		return array(
			array("foo", "bar",  "string", "bar"),
			array("bar", "1",    "bool",   TRUE),
			array("baz", "0",    "bool",   FALSE),
			array("q",   "abc",  "bool",   TRUE),
			array("x",   "5",    "int",    5),
			array("y",   "12.4", "float",  12.4),
		);
	}

	public function testChangingType() {
		$_GET["some"] = "5";

		$some = self::$input->key("some");

		$val = $some->getValue();
		$this->assertInternalType("string", $val);
		$this->assertEquals("5", $val);

		$val = $some->type("int")->getValue();
		$this->assertInternalType("int", $val);
		$this->assertEquals(5, $val);

		$val = $some->type("bool")->getValue();
		$this->assertInternalType("bool", $val);
		$this->assertEquals(TRUE, $val);
	}

	/**
	 * @expectedException UseBB\Utils\Input\NotFoundException
	 */
	public function testUnset() {
		unset($_GET["foo"]);

		$foo = self::$input->key("foo");
		$this->assertFalse($foo->hasValue());
		$foo->getValue();
	}

	public function testValidation() {
		$_GET["foo"] = "5";

		$foo = self::$input->key("foo")->validate(
			function ($k, $v) {
				return $k == "foo" && $v < 5;
			});
		$this->assertFalse($foo->hasValue());

		$foo->validate(
			function ($k, $v) {
				return $v < 10;
			});
		$this->assertTrue($foo->hasValue());

		$foo->type("bool")->validate(
			function ($k, $v) {
				return $v === TRUE;
			});
		$this->assertTrue($foo->hasValue());
	}

	/**
	 * @expectedException UseBB\Utils\Input\ValueInvalidException
	 */
	public function testInvalid() {
		$_GET["a"] = "20";

		$a = self::$input->key("a")->validate(
			function ($k, $v) {
				return $v == 19;
			});
		$this->assertFalse($a->hasValue());
		$a->getValue();
	}
}
