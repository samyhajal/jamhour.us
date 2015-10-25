<?php
session_start();
//spl_autoload_register('myAutoload');
require_once("./includes/essentials.inc.php");
//set_error_handler("custom_error_handler");
if ( !isset($_SESSION["db_data"]) )
{
	$_SESSION["db_data"]	= $db_config;
}
if ( !isset($_SESSION["error"]) )
{
	$_SESSION["error"]	= "";
}
/**
 * Main function of the controller handles the objects
 */
try
{
	if ( isset($_REQUEST["path"]) && !empty($_REQUEST["path"]) )
	{
		$path	= explode("/",$_REQUEST["path"]);
		$model	= array_shift($path);
		$func	= ( sizeof($path) >= 1 ) ? array_shift($path) : "Display";
		$params	= ( sizeof($path) >= 1 ) ? json_encode($path) : null;
	}
	$obj	= new $model();
	if ( get_parent_class($obj) == 'page' )
	{
		$obj->assign('SITE_ROOT', __SITE__);
		$obj->assign('IMAGE_PATH', __PATH_IMAGES__);
	}
	if ( isset($params) )
	{
		$params	= str_replace(array("[","]"), "", $params);
		eval("\$obj->$func($params);");
	}
	else
	{
		eval("\$obj->$func();");
	}
}
catch ( Exception $e )
{
	//trigger_error($e->getMessage());
	die('Dead'.$e->getMessage());
}

/**
 * Autoload function for calling any class, will auto include
 * any class or page file
 * @param string $class The name of the class/page you are trying to call
 *
 */
function __autoload( $class )
{
	if ( file_exists(__PATH_OBJECTS__.$class.".class.php") )
	{
		include_once(__PATH_OBJECTS__.$class.".class.php");
	}
	elseif( file_exists(__PATH_PAGES__.$class.".page.php") )
	{
		include_once(__PATH_PAGES__.$class.".page.php");
	}
	else
	{
		//header('Location:'.  __SITE__ .'Missing/');
		return false;
	}
}


?>