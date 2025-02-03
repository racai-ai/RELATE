<?php

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['current']))die("Invalid call");

$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");
if(!$corpus->hasRights("read"))die("Invalid corpus");

$current=$_REQUEST['current'];

$files=[];
if(startsWith($current,"image/")){
    $files=$corpus->getImage();
    $current=substr($current,6);
}else if(startsWith($current,"audio/")){
    $files=$corpus->getAudio();
    $current=substr($current,6);
}else if(startsWith($current,"video/")){
    $files=$corpus->getVideo();
    $current=substr($current,6);
}else{
    $files=$corpus->getFiles();
}


$found=false;
$next=false;

function cmp($a,$b){
    return strcasecmp($a['name'],$b['name']);
}
usort($files,"cmp");

foreach($files as $ob){
    if($ob['name']==$current){$found=true;continue;}
    if($found){$next=$ob['name'];break;}
}

if($next!==false)echo json_encode(["status"=>"OK","next"=>$next]);
else echo json_encode(["status"=>"ERROR"]);

?>