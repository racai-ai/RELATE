<?php

namespace Modules\deepnewsdef_image;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/standoff/";
    $finalFile=$path.changeFileExtension($fnameOut,"classification");
    if(is_file($finalFile)){
        if(filesize($finalFile)>0 && isset($taskDesc['overwrite']) && $taskDesc['overwrite']===false){
            echo "SKIP $fnameOut\n";
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
    
    $types=["real","fake"];
    $data=[
        "type"=>$types[random_int(0,count($types)-1)],
        "type"=>$types[random_int(0,count($types)-1)],
        "score"=>1.0/floatval(random_int(1,100)),
        "alg.1.score"=>1.0/floatval(random_int(1,100)),
        "alg.1.details"=>"details from alg1",
        "alg.2.score"=>1.0/floatval(random_int(1,100)),
        "alg.2.details"=>"details from alg2",
        "alg.3.score"=>1.0/floatval(random_int(1,100)),
        "alg.3.details"=>"details from alg3",
    ];
    
    file_put_contents($finalFile,json_encode($data));
    @chown($finalFile,$settings->get("owner_user"));
    @chgrp($finalFile,$settings->get("owner_group"));
    
    
    file_put_contents($corpus->getFolderPath()."/changed_standoff.json",json_encode(["changed"=>time()]));            
}


?>