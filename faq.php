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

$location_bar = '<a href="'.$functions->make_url('index.php').'">'.htmlspecialchars($functions->get_config('board_name')).'</a> '.$template->get_config('locationbar_item_delimiter').' '.$lang['FAQ'];
$template->parse('location_bar', 'global', array(
	'location_bar' => $location_bar
));

//
// Get FAQ variables
//
$faq_file = ROOT_PATH.'languages/faq_'.$functions->get_config('language').'.php';
if ( !file_exists($faq_file) || !is_readable($faq_file) )
	$functions->usebb_die('General', 'Unable to get "'.$functions->get_config('language').'" FAQ!', __FILE__, __LINE__);
else
	require($faq_file);

$template->parse('faq_header', 'faq', array(
	'title' => $lang['FAQ']
));

$hi = $qi = 0;

foreach ( $faq as $item ) {
	
	if ( $item[0] == '--' ) {
		
		$hi++;
		
		$template->parse('faq_heading', 'faq', array(
			'heading_title' => $item[1],
			'heading_anchor' => 'h'.$hi
		));
		
	} else {
		
		$qi++;
		
		$template->parse('faq_question', 'faq', array(
			'question_title' => $item[0],
			'question_answer' => $item[1],
			'heading_anchor' => 'q'.$qi
		));
		
	}
	
}

$template->parse('faq_footer', 'faq');

$template->parse('location_bar', 'global', array(
	'location_bar' => $location_bar
));

//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');

?>
