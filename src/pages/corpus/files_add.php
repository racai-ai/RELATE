<?php

function addError($reason){
  echo json_encode(["status"=>false,"reason"=>$reason]);
  die();
}

if(!isset($_FILES['file']))addError("Invalid call");

$expected=["name","corpus","type","desc"];


$data=[];
foreach($expected as $e){
    if(!isset($_REQUEST[$e]))addError("Invalid call");
    
    $data[$e]=$_REQUEST[$e];
}

$expected=[];
if($data['type']=='csv')
    $expected=["delimiter","enclosure","escape","comment","ignore_rows","columns"];

if(count($expected)>0){
    foreach($expected as $e){
        if(!isset($_REQUEST[$e]))addError("Invalid call");
        
        $data[$e]=$_REQUEST[$e];
    }
}


if(strlen(trim($data['name']))==0)$data['name']=basename($_FILES["file"]["name"]);

$corpora=new Corpora();
$corpus=new Corpus($corpora,$data['corpus']);
if(!$corpus->loadData())addError("Invalid corpus");

$data['created_by']=$user->getUsername();
$data['created_date']=strftime("%Y-%m-%d");

if($corpus->addUploadedFile($_FILES['file']['tmp_name'],$data)===false)addError("Error adding file");

if($data['type']=="zip_text")
    header("Location: index.php?path=corpus/corpus&name=".$data['corpus']."#tasks");
else
    header("Location: index.php?path=corpus/corpus&name=".$data['corpus']);
die();


?>