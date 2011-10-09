<?php

// Check PHP version.
if (!function_exists("version_compare") || version_compare(PHP_VERSION, "5.3.0", "<")) {
	die("This version of UseBB requires at least PHP 5.3.0.");
}

error_reporting(E_ALL | E_STRICT);

// Set to current path if not set.
if (!defined("USEBB_ROOT_PATH")) {
	define("USEBB_ROOT_PATH", "./");
}

date_default_timezone_set("UTC");

// UseBB
spl_autoload_register(function($name) {
	$file = USEBB_ROOT_PATH . str_replace("\\", "/", substr($name, 6)) . ".php";

	if (!file_exists($file)) {
		return;
	}

	require $file;
});

// Doctrine

require "Doctrine/Common/ClassLoader.php";

$doctrine = new Doctrine\Common\ClassLoader("Doctrine");
$doctrine->register();

// Include local database configuration.

if (file_exists("./dbConfig.php")) {
	require "./dbConfig.php";
}

if (!isset($dbConfig) || !is_array($dbConfig)) {
	$dbConfig = array();
}
