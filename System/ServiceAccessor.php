<?php

namespace UseBB\System;

/**
 * A class accessing the service registry.
 *
 * Ables a class to access all the various services registered.
 *
 * \author Dietrich Moerman
 */
abstract class ServiceAccessor {
	private $services;
	private $module;
	private $className;
	private $trSection;

	/**
	 * Constructor.
	 *
	 * \param $services ServiceRegistry instance
	 */
	public function __construct(ServiceRegistry $services) {
		$this->services = $services;
		
		$className = get_class($this);
		$parts = explode("\\", $className);
		$this->module = count($parts) >= 3 
			&& $parts[0] == "UseBB" && $parts[1] == "Modules"
			? $parts[2]
			: "system";
		
		// Get the class name with namespace except "UseBB\"
		$this->className = substr($className, 6);
	}

	/**
	 * Get a service.
	 *
	 * \param $name Service name
	 * \returns Service instance
	 * \exception ServiceNotFoundException When service not found
	 */
	protected function getService($name) {
		return $this->services->get($name);
	}

	/**
	 * Get the service registry.
	 *
	 * \returns ServiceRegistry instance
	 */
	public function getServiceRegistry() {
		return $this->services;
	}
	
	/**
	 * Is the system installed?
	 * 
	 * \returns Boolean
	 */
	protected function systemIsInstalled() {
		return $this->getService("config")->get("system", "installed");
	}
	
	/**
	 * Set the section name to be used for the translation functions.
	 * 
	 * \param $section Section name
	 */
	protected function setTranslationSection($section) {
		$this->trSection = $section;
	}
	
	/**
	 * Translate a string.
	 * 
	 * Arguments are applied using AbstractContext::applyArgumentsToString().
	 * 
	 * Available options:
	 * \li \c language: language code
	 * \li \c context: context string (appended to source string with \c __)
	 * 
	 * \param $source Source string
	 * \param $args Arguments
	 * \param $options Options
	 */
	protected function t($source, array $args = array(), 
		array $options = array()) {
		return $this->getService("translation")->translate($this->module, 
			$this->trSection, $source, $args, $options);
	}
	
	/**
	 * Translate plural strings.
	 * 
	 * The plural string is used as a key for the translation files.
	 * 
	 * The \c $count parameter will be added to the arguments as \c @@count.
	 * Arguments are applied using AbstractContext::applyArgumentsToString().
	 * 
	 * Available options:
	 * \li \c language: language code
	 * \li \c context: context string (appended to source string with \c __)
	 * 
	 * \param $singular Singular source string
	 * \param $plural Plural source string
	 * \param $count Count
	 * \param $args Arguments
	 * \param $options Options
	 */
	protected function tp($singular, $plural, $count, $args = array(), 
		array $options = array()) {
		return $this->getService("translation")->translatePlural($this->module, 
			$this->trSection, $singular, $plural, $count, $args, $options);
	}
	
	/**
	 * %Log an event.
	 * 
	 * The message is in the format of translation sources, with arguments
	 * being in the same format too (array of key/values).
	 * 
	 * \param $type Type (see Event class constants)
	 * \param $message Message
	 * \param $args Message arguments
	 * \param $objectType Object type
	 * \param $objectId Object ID
	 * \returns Event instance or \c FALSE
	 */
	protected function log($type, $message, array $args = array(), 
		$objectType = "", $objectId = 0) {
		return $this->getService("log")->logEvent($type, $this->module, 
			$this->className, $message, $args, $objectType, $objectId);
	}
	
	/**
	 * Get the environment name.
	 * 
	 * \returns Environment name
	 */
	public function getEnvironmentName() {
		return $this->services->getEnvironmentName();
	}
	
	/**
	 * Clean array serialize.
	 * 
	 * \param $a Array
	 * \returns String
	 */
	protected function serialize(array $a) {
		if (count($a) == 0) {
			return "";
		}
		
		return serialize($a);
	}
	
	/**
	 * Clean array unserialize.
	 * 
	 * \param $s String
	 * \returns Array
	 */
	protected function unserialize($s) {
		if (empty($s)) {
			return array();
		}
		
		return unserialize($s);
	}
}
