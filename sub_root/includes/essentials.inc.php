<?php
/**
 * List of all files needed to be included
 * (prevents need for listing individually)
 */
require_once("config.inc.php");
require_once("singletons.inc.php");
include_once("error_handler.inc.php");

function var_dump2($var)
{
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
}
?>