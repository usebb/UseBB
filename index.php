<?php

require "./includes/init.php";

$core = new UseBB\Core("development", $dbConfig);
$core->handleRequest();
