<?php

    if(!isset($_REQUEST['word']))die();
    $word=$_REQUEST['word'];
    if(!isset($_REQUEST['sid']))die();
    $sid=$_REQUEST['sid'];
    
    $data=ROWN_call($word,$sid);

		header('Content-Type: text/json');

    echo $data;
