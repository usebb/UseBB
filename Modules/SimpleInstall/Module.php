<?php

namespace UseBB\Modules\SimpleInstall;

use UseBB\System\AbstractModule;

/**
 * Simple %Installer module.
 * 
 * \author Dietrich Moerman
 */
class Module extends AbstractModule {
	public function runForHTTP() {
		$nav = $this->getService("navigation");
		
		$nav->register(array(), "UseBB\Modules\SimpleInstall\Installer");
	}
}
