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
if(!$corpus->hasRights("readwrite"))die("Invalid corpus");

$dstPath=$corpus->getFilePath($file,"files",false,true);
if($dstPath===false)die("Invalid file");

if(!is_file($dstPath['file'])){ // create meta file
    file_put_contents($dstPath['meta'],json_encode([
        "name"=>$file,
        "corpus"=>$corpus->getName(),
        "type"=>"text",
        "desc"=>"",
        "created_by"=>$user->getUsername(),
        "created_date"=>date("Y-m-d")
    ]));
}
file_put_contents($dstPath['file'],$content);

file_put_contents($corpus->getFolderPath()."/changed_files.json",json_encode(["changed"=>time()]));


echo json_encode(["status"=>"OK"]);

?>