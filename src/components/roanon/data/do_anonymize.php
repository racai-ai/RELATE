<?php

if(!isset($_REQUEST['text']))die("Invalid call");

//$data=file_get_contents("http://127.0.0.1:8202/anonymize?text=".urlencode($_REQUEST['text']));
//$data='{"text":"Acesta este un test.","mappings":["aaaa\\t_#312312#"]}';

$data=ANONYMIZATION_anonymize_text($_REQUEST['text'],false);
if(!is_array($data) || !isset($data['text']))die("ERROR");

$data['text_anon']=$data['text'];
if(isset($_REQUEST['replace_identifiers'])){
    $data['text']=ANONYMIZATION_deanonymize_text($data['text']);
}

echo json_encode($data);

die();
