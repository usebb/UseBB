<?php

namespace UseBB\System\ModuleManagement\Tests;

use UseBB\System\ServiceRegistry;
use UseBB\Utils\SchemaManagement\SystemSchema;
use UseBB\Utils\Config\NotFoundException;
use UseBB\System\ModuleManagement\EnableOutdatedModuleException;

require USEBB_ROOT_PATH . "/Modules/FooBar/moduleInfo.php";

class ModulesTest extends \PHPUnit_Framework_TestCase {
	protected $services;
	protected $systemSchema;
	protected $modules;
	
	protected function setUp() {
		$this->services = new ServiceRegistry($GLOBALS["dbConfig"]);
		$this->services->setServiceInstance("context", 
			new \UseBB\System\Context\HTTP($this->services));
		$this->systemSchema = new SystemSchema($this->services);
		$this->systemSchema->install();
		$this->modules = $this->services->get("modules");
	}
	
	protected function tearDown() {
		$this->systemSchema->uninstall();
	}
	
	public function testReading() {
		$all = $this->modules->getAllModulesInfo();
		$this->assertInternalType("array", $all);
		$this->assertTrue(count($all) > 0);
		$this->assertArrayHasKey("FooBar", $all);
		
		$foobar = $this->modules->getModuleInfo("FooBar");
		$this->assertInstanceOf("UseBB\System\ModuleManagement\ModuleInfo", 
			$foobar);
		$this->assertEquals("FooBar", $foobar->getShortName());
		$this->assertEquals($GLOBALS["moduleInfo"]["longName"], 
			$foobar->getLongName());
		$this->assertEquals($GLOBALS["moduleInfo"]["version"], 
			$foobar->getVersion());
		$this->assertEquals($GLOBALS["moduleInfo"]["authors"], 
			$foobar->getAuthors());
		$this->assertEquals("uncategorized", $foobar->getCategory());
		$this->assertFalse($foobar->isVersionChanged());
		$this->assertTrue($foobar->requirementsMet());
		
		$req = $foobar->getRequirements();
		$this->assertInternalType("array", $req);
		$this->assertEquals(2, count($req));
		$this->assertEquals(array(2, TRUE), $req["systemMajorVersion"]);
		$this->assertEquals(array("2.0.0 pre-alpha", TRUE), 
			$req["systemMinVersion"]);
		
		$this->assertFalse($foobar->isInstalled());
		$this->assertFalse($foobar->isEnabled());
	}
	
	public function testInstall() {
		$foobar = $this->modules->getModuleInfo("FooBar");
		
		$this->expectOutputString("Installed FooBar.\nEnabled FooBar.\n" . 
			"Disabled FooBar.\nUninstalled FooBar.\n");
		
		$foobar->install();
		$this->assertTrue($foobar->isInstalled());
		$this->assertFalse($foobar->isEnabled());
		
		$foobar->enable();
		$this->assertTrue($foobar->isInstalled());
		$this->assertTrue($foobar->isEnabled());
		$this->assertFalse($foobar->isVersionChanged());
		
		$foobar->disable();
		$this->assertTrue($foobar->isInstalled());
		$this->assertFalse($foobar->isEnabled());
		
		$foobar->uninstall();
		$this->assertFalse($foobar->isInstalled());
		$this->assertFalse($foobar->isEnabled());
	}
	
	public function testInstallImplicit() {
		$foobar = $this->modules->getModuleInfo("FooBar");
		
		$this->expectOutputString("Installed FooBar.\nEnabled FooBar.\n" . 
			"Disabled FooBar.\nUninstalled FooBar.\n");
		
		$foobar->enable();
		$this->assertTrue($foobar->isInstalled());
		$this->assertTrue($foobar->isEnabled());
		$this->assertFalse($foobar->isVersionChanged());
		
		$foobar->uninstall();
		$this->assertFalse($foobar->isInstalled());
		$this->assertFalse($foobar->isEnabled());
	}
	
	public function testConfig() {
		$foobar = $this->modules->getModuleInfo("FooBar");
		$conf = $this->services->get("config");
		
		$foobar->enable();
		
		// Set in module.
		$this->assertEquals(7, $conf->get("FooBar", "testing"));
		// Set in defaults.
		$this->assertEquals("baz", $conf->get("FooBar", "foo"));
		// Set in auto config.
		$this->assertEquals("thing", $conf->get("FooBar", "some"));
		
		$foobar->disable();
		
		$good = FALSE;
		try {
			$conf->get("FooBar", "testing");
		} catch (NotFoundException $e) {
			$good = TRUE;
		}
		$this->assertTrue($good);
	}
	
	public function testRun() {
		$_GET["testing"] = 1;
		$this->expectOutputString("Installed FooBar.\nEnabled FooBar.\n" .
			"Running FooBar.\n");
		
		$foobar = $this->modules->getModuleInfo("FooBar");
		$foobar->enable();
		$this->modules->runModules();
	}
	
	public function testVersionChange() {
		$this->expectOutputString("Installed FooBar.\nEnabled FooBar.\n" .
			"Disabled FooBar.\nUpdated FooBar from 0.8.0.\n" .
			"Enabled FooBar.\n");
		
		$foobar = $this->modules->getModuleInfo("FooBar");
		$foobar->enable();
		
		$this->services->get("database")->update("modules", array(
			"version" => "0.8.0"
		), array(
			"name" => "FooBar"
		));
		
		// Try twice - but only one time disabled.
		for ($i = 0; $i < 2; $i++) {
			$this->modules->refresh();
			
			$foobar = $this->modules->getModuleInfo("FooBar");
			$this->assertTrue($foobar->isInstalled());
			$this->assertFalse($foobar->isEnabled());
			$this->assertTrue($foobar->isVersionChanged());
			$this->assertEquals("0.9.0", $foobar->getVersion());
			$this->assertEquals("0.8.0", $foobar->getInstalledVersion());
		}
		
		$good = FALSE;
		try {
			$foobar->enable();
		} catch (EnableOutdatedModuleException $e) {
			$good = TRUE;
		}
		$this->assertTrue($good);
		
		$foobar->update();
		$foobar->enable();
		$this->assertTrue($foobar->isEnabled());
		$this->assertFalse($foobar->isVersionChanged());
	}
	
	/**
	 * @expectedException UseBB\System\ModuleManagement\UpdateToOlderVersionException
	 */
	public function testVersionChangeOlder() {
		$foobar = $this->modules->getModuleInfo("FooBar");
		$foobar->enable();
		
		$this->services->get("database")->update("modules", array(
			"version" => "2.0.0"
		), array(
			"name" => "FooBar"
		));
		
		$this->modules->refresh();
			
		$foobar = $this->modules->getModuleInfo("FooBar");
		$this->assertTrue($foobar->isInstalled());
		$this->assertFalse($foobar->isEnabled());
		$this->assertTrue($foobar->isVersionChanged());
		
		$foobar->update();
	}
	
	/**
	 * @expectedException UseBB\System\ModuleManagement\UnmetDependenciesException
	 */
	public function testUninstallable() {
		$foobar = $this->modules->getModuleInfo("Uninstallable");
		
		$this->assertFalse($foobar->requirementsMet());
		
		$req = $foobar->getRequirements();
		$this->assertInternalType("array", $req);
		$this->assertEquals(3, count($req));
		$this->assertEquals(array(9, FALSE), $req["systemMajorVersion"]);
		$this->assertEquals(array("9.3.1", FALSE), 
			$req["systemMinVersion"]);
		$this->assertEquals(array("9.0.0", FALSE), 
			$req["FooBar"]);
		
		$foobar->install();
	}
}
