<?php
function custom_error_handler($errno, $errstr, $errfile="", $errline="")
{
	$the_date 	= date('Y-m-d h:i:s'). "  ";
	$string		= "\n". $the_date . $errstr ."--". $errfile ."--". $errline;

	error_log($string, 3, __PATH_ROOT__."logs". DIRECTORY_SEPARATOR ."site.log");
	echo $string;
	die();
}
?>