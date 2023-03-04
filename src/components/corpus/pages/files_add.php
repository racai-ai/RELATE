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

if($data['type']=='csv'){
		$data['delimiter']=str_replace('\t',"\t",$data['delimiter']);
}else if($data['type']=='zip_text' || $data['type']=='text' || $data['type']=='pdf'){
    $meta=$corpus->getMetadataProfile();
    $data["meta"]=[];
    if(is_array($meta) && isset($meta["fields"])){
        foreach($meta["fields"] as $f){
            if($f["onupload"]){
                $data["meta"][$f["field"]]=isset($_REQUEST[$f["field"]])?($_REQUEST[$f["field"]]):"";
            }
        }
    }

}

if($corpus->addUploadedFile($_FILES['file']['tmp_name'],$data)===false)addError("Error adding file");

if($data['type']=="zip_text" || $data['type']=="zip_annotated")
    header("Location: index.php?path=corpus/corpus&name=".$data['corpus']."#tasks");
else if($data['type']=="standoff")
    header("Location: index.php?path=corpus/corpus&name=".$data['corpus']."#standoff");
else if($data['type']=="goldann")
    header("Location: index.php?path=corpus/corpus&name=".$data['corpus']."#goldann");
else if($data['type']=="goldstandoff")
    header("Location: index.php?path=corpus/corpus&name=".$data['corpus']."#goldstandoff");
else if($data['type']=="annotated")
    header("Location: index.php?path=corpus/corpus&name=".$data['corpus']."#basictagging");
else
    header("Location: index.php?path=corpus/corpus&name=".$data['corpus']);
die();


?>