<?php

namespace UseBB\System\Tests;

use UseBB\System\ServiceRegistry;

class InfoTest extends \PHPUnit_Framework_TestCase {
	protected $info;
	
	protected function setUp() {
		$services = new ServiceRegistry($GLOBALS["dbConfig"]);
		$this->info = $services->get("info");
	}
	
	public function testInfo() {
		$major = $this->info->getMajorUseBBVersion();
		
		$this->assertInternalType("int", $major);
		$this->assertEquals(2, $major);
		
		$version = $this->info->getUseBBVersion();
		
		$this->assertInternalType("string", $version);
		$this->assertTrue(strlen($version) >= 5);
		
		$versions = $this->info->getLibraryVersions();
		
		$this->assertInternalType("array", $versions);
		$this->assertTrue(count($versions) > 0);
	}
}
