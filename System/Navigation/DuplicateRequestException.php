<?php

namespace UseBB\System\Navigation;

use UseBB\System\UseBBException;

/**
 * Duplicate request exception.
 * 
 * Thrown when more than one handler is set for the same request.
 * 
 * \author Dietrich Moerman
 */
class DuplicateRequestException extends UseBBException {
	private $request;
	
	/**
	 * Constructor.
	 * 
	 * \param $request Request
	 */
	public function __construct($request) {
		parent::__construct(sprintf(
			"More than one handler is being set for request '%s'.", 
			$this->requestToString($request)));
		
		$this->request = $request;
	}
	
	/**
	 * Request to string
	 * 
	 * \param $request Request
	 * \returns String
	 */
	private function requestToString($request) {
		$string = "(";
		
		foreach ($request as $k => $v) {
			if (is_int($k)) {
				$string .= $v . ", ";
			} else {
				$string .= $k . " => " . $v . ", ";
			}
		}
		
		$string = substr($string, 0, -2) . ")";
		
		return $string;
	}
	
	/**
	 * Get the request.
	 * 
	 * \returns Request
	 */
	public function getRequest() {
		return $this->request;
	}
}
