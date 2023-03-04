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

function isAllowedExtension($n){
	$ext=['xml','ann','json'];
	foreach($ext as $e)if(endsWith(strtolower($n),$e))return true;
	return false;
}

$ret=[];
foreach($corpus->getFilesStandoff() as $meta){
    if(startsWith($meta['name'],$fname)){
		if(isAllowedExtension($meta['name']))
			$meta["content"]=file_get_contents($corpus->getFilePathStandoff($meta['name']));
		else $meta["content"]=false;
        $ret[]=$meta;
    }
}

echo json_encode($ret);

?>