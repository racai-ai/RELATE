<?php

namespace Modules\deanonymization;

function deanonymizeFileContent($fcontent,$fpathOut){
    file_put_contents($fpathOut,ANONYMIZATION_deanonymize_text($fcontent));
}

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/files/";
    $fnameOut=changeFileExtension($fnameOut,"txt");
    $finalFile=$path.$fnameOut;
    echo "Destination for deanonymization $finalFile\n";
    @mkdir($path);        

    deanonymizeFileContent($contentIn,$finalFile);
    
    file_put_contents($corpus->getFolderPath()."/changed_files.json",json_encode(["changed"=>time()]));            
}


?>