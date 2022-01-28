<?php

if(!isset($_REQUEST['text']))die("Invalid call");
if(!isset($_REQUEST['model']))die("Invalid call");

$models=[
"marcell_punctuation" => ["url"=>"http://127.0.0.1:5105/api/v1.0/punctuation"],
];

$text=$_REQUEST['text'];
$model=$_REQUEST['model'];
if(!isset($models[$model]))die("Invalid call");


$url=$models[$model]["url"];

$data=callPunctuation($url,$text);
//$data='{"text":"Acesta este un test.","mappings":["aaaa\\t_#312312#"]}';

$data=json_decode($data,true);
$newt="";
$rdata=explode(" ",$data['result']);
$tdata=explode(" ",$text);
$ndata=[];
for($i=0;$i<count($tdata);$i++){
    $ndata[]=$tdata[$i];
    if($rdata[$i]=="COMMA")$ndata[]=",";
    if($rdata[$i]=="PERIOD")$ndata[]=".";
}

if($ndata[count($ndata)-1]!=".")$ndata[]=".";

$data['new_text']=implode(" ",$ndata);
echo json_encode($data);

die();
