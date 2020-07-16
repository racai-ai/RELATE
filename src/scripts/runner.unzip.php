<?php

function runUnzip($fnameIn,$pathOut){
    global $runnerFolder,$corpus,$settings,$taskDesc, $DirectoryAnnotated;
    
    @mkdir($pathOut);    

    passthru("unzip -j -o ".escapeshellarg($fnameIn)." -d ".escapeshellarg($pathOut));
   
    $dir_meta=$corpus->getFolderPath();
    $dir_meta.="/meta";
    @mkdir($dir_meta);

    $dir_standoff=$corpus->getFolderPath();
    $dir_standoff.="/standoff";
    @mkdir($dir_standoff);

    $dir_annotated=$corpus->getFolderPath();
    $dir_annotated.="/".$DirectoryAnnotated;
    @mkdir($dir_annotated);

    $dh = opendir($pathOut);
    while (($file = readdir($dh)) !== false) {
        $pathFile=$pathOut."/".$file;
        if(!is_file($pathFile))continue;
        
        $pathMeta=$dir_meta."/".$file;
        $pathStandoff=$dir_standoff."/".$file;
        $pathAnnotated=$dir_annotated."/".$file;
        
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
        }else if(endsWith(strtolower($file),".conllu") || endsWith(strtolower($file),".conllup")){
            @rename($pathFile,$pathAnnotated);
        }else{
            @rename($pathFile,$pathStandoff);
        }
    }
    closedir($dh);
        
    file_put_contents($corpus->getFolderPath()."/changed_files.json",json_encode(["changed"=>time()]));            
    file_put_contents($corpus->getFolderPath()."/changed_standoff.json",json_encode(["changed"=>time()]));            
    file_put_contents($corpus->getFolderPath()."/changed_annotated.json",json_encode(["changed"=>time()]));            
    
}

function runUnzipAnnotated($fnameIn,$pathOut){
    global $runnerFolder,$corpus,$settings,$taskDesc;
    
    @mkdir($pathOut);    

    passthru("unzip -j -o ".escapeshellarg($fnameIn)." -d ".escapeshellarg($pathOut));
   
    file_put_contents($corpus->getFolderPath()."/changed_annotated.json",json_encode(["changed"=>time()]));            
}
