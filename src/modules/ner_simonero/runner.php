<?php

namespace Modules\ner_simonero;

function runNERL($fcontent,$fpathOut,$url){
    $data=NER_callNER($url,$fcontent);
    $data=json_decode($data,true);
    $rdata=[];
    if($data!==null && is_array($data) && isset($data['status']) && $data['status']=="OK"){
        foreach($data['result'] as $ob){
            $rdata[]="${ob['id']}\t${ob['type']} ${ob['start']} ${ob['end']}\t${ob['text']}";
        }

    }
    file_put_contents($fpathOut,implode("\n",$rdata));
}

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/standoff/";
    $fnameOut=changeFileExtension($fnameOut,"ann");
    $finalFile=$path.$fnameOut;
    echo "Destination for NER $finalFile\n";
    @mkdir($path);        

    $ner_models=[
    "simonero" => ["url"=>"http://127.0.0.1:5110/api/v1.0/ner"]
    ];
    $url=$ner_models[$taskDesc['model']]['url'];

    runNERL($contentIn,$finalFile,$url);
    
    file_put_contents($corpus->getFolderPath()."/changed_standoff.json",json_encode(["changed"=>time()]));            
}


?>