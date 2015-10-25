<?php
define("__SITE__", "http://www.jamhour.us/");
define("__PATH_ROOT__", '.'.DIRECTORY_SEPARATOR);
define("__PATH_OBJECTS__", __PATH_ROOT__ ."objects". DIRECTORY_SEPARATOR);
define("__PATH_PAGES__", __PATH_ROOT__ ."pages". DIRECTORY_SEPARATOR);
define("__PATH_HTML__", __PATH_ROOT__ ."html". DIRECTORY_SEPARATOR);
define("__PATH_JAVASCRIPT__", __SITE__ ."javascript/");
define("__PATH_CSS__", __SITE__ ."css/");
define("__PATH_IMAGES__",__SITE__."images/");

$db_config["host"]		= "";
$db_config["user"]		= "";
$db_config["pass"]		= "";
$db_config["database"]	= "";


ini_set("display_errors",1);
date_default_timezone_set("America/New_York");
?>