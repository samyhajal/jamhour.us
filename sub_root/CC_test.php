<?php
include_once('./objects/CC_Utility.class.php');
include_once('./objects/CC_List.class.php');
$cc	= new CC_List();
echo "<pre>";
var_dump($cc->getLists());
echo "</pre>";
?>