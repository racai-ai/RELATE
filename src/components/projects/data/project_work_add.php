<?php
require_once "project.php";
require_once "projects.php";

if(!isset($_REQUEST['project']))die("Invalid call");
if(!isset($_REQUEST['workitem']))die("Invalid call");
if(!isset($_REQUEST['name']))die("Invalid call");
if(!isset($_REQUEST['hours']))die("Invalid call");
if(!isset($_REQUEST['date']))die("Invalid call");
if(!isset($_REQUEST['description']))die("Invalid call");

$projects=new Projects();
$prj=new Project($projects,$_REQUEST['project']);
if(!$prj->loadData())die("Invalid project");
if(!$prj->hasRights("admin"))die("Invalid project");

$prj->addWork([
    "name"=>$_REQUEST['name'], 
    "workitem"=>$_REQUEST['workitem'], 
    "hours"=>$_REQUEST['hours'], 
    "date"=>$_REQUEST['date'],
    "description"=>$_REQUEST['description'],
], $user);
$prj->saveWork(true);

echo json_encode(["status"=>"OK"]);

?>