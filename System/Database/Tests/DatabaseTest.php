<?php

namespace UseBB\System\Database\Tests;

use UseBB\System\Database\Connection;
use Doctrine\DBAL\Schema\SchemaException;

class DatabaseTest extends \PHPUnit_Framework_TestCase {
	private $db;
	private $schema;
	
	protected function setUp() {
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
	
	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Missing database connection info.
	 */
	public function testWrongDbConfig() {
		new Connection(array("foo" => "bar"));
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Unknown database type.
	 */
	public function testWrongDbConfig2() {
		new Connection(array("type" => "unexisting_driver"));
	}
	
	protected function installTestTable() {
		$table = $this->schema->createTable("test");
		$table->addColumn("id", "integer", array(
			"unsigned" => TRUE,
			"autoincrement" => TRUE
		));
		$table->addColumn("foo", "text");
		$table->addColumn("date", "datetime", array(
			"notnull" => FALSE
		));
		$table->setPrimaryKey(array("id"));
		$this->schema->commitChanges();
	}

	public function testVarious() {
		if (!empty($GLOBALS["dbConfig"]["testing"]["prefix"])) {
			$this->assertEquals($GLOBALS["dbConfig"]["testing"]["prefix"], 
				$this->db->getPrefix());
		}
		$this->assertInstanceOf("UseBB\System\Database\Schema",
			$this->db->getSchema());
	}
	
	public function testInsertAndSelect() {
		$this->installTestTable();
		
		$this->db->insert("test", array(
			"foo" => "bar"
		));
		
		$query = $this->db->newQuery();
		$this->assertInstanceOf("UseBB\System\Database\Query", $query);
		
		$stmt = $query->select("*")->from("test", "t")->execute();
		$this->assertInstanceOf("Doctrine\DBAL\Driver\Statement", $stmt);
		$this->assertEquals(1, $stmt->rowCount());
		
		$result = $stmt->fetch();
		$this->assertEquals(1, $result["id"]);
		$this->assertEquals("bar", $result["foo"]);
	}
	
	public function testSelectWhere() {
		$this->installTestTable();
		
		$this->db->insert("test", array(
			"foo" => "bar"
		));
		$this->db->insert("test", array(
			"foo" => "foobar"
		));
		
		$query = $this->db->newQuery();
		$stmt = $query->select("t.id")->from("test", "t")
			->where("t.foo = :value")->setParameter(":value", "bar")
			->execute();
		$this->assertEquals(1, $stmt->rowCount());
		
		$result = $stmt->fetch();
		$this->assertEquals(1, $result["id"]);
		$this->assertFalse(array_key_exists("foo", $result));
	}
	
	public function testUpdate() {
		$this->installTestTable();
		
		$this->db->insert("test", array(
			"foo" => "bar"
		));
		$this->db->insert("test", array(
			"foo" => "foobar"
		));
		
		$num = $this->db->update("test", array(
			"foo" => "baz"
		), array(
			"foo" => "bar"
		));
		$this->assertEquals(1, $num);
		
		$query = $this->db->newQuery();
		$stmt = $query->select("*")->from("test", "t")->execute();
		$this->assertEquals(2, $stmt->rowCount());
		
		$result = $stmt->fetch();
		$this->assertEquals(1, $result["id"]);
		$this->assertEquals("baz", $result["foo"]);
		
		$result = $stmt->fetch();
		$this->assertEquals(2, $result["id"]);
		$this->assertEquals("foobar", $result["foo"]);
	}
	
	public function testDelete() {
		$this->installTestTable();
		
		$this->db->insert("test", array(
			"foo" => "bar"
		));
		$this->db->insert("test", array(
			"foo" => "foobar"
		));
		
		$num = $this->db->delete("test", array(
			"foo" => "bar"
		));
		$this->assertEquals(1, $num);
		
		$query = $this->db->newQuery();
		$stmt = $query->select("*")->from("test", "t")->execute();
		$this->assertEquals(1, $stmt->rowCount());
		
		$result = $stmt->fetch();
		$this->assertEquals(2, $result["id"]);
		$this->assertEquals("foobar", $result["foo"]);
	}
	
	public function testUTF8() {
		$this->installTestTable();
		
		$this->db->insert("test", array(
			"foo" => "a^üPÑç&"
		));
		
		$query = $this->db->newQuery();
		$result = $query->select("*")->from("test", "t")->execute()->fetch();
		
		$this->assertEquals("a^üPÑç&", $result["foo"]);
	}
	
	public function testUTF8WithNewColumn() {
		$this->installTestTable();
		
		$this->schema->getTable("test")->addColumn("bar", "string");
		$this->schema->commitChanges();
		
		$this->db->insert("test", array(
			"foo" => "foo",
			"bar" => "a^üPÑç&"
		));
		
		$query = $this->db->newQuery();
		$result = $query->select("*")->from("test", "t")->execute()->fetch();
		
		$this->assertEquals("a^üPÑç&", $result["bar"]);
	}
	
	public function testDateField() {
		$this->installTestTable();
		
		$date = new \DateTime();
		$this->db->insert("test", array(
			"foo" => "bar",
			"date" => $date
		), TRUE);
		
		$result = $this->db->newQuery()->select("*")
			->from("test", "t")->execute()->fetch();
		$date2 = new \DateTime($result["date"]);
		$this->assertEquals($date, $date2);
		
		$date3 = new \DateTime("2010-01-01");		
		$this->db->update("test", array(
			"date" => $date3
		), array(
			"id" => $result["id"]
		), TRUE);
		
		$result = $this->db->newQuery()->select("*")
			->from("test", "t")->execute()->fetch();
		$date4 = new \DateTime($result["date"]);
		$this->assertEquals($date3, $date4);
	}
	
	public function testTransactionCommit() {
		$this->installTestTable();
		
		$this->db->beginTransaction();
		$this->db->insert("test", array(
			"foo" => "bar"
		));
		$this->db->commit();
		
		$query = $this->db->newQuery();		
		$stmt = $query->select("*")->from("test", "t")->execute();
		$this->assertEquals(1, $stmt->rowCount());
	}
	
	public function testTransactionRollback() {
		$this->installTestTable();
		
		$this->db->beginTransaction();
		$this->db->insert("test", array(
			"foo" => "bar"
		));
		$this->db->rollback();
		
		$query = $this->db->newQuery();		
		$stmt = $query->select("*")->from("test", "t")->execute();
		$this->assertEquals(0, $stmt->rowCount());
	}
	
	public function testTransactionalCommit() {
		$this->installTestTable();
		
		$this->db->transactional(function() {
			$this->db->insert("test", array(
				"foo" => "bar"
			));
		});
		
		$query = $this->db->newQuery();		
		$stmt = $query->select("*")->from("test", "t")->execute();
		$this->assertEquals(1, $stmt->rowCount());
	}
	
	public function testTransactionalRollback() {
		$this->installTestTable();
		
		try {
			$this->db->transactional(function() {
				$this->db->insert("test", array(
					"foo" => "bar"
				));
				throw new \Exception("foo");
			});
		} catch (\Exception $e) {}
		
		$query = $this->db->newQuery();		
		$stmt = $query->select("*")->from("test", "t")->execute();
		$this->assertEquals(0, $stmt->rowCount());
	}
}
