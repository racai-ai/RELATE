<?php

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['current']))die("Invalid call");

$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");

$current=$_REQUEST['current'];

$files=$corpus->getFiles();

function cmp($a,$b){
    return strcasecmp($a['name'],$b['name']);
}
usort($files,"cmp");

$prev=false;
foreach($files as $ob){
    if($ob['name']==$current){break;}
    $prev=$ob['name'];
}

if($prev!==false)echo json_encode(["status"=>"OK","prev"=>$prev]);
else echo json_encode(["status"=>"ERROR"]);

?>