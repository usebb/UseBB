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
 * Requirements test page.
 *
 * @package UseBB
 * @subpackage tests
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */

$missing = array();

// PHP 5.2
if ( version_compare(PHP_VERSION, '5.2', '<') )
{
	$missing[] = array('PHP version 5.2', 'UseBB 2 is based on PHP 5.2. Older versions do not work.');
}

// SimpleXML
if ( !function_exists('simplexml_load_file') )
{
	$missing[] = array('SimpleXML', 'PHP must be compiled with SimpleXML support.');
}

if ( !function_exists('spl_autoload_register') )
{
	$missing[] = array('SPL', 'PHP must be compiled with SPL (Standard PHP library) support.');
}

if ( !function_exists('preg_match') )
{
	$missing[] = array('PCRE', 'PHP must be compiled with PCRE (Perl Compatible Regular Expressions) support.');
}

if ( !preg_match('#^.$#u', 'ñ') )
{
	$missing[] = array('PCRE Unicode', 'PCRE (Perl Compatible Regular Expressions) must be compiled with Unicode support.');
}

echo '<h1>Requirements check</h1>';

if ( !count($missing) )
{
	echo '<h2>All OK</h2>';
}
else
{
	echo '<h2>Missing</h2><dl>';
	
	foreach ( $missing as $item )
	{
		echo '<dt>' . $item[0] . '</dt><dd>' . $item[1] . '</dd>';
	}
	
	echo '</dl>';
}
