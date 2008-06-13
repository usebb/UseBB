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
class UseBB_Language
{
	private $source;
	
	/**
	 * Class constructor.
	 *
	 * @param UseBB_LanguageSource Language source object
	 */
	public function __construct(UseBB_LanguageSource $source)
	{
		$this->source = $source;
	}
	
	/**
	 * Get the language code.
	 *
	 * @return string Language code
	 */
	public function getLanguageCode()
	{
		return $this->source->getLanguageCode();
	}
	
	/**
	 * Get the text direction.
	 *
	 * @return string Direction
	 */
	public function getDirection()
	{
		return $this->source->getDirection();
	}
	
	/**
	 * Get the translation's version.
	 *
	 * @return string Version
	 */
	public function getVersion()
	{
		return $this->source->getVersion();
	}
	
	/**
	 * Get the translation's name.
	 *
	 * @return array English and native name
	 */
	public function getName()
	{
		return $this->source->getName();
	}
	
	/**
	 * Get the translators.
	 *
	 * @return array Array of translators (name and email)
	 */
	public function getTranslators()
	{
		return $this->source->getTranslators();
	}
	
	/**
	 * Get the separators.
	 *
	 * @return array Decimals and thousands separator
	 */
	public function getSeparators()
	{
		return $this->source->getSeparators();
	}
	
	/**
	 * Translate a string.
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
	 * $lang->t('Hello @browser.', array('@browser' => $_SERVER['HTTP_USER_AGENT']));
	 * // Hello Mozilla/5.0.
	 * $lang->t('There are @num posts per day.', array('@num' => 5345.7894));
	 * // There are 5,345.79 posts per day.
	 * </code>
	 * 
	 * The idea for these variable types was borrowed from
	 * {@link http://api.drupal.org/api/function/t Drupal's t() function}.
	 *
	 * @param string $string String
	 * @param array $variables Variable key/value pairs to replace (optional)
	 * @return string Translated string
	 */
	public function t($string, array $variables = array())
	{
		$translation = $this->source->getTranslation($string);
		
		if ( $translation !== FALSE )
		{
			$string = $translation;
		}
		
		return $this->applyVariables($string, $variables);
	}
	
	/**
	 * Translate either the singular or plural string.
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
	 * $lang->plural(count($_GET), 'There is 1 variable.', 'There are @count variables.');
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
	 * @return string Translated string
	 */
	public function plural($count, $singular, $plural, array $variables = array())
	{
		// Add count both as highlighted and regular variable
		$variables['%count'] = $variables['@count'] = $count;
		
		$translation = $this->source->getTranslation($singular, $count);
		
		if ( $translation !== FALSE )
		{
			$string = $translation;
		}
		else
		{
			// Not found, use singular or plural based on English grammar.
			$string = $count === 1 ? $singular : $plural;
		}
		
		return $this->applyVariables($string, $variables);
	}
	
	/**
	 * Format a number.
	 *
	 * This rounds the number to <var>$decimals</var> decimals and adds decimals and thousands separators.
	 *
	 * @param int|float $number Integer or float number
	 * @param int $decimals Number of decimals (for float, optional, defaults to 2)
	 * @param string $languageCode Language code (optional)
	 * @return string Formatted number
	 */
	public function number($number, $decimals = NULL)
	{
		// If this is an integer and less than 1000, formatting has no effect.
		if ( is_int($number) && $number < 1000 )
		{
			return $number;
		}
		
		// Do not use decimals for integers.
		// Otherwise, use given number or 2 when NULL.
		if ( is_int($number) )
		{
			$decimals = 0;
		}
		else
		{
			$decimals = $decimals !== NULL ? $decimals : 2;
		}
		
		$separators = $this->source->getSeparators();
		
		return number_format($number, $decimals, $separators['decimals'], $separators['thousands']);
	}
	
	/**
	 * Apply variables.
	 *
	 * @param string $string String
	 * @param array $variables Variables
	 * @return string String
	 */
	private function applyVariables($string, array $variables)
	{
		// Apply passed variables.
		foreach ( $variables as $key => $val )
		{
			// Apply number formatting to integers and floats.
			if ( is_int($val) || is_float($val) )
			{
				$val = $this->number($val);
			}
			
			$skip = FALSE;
			
			switch ( substr($key, 0, 1) )
			{
				// Regular variable.
				case '@':
					$val = self::escapeString($val);
					break;
				// Highlighted variable.
				case '%':
					$val = '<em>' . self::escapeString($val) . '</em>';
					break;
				// Raw variable, keep as-is.
				case '!':
					break;
				// Something not beginning with @, % or !, ignore.
				// Unfortunately, continue does the same as break inside
				// switch loops, so we need a helper variable and a
				// continue statement outside the switch loop.
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
	 * Escape a string
	 *
	 * @todo Should be moved to more appropriate place.
	 *
	 * @param string $string String
	 * @return string Escaped string
	 */
	private static function escapeString($string)
	{
		if ( is_int($string) || is_float($string) )
		{
			return $string;
		}
		
		return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	}
}
