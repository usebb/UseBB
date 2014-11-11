<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_USEBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_USEBB is defined.");
}


/* Defines the root directory for UseBB.

	Uncomment the below line and set the path manually
	if you experience problems.

	Always add a trailing slash to the end of the path.

	* Path to your copy of UseBB
 */
//define('USEBB_ROOT', "./");

// Attempt autodetection
if(!defined('USEBB_ROOT'))
{
	define('USEBB_ROOT', dirname(dirname(__FILE__))."/");
}
