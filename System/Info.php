<?php

namespace UseBB\System;

use Doctrine\DBAL\Version as DBAL;

/**
 * System information.
 * 
 * %Info on the current UseBB set-up, system, etc.
 * 
 * \author Dietrich Moerman
 */
class Info extends ServiceAccessor {
	/**
	 * UseBB's major version
	 */
	const MAJOR_VERSION = 2;
	
	/**
	 * UseBB's version string
	 */
	const VERSION = "2.0.0 pre-alpha";
	
	/**
	 * Get UseBB's major version.
	 * 
	 * \returns Major ersion
	 */
	public function getMajorUseBBVersion() {
		return self::MAJOR_VERSION;
	}
	
	/**
	 * Get UseBB's version string.
	 * 
	 * \returns Version
	 */
	public function getUseBBVersion() {
		return self::VERSION;
	}
	
	/**
	 * Get library versions
	 * 
	 * \returns Version array
	 */
	public function getLibraryVersions() {
		return array(
			"Doctrine" => DBAL::VERSION
		);
	}
	
	/**
	 * Compare a version number to the current.
	 * 
	 * \param $version Version number
	 * \param $operator Operator
	 * \returns Boolean
	 */
	public function compareVersion($version, $operator = "<=") {
		return version_compare($version, self::VERSION, $operator);
	}
}
