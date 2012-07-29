<?php

namespace UseBB\System\Plugins\Tests;

use UseBB\Tests\TestCase;
use UseBB\System\Plugins\PluginRunningClass;
use UseBB\System\Plugins\Context;

class PluginTestClass extends PluginRunningClass {
	public function test() {
		$plugins = $this->getService("plugins");
		$plugins->register("System\Plugins\Tests\PluginTestClass", "coll",
			function() {
				return TRUE;
			});
		$plugins->register("System\Plugins\Tests\PluginTestClass", "coll",
			function() {
				return TRUE;
			});
		$plugins->register("System\Plugins\Tests\PluginTestClass", "coll",
			function() {
				return FALSE;
			});

		return array(
			$this->runPlugins("coll"),
			$this->runPluginsCollectAnd("coll"),
			$this->runPluginsCollectOr("coll"),
			$this->runPluginsCollectNum("coll")
		);
	}
}

class PluginTest extends TestCase {
	protected $plugins;
	protected $testClass;

	protected function setUp() {
		$this->newServices();
		$this->plugins = $this->getService("plugins");
		$this->testClass = new PluginTestClass($this->getServices());
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage No callback passed.
	 */
	public function testRegisterNoCallback() {
		$this->plugins->register("Foo", "bar", "baz");
	}

	public function testSimplePlugin() {
		$registry = $this->plugins->getRegistry("Foo");

		$this->plugins->register("Foo", "testSimplePlugin", 
			function($c) { 
				return $c->get("x") . $c->get("y");
			});
		$this->assertEquals("foobar", $registry->run("testSimplePlugin", 
			array("x" => "foo", "y" => "bar"), NULL, FALSE, NULL));
	}

	public function testUnexisting() {
		$registry = $this->plugins->getRegistry("Bar");

		$this->plugins->register("Bar", "testSimplePlugin", 
			function() { 
				return TRUE;
			});

		$this->assertNull($registry->run("unexisting", 
			array(), NULL, FALSE, NULL));

		$registry = $this->plugins->getRegistry("Unexisting");

		$this->assertNull($registry->run("testSimplePlugin", 
			array(), NULL, FALSE, NULL));
	}

	public function testCorrectPlugins() {
		$registry = $this->plugins->getRegistry("Baz");

		$this->plugins->register("Baz", "testSimplePlugin", 
			function($c) { 
				return $c->get("x") . $c->get("y");
			});
		$this->plugins->register("Baz", "testOtherPlugin", 
			function() { 
				return "foo";
			});
		$this->assertEquals("foobar", $registry->run("testSimplePlugin", 
			array("x" => "foo", "y" => "bar"), NULL, FALSE, NULL));
	}

	public function testResultPassing() {
		$registry = $this->plugins->getRegistry("X");

		$this->plugins->register("X", "pass",
			function($c) {
				return $c->getResult() + 1;
			});
		$this->plugins->register("X", "pass",
			function($c) {
				return $c->getResult() + 1;
			});
		$this->assertEquals(2, $registry->run("pass", 
			array("foo" => "foo"), 0, FALSE, NULL));
	}

	public function testResultCollecting() {
		$registry = $this->plugins->getRegistry("Y");

		$this->plugins->register("Y", "coll",
			function($c) {
				$res = $c->getResult();
				return end($res) + 1;
			});
		$this->plugins->register("Y", "coll",
			function($c) {
				$res = $c->getResult();
				return end($res) + 1;
			});
		$this->assertEquals(array(1, 2), 
			$registry->run("coll", array(), NULL, TRUE, NULL));
	}

	public function testResultReducer() {
		$registry = $this->plugins->getRegistry("Z");

		$this->plugins->register("Z", "red",
			function() {
				return 2;
			});
		$this->plugins->register("Z", "red",
			function() {
				return 3;
			});
		$this->assertEquals(12, $registry->run("red", array(), 2, TRUE, 
			function($xs, $x) { 
				return $xs * $x; 
			}));
	}

	public function testResultSingleReducer() {
		$registry = $this->plugins->getRegistry("Q");

		$this->plugins->register("Q", "single",
			function() {
				return 2;
			});
		
		$this->assertEquals(2, $registry->run("single", array(), NULL, TRUE, 
			function($xs, $x) { 
				return 5; 
			}));
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage No callable reducer passed.
	 */
	public function testInvalidReducer() {
		$registry = $this->plugins->getRegistry("Q");
		
		$this->plugins->register("Q", "foo",
			function() {
				return 1;
			});
		
		$registry->run("foo", array(), 1, TRUE, "bar");
	}

	public function testTheClass() {
		$results = $this->testClass->test();

		$this->assertInternalType("bool", $results[0]);
		$this->assertEquals(FALSE, $results[0]);

		$this->assertInternalType("bool", $results[1]);
		$this->assertEquals(FALSE, $results[1]);

		$this->assertInternalType("bool", $results[2]);
		$this->assertEquals(TRUE, $results[2]);

		$this->assertInternalType("int", $results[3]);
		$this->assertEquals(2, $results[3]);
	}

	public function testPriority() {
		$registry = $this->plugins->getRegistry("Test");

		$this->plugins->register("Test", "test", 
			function() {
				return 2;
			});
		$this->plugins->register("Test", "test", 
			function() {
				return 1;
			}, \UseBB\System\Plugins\Registry::PRIORITY_HIGH);
		$this->plugins->register("Test", "test", 
			function() {
				return 2;
			});

		$this->assertEquals(array(1, 2, 2), 
			$registry->run("test", array(), NULL, TRUE, NULL));
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Given priority not understood.
	 */
	public function testWrongPriority() {
		$this->plugins->register("Foo", "bar", function() {}, 99);
	}

	public function testMissingArg() {
		$registry = $this->plugins->getRegistry("M");

		$this->plugins->register("M", "test",
			function($c) {
				return $c->get("foo", 5);
			});
		$this->assertEquals(5, $registry->run("test", array(), 
			NULL, FALSE, NULL));
	}

	public function testSetResultsOnContext() {
		$registry = $this->plugins->getRegistry("P");

		$this->plugins->register("P", "test1",
			function($c) {
				$c->saveResult(array(
					$c->collectsResults(),
					5
				));
			});
		$this->assertEquals(array(FALSE, 5), 
			$registry->run("test1", array(), NULL, FALSE, NULL));

		$this->plugins->register("P", "test2",
			function($c) {
				$c->saveResult($c->collectsResults());
				$c->saveMultipleResults(array("foo", "bar"));
			});
		$this->plugins->register("P", "test2",
			function($c) {
				$c->saveMultipleResults(array("baz"));
			});
		$this->assertEquals(array(TRUE, "foo", "bar", "baz"), 
			$registry->run("test2", array(), NULL, TRUE, NULL));
	}

	public function testSetMultipleResultsWithoutCollecting() {
		$registry = $this->plugins->getRegistry("R");

		$this->plugins->register("R", "test3",
			function($c) {
				$c->saveMultipleResults(array("foo", "bar"));
			});
		$this->assertEquals("foo", $registry->run("test3", array(), 
			NULL, FALSE, NULL));
	}
	
	public function testContextSpecials() {
		$args = array("foo" => 1, "bar" => 2);
		$c = new Context($args, array(3), TRUE);
		
		$this->assertEquals($args, $c->getAll());
		$this->assertEquals(array(3), $c->getResults());
		$this->assertNull($c->saveMultipleResults(array()));
	}
}
