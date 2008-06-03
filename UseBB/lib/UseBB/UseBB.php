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
			'Connection' => '/db/Connection.php',
			'Input' => '/util/Input.php',
			'Config' => '/util/Config.php',
			'Lang' => '/i18n/Lang.php',
			'LanguageObject' => '/i18n/LanguageObject.php',
			'Exception' => '/exceptions/Exception.php',
		),
		'directories' => array
		(
		
		),
	);
	
	private $db;
	
	/**
	 * Construct a new UseBB object
	 *
	 * This actually starts processing the request.
	 */
	public function __construct($dbDSN, $dbUsername, $dbPassword, $dbTablePrefix)
	{
		// Open the default database connection
		$this->db = new UseBB_Connection($dbDSN, $dbUsername, $dbPassword, $dbTablePrefix, self::$libPath);
	}
	
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
				throw new UseBB_Exception('No context ' . $context . ' exists.');
		}
		
		var_dump
		(
			$this->db->query('SELECT * FROM {test}')
		);
	}
	
	public static function registerAutoload($libPath)
	{
		self::$libPath = $libPath;
		spl_autoload_register(array('UseBB', 'autoload'));
	}
	
	/**
	 * Autoload function for spl_autoload_register()
	 *
	 * Should <b>never</b> be called directly.
	 *
	 * @link http://www.php.net/manual/en/function.spl-autoload-register.php
	 *
	 * @param string $className Class name
	 */
	public static function autoload($className)
	{
		// Remove 'UseBB_' prefix from name
		$className = substr($className, 6);
		
		// First, check if the class is in the $files array
		if ( array_key_exists($className, self::$classes['files']) )
		{
			require_once self::$libPath . self::$classes['files'][$className];
			
			return;
		}
		
		// Next, search additional directories
		foreach ( self::$classes['directories'] as $directory )
		{
			$fileName = self::$libPath . $directory . '/' . $className . '.php';
			
			if ( file_exists($fileName) && is_readable($fileName) )
			{
				require_once $fileName;
				
				return;
			}
		}
	}
	
	public static function addClassFile($className, $path)
	{
		// Remove 'UseBB_' prefix from name
		if ( strncmp($className, 'UseBB_', 6) === 0 )
		{
			$className = substr($className, 6);
		}
		
		// Add leading slash
		if ( strncmp($path, '/', 1) !== 0 )
		{
			$path = '/' . $path;
		}
		
		self::$classes['files'][$className] = $path;
	}
}
