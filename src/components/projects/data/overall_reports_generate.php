<?php
require_once "project.php";
require_once "projects.php";
require_once "projects_overall.php";
require_once "reports_util.php";

if(!isset($_REQUEST['name']))die("Invalid call");
if(!isset($_REQUEST['type']))die("Invalid call");
if(!isset($_REQUEST['person']))die("Invalid call");
if(!isset($_REQUEST['date']))die("Invalid call");
if(!isset($_REQUEST['signdate']))die("Invalid call");

$pname=$_REQUEST['person'];
$date=$_REQUEST['date'];
$signdate=$_REQUEST['signdate'];
$month=0;
if(strlen($date)>=7)$month=intval(substr($date,5,2));
$dint=intval(str_replace("-","",$date));
$year=intval(substr($_REQUEST['date'],0,4));

$allprj=new ProjectsOverall();
$found=false;
foreach($allprj->getReports() as $rep){
    if($rep['name']==$_REQUEST['name']){$found=true; break;}
}
if(!$found)die("Invalid report");

$repl=getReplDataCommon($date, $signdate, $year, $month, $pname);

$projects=new Projects();
foreach($projects->getList() as $prjData){
    $prj=new Project($projects, $prjData['name']);
    if(!$prj->loadData())continue;
    if(!$prj->hasRights("admin"))continue;

    $prjRepl=getProjectReplData($prj,$prjData['name'].".",$year,$month,$pname,$date,$signdate);
    $repl=array_merge($repl,$prjRepl);
}

var_dump($repl);

makeReport($repl, $pname, $date, $rep, $allprj);

?>