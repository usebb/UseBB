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
 * Free test page.
 *
 * @package UseBB
 * @subpackage tests
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */

echo '<h1>i18n</h1>';

echo 'Available: ';
var_dump(UseBB_Lang::getAvailableLanguages());
echo 'Accepted: ';
var_dump(UseBB_Lang::getAcceptedLanguages());
echo 'Chosen: ';
echo UseBB_Lang::getLanguageCode();

echo '<p>';
echo UseBB_Lang::t('Hello @browser.', array('@browser' => $_SERVER['HTTP_USER_AGENT']));
echo '<p>';
echo UseBB_Lang::plural(count($_GET), 'There is 1 GET variable.', 'There are @count GET variables.');
echo '<p>';
echo UseBB_Lang::t('There are @num posts per day.', array('@num' => 5345.7894));

echo '<p>Info about nl: ';
var_dump(UseBB_LanguageObject::getInstance('nl')->getName());
var_dump(UseBB_LanguageObject::getInstance('nl')->getVersion());
var_dump(UseBB_LanguageObject::getInstance('nl')->getTranslators());

echo '<p>UTF-8 match test: ';
echo UseBB_Lang::t('This is the letter ç.');

###
echo '<hr><h1>DB</h1>';

echo 'default: ';
var_dump(UseBB_Connection::getInstance());

UseBB_Connection::closeAll();
