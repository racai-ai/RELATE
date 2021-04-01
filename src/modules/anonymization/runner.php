<?php

namespace Modules\anonymization;

function anonymizeFileContent($fcontent,$fpathOut){
    file_put_contents($fpathOut,ANONYMIZATION_anonymize_text($fcontent));
}

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/files/";
    $fnameOut=changeFileExtension($fnameOut,"txt")
    $finalFile=$path.$fnameOut;
    echo "Destination for anonymization $finalFile\n";
    @mkdir($path);        

    anonymizeFileContent($contentIn,$finalFile);
    
    file_put_contents($corpus->getFolderPath()."/changed_files.json",json_encode(["changed"=>time()]));            
}


?>