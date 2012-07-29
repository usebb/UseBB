<?php

namespace UseBB\System\ModuleManagement\Tests;

use UseBB\Tests\TestCase;
use UseBB\System\ModuleManagement\EnableOutdatedModuleException;

require USEBB_ROOT_PATH . "/Modules/FooBar/moduleInfo.php";

class ModulesTest extends TestCase {
	protected $modules;
	
	protected function setUp() {
		$this->newServices();
		$this->modules = $this->getService("modules");
		$this->beginTransaction();
	}
	
	protected function tearDown() {
		$this->rollback();
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
		$this->expectOutputString("Installed FooBar.\nEnabled FooBar.\n" . 
			"Disabled FooBar.\nUninstalled FooBar.\n");
		
		$foobar = $this->modules->getModuleInfo("FooBar");
		
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
		$this->expectOutputString("Installed FooBar.\nEnabled FooBar.\n" . 
			"Disabled FooBar.\nUninstalled FooBar.\n");
		
		$foobar = $this->modules->getModuleInfo("FooBar");
		
		$foobar->enable();
		$this->assertTrue($foobar->isInstalled());
		$this->assertTrue($foobar->isEnabled());
		$this->assertFalse($foobar->isVersionChanged());
		
		$foobar->uninstall();
		$this->assertFalse($foobar->isInstalled());
		$this->assertFalse($foobar->isEnabled());
	}
	
	/**
	 * @expectedException UseBB\Utils\Config\NotFoundException
	 */
	public function testConfig() {
		$this->expectOutputString("Installed FooBar.\nEnabled FooBar.\n" . 
			"Disabled FooBar.\n");
		
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
		$conf->get("FooBar", "testing");
	}
	
	public function testRun() {
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
		$this->expectOutputString("Installed FooBar.\nEnabled FooBar.\n" . 
			"Disabled FooBar.\n");
		
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
