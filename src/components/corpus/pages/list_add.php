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
if(!isset($data['hasimage']))addError("Invalid data");
if(!isset($data['hasvideo']))addError("Invalid data");
if(!isset($data['hasgold']))addError("Invalid data");
if(!isset($data['hasbrat']))addError("Invalid data");
if(!isset($data['hasclassification']))addError("Invalid data");
if(!isset($data['hascorrected']))addError("Invalid data");
if(count($data)!=10)addError("Invalid data");

$audio=false; if(isset($data['hasaudio']) && $data['hasaudio']=="yes")$audio=true;
$data['audio']=$audio;
$image=false; if(isset($data['hasimage']) && $data['hasimage']=="yes")$image=true;
$data['image']=$image;
$video=false; if(isset($data['hasvideo']) && $data['hasvideo']=="yes")$video=true;
$data['video']=$video;
$gold=false; if(isset($data['hasgold']) && $data['hasgold']=="yes")$gold=true;
$data['gold']=$gold;
$brat_profiles=false; if(isset($data['hasbrat']) && $data['hasbrat']=="yes")$brat_profiles=true;
$data['brat_profiles']=$brat_profiles;
$has_classification=false; if(isset($data['hasclassification']) && $data['hasclassification']=="yes")$has_classification=true;
$data['hasclassification']=$has_classification;
$has_corrected=false; if(isset($data['hascorrected']) && $data['hascorrected']=="yes")$has_corrected=true;
$data['hascorrected']=$has_corrected;

$corpora=new Corpora();

$data['created_by']=$user->getUsername();
$data['created_date']=strftime("%Y-%m-%d");
$corpus=new Corpus($corpora,$data['name'],$data);
if(!$corpus->saveData(false))addError("Can not save data");

echo json_encode(["status"=>true]);

?>