<?php

if(!isset($_REQUEST['hashtag']))die("Invalid call");

//$data=file_get_contents("http://127.0.0.1:8202/anonymize?text=".urlencode($_REQUEST['text']));
//$data='{"text":"Acesta este un test.","mappings":["aaaa\\t_#312312#"]}';

$hashtag=$_REQUEST['hashtag'];
if(!preg_match("/^[#a-zA-Z0-9_ăîâșțĂÎÂȘȚ]+$/",$hashtag))die("Invalid call");

$cmd="./hashtag.sh ".escapeshellarg($settings->get("tools.python.venv"))." \"$hashtag\"";

passthru($cmd);

die();
