<?php

function runBasicTaggingText($text,$fout){
    global $corpus;
    
    runBasicTaggingText_internal($text,$fout);
    
    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}

function runBasicTaggingText_internal($text,$fout){
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
    if($lang=="en"){
        runBasicTaggingText_en($text,$finalFile);
    }else if($lang=="ro"){
        runBasicTaggingText_ro($text,$finalFile);
    }
}
