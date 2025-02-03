<?php

if(!isset($_REQUEST['log']))die("Invalid call");

$log=$_REQUEST['log'];
$logFilePath=false;
if($log=="scheduler"){
    $logFilePath="${LIB_PATH}/../scripts/scheduler.log";
}else if(startsWith($log,"runner.")){
    $nRunner=intval(substr($log,7));
    if($nRunner<0 || $nRunner>=$settings->get("TaskRunners"))die("Invalid call");
    $logFilePath="${LIB_PATH}/../scripts/runner.${nRunner}.log";
}

if($logFilePath===false)die("Invalid call");

passthru("tail -n 100 \"$logFilePath\" 2>&1");

