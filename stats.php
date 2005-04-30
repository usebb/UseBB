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
$session->update('stats');
 
//
// Include the page header
//
require(ROOT_PATH.'sources/page_head.php');
 

 
//
// Include the page footer
//
require(ROOT_PATH.'sources/page_foot.php');
 
?>