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
 * Embedded language source (English).
 *
 * @package UseBB
 * @subpackage i18n
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */
class UseBB_LanguageEmbeddedSource implements UseBB_LanguageSource
{
	/**
	 * Get the language code.
	 *
	 * @return string Language code
	 */
	public function getLanguageCode()
	{
		return 'en';
	}
	
	/**
	 * Get the text direction.
	 *
	 * @return string Direction
	 */
	public function getDirection()
	{
		return 'ltr';
	}
	
	/**
	 * Get the translation's version.
	 *
	 * @return string Version
	 */
	public function getVersion()
	{
		return UseBB::VERSION;
	}
	
	/**
	 * Get the translation's name.
	 *
	 * @return array English and native name
	 */
	public function getName()
	{
		return array
		(
			'english' => 'English',
			'native' => 'English',
		);
	}
	
	/**
	 * Get the translators.
	 *
	 * @return array Array of translators (name and email)
	 */
	public function getTranslators()
	{
		return array
		(
			array
			(
				'name' => 'UseBB Contributors',
				'email' => '',
			),
		);
	}
	
	/**
	 * Get the separators.
	 *
	 * @return array Decimals and thousands separator
	 */
	public function getSeparators()
	{
		return array
		(
			'decimals' => '.',
			'thousands' => ',',
		);
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
		// Return FALSE so that the internal string is always used.
		return FALSE;
	}
}
