<?php

// Check PHP version.
if (!function_exists("version_compare") || version_compare(PHP_VERSION, "5.3.0", "<")) {
	die("This version of UseBB requires at least PHP 5.3.0.");
}

error_reporting(E_ALL | E_STRICT);

// Set to current path if not set.
if (!defined("USEBB_ROOT_PATH")) {
	define("USEBB_ROOT_PATH", realpath("./"));
}

date_default_timezone_set("UTC");

// UseBB
spl_autoload_register(function($name) {
	if (strncmp($name, "UseBB\\", 6) !== 0) {
		return;
	}
	
	$file = USEBB_ROOT_PATH . str_replace("\\", "/", 
		substr_replace($name, "/", 0, 6)) . ".php";
	
	if (!file_exists($file)) {
		return;
	}
	
	require $file;
});

// Doctrine

require_once "Doctrine/Common/ClassLoader.php";

$doctrine = new Doctrine\Common\ClassLoader("Doctrine");
$doctrine->register();

// Include local database configuration.
$dbConfig = new UseBB\Utils\File\InfoFile("./dbConfig.php");
$dbConfig = $dbConfig->getInfo();
