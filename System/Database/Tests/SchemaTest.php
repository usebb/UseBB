<?php

namespace UseBB\System\Database\Tests;

use UseBB\Tests\TestCase;
use UseBB\System\Database\Connection;
use Doctrine\DBAL\Schema\SchemaException;

/**
 * NOTE
 * 
 * This test case does not test all changes to tables, columns, indexes and such.
 * Since changes are applied with Doctrine DBAL, their tests should be run to
 * assert the correct working.
 */
class SchemaTest extends TestCase {
	private $db;
	private $schema;
	
	public function setUp() {
		$this->db = new Connection($GLOBALS["dbConfig"]["testing"]);
		$this->schema = $this->db->getSchema();
	}
	
	protected function tearDown() {
		try {
			$this->schema->dropTable("test");
			$this->schema->commitChanges();
		} catch (SchemaException $e) {}
		$this->db->close();
	}
	
	protected function installTestTable() {
		$table = $this->schema->createTable("test");
		$table->addColumn("id", "integer", array(
			"unsigned" => TRUE,
			"autoincrement" => TRUE
		));
		$table->addColumn("foo", "text");
		$table->setPrimaryKey(array("id"));
		$this->schema->commitChanges();
	}

	public function testCreate() {
		$table = $this->schema->createTable("test");
		$this->assertInstanceOf("Doctrine\DBAL\Schema\Table", $table);
		
		$table->addColumn("id", "integer", array(
			"unsigned" => TRUE,
			"autoincrement" => TRUE
		));
		$table->addColumn("foo", "text");
		$table->setPrimaryKey(array("id"));
		
		$this->schema->commitChanges();
		
		$table = $this->schema->getTable("test");
		$this->assertInstanceOf("Doctrine\DBAL\Schema\Table", $table);
		$this->assertEquals($this->db->getPrefix() . "test", $table->getName());
		
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
		
		$this->schema->renameTable("test", "foobar");
		$this->schema->commitChanges();
		
		$table = $this->schema->getTable("foobar");
		$this->assertInstanceOf("Doctrine\DBAL\Schema\Table", $table);
		$this->assertEquals($this->db->getPrefix() . "foobar", $table->getName());
		
		$this->schema->dropTable("foobar");
		$this->schema->commitChanges();
	}
	
	/**
	 * @expectedException Doctrine\DBAL\Schema\SchemaException
	 * @expectedExceptionMessage There is no table
	 */
	public function testRefresh() {
		$this->installTestTable();
		
		$this->schema->renameTable("test", "foobar");
		$this->schema->refresh();
		$this->schema->getTable("foobar");		
	}
	
	/**
	 * @expectedException Doctrine\DBAL\Schema\SchemaException
	 * @expectedExceptionMessage There is no table
	 */
	public function testDrop() {
		$this->installTestTable();
		
		$this->schema->dropTable("test");
		$this->schema->commitChanges();
		
		$this->schema->getTable("test");
	}
	
	public function testRollback() {
		$this->installTestTable();
		
		$this->schema->renameTable("test", "foobar");
		$this->schema->rollback();
		$this->schema->commitChanges();
		
		$table = $this->schema->getTable("test");
		$this->assertInstanceOf("Doctrine\DBAL\Schema\Table", $table);
	}
}
