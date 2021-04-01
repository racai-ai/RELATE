<?php

if(!isset($_REQUEST['pin'])){die();}

$allowed="abcdefghijklmnopqrstuvwxyz0123456789";
$allowedArr=[];
for($i=0;$i<strlen($allowed);$i++)$allowedArr[$allowed[$i]]=1;
$ok=true;
for($i=0;$i<strlen($pin);$i++)if(!isset($allowedArr[$pin[$i]]))$ok=false;
if(strlen($pin)<4 || strlen($pin)>50 || !$ok || !is_file("$LIB_PATH/../DB/datag/$pin/metadata.json")){
    die();
}

$fname="$LIB_PATH/../DB/datag/$pin/list.json";
if(is_file($fname))
    echo file_get_contents("$LIB_PATH/../DB/datag/$pin/list.json");
else
    echo "[]";


?>