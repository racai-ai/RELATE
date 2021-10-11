<?php

global $DirectoryAnnotated;

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['file']))die("Invalid call");
if(!isset($_REQUEST['meta']))die("Invalid call");
if(!isset($_REQUEST['content']))die("Invalid call");

$file=$_REQUEST['file'];
$meta=$_REQUEST['meta'];
$content=$_REQUEST['content'];


$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");

file_put_contents($corpus->getFilePathStandoff($meta),$content);

file_put_contents($corpus->getFolderPath()."/changed_standoff.json",json_encode(["changed"=>time()]));


echo json_encode(["status"=>"OK"]);

?>