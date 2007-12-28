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
 * Represents a language (translation) object.
 *
 * @package UseBB
 * @subpackage i18n
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */
final class UseBB_LanguageObject
{
	private static $objects = array();
	
	private $xml;
	private $code;
	private $direction;
	private $version;
	private $name;
	private $translators = array();
	private $separators;
	
	/**
	 * Construct a new language object
	 *
	 * @param string $languageCode Language code to get
	 */
	private function __construct($languageCode)
	{
		$fileName = USEBB_LIB_USEBB . '/i18n/languages/' . $languageCode . '.xml';
		
		// Exception when the file does not exist or is not readable
		if ( !file_exists($fileName) || !is_readable($fileName) )
		{
			throw new UseBB_Exception('Language file ' . $languageCode . '.xml does not exist or is not readable.');
		}
		
		// Get the SimpleXMLElement object
		$this->xml = simplexml_load_file($fileName);
		
		// Root element must be language and code attribute must be the same as code of file
		if ( $this->xml === FALSE || $this->xml->getName() != 'language' || $this->xml['code'] != $languageCode )
		{
			throw new UseBB_Exception('Language file ' . $languageCode . '.xml is invalid.');
		}
		
		// Set the language code, combined (can be language-country) and short (language-only)
		$this->code = array
		(
			'combined' => $languageCode,
			'short' => ( ( $pos = strpos($languageCode, '-') ) !== FALSE ) ? substr_replace($languageCode, '', $pos) : $languageCode,
		);
		
		// Set the text direction (ltr or rtl)
		$this->direction = ( $this->xml['direction'] == 'rtl' ) ? 'rtl' : 'ltr';
		
		// Set the version the translation was updated for
		$this->version = (string) $this->xml['version'];
		
		// Set the name of the translation
		$this->name = array
		(
			'english' => ( ( $english = (string) $this->xml->name->english ) != '' ) ? $english : $languageCode,
			'native' => ( ( $native = (string) $this->xml->name->native ) != '' ) ? $native : $languageCode,
		);
		
		// Set the names and email addresses of the translators
		foreach ( $this->xml->translators->translator as $translator )
		{
			$this->translators[] = array
			(
				'name' => (string) $translator->name,
				'email' => (string) $translator->email,
			);
		}
		
		// Set the decimals and thousands separator
		$this->separators = array
		(
			'decimals' => ( ( $decimalsSep = (string) $this->xml->separators['decimals'] ) != '' ) ? $decimalsSep : '.',
			'thousands' => ( ( $thousandsSep = (string) $this->xml->separators['thousands'] ) != '' ) ? $thousandsSep : ',',
		);
	}
	
	/**
	 * Get an instance for a given language code
	 *
	 * This is the only way of retrieving an instance of UseBB_LanguageObject.
	 * 
	 * Note there is no object for language code "en" (source embedded).
	 *
	 * @param string $languageCode Language code
	 * @returns UseBB_LanguageObject Language object
	 */
	public static function getInstance($languageCode)
	{
		// There is no LanguageObject for English (en)
		if ( $languageCode == 'en' )
		{
			throw new UseBB_Exception('A LanguageObject for the embedded "en" language does not exist.');
		}
		
		// If it hasn't been created yet, do so now
		if ( !array_key_exists($languageCode, self::$objects) )
		{
			self::$objects[$languageCode] = new UseBB_LanguageObject($languageCode);
		}
		
		return self::$objects[$languageCode];
	}
	
	/**
	 * Get the language code
	 *
	 * @returns array Language code (combined and short)
	 */
	public function getLanguageCode()
	{
		return $this->code;
	}
	
	/**
	 * Get the direction
	 *
	 * @returns string Direction
	 */
	public function getDirection()
	{
		return $this->direction;
	}
	
	/**
	 * Get the version
	 *
	 * @returns string Version
	 */
	public function getVersion()
	{
		return $this->version;
	}
	
	/**
	 * Get the name
	 *
	 * @returns array Names
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Get the translators
	 *
	 * @returns array Translators
	 */
	public function getTranslators()
	{
		return $this->translators;
	}
	
	/**
	 * Get the separators
	 *
	 * @returns array Separators
	 */
	public function getSeparators()
	{
		return $this->separators;
	}
	
	/**
	 * Get the translation of a string
	 *
	 * This method should not be used for translation purposes, use
	 * {@link UseBB_Lang::t()} instead.
	 *
	 * @link http://doc.trolltech.com/qq/qq19-plurals.html
	 * @link http://www.loc.gov/standards/iso639-2/php/code_list.php
	 *
	 * @param string $string String
	 * @param int $n Count (for plural translations, optional)
	 * @returns mixed Translated string or FALSE when not found
	 */
	public function getTranslation($string, $n = NULL)
	{
		if ( $n !== NULL )
		{
			// Get the plural form for the used language code and given count
			switch ( $this->code['short'] )
			{
				// French
				case 'fr':
					$form = ( $n < 2 ) ? 1 : 2;
					break;
				// Czech
				case 'cs':
					$form = ( $n % 100 == 1 ) ? 1 : ( $n % 100 >= 2 && $n % 100 <= 4 ) ? 2 : 3;
					break;
				// Irish
				case 'ga':
					$form = ( $n == 1 ) ? 1 : ( $n == 2 ) ? 2 : 3;
					break;
				// Latvian
				case 'lv':
					$form = ( $n % 10 == 1 && $n % 100 != 11 ) ? 1 : ( $n != 0 ) ? 2 : 3;
					break;
				// Lithuanian
				case 'lt':
					$form = ( $n % 10 == 1 && $n % 100 != 11 ) ? 1 : ( $n % 100 != 12 && $n % 10 == 2 ) ? 2 : 3;
					break;
				// Macedonian
				case 'mk':
					$form = ( $n % 10 == 1 ) ? 1 : ( $n % 10 == 2 ) ? 2 : 3;
					break;
				// Polish
				case 'pl':
					$form = ( $n == 1 ) ? 1 : ( $n % 10 >= 2 && $n % 10 <= 4 && ( $n % 100 < 10 || $n % 100 > 20 ) ) ? 2 : 3;
					break;
				// Romanian
				case 'ro':
					$form = ( $n == 1 ) ? 1 : ( $n == 0 || ( $n % 100 >= 1 && $n % 100 <= 20 ) ) ? 2 : 3;
					break;
				// Russian
				case 'ru':
					$form = ( $n % 10 == 1 && $n % 100 != 11 ) ? 1 : ( $n % 10 >= 2 && $n % 10 <= 4 && ( $n % 100 < 10 || $n % 100 >= 20 ) ) ? 2 : 3;
					break;
				// Slovak
				case 'sk':
					$form = ( $n == 1 ) ? 1 : ( $n >= 2 && $n <= 4 ) ? 2 : 3;
					break;
				// Japanese
				case 'ja':
					$form = 1;
					break;
				// Others
				default:
					$form = ( $n == 1 ) ? 1 : 2;
			}
			
			// Make it part of an XPath expression
			$form = '[@form=' . $form . ']';
		}
		else
		{
			// Regular translation, no form part
			$form = '';
		}
		
		$found = $this->xml->xpath('/language/messages/message[original="' . $string . '"]/translation' . $form);
		
		return ( $found !== FALSE ) ? (string) $found[0] : FALSE;
	}
	
	public function __toString()
	{
		return 'Language object for ' . $this->name['english'].' ('.$this->code['combined'].')';
	}
}
