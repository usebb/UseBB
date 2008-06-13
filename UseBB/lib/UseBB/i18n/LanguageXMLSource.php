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
 * XML language source (non-English).
 *
 * @package UseBB
 * @subpackage i18n
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */
class UseBB_LanguageXMLSource implements UseBB_LanguageSource
{
	private $xml;
	private $languageCode;
	private $shortLanguageCode;
	private $direction;
	private $version;
	private $name;
	private $translators;
	private $separators;
	private $cache = array();
	
	/**
	 * Class constructor.
	 *
	 * @param string $fileName XML file name (full path)
	 */
	public function __construct($fileName)
	{
		// Get the language code from the file name.
		$languageCode = basename($fileName, '.xml');
		
		// Exception when the file does not exist or is not readable.
		if ( !file_exists($fileName) || !is_readable($fileName) )
		{
			throw new UseBB_Exception('Language file ' . $languageCode . '.xml does not exist or is not readable.');
		}
		
		// Get the SimpleXMLElement object.
		$this->xml = simplexml_load_file($fileName);
		
		// Root element must be language and code attribute must be the same as code of file.
		if ( $this->xml === FALSE || $this->xml->getName() != 'language' || $this->xml['code'] != $languageCode )
		{
			throw new UseBB_Exception('Language file ' . $languageCode . '.xml is invalid.');
		}
		
		// Set the language code.
		$this->languageCode = $languageCode;
		
		// Short code used internally (for getForm()).
		$this->shortLanguageCode = ( $pos = strpos($languageCode, '-') ) !== FALSE ? substr_replace($languageCode, '', $pos) : $languageCode;
		
		// Set the text direction (ltr or rtl).
		$this->direction = ( $this->xml['direction'] == 'rtl' ) ? 'rtl' : 'ltr';
		
		// Set the version the translation was updated for.
		$this->version = (string) $this->xml['version'];
		
		// Set the name of the translation.
		$this->name = array
		(
			'english' => ( $english = (string) $this->xml->name->english ) != '' ? $english : $languageCode,
			'native' => ( $native = (string) $this->xml->name->native ) != '' ? $native : $languageCode,
		);
		
		// Set the names and email addresses of the translators.
		$this->translators = array();
		foreach ( $this->xml->translators->translator as $translator )
		{
			$this->translators[] = array
			(
				'name' => (string) $translator->name,
				'email' => (string) $translator->email,
			);
		}
		
		// Set the decimals and thousands separator.
		$this->separators = array
		(
			'decimals' => ( $decimalsSep = (string) $this->xml->separators['decimals'] ) != '' ? $decimalsSep : '.',
			'thousands' => ( $thousandsSep = (string) $this->xml->separators['thousands'] ) != '' ? $thousandsSep : ',',
		);
	}
	
	/**
	 * Get the language code.
	 *
	 * @return string Language code
	 */
	public function getLanguageCode()
	{
		return $this->languageCode;
	}
	
	/**
	 * Get the text direction.
	 *
	 * @return string Direction
	 */
	public function getDirection()
	{
		return $this->direction;
	}
	
	/**
	 * Get the translation's version.
	 *
	 * @return string Version
	 */
	public function getVersion()
	{
		return $this->version;
	}
	
	/**
	 * Get the translation's name.
	 *
	 * @return array English and native name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Get the translators.
	 *
	 * @return array Array of translators (name and email)
	 */
	public function getTranslators()
	{
		return $this->translators;
	}
	
	/**
	 * Get the separators.
	 *
	 * @return array Decimals and thousands separator
	 */
	public function getSeparators()
	{
		return $this->separators;
	}
	
	/**
	 * Get a string's translation.
	 *
	 * @param string $string String
	 * @param int $n Count (for plurals)
	 * @return string|FALSE Translation or FALSE when not found
	 */
	public function getTranslation($string, $n = NULL)
	{
		// Calculate hash.
		$hash = md5((int) $n . $string);
		
		// Lookup in cache.
		if ( array_key_exists($hash, $this->cache) )
		{
			return $this->cache[$hash];
		}
		
		// Get the form for the given count and use it in an XPath expression.
		$form = $n !== NULL ? '[@form=' . $this->getForm($n) . ']' : '';
		
		// Find the translation in the XML file.
		$found = $this->xml->xpath('/language/messages/message[original="' . $string . '"]/translation' . $form);
		$found = $found !== FALSE ? (string) $found[0] : FALSE;
		
		// Save in cache.
		$this->cache[$hash] = $found;
		
		return $found;
	}
	
	/**
	 * Get the form needed for the supplied count.
	 *
	 * @link http://doc.trolltech.com/qq/qq19-plurals.html
	 * @link http://www.loc.gov/standards/iso639-2/php/code_list.php
	 *
	 * @param int $n Count
	 * @return int Form (1 to 3)
	 */
	private function getForm($n)
	{
		switch ( $this->shortLanguageCode )
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
		
		return $form;
	}
}
