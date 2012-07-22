<?php

namespace UseBB\System\Database\Tests;

use UseBB\System\Database\Connection;
use Doctrine\DBAL\Schema\SchemaException;

/**
 * NOTE
 * 
 * This test case does not test all changes to tables, columns, indexes and such.
 * Since changes are applied with Doctrine DBAL, their tests should be run to
 * assert the correct working.
 */
class SchemaTest extends \PHPUnit_Framework_TestCase {
	private static $db;
	private static $schema;
	
	public static function setUpBeforeClass() {
		self::$db = new Connection($GLOBALS["dbConfig"]["testing"]);
		self::$schema = self::$db->getSchema();
	}
	
	protected function tearDown() {
		try {
			self::$schema->dropTable("test");
			self::$schema->commitChanges();
		} catch (SchemaException $e) {}
	}
	
	public static function tearDownAfterClass() {
		self::$db->close();
	}
	
	protected function installTestTable() {
		$table = self::$schema->createTable("test");
		$table->addColumn("id", "integer", array(
			"unsigned" => TRUE,
			"autoincrement" => TRUE
		));
		$table->addColumn("foo", "text");
		$table->setPrimaryKey(array("id"));
		self::$schema->commitChanges();
	}

	public function testCreate() {
		$table = self::$schema->createTable("test");
		$this->assertInstanceOf("Doctrine\DBAL\Schema\Table", $table);
		
		$table->addColumn("id", "integer", array(
			"unsigned" => TRUE,
			"autoincrement" => TRUE
		));
		$table->addColumn("foo", "text");
		$table->setPrimaryKey(array("id"));
		
		self::$schema->commitChanges();
		
		$table = self::$schema->getTable("test");
		$this->assertInstanceOf("Doctrine\DBAL\Schema\Table", $table);
		$this->assertEquals(self::$db->getPrefix() . "test", $table->getName());
		
		$id = $table->getColumn("id");
		$this->assertInstanceOf("Doctrine\DBAL\Schema\Column", $id);
		$this->assertEquals("integer", $id->getType()->getName());
		$this->assertTrue($id->getAutoincrement());
		
		$foo = $table->getColumn("foo");
		$this->assertInstanceOf("Doctrine\DBAL\Schema\Column", $foo);
		$this->assertEquals("text", $foo->getType()->getName());
		
		$this->assertTrue($table->hasPrimaryKey());
	}
	
	public function testRename() {
		$this->installTestTable();
		
		self::$schema->renameTable("test", "foobar");
		self::$schema->commitChanges();
		
		$table = self::$schema->getTable("foobar");
		$this->assertInstanceOf("Doctrine\DBAL\Schema\Table", $table);
		$this->assertEquals(self::$db->getPrefix() . "foobar", $table->getName());
		
		self::$schema->dropTable("foobar");
		self::$schema->commitChanges();
	}
	
	/**
	 * @expectedException Doctrine\DBAL\Schema\SchemaException
	 * @expectedExceptionMessage There is no table
	 */
	public function testRefresh() {
		$this->installTestTable();
		
		self::$schema->renameTable("test", "foobar");
		self::$schema->refresh();
		self::$schema->getTable("foobar");		
	}
	
	/**
	 * @expectedException Doctrine\DBAL\Schema\SchemaException
	 * @expectedExceptionMessage There is no table
	 */
	public function testDrop() {
		$this->installTestTable();
		
		self::$schema->dropTable("test");
		self::$schema->commitChanges();
		
		self::$schema->getTable("test");
	}
	
	public function testRollback() {
		$this->installTestTable();
		
		self::$schema->renameTable("test", "foobar");
		self::$schema->rollback();
		self::$schema->commitChanges();
		
		$table = self::$schema->getTable("test");
		$this->assertInstanceOf("Doctrine\DBAL\Schema\Table", $table);
	}
}
