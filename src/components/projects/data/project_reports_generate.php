<?php
require_once "reports_util.php";

if(!isset($_REQUEST['project']))die("Invalid call");
if(!isset($_REQUEST['name']))die("Invalid call");
if(!isset($_REQUEST['type']))die("Invalid call");
if(!isset($_REQUEST['person']))die("Invalid call");
if(!isset($_REQUEST['date']))die("Invalid call");
if(!isset($_REQUEST['signdate']))die("Invalid call");

$prj=getProjectForReport($_REQUEST['project']);
$rep=getReportByName($_REQUEST['name']);
$signdate=$_REQUEST['signdate'];
$pname=$_REQUEST['person'];

$date=$_REQUEST['date'];
$month=0;
if(strlen($date)>=7)$month=intval(substr($date,5,2));
$dint=intval(str_replace("-","",$date));
$year=intval(substr($_REQUEST['date'],0,4));

$repl=getReplDataCommon($date, $signdate, $year, $month, $pname);
$repl=array_merge($repl, getProjectReplData($prj,false,$year,$month,$pname,$date,$signdate));

makeReport($repl,$pname, $date, $rep, $prj);


?>