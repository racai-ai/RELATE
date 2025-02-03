<?php

global $DirectoryAnnotated;

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['file']))die("Invalid call");
$fname=$_REQUEST['file'];

$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");
if(!$corpus->hasRights("read"))die("Invalid corpus");

$dir=$corpus->getFolderPath()."/standoff/";
@mkdir($dir);
$dir.=$fname;
$dir=changeFileExtension($dir,"corrected");
if(is_file($dir)){
    echo json_encode(["status"=>"OK","data"=>file_get_contents($dir)]);
}else{
    echo json_encode(["status"=>"ERROR"]);
}

    exit;

?>