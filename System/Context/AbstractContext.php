<?php

namespace UseBB\System\Context;

use UseBB\System\Plugins\PluginRunningClass;
use UseBB\Utils\Events\Event;

/**
 * Abstract context class.
 * 
 * A context retrieves the initial UseBB request and can prepare various
 * stuff/services specific to the context. Example contexts are HTTP and CLI.
 * 
 * \author Dietrich Moerman
 */
abstract class AbstractContext extends PluginRunningClass {
	/**
	 * Get the context name.
	 */
	public function getName() {
		$className = explode("\\", get_class($this));
	
		return end($className);
	}
	
	/**
	 * Handle the current request.
	 * 
	 * \attention This method is called automatically.
	 */
	abstract public function handleRequest();
	
	/**
	 * Get the language to be used.
	 * 
	 * The context can select one from the available languages, using 
	 * specific means of giving priorities to languages.
	 * 
	 * \param $available Available language codes
	 * \returns Language code
	 */
	abstract public function getLanguage(array $available);
	
	/**
	 * Escape a string for safe output.
	 * 
	 * \param $string String
	 * \returns Escaped string
	 */
	abstract public function escapeString($string);
	
	/**
	 * Apply arguments to a string.
	 * 
	 * Argument types are designated with the first character of the key.
	 * \li \c @: escaped
	 * \li \c %: escaped and highlighted
	 * \li \c !: raw (not escaped)
	 * 
	 * \warning It is not safe to use raw variables for %HTTP, shell or most 
	 * other output. Unless unnecessary or already escaped, use the escaped 
	 * variable types.
	 * 
	 * Example:
	 * \code
	 * $c->applyArgumentsToString("My name is @name.", array(
	 * 	"@name" => "Unknown"
	 * ));
	 * \endcode
	 * 
	 * \param $string String
	 * \param $args Arguments
	 * \returns New string
	 */
	abstract public function applyArgumentsToString($string, array $args);
	
	/**
	 * Generate a link.
	 * 
	 * Given a number of parameters (key/value pairs), generate a context-
	 * specific string representing a link.
	 * 
	 * \param $params Parameters
	 * \returns Link
	 */
	abstract public function generateLink(array $params);
	
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
	*/
	public function handleError($number, $string, $file, $line, 
		array $context) {
		$this->log(Event::LEVEL_ERROR, "Error '@string' at @file:@line.", array(
			"@string" => $string,
			"@file"   => $file,
			"@line"   => $line,
		));
		
		return $this->runPluginsCollectAnd("error", array(
			"number"  => $number, 
			"string"  => $string, 
			"file"    => $file, 
			"line"    => $line, 
			"context" => $context
		));
	}
	
	/**
	 * Uncaught exception handler.
	 *
	 * \attention Do not call this method yourself.
	 *
	 * \param $e Exception
	 */
	public function handleException(\Exception $e) {
		$names = explode("\\", get_class($e));
		$name = end($names);
		
		$this->log(Event::LEVEL_ERROR, "Exception '@name' at @file:@line.\n"
			. "@message", array(
			"@name"    => $name,
			"@file"    => $e->getFile(),
			"@line"    => $e->getLine(),
			"@message" => $e->getMessage(),
		));
		
		return $this->runPluginsCollectAnd("exception", array(
			"exception" => $e
		));
	}
}
