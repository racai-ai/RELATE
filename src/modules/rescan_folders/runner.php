<?php

namespace Modules\rescan_folders;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $fnameIn=$data['fpath'];
    $fnameMeta=$corpus->getFolderPath()."/meta/";
    if(!is_dir($fnameMeta))@mkdir($fnameMeta);
    $fnameMeta.=basename($fnameIn).".meta";
    if(!is_file($fnameMeta)){
        file_put_contents($fnameMeta, json_encode([
            "name"=>basename($fnameIn),
            "type"=>"text",
            "corpus"=>$corpus->getName(),
            "desc"=>"Discovered by Rescan Folder task",
            "created_by"=> $taskDesc['created_by'],
            "created_date"=> $taskDesc['created_date'],
        ]));
    }
    
    file_put_contents($corpus->getFolderPath()."/changed_files.json",json_encode(["changed"=>time()]));
}


?>