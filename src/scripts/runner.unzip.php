<?php

function runUnzip($fnameIn,$pathOut){
    global $runnerFolder,$corpus,$settings,$taskDesc;
    
    @mkdir($pathOut);    

    passthru("unzip -j -o ".escapeshellarg($fnameIn)." -d ".escapeshellarg($pathOut));
   
    $dir_meta=$corpus->getFolderPath();
    $dir_meta.="/meta";
    @mkdir($dir_meta);


    $dh = opendir($pathOut);
    while (($file = readdir($dh)) !== false) {
        if(is_file($pathOut."/".$file) && !is_file($dir_meta."/".$file)){
            file_put_contents($dir_meta."/".$file.".meta",json_encode([
                'name' => $file,
                'corpus' => $corpus->getData("name","unknown"),
                'type' => 'text',
                'desc' => '',
                'created_by' => $taskDesc['created_by'],
                'created_date' => $taskDesc['created_date']
            ]));
            
        }
    }
    closedir($dh);
        
    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
    
}