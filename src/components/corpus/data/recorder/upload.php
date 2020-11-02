<?php

if(!isset($_REQUEST['corpus']) || !isset($_FILES['blob']))die("Invalid call");

$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");

$rname=$user->getProfile("recorder_name","UNKNOWN");

$data=$corpus->getAudioDataNext($rname);
$current=$data['current'];
if($current<0)die("Invalid current file");

$fname=$rname."_".$current.".wav";
$path=$corpus->getFolderPath()."/audio/";
if(!is_dir($path))@mkdir($path);
$path.=$fname;

if(is_file($path))@unlink($path);

move_uploaded_file($_FILES['blob']['tmp_name'],$path);

$data=$corpus->getAudioDataNext($rname);
$total=$data['total'];
$sentence=$data['sent'];
$current=$data['current'];

echo json_encode(["status"=>"OK","current"=>($current<0)?($current):($current+1),"total"=>$total,"sentence"=>$sentence]);

?>