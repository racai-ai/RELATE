<?php

function ANONYMIZATION_anonymize_text($text){

    $url="http://127.0.0.1:8202/anonymize";

    /*$out=[];
    foreach(explode("\n",$text) as $line){
            $l=trim($line);
            if(empty($l))$out[]=$line;
            else{
                $anon=file_get_contents("http://127.0.0.1:8202/anonymize?text=".urlencode($line));
                $anon=json_decode($anon,true);
                $anon=$anon['text'];
                $out[]=$anon;
            }
    }
    
    return implode("\n",$out);*/
    
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
    
    $anon=json_decode($server_output,true);
    $anon=$anon['text'];
    
    
    return $anon;

}
