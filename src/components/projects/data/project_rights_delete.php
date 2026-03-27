<?php
require_once "project.php";
require_once "projects.php";

if(!isset($_REQUEST['project']))die("Invalid call");
if(!isset($_REQUEST['old_pattern']))die("Invalid call");
if(!isset($_REQUEST['old_rights']))die("Invalid call");

$old_pattern=$_REQUEST['old_pattern'];
$old_rights=$_REQUEST['old_rights'];

$projects=new Projects();
$prj=new Project($projects,$_REQUEST['project']);
if(!$prj->loadData())die("Invalid project");
if(!$prj->hasRights("admin"))die("Invalid project");

$prj->deleteRights($old_pattern, $old_rights, $user);
$prj->saveData(true);

echo json_encode(["status"=>"OK"]);

?>