<?php

namespace UseBB\System\Context;

/**
 * Command Line Interface context.
 * 
 * \author Dietrich Moerman
 */
class CLI extends AbstractContext {
	public function handleRequest() {
	 	echo "UseBB version " . 
	 		$this->getService("info")->getUseBBVersion() . "\n";
		echo str_repeat("=", 29) . "\n\n";
		echo "The CLI interface is not available in this version.\n";
	}
	
	public function getLanguage(array $available) {
		// TODO
		return "en";
	}
	
	public function escapeString($string) {
		// TODO
		return $string;
	}
	
	public function applyArgumentsToString($string, array $args) {
		// TODO
		return $string;
	}
	
	public function generateLink(array $params) {
		// TODO
		return "";
	}

	/**
	 * PHP error handler.
	 *
	 * \attention Do not call this method yourself.
	 *
	 * \param $number Error type
	 * \param $string Description
	 * \param $file File
	 * \param $line Line number
	 * \param $context Context
	 *
	 * \hook{error, number\, string\, file\, line\, context} \hookEndsMethodOnFalse
	 */
	public function handleError($number, $string, $file, $line, 
		array $context) {
		if (parent::handleError($number, $string, $file, $line, $context) 
			=== FALSE) {
			return;
		}
		
		printf("Error '%s' at %s:%d.\n", $string, $file, $line);
	}

	/**
	 * Uncaught exception handler.
	 *
	 * \attention Do not call this method yourself.
	 *
	 * \param $e Exception
	 *
	 * \hook{exception, exception} \hookEndsMethodOnFalse
	 */
	public function handleException(\Exception $e) {
		if (parent::handleException($e) === FALSE) {
			return;
		}
		
		$names = explode("\\", get_class($e));
		$name = end($names);
		
		printf("Exception '%s' at %s:%d.\n%s\n", 
			$name, $e->getFile(), $e->getLine(), $e->getMessage());
	}
}
