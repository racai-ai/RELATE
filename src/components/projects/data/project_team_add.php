<?php
require_once "project.php";
require_once "projects.php";

if(!isset($_REQUEST['project']))die("Invalid call");
if(!isset($_REQUEST['name']))die("Invalid call");
if(!isset($_REQUEST['supervisor']))die("Invalid call");
if(!isset($_REQUEST['position']))die("Invalid call");
if(!isset($_REQUEST['hour_cost']))die("Invalid call");
if(!isset($_REQUEST['max_daily_hours']))die("Invalid call");
if(!isset($_REQUEST['max_month_hours']))die("Invalid call");
if(!isset($_REQUEST['max_month_cost']))die("Invalid call");

$projects=new Projects();
$prj=new Project($projects,$_REQUEST['project']);
if(!$prj->loadData())die("Invalid project");
if(!$prj->hasRights("admin"))die("Invalid project");

$prj->addTeamMember([
    "name"=>$_REQUEST['name'], 
    "supervisor"=>$_REQUEST['supervisor'], 
    "position"=>$_REQUEST['position'], 
    "hour_cost"=>$_REQUEST['hour_cost'], 
    "max_daily_hours"=>$_REQUEST['max_daily_hours'],
    "max_month_hours"=>$_REQUEST['max_month_hours'],
    "max_month_cost"=>$_REQUEST['max_month_cost'],
], $user);
$prj->saveData(true);

echo json_encode(["status"=>"OK"]);

?>