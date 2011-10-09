<?php

namespace UseBB\System\Database\Tests;

use UseBB\System\Database\Connection;
use Doctrine\DBAL\Schema\SchemaException;

class DatabaseTest extends \PHPUnit_Framework_TestCase {
	private static $db;
	private static $schema;
	
	public static function setUpBeforeClass() {
		self::$db = new Connection($GLOBALS["dbConfig"]);
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

	public function testVarious() {
		if (!empty($GLOBALS["dbConfig"]["prefix"])) {
			$this->assertEquals($GLOBALS["dbConfig"]["prefix"], 
				self::$db->getPrefix());
		}
		$this->assertInstanceOf("UseBB\System\Database\Schema",
			self::$db->getSchema());
	}
	
	public function testInsertAndSelect() {
		$this->installTestTable();
		
		self::$db->insert("test", array(
			"foo" => "bar"
		));
		
		$query = self::$db->newQuery();
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
		
		self::$db->insert("test", array(
			"foo" => "bar"
		));
		self::$db->insert("test", array(
			"foo" => "foobar"
		));
		
		$query = self::$db->newQuery();
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
		
		self::$db->insert("test", array(
			"foo" => "bar"
		));
		self::$db->insert("test", array(
			"foo" => "foobar"
		));
		
		$num = self::$db->update("test", array(
			"foo" => "baz"
		), array(
			"foo" => "bar"
		));
		$this->assertEquals(1, $num);
		
		$query = self::$db->newQuery();
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
		
		self::$db->insert("test", array(
			"foo" => "bar"
		));
		self::$db->insert("test", array(
			"foo" => "foobar"
		));
		
		$num = self::$db->delete("test", array(
			"foo" => "bar"
		));
		$this->assertEquals(1, $num);
		
		$query = self::$db->newQuery();
		$stmt = $query->select("*")->from("test", "t")->execute();
		$this->assertEquals(1, $stmt->rowCount());
		
		$result = $stmt->fetch();
		$this->assertEquals(2, $result["id"]);
		$this->assertEquals("foobar", $result["foo"]);
	}
	
	public function testUTF8() {
		$this->installTestTable();
		
		self::$db->insert("test", array(
			"foo" => "a^üPÑç&"
		));
		
		$query = self::$db->newQuery();
		$result = $query->select("*")->from("test", "t")->execute()->fetch();
		
		$this->assertEquals("a^üPÑç&", $result["foo"]);
	}
	
	public function testUTF8WithNewColumn() {
		$this->installTestTable();
		
		self::$schema->getTable("test")->addColumn("bar", "string");
		self::$schema->commitChanges();
		
		self::$db->insert("test", array(
			"foo" => "foo",
			"bar" => "a^üPÑç&"
		));
		
		$query = self::$db->newQuery();
		$result = $query->select("*")->from("test", "t")->execute()->fetch();
		
		$this->assertEquals("a^üPÑç&", $result["bar"]);
	}
	
	public function testDateField() {
		$table = self::$schema->createTable("test");
		$table->addColumn("id", "integer", array(
			"unsigned" => TRUE,
			"autoincrement" => TRUE
		));
		$table->addColumn("foo", "datetime");
		$table->setPrimaryKey(array("id"));
		self::$schema->commitChanges();
		
		$date = new \DateTime();
		self::$db->insert("test", array(
			"foo" => $date
		), TRUE);
		
		$result = self::$db->newQuery()->select("*")
			->from("test", "t")->execute()->fetch();
		$date2 = new \DateTime($result["foo"]);
		$this->assertEquals($date, $date2);
	}
}
