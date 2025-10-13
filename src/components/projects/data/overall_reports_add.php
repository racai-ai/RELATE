<?php
require_once "project.php";
require_once "projects.php";
require_once "projects_overall.php";

if(!isset($_REQUEST['name']))die("Invalid call");
if(!isset($_FILES['file']))die("Invalid call");
if(!isset($_REQUEST['description']))die("Invalid call");

$prj=new ProjectsOverall();
//if(!$prj->loadData())die("Invalid project");
//if(!$prj->hasRights("admin"))die("Invalid project");

$prj->addUploadedReport([
    "name"=>$_REQUEST['name'], 
    "description"=>$_REQUEST['description'],
], $user);

echo json_encode(["status"=>"OK"]);

?>