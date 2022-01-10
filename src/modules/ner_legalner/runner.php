<?php

namespace Modules\ner_legalner;

function runNERL($fcontent,$fpathOut,$url){
    file_put_contents($fpathOut,NER_callNER($url,$fcontent));
}

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/standoff/";
    $fnameOut=changeFileExtension($fnameOut,"ann");
    $finalFile=$path.$fnameOut;
    echo "Destination for NER $finalFile\n";
    @mkdir($path);        

    $ner_models=[
    "legalnero_legal_per_loc_org_time" => ["url"=>"http://127.0.0.1:5101/api/v1.0/ner"],
    "legalnero_per_loc_org_time" => ["url"=>"http://127.0.0.1:5102/api/v1.0/ner"],
    "legalnero_legal_per_loc_org_time_gaz" => ["url"=>"http://127.0.0.1:5103/api/v1.0/ner"],
    "legalnero_per_loc_org_time_gaz" => ["url"=>"http://127.0.0.1:5104/api/v1.0/ner"],
    ];
    $url=$ner_models[$taskDesc['model']]['url'];

    runNERL($contentIn,$finalFile,$url);
    
    file_put_contents($corpus->getFolderPath()."/changed_standoff.json",json_encode(["changed"=>time()]));            
}


?>