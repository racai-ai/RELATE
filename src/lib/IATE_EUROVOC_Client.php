<?php

function IATE_EUROVOC_Annotate($conllu,$process=false,$debug=false){
   global $IATE_EUROVOC_Server_URL, $IATE_EUROVOC_Server_URLS;
   $ch = curl_init();
   set_time_limit(0);
   ini_set("default_socket_timeout", 600);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
   curl_setopt($ch, CURLOPT_TIMEOUT_MS, 600 * 1000); //timeout in seconds
   curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
   
    if($process===false){
        curl_setopt($ch, CURLOPT_URL,"${IATE_EUROVOC_Server_URL}/annotate");
    }else{
        if($process>=count($IATE_EUROVOC_Server_URLS))$process=0;
        curl_setopt($ch, CURLOPT_URL,$IATE_EUROVOC_Server_URLS[$process]."/annotate");
    }
   
   curl_setopt($ch, CURLOPT_POST, 1);
   if($debug)curl_setopt($ch, CURLOPT_VERBOSE, 1);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
       'Content-Type: text/text',
       'Content-Length: ' . strlen($conllu))
   );
   curl_setopt($ch, CURLOPT_POSTFIELDS, $conllu);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
   $server_output = curl_exec($ch);
   curl_close ($ch);
   
   return $server_output;
}

?>