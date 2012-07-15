<?php

require "./includes/init.php";

$core = new UseBB\Core($dbConfig);
$core->handleRequest();
