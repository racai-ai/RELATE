<?php
require_once "reports_util.php";

if(!isset($_REQUEST['project']))die("Invalid call");
if(!isset($_REQUEST['name']))die("Invalid call");
if(!isset($_REQUEST['type']))die("Invalid call");
if(!isset($_REQUEST['person']))die("Invalid call");
if(!isset($_REQUEST['date']))die("Invalid call");
if(!isset($_REQUEST['date2']))die("Invalid call");
if(!isset($_REQUEST['signdate']))die("Invalid call");

$prj=getProjectForReport($_REQUEST['project']);
$rep=getReportByName($prj,$_REQUEST['name']);
$signdate=$_REQUEST['signdate'];
$pname=$_REQUEST['person'];

$date1=$_REQUEST['date'];
$month1=0; if(strlen($date1)>=7)$month1=intval(substr($date1,5,2));
$year1=intval(substr($date1,0,4));

$date2=$_REQUEST['date2'];
$month2=0; if(strlen($date2)>=7)$month2=intval(substr($date2,5,2));
$year2=intval(substr($date2,0,4));

$year=$year1;
$month=$month1;
$finalRepl=false;
while(true){
    $date="$year-$month";

    $repl=getReplDataCommon($date, $signdate, $year, $month, $pname);
    $repl=array_merge($repl, getProjectReplData($prj,false,$year,$month,$pname,$date,$signdate));

    if($finalRepl===false)$finalRepl=$repl;
    else{
        foreach($repl as $k=>$v){
            if(isset($finalRepl[$k]) && is_int($v))$finalRepl[$k]+=$v;
        }
    }

    if($year==$year2 && $month==$month2)break;

    $year+=($month==12)?(1):(0);
    $month=($month==12)?(1):($month+1);
}

makeReport($finalRepl,$pname, "${year1}${month1}-${year2}${month2}", $rep, $prj);


?>