<?php

global $DirectoryAnnotated;

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['file']))die("Invalid call");
if(!isset($_REQUEST['data']))die("Invalid call");
$fname=$_REQUEST['file'];
$data=json_decode($_REQUEST['data'],true);

$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");
if(!$corpus->hasRights("readwrite"))die("Invalid corpus");

$dir=$corpus->getFolderPath()."/standoff/";
@mkdir($dir);
$dir.=$fname;
$dir=changeFileExtension($dir,"classification");
file_put_contents($dir,json_encode($data));

file_put_contents($corpus->getFolderPath()."/changed_standoff.json",json_encode(["changed"=>time()]));

echo json_encode(["status"=>"OK"]);

    exit;

?>