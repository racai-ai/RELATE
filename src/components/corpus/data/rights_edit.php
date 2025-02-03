<?php

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['pattern']))die("Invalid call");
if(!isset($_REQUEST['rights']))die("Invalid call");
if(!isset($_REQUEST['old_pattern']))die("Invalid call");
if(!isset($_REQUEST['old_rights']))die("Invalid call");

$pattern=$_REQUEST['pattern'];
$rights=$_REQUEST['rights'];
$old_pattern=$_REQUEST['old_pattern'];
$old_rights=$_REQUEST['old_rights'];

$validRights=array_flip(["admin","readwrite","read"]);
if(!isset($validRights[$rights]))die("Invalid call");

$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");
if(!$corpus->hasRights("admin"))die("Invalid corpus");

$corpus->editRights($old_pattern, $old_rights, $pattern, $rights, $user);
$corpus->saveData(true);

echo json_encode(["status"=>"OK"]);

?>