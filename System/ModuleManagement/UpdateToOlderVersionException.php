<?php

namespace UseBB\System\ModuleManagement;

use UseBB\System\UseBBException;

/**
 * Update to older version exception.
 *
 * Thrown when a module is being updated to an older version.
 *
 * \author Dietrich Moerman
 */
class UpdateToOlderVersionException extends UseBBException {
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
		parent::__construct(sprintf("Module '%s' can not be updated to an "
			. "older version %s from %s.", $name, $newVersion, $curVersion));

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
