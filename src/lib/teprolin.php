<?php

function TEPROLIN_call($data,$process=false,$debug=false){
    global $TEPROLIN_baseurl,$TEPROLIN_baseurls;

    if(!isset($data['text']))return false;

    $ch = curl_init();
  
    set_time_limit(0);
    ini_set("default_socket_timeout", 600);
    
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 600 * 1000); //timeout in seconds
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    if($process===false){
        curl_setopt($ch, CURLOPT_URL,"${TEPROLIN_baseurl}/process");
    }else{
        if($process>=count($TEPROLIN_baseurls))$process=0;
        curl_setopt($ch, CURLOPT_URL,$TEPROLIN_baseurls[$process]."/process");
    }
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    if($debug)curl_setopt($ch, CURLOPT_VERBOSE, 1); 

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $server_output = curl_exec($ch);
    
    curl_close ($ch);
    
    return $server_output;

}

function TEPROLIN_getOperations($process=false){
    global $TEPROLIN_baseurl,$TEPROLIN_baseurls;

    if($process===false){
        $url="${TEPROLIN_baseurl}/operations";
    }else{
        if($process>=count($TEPROLIN_baseurls))$process=0;
        $url=$TEPROLIN_baseurls[$process]."/operations";
    }


    $arr=json_decode(file_get_contents($url),true);
    return $arr['can-do'];
}

function TEPROLIN_getOperations_raw(){
    global $TEPROLIN_baseurl;

    return file_get_contents("${TEPROLIN_baseurl}/operations");
}


function TEPROLIN_getAppsForOp($op){
    global $TEPROLIN_baseurl;

    $arr=json_decode(file_get_contents("${TEPROLIN_baseurl}/apps/".$op),true);
    return $arr[$op];
}

function TEPROLIN_getStat($stat,$period,$num){
    global $TEPROLIN_baseurl;

    $json=json_decode(file_get_contents("${TEPROLIN_baseurl}/stats/${stat}/${period}/${num}"),true);
    $data=$json[$stat];
    if(count($data)<$num){
        for($i=0;$i<$num-count($data);$i++)
            array_unshift($data,["",0]);
    }
    
    return $data;
}

function TEPROLIN_json2conllu($fname,$json,$sid,$useSentId=true,$stripBI=false){
    $conllu=[];
    if(!is_array($json) || !isset($json['teprolin-result']) || !isset($json['teprolin-result']['sentences'])){
        echo "Invalid TEPROLIN result\n";
        var_dump($json);
        return [$conllu,$sid];
    }
    foreach($json['teprolin-result']['sentences'] as $k=>$sentence){
        $sid++;
        if($sid>1)$conllu[]="";
        
        if($useSentId)$conllu[]="# sent_id = ${fname}.{$sid}";
        $conllu[]="# text = ${sentence}";
        $prev_ner="O";
        $prev_chunk="O";
        
        foreach($json['teprolin-result']['tokenized'][$k] as $tok){
            $ner=$tok['ner'];
            
            if(strlen($ner)==0)$ner="O";
            
            $fner=$ner;
            if(strcasecmp($ner,'O')!=0){
                if(startsWith($ner,"B-") || startsWith($ner,"I-"))$ner=substr($ner,2);
                if(!$stripBI){
                  if(strcasecmp($prev_ner,"O")==0)$fner="B-".$ner;
                  else if(strcasecmp($prev_ner,$ner)==0)$fner="I-".$ner;
                  else $fner="B-".$ner;
                }else $fner=$ner;
            }
            $prev_ner=$ner;
            $ner=$fner;
            
            $chunk=$tok['_chunk'];
            $cdata=explode(",",$chunk);
            $chunk="O";
            foreach($cdata as $c){
                if(strncasecmp($c,"Np",2)===0){$chunk=$c;break;}
            }
            $fchunk=$chunk;
            if(strcasecmp($chunk,"O")!=0){
                if(strcasecmp($prev_chunk,$chunk)==0)$fchunk="I-NP";
                else $fchunk="B-NP";
            }
            $prev_chunk=$chunk;
            $chunk=$fchunk;
        
            $deprel=trim($tok['_deprel']);
            if(strlen($deprel)==0)$deprel="_";
        
            $conllu[]= $tok['_id']."\t".
                $tok['_wordform']."\t".
                $tok['_lemma']."\t".
                $tok['upos']."\t".
                $tok['_msd']."\t".
                MSD2UDFEATS($tok['_msd'])."\t". 
                $tok['_head']."\t".
                $deprel."\t".
                "_"."\t".
                "_"."\t".
                $ner."\t".
                $chunk;
        }
        
    }
    
    return [$conllu,$sid];
}

?>