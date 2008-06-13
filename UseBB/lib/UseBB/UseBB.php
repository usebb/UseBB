<?php

/*
	This file is part of UseBB.

	UseBB is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	UseBB is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with UseBB.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * UseBB initialization class.
 *
 * @package UseBB
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */
final class UseBB
{
	/**
	 * UseBB version
	 */
	const VERSION = '2.0-alpha';
	
	private static $libPath;
	private static $classes = array
	(
		'files' => array
		(
			// Database
			'ConnectionFactory' => 'db',
			'Connection' => 'db',
			'Statement' => 'db',
			'MySQLConnection' => 'db',
			'MySQLStatement' => 'db',
			
			// Internationalization
			'Language' => 'i18n',
			'LanguageSource' => 'i18n',
			'LanguageEmbeddedSource' => 'i18n',
			'LanguageXMLSource' => 'i18n',
			'LanguageFactory' => 'i18n',
			'LanguageManagement' => 'i18n',
			
			'Input' => 'util',
			'Config' => 'util',
			'Exception' => 'exceptions',
		),
	);
	
	private $db;
	private $lang; // will be moved to contexts...
	
	/**
	 * Class constructor.
	 *
	 * @param string $dbDsn DSN (data source name)
	 * @param string $dbUserName Username
	 * @param string $dbPassword Password
	 * @param string $dbTablePrefix Table prefix
	 */
	public function __construct($dbDsn, $dbUserName, $dbPassword, $dbTablePrefix)
	{
		// Open the default database connection
		$this->db = UseBB_ConnectionFactory::newConnection($dbDsn, $dbUserName, $dbPassword, $dbTablePrefix);
		
		// Open the default language
		$this->lang = UseBB_LanguageFactory::newDefaultLanguage();
	}
	
	/**
	 * Start processing a HTTP request.
	 */
	public function processRequest()
	{
		$context = !empty($_GET['context']) ? $_GET['context'] : 'web';
		
		switch ( $context )
		{
			case 'web':
				//
				break;
			case 'json':
				//
				break;
			case 'cron':
				//
				break;
			default:
				throw new UseBB_Exception('Context ' . $context . ' not available.');
		}
		
		// test!
		
		$query = $this->db->query('SELECT * FROM {test}');
		var_dump($query);
		
		foreach ( $query as $key => $row )
		{
			var_dump($key);
			var_dump($row);
		}
		
		var_dump($this->db->query('UPDATE {test} SET test_baz = :to WHERE test_id = :id', array
		(
			':to' => 100,
			':id' => 5,
		)));
		
		var_dump($this->lang->t('Hello @browser.', array
		(
			'@browser' => $_SERVER['HTTP_USER_AGENT']
		)));
		
		var_dump($this->lang->plural(count($_GET), 'There is 1 GET variable.', 'There are @count GET variables.'));
		
		var_dump($this->lang->t('There are @num posts per day.', array
		(
			'@num' => 51794.5464
		)));
		
		$en = UseBB_LanguageFactory::newLanguage('en');
		var_dump($this->lang);
		var_dump($en);
	}
	
	/**
	 * Register UseBB autoload function with SPL.
	 *
	 * @param string $libPath Path to UseBB library.
	 */
	public static function registerAutoload($libPath)
	{
		self::$libPath = $libPath;
		spl_autoload_register(array('UseBB', 'autoload'));
	}
	
	/**
	 * Autoload function for spl_autoload_register().
	 *
	 * Should <b>never</b> be called directly.
	 *
	 * @link http://www.php.net/manual/en/function.spl-autoload-register.php
	 *
	 * @param string $className Class name
	 */
	public static function autoload($className)
	{
		// Remove 'UseBB_' prefix from name.
		$className = substr($className, 6);
		
		// Class is a controller.
		if ( substr($className, -10) === 'Controller' )
		{
			self::loadClass('controllers', $className);
			
			return;
		}
		
		// Check if the class is in the $files array.
		if ( array_key_exists($className, self::$classes['files']) )
		{
			self::loadClass(self::$classes['files'][$className], $className);
			
			return;
		}
		
		// Check if it exists as a model.
		$file = self::loadClass('models', $className, TRUE);
		if ( file_exists($file) )
		{
			require_once $file;
		}
	}
	
	/**
	 * Load a class.
	 *
	 * @param string $path Path
	 * @param string $className Class name
	 * @param bool $return Return full path and do not load.
	 */
	private static function loadClass($path, $className, $return = FALSE)
	{
		$file = self::$libPath . '/' . $path . '/' . $className . '.php';
		
		if ( $return )
		{
			return $file;
		}
		
		require_once $file;
	}
	
	/**
	 * Add a path for a class file.
	 *
	 * @param string $className Class name
	 * @param string $path Path
	 */
	public static function addClassFile($className, $path)
	{
		self::$classes['files'][$className] = $path;
	}
	
	/**
	 * Get the UseBB library path.
	 *
	 * @return string Library path
	 */
	public static function getLibPath()
	{
		return self::$libPath;
	}
}
