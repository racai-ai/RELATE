<?php

global $DirectoryAnnotated;

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['file']))die("Invalid call");
if(!isset($_REQUEST['content']))die("Invalid call");

$file=$_REQUEST['file'];
$content=$_REQUEST['content'];

$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");

$dstPath=$corpus->getFilePath($file,"files");
if($dstPath===false)die("Invalid file");

file_put_contents($dstPath,$content);

file_put_contents($corpus->getFolderPath()."/changed_files.json",json_encode(["changed"=>time()]));


echo json_encode(["status"=>"OK"]);

?>