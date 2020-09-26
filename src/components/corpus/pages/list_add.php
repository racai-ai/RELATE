<?php

function addError($reason){
  echo json_encode(["status"=>false,"reason"=>$reason]);
  die();
}

if(!isset($_REQUEST['data']))addError("Invalid call");

$data=json_decode($_REQUEST['data'],true);

if(!isset($data['name']))addError("Invalid data");
if(!isset($data['lang']))addError("Invalid data");
if(!isset($data['desc']))addError("Invalid data");
if(!isset($data['hasaudio']))addError("Invalid data");
if(count($data)!=4)addError("Invalid data");

$audio=false; if(isset($data['hasaudio']) && $data['hasaudio']=="yes")$audio=true;
$data['audio']=$audio;

$corpora=new Corpora();

$data['created_by']=$user->getUsername();
$data['created_date']=strftime("%Y-%m-%d");
$corpus=new Corpus($corpora,$data['name'],$data);
if(!$corpus->saveData(false))addError("Can not save data");

echo json_encode(["status"=>true]);

?>