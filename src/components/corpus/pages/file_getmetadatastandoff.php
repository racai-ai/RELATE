<?php

global $DirectoryAnnotated;

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['file']))die("Invalid call");


$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");

$file=$_REQUEST['file'];
$pos=strrpos($file,".");
$fname=$file;
if($pos!==false)$fname=substr($file,0,$pos);

$ret=[];
foreach($corpus->getFilesStandoff() as $meta){
    if(startsWith($meta['name'],$fname)){
        $meta["content"]=file_get_contents($corpus->getFilePathStandoff($meta['name']));
        $ret[]=$meta;
    }
}

echo json_encode($ret);

?>