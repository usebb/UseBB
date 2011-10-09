<?php

namespace UseBB\System\ModuleManagement;

use UseBB\System\UseBBException;

/**
 * Enable outdated module exception.
 *
 * Thrown when an outdated module is being enabled.
 *
 * \author Dietrich Moerman
 */
class EnableOutdatedModuleException extends UseBBException {
	private $name;
	private $curVersion;
	private $newVersion;
	
	/**
	 * Constructor.
	 *
	 * \param $name Name
	 * \param $newVersion New version
	 * \param $curVersion Current version
	 */
	public function __construct($name, $newVersion, $curVersion) {
		parent::__construct(sprintf("Outdated module '%s' can not be enabled "
			. "(new version: %s, current version: %s). Please update first.", 
			$name, $newVersion, $curVersion));

		$this->name = $name;
		$this->curVersion = $curVersion;
		$this->newVersion = $newVersion;
	}

	/**
	 * Get the name.
	 *
	 * \returns Name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Get the current version.
	 *
	 * \returns Current version
	 */
	public function getCurrentVersion() {
		return $this->curVersion;
	}

	/**
	 * Get the new version.
	 *
	 * \returns New version
	 */
	public function getNewVersion() {
		return $this->newVersion;
	}
}
