<?php

if(!isset($_REQUEST['text']))die("Invalid call");
if(!isset($_REQUEST['model']))die("Invalid call");

$models=[
"qa_covid" => ["url"=>"http://localhost:9550/respond?question="],
];

$text=$_REQUEST['text'];
$model=$_REQUEST['model'];
if(!isset($models[$model]))die("Invalid call");


$url=$models[$model]["url"].urlencode($text);
$data=file_get_contents($url);
echo $data;

die(); 
