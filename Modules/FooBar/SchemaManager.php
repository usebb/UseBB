<?php

namespace UseBB\Modules\FooBar;

use UseBB\Utils\SchemaManagement\AbstractSchemaManager;

/**
 * FooBar schema manager.
 * 
 * \author Dietrich Moerman
 */
class SchemaManager extends AbstractSchemaManager {
	public function install() {
		$this->setSchemaVersion();
		echo "Installed FooBar.\n";
	}
	
	public function uninstall() {
		echo "Uninstalled FooBar.\n";
	}
}
