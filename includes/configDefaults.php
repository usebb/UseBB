<?php

/*
 * This is a file listing default values for the core system. It is NOT
 * the replacement of config.php of UseBB 1, and should not be edited to
 * adjust configuration settings.
 * 
 * Please use the administration control panel or the CLI utility (TODO) to
 * change settings.
 */

$configDefaults = array(
	"installed"                 => array(
		"production"  => FALSE,
		"development" => FALSE,
		"testing"     => TRUE
	),
	"enabled"                   => TRUE,
	"cookiePath"                => "",
	"cookieDomain"              => "",
	"sessionLifetime"           => 1800,
	"logMode"                   => "enabled",
	"logExcludedModules"        => array(),
	"logIncludedModules"        => array(),
	"mailEnableSenderParameter" => FALSE
);
