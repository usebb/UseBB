<?php

$dbConfig = array(
	"production" => array(
		"type"     => "mysql",
		"host"     => "localhost",
		"user"     => "usebb",
		"password" => "usebb",
		"dbname"   => "usebb2_prod",
		"prefix"   => "usebb_"
	),
	"development" => array(
		"type"     => "mysql",
		"host"     => "localhost",
		"user"     => "usebb",
		"password" => "usebb",
		"dbname"   => "usebb2_dev",
		"prefix"   => "usebb_"
	),
	"testing" => array(
		"type"     => "mysql",
		"host"     => "localhost",
		"user"     => "usebb",
		"password" => "usebb",
		"dbname"   => "usebb2_test",
		"prefix"   => "usebb_"
	)
);
