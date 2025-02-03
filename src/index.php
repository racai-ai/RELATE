<?php

require "lib/lib.php";
require_once "securimage/securimage.php";

$settings=new Settings();
$settings->load();

$additionalPath=$settings->get("path","");
if(is_string($additionalPath) && strlen($additionalPath)>0){
    putenv("PATH=$additionalPath");
}

$modules=new Modules();
$modules->load();

$components=new Components();
$components->load();
$components->registerHandlers();

$PLATFORM=[
  //"path"=>"teprolin/complete",   // this is the main page
  "path"=>$settings->get("platform.default_page"),
  "menu"=>$components->getMenu()
];


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