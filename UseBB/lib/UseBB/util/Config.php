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
 * Configuration retrieval and management class.
 *
 * @package UseBB
 * @subpackage util
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */
final class UseBB_Config
{
	private static $values = array
	(
		'language' => 'en',
		'useAcceptLanguageHeader' => TRUE,
	);
	
	private function __construct() {}
	
	/**
	 * Get a config variable
	 *
	 * @param string $key Key
	 * @returns mixed Value
	 */
	public static function get($key)
	{
		// Throw exception when key was not found
		if ( !array_key_exists($key, self::$values) )
		{
			throw new UseBB_Exception('Configuration value ' . $key . ' not found.');
		}
		
		return self::$values[$key];
	}
	
	/**
	 * Set a config value
	 *
	 * @param string $key Key
	 * @param string $value Value
	 */
	public static function set($key, $value)
	{
		self::$values[$key] = $value;
	}
}
