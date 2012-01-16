<?php

/*
	Copyright (C) 2003-2012 UseBB Team
	http://www.usebb.net
	
	$Id$
	
	This file is part of UseBB.
	
	UseBB is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	UseBB is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with UseBB; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Frequently Asked Questions
 *
 * Parses a list of frequently asked questions.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2012 UseBB Team
 * @package	UseBB
 */

define('INCLUDED', true);
define('ROOT_PATH', './');

//
// Include usebb engine
//
require(ROOT_PATH.'sources/common.php');

//
// Update and get the session information
//
$session->update('faq');

//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');

$template->add_breadcrumb($lang['FAQ'], array('faq.php'));

//
// Get FAQ variables
//
$faq_file = ROOT_PATH.'languages/faq_'.$functions->get_config('language').'.php';
if ( !file_exists($faq_file) || !is_readable($faq_file) )
	trigger_error('Unable to get "'.$functions->get_config('language').'" FAQ!', E_USER_ERROR);
else
	require($faq_file);

//
// Loop over FAQ and assign MD5 based keys
//
$faq2 = array();
$all_keys = array();
foreach ( $faq as $item ) {
	
	//
	// Section
	//
	if ( $item[0] == '--' ) {
		
		$faq2[] = array('--', $item[1], '--');
		continue;

	}

	//
	// Question: find and set key
	//
	$key = md5($item[0]);
	$len = 5;
	$used_key = '';
	do
		$used_key = substr($key, 0, $len++);
	while ( in_array($used_key, $all_keys) );

	$faq2[] = array($item[0], $item[1], $used_key);
	$all_keys[] = $used_key;
	
}

$faq = $faq2;
unset($faq2);

//
// Show individual question
//
if ( !empty($_GET['q']) ) {
	
	foreach ( $faq as $question ) {
		
		if ( $question[2] != $_GET['q'] )
			continue;
		
		$template->add_breadcrumb($question[0]);
		
		$template->parse('question', 'faq', array(
			'question_title' => $question[0],
			'question_answer' => nl2br($question[1])
		));

		break;
		
	}
	
}

//
// Parse list
//
$template->parse('contents_header', 'faq');

$first = true;

foreach ( $faq as $item ) {
	
	if ( $item[0] == '--' ) {
		
		if ( !$first )
			$template->parse('contents_cat_footer', 'faq');
		else
			$first = false;
		
		$template->parse('contents_cat_header', 'faq', array(
			'cat_name' => $item[1]
		));
		
	} else {
		
		$current = ( !empty($_GET['q']) && $_GET['q'] == $item[2] );
		$question_link = $functions->make_url('faq.php', array('q' => $item[2]));

		$template->parse('contents_question', 'faq', array(
			'question_link' => $question_link,
			'question_title' => $item[0],
			'question_entry' => ( $current ) ? $item[0] : '<a href="'.$question_link.'">'.$item[0].'</a>'
		));
		
	}
	
}

if ( count($faq) )
	$template->parse('contents_cat_footer', 'faq');

$template->parse('contents_footer', 'faq');

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
