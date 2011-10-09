<?php

namespace UseBB\System\Navigation;

use UseBB\System\UseBBException;

/**
 * No controller found exception.
 * 
 * Thrown when no suitable handler was found for the command set.
 * 
 * \author Dietrich Moerman
 */
class NoControllerFoundException extends UseBBException {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct("No controller found to handle this request.");
	}
}
