<?php

function runUdpipe($text,$fout){
    global $corpus;
    
    runUdpipe_internal($text,$fout);
    
    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}

function runUdpipe_internal($text,$fout){
    global $runnerFolder,$corpus,$settings,$trun,$taskDesc,$DirectoryAnnotated;
    
    $path=$corpus->getFolderPath()."/$DirectoryAnnotated/";
    $finalFile=$path.$fout;
    if(is_file($finalFile)){
        if(filesize($finalFile)>0 && isset($taskDesc['overwrite']) && $taskDesc['overwrite']===false){
            echo "SKIP $fout\n";
            return false;
        }
    
        $fmtime=filemtime($finalFile);
        $tctime=strtotime($taskDesc['created_date']);
        if($fmtime>$tctime && filesize($finalFile)>100){
            echo "SKIP $fout\n";
            return false;
        }
    }
    
    @mkdir($path);    
    
    $lang=$corpus->getData("lang","en");
    
    $r=UDPIPE_call($text,$lang,$trun+1);
    
    if($r!==false && $r!==null){
				$r=json_decode($r,true);
				if(isset($r['result']))file_put_contents($finalFile,$r['result']);
		}
}
