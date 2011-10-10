<?php

namespace UseBB\System\Navigation;

use UseBB\System\UseBBException;

/**
 * No registered request found exception.
 * 
 * Thrown when no request could be found for the specified controller and name.
 * 
 * \author Dietrich Moerman
 */
class NoRegisteredRequestException extends UseBBException {
	private $controller;
	private $name;
	
	/**
	 * Constructor.
	 * 
	 * \param $controller Controller
	 * \param $name Name
	 */
	public function __construct($controller, $name) {
		if (empty($name)) {
			$name = "-";
		}
		
		parent::__construct(sprintf("No request registered for %s with name %s.",
			$controller, $name));
		
		$this->controller = $controller;
		$this->name = $name;
	}
	
	/**
	 * Get the controller.
	 * 
	 * \returns Controller
	 */
	public function getController() {
		return $this->controller;
	}
	
	/**
	 * Get the name.
	 * 
	 * \returns Name
	 */
	public function getName() {
		return $this->name;
	}
}
