<?php

if(!isset($_REQUEST['text']))die("Invalid call");
if(!isset($_REQUEST['num']))die("Invalid call");
if(!isset($_REQUEST['threshold']))die("Invalid call");

$text=cleanupTextForServerFastText($_REQUEST['text']);

$ret=callFastTextClassifier(
    $text,
    "http://127.0.0.1:8201/classifier_predict",
    intval($_REQUEST['num']),floatval($_REQUEST['threshold']),true
);

//$ret=["428","7927"];

sort($ret);
$mtids=EUROVOC_getMT($ret);
sort($mtids);
$mt=EUROVOC_getMTText($mtids);
$ids=EUROVOC_getIdText($ret);
$domains=EUROVOC_getDomains($mtids);
sort($domains);
$dtext=EUROVOC_getDomainText($domains);

$mtRet=[];
foreach($mt as $k=>$v)$mtRet[]=["mt"=>$k,"text"=>$v];

$idRet=[];
foreach($ids as $k=>$v)$idRet[]=["id"=>$k,"text"=>$v];

$dRet=[];
foreach($dtext as $k=>$v)$dRet[]=["d"=>$k,"text"=>$v];


die(json_encode(["status"=>"OK","ids"=>$idRet,"mt"=>$mtRet,"domains"=>$dRet]));
