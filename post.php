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
 * Post doorway
 *
 * Forms the doorway to posting topics and posts.
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
// Include the right file for either topic or reply posting...
//
if ( !empty($_GET['topic']) && valid_int($_GET['topic']) ) {
	
	require(ROOT_PATH.'sources/post_reply.php');
	
} elseif ( !empty($_GET['forum']) && valid_int($_GET['forum']) ) {
	
	require(ROOT_PATH.'sources/post_topic.php');
	
} else {
	
	//
	// There's no ID! Get us back to the index...
	//
	$functions->redirect('index.php');
	
}

?>
