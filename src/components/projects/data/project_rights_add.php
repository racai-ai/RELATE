<?php
require_once "project.php";
require_once "projects.php";

if(!isset($_REQUEST['project']))die("Invalid call");
if(!isset($_REQUEST['pattern']))die("Invalid call");
if(!isset($_REQUEST['rights']))die("Invalid call");

$pattern=$_REQUEST['pattern'];
$rights=$_REQUEST['rights'];

$validRights=array_flip(["admin","readwrite","read"]);
if(!isset($validRights[$rights]))die("Invalid call");

$projects=new Projects();
$prj=new Project($projects,$_REQUEST['project']);
if(!$prj->loadData())die("Invalid project");
if(!$prj->hasRights("admin"))die("Invalid project");

$prj->addRights($pattern, $rights, $user);
$prj->saveData(true);

echo json_encode(["status"=>"OK"]);

?>