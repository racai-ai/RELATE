<?php

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['old_pattern']))die("Invalid call");
if(!isset($_REQUEST['old_rights']))die("Invalid call");

$old_pattern=$_REQUEST['old_pattern'];
$old_rights=$_REQUEST['old_rights'];

$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");
if(!$corpus->hasRights("admin"))die("Invalid corpus");

$corpus->deleteRights($old_pattern, $old_rights, $user);
$corpus->saveData(true);

echo json_encode(["status"=>"OK"]);

?>