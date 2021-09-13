<?php

$PYEUROVOC_URL="http://127.0.0.1:9024/predict";
$PYEUROVOC_DEBUG=false;

function PYEUROVOC_Predict($text){
		global $PYEUROVOC_URL, $PYEUROVOC_DEBUG;
    
    set_time_limit(80);

    $ch = curl_init();

		$url=$PYEUROVOC_URL;
    
    $data=json_encode(["data"=>$text]) ;

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
          "Content-Type: application/json",
          "Content-Length: " . strlen($data)
      
        ),
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_VERBOSE => $PYEUROVOC_DEBUG
    ));
    
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    if($PYEUROVOC_DEBUG)var_dump($server_output);
    
    
    return $server_output;

}

?>