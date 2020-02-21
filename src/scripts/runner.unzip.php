<?php

function runUnzip($fnameIn,$pathOut){
    global $runnerFolder,$corpus,$settings,$taskDesc;
    
    @mkdir($pathOut);    

    passthru("unzip -j -o ".escapeshellarg($fnameIn)." -d ".escapeshellarg($pathOut));
   
    $dir_meta=$corpus->getFolderPath();
    $dir_meta.="/meta";
    @mkdir($dir_meta);

    $dir_standoff=$corpus->getFolderPath();
    $dir_standoff.="/standoff";
    @mkdir($dir_standoff);

    $dh = opendir($pathOut);
    while (($file = readdir($dh)) !== false) {
        $pathFile=$pathOut."/".$file;
        if(!is_file($pathFile))continue;
        
        $pathMeta=$dir_meta."/".$file;
        $pathStandoff=$dir_standoff."/".$file;
        
        if(endsWith(strtolower($file),".txt")){
            if(!is_file($pathMeta)){
                file_put_contents($dir_meta."/".$file.".meta",json_encode([
                    'name' => $file,
                    'corpus' => $corpus->getData("name","unknown"),
                    'type' => 'text',
                    'desc' => '',
                    'created_by' => $taskDesc['created_by'],
                    'created_date' => $taskDesc['created_date']
                ]));
                
            }
        }else{
            @rename($pathFile,$pathStandoff);
        }
    }
    closedir($dh);
        
    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
    
}