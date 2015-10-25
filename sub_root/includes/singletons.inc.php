<?php
/**
 * Singleton function to connect to the database
 * @param array $params An array of connection parameters
 * @return obj    Returns a mysqli database object
 *
 */
function connect_db($params=null)
{
	$params	= ( $params == null ) ? $_SESSION["db_data"] : $params;
	$db	= MySQLIE::connect_db($params);
	return $db;
}
/*
 *  Creates a GUID
 *  @return GUID A Globally Unique ID
 */
function guid(){
	$GUID = GUID::getguid();
	$id = $GUID->create_guid();
	return $id;
}

function connect_gateway($name = null, $key = null)
{
	global $authdotnet;
	$name		= ( $name == null ) ? $authdotnet['name'] : $name;
	$key		= ( $key == null ) ? $authdotnet['key'] : $key;
	$gateway	= AuthorizeDotNet::connect_gateway($name, $key);
	return $gateway;
}
?>