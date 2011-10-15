<?php

namespace UseBB\Utils\File\Tests;

use UseBB\Utils\File\InfoFile;

class FileTest extends \PHPUnit_Framework_TestCase {
	public function testExistingInfoFile() {
		$file = new InfoFile(__DIR__ . "/testFile.php");
		$info = $file->getInfo();
		
		$this->assertInternalType("array", $info);
		$this->assertEquals(2, count($info));
		$this->assertEquals("bar", $info["foo"]);
	}
	
	public function testExistingInfoFile2() {
		$file = new InfoFile(__DIR__ . "/testFile2.php", "foobar");
		$info = $file->getInfo();
		
		$this->assertInternalType("array", $info);
		$this->assertEquals(2, count($info));
		$this->assertEquals("bar", $info["foo"]);
	}
	
	public function testUnexistingInfoFile() {
		$file = new InfoFile(__DIR__ . "/foobar.php");
		$info = $file->getInfo();
		
		$this->assertInternalType("array", $info);
		$this->assertEquals(0, count($info));
	}
	
	public function testUnexistingRootInfoFile() {
		$file = new InfoFile(__DIR__ . "/testFile.php", "foobar");
		$info = $file->getInfo();
		
		$this->assertInternalType("array", $info);
		$this->assertEquals(0, count($info));
	}
	
	public function testUnexistingRootInfoFile2() {
		$file = new InfoFile(__DIR__);
		$info = $file->getInfo();
		
		$this->assertInternalType("array", $info);
		$this->assertEquals(0, count($info));
	}
}
