<?php

namespace UseBB\Utils\Text\Tests;

use UseBB\Utils\Text\StringOperations;

class StringTest extends \PHPUnit_Framework_TestCase {
	protected static $str;
	
	public static function setUpBeforeClass() {
		self::$str = new StringOperations();
	}
	
	public function testStrlen() {
		$this->assertEquals(7, self::$str->strlen("a^üPÑç&"));
	}
	
	public function testSubstr() {
		$this->assertEquals("PÑç&", self::$str->substr("a^üPÑç&", 3));
		$this->assertEquals("PÑ", self::$str->substr("a^üPÑç&", 3, 2));
	}
	
	public function testSubstrReplace() {
		$this->assertEquals("a^üèîPÑç&", 
			self::$str->substr_replace("a^üPÑç&", "èî", 3));
		$this->assertEquals("a^üèîç&", 
			self::$str->substr_replace("a^üPÑç&", "èî", 3, 2));
		$this->assertEquals("a^üèî&", 
			self::$str->substr_replace("a^üPÑç&", "èî", 3, 3));
	}
	
	public function testLtrim() {
		$this->assertEquals("a^üPÑç&", self::$str->ltrim(" \ta^üPÑç&"));
	}
	
	public function testRtrim() {
		$this->assertEquals("a^üPÑç&", self::$str->rtrim("a^üPÑç& \t"));
	}
	
	public function testTrim() {
		$this->assertEquals("a^üPÑç&", self::$str->trim(" \ta^üPÑç& \t"));
	}
	
	public function testStrtolower() {
		$this->assertEquals("a^üpñç&", self::$str->strtolower("a^üPÑç&"));
	}
	
	public function testStrtoupper() {
		$this->assertEquals("A^ÜPÑÇ&", self::$str->strtoupper("a^üPÑç&"));
	}
	
	public function testUcfirst() {
		$this->assertEquals("ÜPÑç&", self::$str->ucfirst("üPÑç&"));
	}
	
	public function testUcwords() {
		$this->assertEquals("ÜPÑ Ç&", self::$str->ucwords("üPÑ ç&"));
	}
	
	public function testStrpos() {
		$this->assertEquals(3, self::$str->strpos("a^üPÑç&", "PÑ"));
	}
	
	public function testHTMLEntities() {
		$this->assertEquals("a^&#252;P&#209;&#231;&", 
			self::$str->toHTMLEntities("a^üPÑç&"));
		
		$this->assertEquals("a^üPÑç&", 
			self::$str->fromHTMLEntities("a^&#252;P&#209;&#231;&"));
	}
	
	public function testMIME() {
		$this->assertEquals("", self::$str->toMIME(""));
		$this->assertEquals("=?utf-8?B?dGVzdGluZw==?=", 
			self::$str->toMIME("testing"));
	}
}
