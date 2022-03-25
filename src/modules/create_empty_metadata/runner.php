<?php

namespace Modules\create_empty_metadata;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/standoff/";
    $fnameOut=changeFileExtension($fnameOut,$taskDesc['extension']);
    $finalFile=$path.$fnameOut;
    echo "Destination for create metadata $finalFile\n";
    @mkdir($path);        

    file_put_contents($finalFile,"");
    
    file_put_contents($corpus->getFolderPath()."/changed_standoff.json",json_encode(["changed"=>time()]));            
}


?>