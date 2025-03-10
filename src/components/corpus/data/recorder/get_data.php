<?php

if(!isset($_REQUEST['corpus']))die("Invalid call");

$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");
if(!$corpus->hasRights("read"))die("Invalid corpus");

$rname=$user->getProfile("recorder_name","UNKNOWN");
$data=$corpus->getAudioDataNext($rname);

$current=$data['current'];
$total=$data['total'];
$sentence=$data['sent'];

echo json_encode(["status"=>"OK","current"=>($current<0)?($current):($current+1),"total"=>$total,"sentence"=>$sentence]);

?>