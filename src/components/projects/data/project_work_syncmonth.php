<?php
require_once "project.php";
require_once "projects.php";

if(!isset($_REQUEST['project']))die("Invalid call");
if(!isset($_REQUEST['month']))die("Invalid call");
if(!isset($_REQUEST['work']))die("Invalid call");

$projects=new Projects();
$prj=new Project($projects,$_REQUEST['project']);
if(!$prj->loadData())die("Invalid project");
if(!$prj->hasRights("admin"))die("Invalid project");

$date=$_REQUEST['month'];
$month=0;
if(strlen($date)>=7)$month=intval(substr($date,5,2));
$dint=intval(str_replace("-","",$_REQUEST['month']));
$year=intval($dint/100);

$prj->syncWork(json_decode($_REQUEST['work'],true), $year, $month, $user);

echo json_encode(["status"=>"OK"]);

?>