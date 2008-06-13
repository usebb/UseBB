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
 * Language management class.
 *
 * @package UseBB
 * @subpackage i18n
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */
class UseBB_LanguageManagement
{
	private static $availableLanguages;
	
	/**
	 * Get available languages.
	 *
	 * @return array Language code-indexed array of full XML file paths
	 */
	public static function getAvailableLanguages()
	{
		if ( self::$availableLanguages !== NULL )
		{
			return self::$availableLanguages;
		}
		
		// English is always available and does not have an XML file.
		$availableLanguages = array
		(
			'en' => '',
		);
		
		// Iterate through the languages directory.
		$directory = UseBB::getLibPath() . '/i18n/languages/';
		foreach ( new DirectoryIterator($directory) as $file )
		{
			$fileName = $file->getFilename();
			
			// Skip if no readable XML file.
			if ( !$file->isFile() || !$file->isReadable() || substr($fileName, -4) != '.xml' )
			{
				continue;
			}
			
			// Add the language code as key and full path as value.
			$availableLanguages[basename($fileName, '.xml')] = $directory . $fileName;
		}
		
		self::$availableLanguages = $availableLanguages;
		
		return $availableLanguages;
	}
}
