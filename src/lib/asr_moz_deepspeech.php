<?php

$ASR_MOZILLA_DEEPSPEECH_URL="http://127.0.0.1:7006/stt";
$ASR_MOZILLA_DEEPSPEECH_DEBUG=false;

function ASR_MOZILLA_DEEPSPEECH_runASR($wave){
		global $ASR_MOZILLA_DEEPSPEECH_URL,$ASR_MOZILLA_DEEPSPEECH_DEBUG;
    
    set_time_limit(80);

    $ch = curl_init();

		$url=$ASR_MOZILLA_DEEPSPEECH_URL;
    
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
        CURLOPT_VERBOSE => $ASR_MOZILLA_DEEPSPEECH_DEBUG
    ));
    
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    if($ASR_MOZILLA_DEEPSPEECH_DEBUG)var_dump($server_output);
    
    
    return $server_output;

}

?>