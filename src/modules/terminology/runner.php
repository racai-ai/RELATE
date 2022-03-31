<?php

namespace Modules\terminology;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/".$settings->get("dir.annotated")."/";
    $finalFile=$path.$fnameOut;
    if( (!isset($taskDesc['overwrite']) || $taskDesc['overwrite']===false) && is_file($finalFile)){
        /*if(isset($taskDesc['overwrite']) && $taskDesc['overwrite']===false){
            echo "SKIP $fnameOut\n";
            return false;
        } */
    
        $fmtime=filemtime($finalFile);
        $tctime=strtotime($taskDesc['created_date']);
        if($fmtime>$tctime && filesize($finalFile)>100){
            echo "SKIP $fnameOut\n";
            return false;
        }
    }
    
    $column=$taskDesc['column'];
    $max_term_size=intval($taskDesc['max_term_size']);
    $terminology=$corpus->getFolderPath()."/standoff/".$taskDesc['terminology'];
    
    @mkdir($path);    
    
    $fpathIn=changeFileExtension($fnameOut,".temp_terms");
    file_put_contents($fpathIn,$contentIn);
    
    global $LIB_PATH;
	$cwd=getcwd();
    @chdir("${LIB_PATH}/../modules/terminology/annotator");
    $cmd="./annotator.sh ".
        escapeshellarg($settings->get("tools.python.venv"))." ".
        escapeshellarg($terminology)." ".
        $max_term_size." ".
        escapeshellarg($column)." ".
        escapeshellarg($fpathIn)." ".
        escapeshellarg($fnameOut)
        ;
    echo "RUNNING [$cmd]\n";
    passthru($cmd);
    @unlink($fpathIn);

    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}


?>