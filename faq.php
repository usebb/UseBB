<?php

/*
	Copyright (C) 2003-2005 UseBB Team
	http://www.usebb.net
	
	$Header$
	
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
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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

$template->set_page_title($lang['FAQ']);

//
// Get FAQ variables
//
$faq_file = ROOT_PATH.'languages/faq_'.$functions->get_config('language').'.php';
if ( !file_exists($faq_file) || !is_readable($faq_file) )
	trigger_error('Unable to get "'.$functions->get_config('language').'" FAQ!');
else
	require($faq_file);

if ( !empty($_GET['q']) ) {
	
	$questions = array();
	foreach ( $faq as $item ) {
		
		if ( $item[0] != '--' )
			$questions[] = $item;
		
	}
	
	reset($questions);
	
	foreach ( $questions as $question ) {
		
		if ( substr(md5($question[0]), 0, 5) != $_GET['q'] )
			continue;
		
		$template->parse('question', 'faq', array(
			'question_title' => $question[0],
			'question_answer' => $question[1]
		));
		
	}
	
}

$template->parse('contents_header', 'faq');

$hi = $qi = 0;

foreach ( $faq as $item ) {
	
	if ( $item[0] == '--' ) {
		
		if ( $hi )
			$template->parse('contents_cat_footer', 'faq');
		
		$hi++;
		
		$template->parse('contents_cat_header', 'faq', array(
			'cat_name' => $item[1]
		));
		
	} else {
		
		$qi++;
		
		$template->parse('contents_question', 'faq', array(
			'question_link' => $functions->make_url('faq.php', array('q' => substr(md5($item[0]), 0, 5))),
			'question_title' => $item[0],
		));
		
	}
	
}

$template->parse('contents_cat_footer', 'faq');
$template->parse('contents_footer', 'faq');

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
