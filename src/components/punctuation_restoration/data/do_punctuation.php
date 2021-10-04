<?php

if(!isset($_REQUEST['text']))die("Invalid call");
if(!isset($_REQUEST['model']))die("Invalid call");

$models=[
"marcell_punctuation" => ["url"=>"http://127.0.0.1:5105/api/v1.0/punctuation"],
];

$text=$_REQUEST['text'];
$model=$_REQUEST['model'];
if(!isset($models[$model]))die("Invalid call");


function callPunctuation($url,$text){
    $ch = curl_init();
    
    $boundary = uniqid();
    $delimiter = '-------------' . $boundary;
    $eol = "\r\n";
    $data="";
    $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="text"; filename="text.txt"'.$eol
                . 'Content-type: text/text'.$eol.$eol
                . $text . $eol;
    $data .= "--" . $delimiter . "--".$eol;

    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 60,
        //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
          //"Authorization: Bearer $TOKEN",
          "Content-Type: multipart/form-data; boundary=" . $delimiter,
          "Content-Length: " . strlen($data)
      
        ),
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        //CURLOPT_VERBOSE => $ROBIN_DEBUG
    ));
    
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);

    return $server_output;
}




$url=$models[$model]["url"];

$data=callPunctuation($url,$text);
//$data='{"text":"Acesta este un test.","mappings":["aaaa\\t_#312312#"]}';

echo $data;

die();
