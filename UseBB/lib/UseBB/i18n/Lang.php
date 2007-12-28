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
 * Language (translation) functionality.
 *
 * @package UseBB
 * @subpackage i18n
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */
final class UseBB_Lang
{
	private static $availableLanguages;
	private static $acceptedLanguages;
	private static $languageCode;
	
	private function __construct() {}
	
	/**
	 * Get available languages
	 *
	 * Available languages are languages which have an .xml file in the
	 * languages directory.
	 *
	 * This will return the short language codes only (not the full names).
	 *
	 * @returns array Available languages
	 */
	public static function getAvailableLanguages()
	{
		if ( self::$availableLanguages !== NULL )
		{
			return self::$availableLanguages;
		}
		
		$availableLanguages = array('en');
		
		// Iterate through the languages directory
		foreach ( new DirectoryIterator(USEBB_LIB_USEBB . '/i18n/languages') as $file )
		{
			$fileName = $file->getFilename();
			
			// Skip if no readable XML file
			if ( !$file->isFile() || !$file->isReadable() || substr($fileName, -4) != '.xml' )
			{
				continue;
			}
			
			$availableLanguages[] = basename($fileName, '.xml');
		}
		
		self::$availableLanguages = $availableLanguages;
		
		return $availableLanguages;
	}
	
	/**
	 * Get accepted languages
	 *
	 * Accepted languages are retrieved from the ACCEPT_LANGUAGE header. When
	 * combined language/country codes are found, the general language code
	 * is added as well.
	 *
	 * @returns array Accepted languages
	 */
	public static function getAcceptedLanguages()
	{
		if ( self::$acceptedLanguages !== NULL )
		{
			return self::$acceptedLanguages;
		}
		
		// Don't bother when the header isn't there or is empty
		if ( empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) )
		{
			self::$acceptedLanguages = $acceptedLanguages = array();
			
			return $acceptedLanguages;
		}
		
		$acceptedLanguages = $addLater = array();
		
		// Get available codes from ACCEPT_LANGUAGE
		foreach ( explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $languageCode )
		{
			// Remove the ;q= part
			if ( ( $pos = strpos($languageCode, ';') ) !== FALSE )
			{
				$languageCode = substr_replace($languageCode, '', $pos);
			}
			
			// Skip if this can't be a valid language code
			if ( !preg_match('#^[a-z]{2}(\-[a-z]{2})?$#', $languageCode) )
			{
				continue;
			}
			
			$acceptedLanguages[] = $languageCode;
			
			// If this is a combined language/country code, also add the language code separately
			if ( ( $pos = strpos($languageCode, '-') ) !== FALSE )
			{
				$addLater[] = substr_replace($languageCode, '', $pos);
			}
		}
		
		// Merge the two arrays, filtering out duplicates
		self::$acceptedLanguages = $acceptedLanguages = array_values(array_unique(array_merge($acceptedLanguages, $addLater)));
		
		return $acceptedLanguages;
	}
	
	/**
	 * Get the language currently used
	 *
	 * @returns string Language code
	 */
	public static function getLanguageCode()
	{
		if ( self::$languageCode !== NULL )
		{
			return self::$languageCode;
		}
		
		$availableLanguages = self::getAvailableLanguages();
		
		// When enabled, loop through the accepted languages,
		// picking the first available one
		if ( UseBB_Config::get('useAcceptLanguageHeader') )
		{
			foreach ( self::getAcceptedLanguages() as $languageCode )
			{
				if ( in_array($languageCode, $availableLanguages) )
				{
					self::$languageCode = $languageCode;
					
					return $languageCode;
				}
			}
		}
		
		// If none was found, pick the configured one (when available)
		$configured = UseBB_Config::get('language');
		self::$languageCode = $languageCode = ( in_array($configured, $availableLanguages) ) ? $configured : 'en';
		
		return $languageCode;
	}
	
	/**
	 * Apply variables
	 *
	 * @param string $string String
	 * @param array $variables Variables
	 * @param string $languageCode Language code
	 * @returns string String
	 */
	private static function applyVariables($string, array $variables, $languageCode)
	{
		// Apply passed variables
		foreach ( $variables as $key => $val )
		{
			// Apply number formatting to integers and floats
			if ( is_int($val) || is_float($val) )
			{
				$val = self::number($val, NULL, $languageCode);
			}
			
			$skip = FALSE;
			
			switch ( substr($key, 0, 1) )
			{
				// Regular variable
				case '@':
					$val = self::escapeString($val);
					break;
				// Highlighted variable
				case '%':
					$val = '<em>' . self::escapeString($val) . '</em>';
					break;
				// Raw variable, keep as-is
				case '!':
					break;
				// Something not beginning with @, % or !, ignore
				// Unfortunately, continue does the same as break inside
				// switch loops, so we need a helper variable and a
				// continue statement outside the switch loop
				default:
					$skip = TRUE;
			}
			
			if ( $skip )
			{
				continue;
			}
			
			$string = str_replace($key, $val, $string);
		}
		
		return $string;
	}
	
	/**
	 * Translate a string
	 *
	 * This uses the currently used language (auto-detected or user-set),
	 * unless the language code is passed as a parameter.
	 *
	 * Variables can be in the format
	 *  - @foo: regular escaped
	 *  - %foo: escaped + highlighted
	 *  - !foo: raw variable (should <b>not</b> be used for web output)
	 *
	 * Integer and float variables are automatically formatted using
	 * {@link number()} (with default parameters). If you do not wish this
	 * or wish to use different formatting parameters, cast the value to a
	 * string or call {@link number()} manually.
	 *
	 * Example usage:
	 * <code>
	 * UseBB_Lang::t('Hello @browser.', array('@browser' => $_SERVER['HTTP_USER_AGENT']));
	 * // Hello Mozilla/5.0.
	 * UseBB_Lang::t('There are @num posts per day.', array('@num' => 5345.7894));
	 * // There are 5,345.79 posts per day.
	 * </code>
	 * 
	 * The idea for these variable types was borrowed from
	 * {@link http://api.drupal.org/api/function/t Drupal's t() function}.
	 *
	 * @param string $string String
	 * @param array $variables Variable key/value pairs to replace (optional)
	 * @param string $languageCode Language code (optional)
	 * @returns string Translated string
	 */
	public static function t($string, array $variables = array(), $languageCode = NULL)
	{
		// Fallback to global language code when no code is passed
		if ( $languageCode === NULL )
		{
			$languageCode = self::getLanguageCode();
		}
		
		// If not English and translation found, use it
		if ( $languageCode != 'en' && ( $found = UseBB_LanguageObject::getInstance($languageCode)->getTranslation($string) ) !== FALSE )
		{
			$string = $found;
		}
		
		return self::applyVariables($string, $variables, $languageCode);
	}
	
	/**
	 * Translate either the singular or plural string
	 *
	 * For non-English languages, the plural string is not used but retrieved
	 * from the translation file. Some languages may use different rules and
	 * more than two forms. For more information, see the translation guide.
	 *
	 * Formatted variables %count and @count are added automatically.
	 *
	 * This uses the currently used language (auto-detected or user-set),
	 * unless the language code is passed as a parameter.
	 *
	 * Example usage:
	 * <code>
	 * UseBB_Lang::plural(count($_GET), 'There is 1 variable.', 'There are @count variables.');
	 * // There are 0 variables.
	 * </code>
	 *
	 * @see t()
	 * @todo Provide link to translation guide.
	 *
	 * @param int $count Count
	 * @param string $singular Singular string
	 * @param string $plural Plural string
	 * @param array $variables Variable key/value pairs to replace (optional)
	 * @param string $languageCode Language code (optional)
	 * @returns string Translated string
	 */
	public static function plural($count, $singular, $plural, array $variables = array(), $languageCode = NULL)
	{
		// Fallback to global language code when no code is passed
		if ( $languageCode === NULL )
		{
			$languageCode = self::getLanguageCode();
		}
		
		// Add count both as highlighted and regular variable
		$variables['%count'] = $variables['@count'] = $count;
		
		// If not English and translation found, use it
		// Otherwise, fallback to default English singular/plural form
		if ( $languageCode != 'en' && ( $found = UseBB_LanguageObject::getInstance($languageCode)->getTranslation($singular, $count) ) !== FALSE )
		{
			$string = $found;
		}
		else
		{
			$string = ( $count == 1 ) ? $singular : $plural;
		}
		
		return self::applyVariables($string, $variables, $languageCode);
	}
	
	/**
	 * Format a number
	 *
	 * This rounds the number to <var>$decimals</var> decimals and adds decimals and thousands separators.
	 *
	 * @param mixed $number Integer or float number
	 * @param int $decimals Number of decimals (for float, optional, defaults to 2)
	 * @param string $languageCode Language code (optional)
	 * @returns string Formatted number
	 */
	public static function number($number, $decimals = NULL, $languageCode = NULL)
	{
		// If this is an integer and less than 1000, formatting has no effect
		if ( is_int($number) && $number < 1000 )
		{
			return $number;
		}
		
		// Fallback to global language code when no code is passed
		if ( $languageCode === NULL )
		{
			$languageCode = self::getLanguageCode();
		}
		
		// Do not use decimals for integers
		// Otherwise, use given number or 2 when NULL
		if ( is_int($number) )
		{
			$decimals = 0;
		}
		else
		{
			$decimals = ( $decimals !== NULL ) ? $decimals : 2;
		}
		
		// For English, use the standard separators
		if ( $languageCode == 'en' )
		{
			return number_format($number, $decimals, '.', ',');
		}
		
		// Get the characters from the language object
		$separators = UseBB_LanguageObject::getInstance($languageCode)->getSeparators();
		
		return number_format($number, $decimals, $separators['decimals'], $separators['thousands']);
	}
	
	/**
	 * Escape a string
	 *
	 * @todo Might be moved to the UTF-8 handler class.
	 *
	 * @param string $string String
	 * @returns string Escaped string
	 */
	private static function escapeString($string)
	{
		if ( !is_string($string) )
		{
			return $string;
		}
		
		return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	}
}
