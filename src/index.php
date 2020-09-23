<?php

require "lib/lib.php";

require_once "securimage/securimage.php";

require_once "handlers.php";

require_once "menu.php";

$settings=new Settings();
$settings->load();

$modules=new Modules();
$modules->load();

session_start();

$user=new User();
$user->initFromSession();

if(!$user->isLoggedIn() && isset($_REQUEST['path']) && 
    isset($HANDLERS[$_REQUEST['path']]) && $HANDLERS[$_REQUEST['path']]['isData']
){
    $user->initFromHeaders();
}


if(isset($_REQUEST['path']) && isset($HANDLERS[$_REQUEST['path']]) && $user->hasAccess($HANDLERS[$_REQUEST['path']]['rights']))
    $PLATFORM['path']=$_REQUEST['path'];

require $HANDLERS[$PLATFORM['path']]['fname'];

if(function_exists("PageInit"))PageInit();

session_write_close();
@ob_end_flush();

if($HANDLERS[$PLATFORM['path']]['isData'])die();

require "template.php";

?>