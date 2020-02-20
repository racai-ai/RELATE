<?php

$options=["text","sysid"];

$data=[];
foreach($options as $o){
    if(isset($_REQUEST[$o]))$data[$o]=$_REQUEST[$o];
}


if(!isset($data['text']) || !isset($data['sysid']))
  die("Invalid usage");

$translate=TILDE_Translate($data['sysid'],$data['text']);

echo json_encode(["text"=>$data['text'],"sysid"=>$data['sysid'],"translate"=>trim($translate,'"')]);

?>