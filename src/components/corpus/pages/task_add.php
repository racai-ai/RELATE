<?php

function addError($reason){
  echo json_encode(["status"=>false,"reason"=>$reason]);
  die();
}

$expected=["corpus","type","desc"];

$data=[];
foreach($expected as $e){
    if(!isset($_REQUEST[$e]))addError("Invalid call");
    
    $data[$e]=$_REQUEST[$e];
}

if(isset($_REQUEST['runners']))$data['runners']=trim($_REQUEST['runners']);
if(isset($_REQUEST['fname']))$data['fname']=trim($_REQUEST['fname']);
if(isset($_REQUEST['overwrite']))$data['overwrite']=(strcasecmp(trim($_REQUEST['overwrite']),"1")===0);
else $data['overwrite']=false;

$modules->setTaskDefaults($data['type'],$data);

$corpora=new Corpora();
$corpus=new Corpus($corpora,$data['corpus']);
if(!$corpus->loadData())addError("Invalid corpus");

$tasks=new Task($corpus);

$data['created_by']=$user->getUsername();
$data['created_date']=strftime("%Y-%m-%d %H:%M:%S");

if($tasks->addTask($data)===false)addError("Error adding task");

header("Location: index.php?path=corpus/corpus&name=".$data['corpus']."#tasks");
die();


?>