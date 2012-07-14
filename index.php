<?php

// Location of UseBB files.
if (!defined("USEBB_ROOT_PATH")) {
	define("USEBB_ROOT_PATH", realpath("./"));
}

require USEBB_ROOT_PATH . "/includes/init.php";

$core = new UseBB\Core($dbConfig);
$core->handleRequest();
