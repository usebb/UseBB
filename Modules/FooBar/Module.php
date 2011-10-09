<?php

namespace UseBB\Modules\FooBar;

use UseBB\System\AbstractModule;

/**
 * FooBar test module.
 * 
 * \note Unit testing only.
 * 
 * \author Dietrich Moerman
 */
class Module extends AbstractModule {
	public function runForHTTP() {
		if ($this->getService("input")->key("testing")->hasValue()) {
			echo "Running FooBar.\n";
		}
	}
	
	public function getSchemaManager() {
		return new SchemaManager($this->getServiceRegistry());
	}
	
	public function enable() {
		$this->getService("config")->set("FooBar", "testing", 7);
		echo "Enabled FooBar.\n";
	}
	
	public function disable() {
		$this->getService("config")->delete("FooBar", "testing");
		echo "Disabled FooBar.\n";
	}
	
	public function update($oldVersion) {
		echo "Updated FooBar from " . $oldVersion . ".\n";
	}
}
