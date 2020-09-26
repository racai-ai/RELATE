<?php

if(!isset($_REQUEST['corpus']))die("Invalid call");

$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");

$rname=$user->getProfile("recorder_name","UNKNOWN");

$current=$corpus->getAudioCurrent($rname);
$data=$corpus->getAudioData($current);
$total=$data['total'];
$sentence=$data['sent'];

echo json_encode(["status"=>"OK","current"=>$current+1,"total"=>$total,"sentence"=>$sentence]);

?>