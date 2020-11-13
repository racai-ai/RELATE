<?php

if(!isset($_REQUEST['text']))die("Invalid call");

$data=file_get_contents("http://127.0.0.1:8202/anonymize?text=".urlencode($_REQUEST['text']));
//$data='{"text":"Acesta este un test.","mappings":["aaaa\\t_#312312#"]}';

echo $data;

die();
