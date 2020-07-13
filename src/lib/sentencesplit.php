<?php

function SentenceSplit($data,$process=false,$debug=false){
    return explode("\n",trim(SentenceSplit_call($data,$process,$debug)));
}

function SentenceSplit_call($data,$process=false,$debug=false){
    global $SSPLIT_baseurl,$SSPLIT_baseurls;

    if(!isset($data['text']))return false;

    $ch = curl_init();
  
    set_time_limit(0);
    ini_set("default_socket_timeout", 600);
    
    $datastr="------WebKitFormBoundary6XfRB2HxKQdC87hB\r\nContent-Disposition: form-data; name=\"text\"\r\n\r\n{{data_text}}\r\n------WebKitFormBoundary6XfRB2HxKQdC87hB--\r\n";    
    $datastr=str_replace("{{data_text}}",$data['text'],$datastr);    
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: multipart/form-data; boundary=----WebKitFormBoundary6XfRB2HxKQdC87hB',
    ));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 600 * 1000); //timeout in seconds
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    if($process===false){
        curl_setopt($ch, CURLOPT_URL,"${SSPLIT_baseurl}/split");
    }else{
        if($process>=count($SSPLIT_baseurls))$process=0;
        curl_setopt($ch, CURLOPT_URL,$SSPLIT_baseurls[$process]."/split");
    }
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datastr);
    if($debug)curl_setopt($ch, CURLOPT_VERBOSE, 1); 

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    return $server_output;

}

function SentenceSplit_makeSegments($maxSize, $data, $process=false, $debug=false){
    $sentences=SentenceSplit($data,$process,$debug);
    
    $segments=[];
    
    $current="";
    foreach($sentences as $sent){
	if(strlen($current)+strlen($sent)<$maxSize){
	    if(strlen($current)>0)$current .= " ";
	    $current .= $sent;
	}else{
	    if(strlen($current)>0)$segments[]=$current;
	    $current=$sent;
	}
    }

    if(strlen($current)>0)$segments[]=$current;
    
    return $segments;
}

?>