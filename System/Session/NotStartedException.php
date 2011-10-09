<?php

namespace UseBB\System\Session;

use UseBB\System\UseBBException;

/**
 * %Session not started exception.
 * 
 * Thrown when a session has not been started but system is trying to access
 * properties.
 * 
 * \author Dietrich Moerman
 */
class NotStartedException extends UseBBException {
	private $key;
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct("Session has not been started yet.");
	}
}
