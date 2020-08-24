<?php

function UDPIPE_call($text,$lang,$process=false,$debug=false){
    global $UDPIPE_baseurls;

		if(!isset($UDPIPE_baseurls[$lang]))return false;

    $ch = curl_init();
  
    set_time_limit(0);
    ini_set("default_socket_timeout", 600);
    
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 600 * 1000); //timeout in seconds
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    if($process===false)$process=0;
    if($process>=count($UDPIPE_baseurls[$lang]))$process=0;
    curl_setopt($ch, CURLOPT_URL,$UDPIPE_baseurls[$lang][$process]."/process");
    curl_setopt($ch, CURLOPT_POST, 1);
    $data=["data"=>$text,"tokenizer"=>"","tagger"=>"","parser"=>""];
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    if($debug)curl_setopt($ch, CURLOPT_VERBOSE, 1); 

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    return $server_output;

}

?>