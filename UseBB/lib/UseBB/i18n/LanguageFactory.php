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
 * Language instance factory.
 *
 * @package UseBB
 * @subpackage i18n
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */
class UseBB_LanguageFactory
{
	/**
	 * Create a new language object for the default language.
	 *
	 * This keeps in mind the user-chosen language, the available languages,
	 * the HTTP accepted languages, etc.
	 *
	 * @todo Needs integration with config system.
	 *
	 * @return UseBB_Language Language object
	 */
	public static function newDefaultLanguage()
	{
		// Available languages.
		$availableLanguages = UseBB_LanguageManagement::getAvailableLanguages();
		
		// When enabled, loop through the accepted languages, picking the first available one.
		if ( TRUE )
		{
			foreach ( self::getAcceptedLanguages() as $languageCode )
			{
				if ( array_key_exists($languageCode, $availableLanguages) )
				{
					return self::loadLanguage($languageCode, $availableLanguages[$languageCode]);
				}
			}
		}
		
		return self::loadLanguage('en');
	}
	
	/**
	 * Create a new language object for the supplied code.
	 *
	 * @todo Needs integration with config system.
	 *
	 * @param string $languageCode Language code
	 * @return UseBB_Language Language object
	 */
	public static function newLanguage($languageCode)
	{
		if ( $languageCode === 'en' )
		{
			return self::loadLanguage('en');
		}
		
		// Available languages.
		$availableLanguages = UseBB_LanguageManagement::getAvailableLanguages();
		
		if ( !array_key_exists($languageCode, $availableLanguages) )
		{
			return self::loadLanguage('en');
		}
		
		return self::loadLanguage($languageCode, $availableLanguages[$languageCode]);
	}
	
	/**
	 * Load a language.
	 *
	 * @param string $languageCode Language code
	 * @param string $fileName XML file name (full path)
	 * @return UseBB_Language Language object
	 */
	private static function loadLanguage($languageCode, $fileName = NULL)
	{
		if ( $languageCode === 'en' )
		{
			// Use the embedded strings for English.
			$source = new UseBB_LanguageEmbeddedSource();
		}
		else
		{
			// Create the XML source object for other languages.
			$source = new UseBB_LanguageXMLSource($fileName);
		}
		
		return new UseBB_Language($source);
	}
	
	/**
	 * Get the language codes the user agent accepts.
	 *
	 * @return array Language codes
	 */
	private static function getAcceptedLanguages()
	{
		// Don't bother when the header isn't there or is empty.
		if ( empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) )
		{
			return array();
		}
		
		$acceptedLanguages = $addLater = array();
		
		// Get available codes from ACCEPT_LANGUAGE.
		foreach ( explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $languageCode )
		{
			// Remove the ;q= part.
			if ( ( $pos = strpos($languageCode, ';') ) !== FALSE )
			{
				$languageCode = substr_replace($languageCode, '', $pos);
			}
			
			// Skip if this can't be a valid language code.
			if ( !preg_match('#^[a-z]{2}(\-[a-z]{2})?$#', $languageCode) )
			{
				continue;
			}
			
			$acceptedLanguages[] = $languageCode;
			
			// If this is a combined language/country code, also add the language code separately.
			if ( ( $pos = strpos($languageCode, '-') ) !== FALSE )
			{
				$addLater[] = substr_replace($languageCode, '', $pos);
			}
		}
		
		// Merge the two arrays, filtering out duplicates.
		return array_values(array_unique(array_merge($acceptedLanguages, $addLater)));
	}
}
