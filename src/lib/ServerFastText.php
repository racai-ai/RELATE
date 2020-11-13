<?php

function callFastTextClassifier($text,$url,$numLabels,$threshold,$debug=false){
   $ch = curl_init();
   set_time_limit(0);
   ini_set("default_socket_timeout", 600);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
   curl_setopt($ch, CURLOPT_TIMEOUT_MS, 600 * 1000); //timeout in seconds
   curl_setopt($ch, CURLOPT_NOSIGNAL, 1);

   curl_setopt($ch, CURLOPT_URL,"$url?num=$numLabels&threshold=$threshold");

   curl_setopt($ch, CURLOPT_POST, 1);
   if($debug)curl_setopt($ch, CURLOPT_VERBOSE, 1);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
       'Content-Type: text/text',
       'Content-Length: ' . strlen($text))
   );
   curl_setopt($ch, CURLOPT_POSTFIELDS, $text);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
   $server_output = curl_exec($ch);
   curl_close ($ch);
   
   $data=explode(" ",$server_output);
   $dataRet=[];
   foreach($data as $d){
       if(strncasecmp($d,"__label__",9)===0)$d=substr($d,9);
       if(empty($d))continue;
       $dataRet[]=$d;
   }
   
   return $dataRet;
}


function cleanupTextForServerFastText($text){
    $line=str_replace("\n"," ",$text);
    $line=preg_replace("/[<][^>]*[>]/"," ",$line);
    $line=html_entity_decode($line);
    $line=preg_replace("/[()0-9\"%]/"," ",$line);
    $line=trim(preg_replace("/[ ]+/"," ",$line));
    $line=trim(preg_replace("/([.\\\\!?,'\\/()])/"," \${1} ",$line));
    $line=mb_strtolower($line);
    return $line;
}
