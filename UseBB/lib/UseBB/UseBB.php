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
	
	private static $files = array
	(
		'Config' => '/util/Config.php',
		'Lang' => '/i18n/Lang.php',
		'LanguageObject' => '/i18n/LanguageObject.php',
		'Exception' => '/exceptions/Exception.php',
		'Connection' => '/db/Connection.php',
	);
	private static $directories = array
	(
		// TODO
	);
	
	/**
	 * Construct a new UseBB object
	 *
	 * This actually starts processing the request.
	 */
	public function __construct()
	{
		header('Content-type: text/html; charset=utf-8');
		
		// Open the default database connection
		UseBB_Connection::open(UseBB_Connection::DEFAULT_NAME, USEBB_DB_DSN, USEBB_DB_USERNAME, USEBB_DB_PASSWORD, USEBB_DB_TABLE_PREFIX);
		
		// free test
		require USEBB_LIB_USEBB . '/tests/free.php';
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
		if ( array_key_exists($className, self::$files) )
		{
			require_once USEBB_LIB_USEBB . self::$files[$className];
			
			return;
		}
		
		// Next, search additional directories
		foreach ( self::$directories as $directory )
		{
			$fileName = USEBB_LIB_USEBB . $directory . '/' . $className . '.php';
			
			if ( file_exists($fileName) && is_readable($fileName) )
			{
				require_once $fileName;
				
				return;
			}
		}
	}
}
