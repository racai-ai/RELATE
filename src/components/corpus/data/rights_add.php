<?php

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['pattern']))die("Invalid call");
if(!isset($_REQUEST['rights']))die("Invalid call");

$pattern=$_REQUEST['pattern'];
$rights=$_REQUEST['rights'];

$validRights=array_flip(["admin","readwrite","read"]);
if(!isset($validRights[$rights]))die("Invalid call");

$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");
if(!$corpus->hasRights("admin"))die("Invalid corpus");

$corpus->addRights($pattern, $rights, $user);
$corpus->saveData(true);

echo json_encode(["status"=>"OK"]);

?>