<?php

namespace UseBB\Utils\Events;

use UseBB\System\ServiceAccessor;
use UseBB\System\ServiceRegistry;

/**
 * %Event type.
 * 
 * Represents an event logged in the events log.
 * 
 * \author Dietrich Moerman
 */
class Event extends ServiceAccessor {
	/**
	 * Debug level
	 */
	const LEVEL_DEBUG = 1;
	
	/**
	 * Information level
	 */
	const LEVEL_INFO = 2;
	
	/**
	 * Warning level
	 */
	const LEVEL_WARN = 3;
	
	/**
	 * Error level
	 */
	const LEVEL_ERROR = 4;
	
	private $level;
	private $module;
	private $className;
	private $message;
	private $args;
	private $objectType;
	private $objectId;
	private $saved = FALSE;
	
	/**
	 * Constructor.
	 * 
	 * \attention You should not instantiate this class yourself but use 
	 * Log::logEvent().
	 * 
	 * \param $services ServiceRegistry instance
	 * \param $level Level (see class constants)
	 * \param $module Module name
	 * \param $className Class name
	 * \param $message Message
	 * \param $args Message arguments
	 * \param $objectType Object type
	 * \param $objectId Object ID
	 */
	public function __construct(ServiceRegistry $services, $level, $module, 
		$className, $message, array $args = array(), $objectType = "", 
		$objectId = 0) {
		parent::__construct($services);
		
		// TODO: use ORM stuff
		$this->level = $level;
		$this->module = $module;
		$this->className = $className;
		$this->message = $message;
		$this->args = $args;
		$this->objectType = $objectType;
		$this->objectId = $objectId;
	}
	
	/**
	 * Save in the database.
	 */
	public function save() {
		if ($this->saved) {
			return;
		}
		
		$this->getService("database")->insert("events", array(
			"level"        => $this->level,
			"module"       => $this->module,
			"class"        => $this->className,
			"user_id"      => 0,
			"object_type"  => $this->objectType,
			"object_id"    => $this->objectId,
			"date"         => new \DateTime(),
			"message"      => $this->message,
			"message_args" => $this->serialize($this->args)
		), TRUE);
		$this->saved = TRUE;
	}
	
	/**
	 * Get the level.
	 * 
	 * \returns Level
	 */
	public function getLevel() {
		return $this->level;
	}
	
	/**
	 * Get the module.
	 * 
	 * \returns Module name
	 */
	public function getModule() {
		return $this->module;
	}
	
	/**
	 * Get the class name.
	 * 
	 * \returns Class name
	 */
	public function getClassName() {
		return $this->className;
	}
	
	/**
	 * Get the raw message.
	 * 
	 * \returns Raw message
	 */
	public function getRawMessage() {
		return $this->message;
	}
	
	/**
	 * Get the message.
	 * 
	 * Translates and applies arguments.
	 * 
	 * \returns Message
	 */
	public function getMessage() {
		return $this->t($this->message, $this->args);
	}
}
