<?php

$ROBIN_ASR_URL="https://89.38.230.18/upload";
$ROBIN_TTS_URL="http://89.38.230.18:8080/synthesis";
$ROBIN_DEBUG=false;

function ROBIN_runASR($wave){
    global $ROBIN_ASR_URL,$ROBIN_DEBUG;
    
    set_time_limit(80);

    $ch = curl_init();
    $url=$ROBIN_ASR_URL;
    
    $boundary = uniqid();
    $delimiter = '-------------' . $boundary;
    $eol = "\r\n";
    $data="";
    $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="file"; filename="asr.wav"'.$eol
                . 'Content-type: audio/wav'.$eol.$eol
                . $wave . $eol;
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
        CURLOPT_VERBOSE => $ROBIN_DEBUG
    ));
    
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    if($ROBIN_DEBUG)var_dump($server_output);
    
    
    return json_decode($server_output,true);

}

function ROBIN_runTTS($data){
    global $ROBIN_TTS_URL,$ROBIN_DEBUG;
    
    set_time_limit(80);

    $ch = curl_init();
    $url=$ROBIN_TTS_URL;
    
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
          "Content-Type: application/json;charset=UTF-8",
          "Content-Length: " . strlen($data)
      
        ),
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_VERBOSE => $ROBIN_DEBUG
    ));
    
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    if($ROBIN_DEBUG)var_dump($server_output);
    
    
    return $server_output;

}

?>