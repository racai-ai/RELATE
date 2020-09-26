<?php

    if(!isset($_REQUEST['word']))die();
    $word=$_REQUEST['word'];
    if(!isset($_REQUEST['sid']))die();
    $sid=$_REQUEST['sid'];
    if(!isset($_REQUEST['wn']))die();
    $wn=$_REQUEST['wn'];
    
    $port=0;
    if(strcasecmp($wn,"ro")==0)$port=8012;
    else if(strcasecmp($wn,"en")==0)$port=8013;
    else die();
    
    $data=ROWN_call($word,$sid,$port);
    
    echo $data;
?>