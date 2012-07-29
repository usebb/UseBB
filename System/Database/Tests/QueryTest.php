<?php

namespace UseBB\System\Database\Tests;

use UseBB\Tests\TestCase;
use UseBB\System\Database\Query;

class QueryTest extends TestCase {
	private $builder;
	private $prefix;
	private $query;
	
	protected function setUp() {
		$this->builder = $this->getMockWithoutConstructor(
			"Doctrine\DBAL\Query\QueryBuilder", 
			array("delete", "update", "from", "join", "innerJoin", "leftJoin", "rightJoin", "foobar"));
		$this->prefix = "prefix_";
		$this->query = new Query($this->builder, $this->prefix);
	}
	
	public function testDeleteNull() {
		$this->builder->expects($this->never())->method("delete");
		$this->assertEquals($this->query, $this->query->delete());
	}
	
	public function testDelete() {
		$this->builder->expects($this->once())->method("delete")->with(
			$this->equalTo($this->prefix . "foo"), $this->isNull());
		$this->assertEquals($this->query, $this->query->delete("foo"));
	}
	
	public function testDeleteAlias() {
		$this->builder->expects($this->once())->method("delete")->with(
			$this->equalTo($this->prefix . "foo"), $this->equalTo("f"));
		$this->assertEquals($this->query, $this->query->delete("foo", "f"));
	}
	
	public function testUpdateNull() {
		$this->builder->expects($this->never())->method("update");
		$this->assertEquals($this->query, $this->query->update());
	}
	
	public function testUpdate() {
		$this->builder->expects($this->once())->method("update")->with(
			$this->equalTo($this->prefix . "foo"), $this->isNull());
		$this->assertEquals($this->query, $this->query->update("foo"));
	}
	
	public function testUpdateAlias() {
		$this->builder->expects($this->once())->method("update")->with(
			$this->equalTo($this->prefix . "foo"), $this->equalTo("f"));
		$this->assertEquals($this->query, $this->query->update("foo", "f"));
	}
	
	public function testFrom() {
		$this->builder->expects($this->once())->method("from")->with(
			$this->equalTo($this->prefix . "foo"), $this->equalTo("f"));
		$this->assertEquals($this->query, $this->query->from("foo", "f"));
	}
	
	public function testJoin() {
		$this->builder->expects($this->once())->method("join")->with(
			$this->equalTo("b"), $this->equalTo($this->prefix . "foo"), 
			$this->equalTo("f"), $this->isNull());
		$this->assertEquals($this->query, $this->query->join("b", "foo", "f"));
	}
	
	public function testJoinCondition() {
		$this->builder->expects($this->once())->method("join")->with(
			$this->equalTo("b"), $this->equalTo($this->prefix . "foo"), 
			$this->equalTo("f"), $this->equalTo("cond"));
		$this->assertEquals($this->query, 
			$this->query->join("b", "foo", "f", "cond"));
	}
	
	public function testInnerJoin() {
		$this->builder->expects($this->once())->method("innerJoin")->with(
			$this->equalTo("b"), $this->equalTo($this->prefix . "foo"), 
			$this->equalTo("f"), $this->isNull());
		$this->assertEquals($this->query, 
			$this->query->innerJoin("b", "foo", "f"));
	}
	
	public function testInnerJoinCondition() {
		$this->builder->expects($this->once())->method("innerJoin")->with(
			$this->equalTo("b"), $this->equalTo($this->prefix . "foo"), 
			$this->equalTo("f"), $this->equalTo("cond"));
		$this->assertEquals($this->query, 
			$this->query->innerJoin("b", "foo", "f", "cond"));
	}
	
	public function testLeftJoin() {
		$this->builder->expects($this->once())->method("leftJoin")->with(
			$this->equalTo("b"), $this->equalTo($this->prefix . "foo"), 
			$this->equalTo("f"), $this->isNull());
		$this->assertEquals($this->query, 
			$this->query->leftJoin("b", "foo", "f"));
	}
	
	public function testLeftJoinCondition() {
		$this->builder->expects($this->once())->method("leftJoin")->with(
			$this->equalTo("b"), $this->equalTo($this->prefix . "foo"), 
			$this->equalTo("f"), $this->equalTo("cond"));
		$this->assertEquals($this->query, 
			$this->query->leftJoin("b", "foo", "f", "cond"));
	}
	
	public function testRightJoin() {
		$this->builder->expects($this->once())->method("rightJoin")->with(
			$this->equalTo("b"), $this->equalTo($this->prefix . "foo"), 
			$this->equalTo("f"), $this->isNull());
		$this->assertEquals($this->query, 
			$this->query->rightJoin("b", "foo", "f"));
	}
	
	public function testRightJoinCondition() {
		$this->builder->expects($this->once())->method("rightJoin")->with(
			$this->equalTo("b"), $this->equalTo($this->prefix . "foo"), 
			$this->equalTo("f"), $this->equalTo("cond"));
		$this->assertEquals($this->query, 
			$this->query->rightJoin("b", "foo", "f", "cond"));
	}
	
	public function testOthersSelf() {
		$this->builder->expects($this->once())->method("foobar")->with(
			$this->equalTo("baz"))->will($this->returnSelf());
		$this->assertEquals($this->query, $this->query->foobar("baz"));
	}
	
	public function testOthersValue() {
		$this->builder->expects($this->once())->method("foobar")->with(
			$this->equalTo("baz"))->will($this->returnValue("Foo-123"));
		$this->assertEquals("Foo-123", $this->query->foobar("baz"));
	}
}
