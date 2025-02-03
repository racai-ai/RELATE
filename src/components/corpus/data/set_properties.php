<?php

if(!isset($_REQUEST['corpus']))die("Invalid call");

$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");
if(!$corpus->hasRights("readwrite"))die("Invalid corpus");

$props=[
    "proplang" => ["field" => "lang", "type" => "string"],
    "propdesc" => ["field" => "desc", "type" => "string"],
    "prophasaudio" => ["field" => "audio", "type" => "bool"],
    "prophasimage" => ["field" => "image", "type" => "bool"],
    "prophasvideo" => ["field" => "video", "type" => "bool"],
    "prophasgold" => ["field" => "gold", "type" => "bool"],
    "prophasbrat" => ["field" => "brat_profiles", "type" => "bool"],
    "prophasclassification" => ["field" => "hasclassification", "type" => "bool"],
    "prophascorrected" => ["field" => "hascorrected", "type" => "bool"],
];

foreach($props as $k=>$v){
    if(!isset($_REQUEST[$k]) && $v['type']!="bool")die("Invalid call");
    $data=false; 
    if(isset($_REQUEST[$k])){
        $data=$_REQUEST[$k];
        if($v['type']=="bool")$data= (($data=="yes")?(true):(false));
    }
    $corpus->setData($v['field'],$data);
}

$corpus->setData("modified_by",$user->getUsername());
$corpus->setData("modified_date",date("Y-m-d"));
$corpus->saveData(true);

header('Location: index.php?path=corpus/corpus&name='.$corpus->getName());
die();
