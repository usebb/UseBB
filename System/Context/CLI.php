<?php

namespace UseBB\System\Context;

use UseBB\Utils\SchemaManagement\SystemSchema;

/**
 * Command Line Interface context.
 * 
 * \author Dietrich Moerman
 */
class CLI extends AbstractContext {
	public function handleRequest() {
	 	echo "UseBB version " . $this->getService("info")->getUseBBVersion() . 
	 		" (" . $this->getEnvironmentName() . ")\n\n";
		
		// TODO clean-up
		$all_opts = array("install-db");
		$opts = array_keys(getopt("", $all_opts));
		if (count($opts) === 0) {
			echo "Available options: " . join(", ", $all_opts) . "\n";
		} else {
			foreach ($opts as $opt) {
				switch ($opt) {
					case "install-db":
						$this->systemSchema = new SystemSchema(
							$this->getServiceRegistry());
						$this->systemSchema->install();
						break;
				}
			}
			echo "Done.\n";
		}
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
	
	/**
	 * Get the forced environment name.
	 * 
	 * Any environment name returned here will override the one passed to
	 * UseBB\Core::__construct.
	 * 
	 * \returns Environment name
	 */
	public function getForcedEnvironmentName() {
		$opts = getopt("", array("env::"));
		if (is_array($opts) && isset($opts["env"])) {
			return $opts["env"];
		}
		
		return NULL;
	}
}
