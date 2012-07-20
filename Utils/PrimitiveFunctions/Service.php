<?php

namespace UseBB\Utils\PrimitiveFunctions;

/**
 * Primitive functions service
 * 
 * Wrapper around primitive PHP functions, especially those that have side
 * effects and have to be mocked in tests. E.g. setcookie, header, etc.
 * 
 * \author Dietrich Moerman
 */
class Service {
	public function __call($name, array $args) {
		return call_user_func_array($name, $args);
	}
}
