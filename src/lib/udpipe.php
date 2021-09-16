<?php

function UDPIPE_call_internal($text,$lang,$process=false,$debug=false){
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


function UDPIPE_call($text,$lang,$process=false,$debug=false){
    global $UDPIPE_baseurls;

	if(!isset($UDPIPE_baseurls[$lang]))return false;

    $sz=strlen($text);
    if($sz<770000){  // size of a known working file was 778787; 780469 failed 
        return UDPIPE_call_internal($text,$lang,$process,$debug);
    }

    // use chunks
    
    $current="";
    $ret="";
    $lastSentId=0;
    foreach(explode("\n",$text) as $line){
        if(strlen($current)+strlen($line)+1>770000){
            $r=UDPIPE_call_internal($current,$lang,$process,$debug);
            if($r===false || $r===null)return false;
			$r=json_decode($r,true);
			if(!isset($r['result']))return false;
            if(strlen($ret)==0){
                $ret=$r['result'];
                foreach(explode("\n",$ret) as $l){
                    if(startsWith($l,"# sent_id ="))$lastSentId++;
                }
            }else{
                foreach(explode("\n",$r['result']) as $l){
                    if(startsWith($l,"# newdoc"))continue;
                    if(startsWith($l,"# sent_id =")){
                        $lastSentId++;
                        $ret.="# sent_id = $lastSentId\n";
                        continue;
                    }
                    $ret.="$l\n";
                }            
            }
            $current=$line;
        }else $current.="$line\n";
    }
    
    $current=trim($current);
    if(strlen($current)>0){
            $r=UDPIPE_call_internal($current,$lang,$process,$debug);
            if($r===false || $r===null)return false;
			$r=json_decode($r,true);
			if(!isset($r['result']))return false;
            if(strlen($ret)==0){
                $ret=$r['result'];
                foreach(explode("\n",$ret) as $l){
                    if(startsWith($l,"# sent_id ="))$lastSentId++;
                }
            }else{
                foreach(explode("\n",$r['result']) as $l){
                    if(startsWith($l,"# newdoc"))continue;
                    if(startsWith($l,"# sent_id =")){
                        $lastSentId++;
                        $ret.="# sent_id = $lastSentId\n";
                        continue;
                    }
                    $ret.="$l\n";
                }            
            }
    }
    
    return json_encode(["result"=>$ret]);
}

?>