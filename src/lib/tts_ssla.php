<?php

$TTS_SSLA_DEBUG=false;
$TTS_SSLA_URL="https://slp.racai.ro/services/tts/synth.php";

function TTS_SSLA_runTTS($text){
    global $TTS_SSLA_URL,$TTS_SSLA_DEBUG,$settings;
    
    set_time_limit(80);

    $ch = curl_init();
    $url=$TTS_SSLA_URL;
    
    $url.="?lang=ro&coder=straight&voice=anca&text=".urlencode($text);
		
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 60,
        //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //CURLOPT_CUSTOMREQUEST => "POST",
        //CURLOPT_POST => 1,
        //CURLOPT_POSTFIELDS => $fields_string,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_VERBOSE => $TTS_SSLA_DEBUG
    ));
    
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    if($TTS_SSLA_DEBUG)var_dump($server_output);
    
    
    return $server_output;

}

?>