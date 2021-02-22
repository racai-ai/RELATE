<?php

$ROMANIANTTS_DEBUG=false;
$ROMANIANTTS_URL="http://romaniantts.com/scripts/api-rotts16.php";

function ROMANIANTTS_runTTS($text){
    global $ROMANIANTTS_URL,$ROMANIANTTS_DEBUG,$settings;
    
    set_time_limit(80);

    $ch = curl_init();
    $url=$ROMANIANTTS_URL;
    $key=$settings->get("RomanianTTS.key");
    
    $data=[
				'voice'=>'sam16',
				'inputText' => $text,
				'vocoder' => 'world', 
				'key' => $key
		];
		
		$fields_string = http_build_query($data);
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 60,
        //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $fields_string,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_VERBOSE => $ROMANIANTTS_DEBUG
    ));
    
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    if($ROMANIANTTS_DEBUG)var_dump($server_output);
    
    
    return $server_output;

}

?>