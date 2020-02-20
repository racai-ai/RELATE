<?php

function runIateEurovoc($text,$fout){
    global $corpus;
    
    runIateEurovoc_internal($text,$fout);
    
    file_put_contents($corpus->getFolderPath()."/changed_annotated.json",json_encode(["changed"=>time()]));            
}

function runIateEurovoc_internal($text,$fout){
    global $runnerFolder,$corpus,$settings,$trun,$taskDesc,$DirectoryAnnotated;
    
    $path=$corpus->getFolderPath()."/$DirectoryAnnotated/";
    $finalFile=$path.$fout;
    if(is_file($finalFile)){
        /*if(isset($taskDesc['overwrite']) && $taskDesc['overwrite']===false){
            echo "SKIP $fout\n";
            return false;
        } */
    
        $fmtime=filemtime($finalFile);
        $tctime=strtotime($taskDesc['created_date']);
        if($fmtime>$tctime && filesize($finalFile)>100){
            echo "SKIP $fout\n";
            return false;
        }
    }
    
    @mkdir($path);    
    
    $data=IATE_EUROVOC_Annotate($text,$trun+1);
    if(strlen($data)>1000 || strlen($data)>strlen($text)){
        file_put_contents($finalFile,$data);
    }else{
        echo "ERROR $fout\n";
        return false;
    }
    
    return true;
}
