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

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

class admin_functions {
	
	//
	// Create the ACP menu
	//
	function create_acp_menu($location) {
		
		global $functions, $lang;
		
		$categories = array(
			'main',
			'forums',
		);
		$items = array(
			'main' => array(
				'index',
				'generalconfig',
			),
			'forums' => array(
				'categories',
				'forums'
			),
		);
		
		$out = '<ul>';
		foreach ( $categories as $category ) {
			
			$out .= '<li><strong>'.$lang['ACPCategory-'.$category].'</strong><ul>';
			foreach ( $items[$category] as $item ) {
				
				$selected1 = ( $location == $item ) ? '<strong>' : '';
				$selected2 = ( $location == $item ) ? '</strong>' : '';
				
				$out .= '<li><a href="'.$functions->make_url('admin.php', array('act' => $item)).'">'.$selected1.$lang['ACPItem-'.$item].$selected2.'</a></li>';
				
			}
			$out .= '</ul></li>';
			
		}
		$out .= '</ul>';
		
		return $out;
		
	}
	
	//
	// Create the admin body
	//
	function create_body($location, $content) {
		
		global $template, $lang;
		
		if ( $location == 'index' )
			$template->set_page_title($lang['ACP']);
		else
			$template->set_page_title('<a href="'.$functions->make_url('admin.php').'">'.$lang['ACP'].'</a>'.$template->get_config('locationbar_item_delimiter').$lang['ACPItem-'.$location]);
		
		$template->parse('main', 'admin', array(
			'admin_menu' => $this->create_acp_menu($location),
			'admin_title' => $lang['ACPItem-'.$location],
			'admin_content' => $content
		));
		
	}
	
}

?>
