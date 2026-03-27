<?php
require_once "project.php";
require_once "projects.php";

if(!isset($_REQUEST['project']))die("Invalid call");
if(!isset($_REQUEST['name']))die("Invalid call");
if(!isset($_REQUEST['description']))die("Invalid call");

$projects=new Projects();
$prj=new Project($projects,$_REQUEST['project']);
if(!$prj->loadData())die("Invalid project");
if(!$prj->hasRights("admin"))die("Invalid project");

$prj->addWorkItem([
    "name"=>$_REQUEST['name'], 
    "description"=>$_REQUEST['description'], 
], $user);
$prj->saveData(true);

echo json_encode(["status"=>"OK"]);

?>