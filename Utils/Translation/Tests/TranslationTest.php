<?php

namespace UseBB\Modules\FooBar {

use UseBB\System\ServiceAccessor;

class ModuleTestClass extends ServiceAccessor {
	public function testT($string, $lang = NULL, $args = array(), 
		$section = "main") {
		$this->setTranslationSection($section);
		echo $this->t($string, $args, array(
			"language" => $lang
		));
	}
		
	public function testTp($singular, $plural, $count, $lang = NULL, 
		$args = array(), $section = "main") {
		$this->setTranslationSection($section);
		echo $this->tp($singular, $plural, $count, $args, array(
			"language" => $lang
		));
	}
}

}

namespace UseBB\Utils\Translation\Tests {

use UseBB\System\ServiceAccessor;
use UseBB\System\ServiceRegistry;
use UseBB\Modules\FooBar\ModuleTestClass;

class SystemTestClass extends ServiceAccessor {
	public function testT($string, $lang = NULL, $args = array(), 
		$section = "test") {
		$this->setTranslationSection($section);
		echo $this->t($string, $args, array(
			"language" => $lang
		));
	}
		
	public function testTp($singular, $plural, $count, $lang = NULL, 
		$args = array(), $section = "test") {
		$this->setTranslationSection($section);
		echo $this->tp($singular, $plural, $count, $args, array(
			"language" => $lang
		));
	}
}

class TranslationTest extends \PHPUnit_Extensions_OutputTestCase {
	protected static $services;
	protected static $translation;
	protected static $systemTestClass;
	protected static $moduleTestClass;
	
	public static function setUpBeforeClass() {
		self::$services = new ServiceRegistry($GLOBALS["dbConfig"]);
		self::$services->setForcedContext("UseBB\System\Context\HTTP");
		self::$translation = self::$services->get("translation");
		self::$systemTestClass = new SystemTestClass(self::$services);
		self::$moduleTestClass = new ModuleTestClass(self::$services);
	}
	
	public function testList() {
		$all = self::$translation->getAvailableLanguages();
		$this->assertInternalType("array", $all);
		$this->assertTrue(count($all) >= 2);
		$this->assertEquals(array("English", "English"), $all["en"]);
		$this->assertEquals(array("Dutch", "Nederlands"), $all["nl"]);
	}
	
	// TODO use input value functions for some of these with copy-pasted code.
	public function testTranslate() {
		// Plain English.
		$this->assertEquals("This is a test.", 
			self::$translation->translate("system", "test", 
			"This is a test.", array(), array()));
		// Dutch.
		$this->assertEquals("Dit is een test.", 
			self::$translation->translate("system", "test", 
			"This is a test.", array(), array(
				"language" => "nl"
			)));
		// No sections foobar (and main).
		$this->assertEquals("This is a test.", 
			self::$translation->translate("system", "foobar", 
			"This is a test.", array(), array(
				"language" => "nl"
			)));
		
		// Plain English.
		$this->assertEquals("This might be something.", 
			self::$translation->translate("FooBar", "main", 
			"This might be something.", array(), array()));
		// Dutch.
		$this->assertEquals("Dit zou iets kunnen zijn.", 
			self::$translation->translate("FooBar", "main", 
			"This might be something.", array(), array(
				"language" => "nl"
			)));
		// No section "something" (but main).
		$this->assertEquals("Dit zou iets kunnen zijn.", 
			self::$translation->translate("FooBar", "something", 
			"This might be something.", array(), array(
				"language" => "nl"
			)));
		// No language "something".
		$this->assertEquals("This might be something.", 
			self::$translation->translate("FooBar", "main", 
			"This might be something.", array(), array(
				"language" => "something"
			)));
	}
	
	public function testTranslateArgs() {
		// Plain English.
		$this->assertEquals("This is a check.", 
			self::$translation->translate("system", "test", 
			"This is a @test.", array(
				"@test" => "check"
			), array()));
		// Dutch.
		$this->assertEquals("Dit is een check.", 
			self::$translation->translate("system", "test", 
			"This is a @test.", array(
				"@test" => "check"
			), array(
				"language" => "nl"
			)));
		// Unexisting
		$this->assertEquals("This is a check.", 
			self::$translation->translate("system", "test", 
			"This is a @test.", array(
				"@test" => "check"
			), array(
				"language" => "foo"
			)));
	}
	
	public function testTranslatePlural() {
		// Plain English.
		$this->assertEquals("This is 1 check.", 
			self::$translation->translatePlural("system", "test", 
			"This is 1 check.", "These are @count checks.", 1, 
			array(), array()));
		$this->assertEquals("These are 5 checks.", 
			self::$translation->translatePlural("system", "test", 
			"This is 1 check.", "These are @count checks.", 5, 
			array(), array()));
		// Dutch.
		$this->assertEquals("Dit is 1 check.", 
			self::$translation->translatePlural("system", "test", 
			"This is 1 check.", "These are @count checks.", 1, 
			array(), array(
				"language" => "nl"
			)));
		$this->assertEquals("Dit zijn 5 checks.", 
			self::$translation->translatePlural("system", "test", 
			"This is 1 check.", "These are @count checks.", 5, 
			array(), array(
				"language" => "nl"
			)));
		// Unexisting language.
		$this->assertEquals("This is 1 check.", 
			self::$translation->translatePlural("system", "test", 
			"This is 1 check.", "These are @count checks.", 1, 
			array(), array(
				"language" => "foo"
			)));
		$this->assertEquals("These are 5 checks.", 
			self::$translation->translatePlural("system", "test", 
			"This is 1 check.", "These are @count checks.", 5, 
			array(), array(
				"language" => "foo"
			)));
	}
	
	public function testContext() {
		// Plain English.
		$this->assertEquals("May", 
			self::$translation->translate("system", "test", 
			"May", array(), array(
				"context" => "month"
			)));
		// Dutch.
		$this->assertEquals("mei", 
			self::$translation->translate("system", "test", 
			"May", array(), array(
				"language" => "nl",
				"context" => "month"
			)));
		// No sections foobar (and main).
		$this->assertEquals("May", 
			self::$translation->translate("system", "foobar", 
			"May", array(), array(
				"language" => "nl",
				"context" => "month"
			)));
		
		// Plain English.
		$this->assertEquals("This is 1 check.", 
			self::$translation->translatePlural("system", "test", 
			"This is 1 check.", "These are @count checks.", 1, 
			array(), array(
				"context" => "foo"
			)));
		// Dutch.
		$this->assertEquals("Dit is 1 check.", 
			self::$translation->translatePlural("system", "test", 
			"This is 1 check.", "These are @count checks.", 1, 
			array(), array(
				"language" => "nl",
				"context" => "foo"
			)));
		// Unexisting language.
		$this->assertEquals("This is 1 check.", 
			self::$translation->translatePlural("system", "test", 
			"This is 1 check.", "These are @count checks.", 1, 
			array(), array(
				"language" => "foo",
				"context" => "foo"
			)));
	}
	
	public function testNumberFormat() {
		// Plain English.
		$this->assertEquals("These are 12,351.5 checks.", 
			self::$translation->translatePlural("system", "test", 
			"This is 1 check.", "These are @count checks.", 12351.4643, 
			array(), array()));
		$this->assertEquals("These are 12,351.464 checks.", 
			self::$translation->translatePlural("system", "test", 
			"This is 1 check.", "These are @count checks.", 12351.4643, 
			array(), array(
				"decimals" => 3
			)));
		$this->assertEquals("This is the number 2,573.4.", 
			self::$translation->translate("system", "test", 
			"This is the number @number.", array(
				"@number" => 2573.352
			), array()));
		$this->assertEquals("This is the number 2,573.", 
			self::$translation->translate("system", "test", 
			"This is the number @number.", array(
				"@number" => 2573
			), array()));
		$this->assertEquals("This is the number 2573.352.", 
			self::$translation->translate("system", "test", 
			"This is the number @number.", array(
				"@number" => 2573.352
			), array(
				"noNumberFormat" => TRUE
			)));
		// Dutch.
		$this->assertEquals("Dit zijn 12.351,5 checks.", 
			self::$translation->translatePlural("system", "test", 
			"This is 1 check.", "These are @count checks.", 12351.4643, 
			array(), array(
				"language" => "nl"
			)));
		// Unexisting language.
		$this->assertEquals("These are 12,351.5 checks.", 
			self::$translation->translatePlural("system", "test", 
			"This is 1 check.", "These are @count checks.", 12351.4643, 
			array(), array(
				"language" => "foo"
			)));
	}
	
	public function testSetLanguage() {
		// Plain English.
		$this->assertEquals("This is a test.", 
			self::$translation->translate("system", "test", 
			"This is a test.", array(), array()));
		self::$translation->setLanguage("nl");
		// Dutch.
		$this->assertEquals("Dit is een test.", 
			self::$translation->translate("system", "test", 
			"This is a test.", array(), array()));
		self::$translation->setLanguage("en");
	}
	
	public function testSystemT() {
		$this->expectOutputString("This is a test."
			. "This is a test."
			. "Dit is een test."
			. "This is a test."
			. "This is 1 check."
			. "These are 7 checks."
			. "Dit zijn 5 checks.");
		
		// Using the "test" section.
		
		self::$systemTestClass->testT("This is a test.");
		// No language "foo".
		self::$systemTestClass->testT("This is a test.", "foo");
		self::$systemTestClass->testT("This is a test.", "nl");
		// No section "bar".
		self::$systemTestClass->testT("This is a test.", "nl", array(), "bar");
		
		self::$systemTestClass->testTp("This is 1 check.", 
			"These are @count checks.", 1);
		self::$systemTestClass->testTp("This is 1 check.", 
			"These are @count checks.", 7);
		self::$systemTestClass->testTp("This is 1 check.", 
			"These are @count checks.", 5, "nl");
	}
	
	public function testModuleT() {
		$this->expectOutputString("This might be something."
			. "This might be something."
			. "Dit zou iets kunnen zijn."
			. "Dit zou iets kunnen zijn."
			. "This is a test."
			. "Dit is een test.");
		
		// Using the "main" section.
		
		self::$moduleTestClass->testT("This might be something.");
		// No language "foo".
		self::$moduleTestClass->testT("This might be something.", "foo");
		self::$moduleTestClass->testT("This might be something.", "nl");
		// No section "bar", but falls back to "main".
		self::$moduleTestClass->testT("This might be something.", "nl", 
			array(), "bar");
		
		// Falls back to system translations, but no section "main".
		self::$moduleTestClass->testT("This is a test.", "nl");
		// Falls back to system translations, using section "test".
		self::$moduleTestClass->testT("This is a test.", "nl", array(), "test");
	}
}

}
