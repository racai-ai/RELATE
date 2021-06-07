<?php

if(!isset($_REQUEST['text']))die("Invalid call");
if(!isset($_REQUEST['model']))die("Invalid call");

$models=[
"legalnero_legal_per_loc_org_time" => ["url"=>"http://127.0.0.1:5101/api/v1.0/ner"],
"legalnero_per_loc_org_time" => ["url"=>"http://127.0.0.1:5102/api/v1.0/ner"],
];

$text=$_REQUEST['text'];
$model=$_REQUEST['model'];
if(!isset($models[$model]))die("Invalid call");

$url=$models[$model]["url"];

$data=file_get_contents("$url?text=".urlencode($text));
//$data='{"text":"Acesta este un test.","mappings":["aaaa\\t_#312312#"]}';

echo $data;

die();
