<?php

namespace UseBB\Utils\SchemaManagement\Tests;

use UseBB\System\ServiceRegistry;
use UseBB\Utils\SchemaManagement\SystemSchema;
use Doctrine\DBAL\Schema\SchemaException;

class DatabaseTest extends \PHPUnit_Framework_TestCase {
	protected static $services;
	
	public static function setUpBeforeClass() {
		self::$services = new ServiceRegistry($GLOBALS["dbConfig"]);
	}
	
	public function testInstallSchema() {
		$systemSchema = new SystemSchema(self::$services);
		$systemSchema->install();
		
		$schema = self::$services->get("database")->getSchema();
		$tables = array("config", "modules");
		
		foreach ($tables as $table) {
			$this->assertInstanceOf("Doctrine\DBAL\Schema\Table",
				$schema->getTable($table));
		}
		
		$systemSchema->uninstall();
		$schema->refresh();
		
		foreach ($tables as $table) {
			$good = FALSE;
			try {
				$schema->getTable($table);
			} catch (SchemaException $e) {
				$good = TRUE;
			}
			
			$this->assertTrue($good);
		}
	}
}
