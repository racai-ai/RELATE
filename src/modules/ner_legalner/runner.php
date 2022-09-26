<?php

namespace Modules\ner_legalner;

function runNERL($fcontent,$fpathOut,$url){
    $rdata=[];
    
    $last_pos=0;
    $id=1;
    while(true){
        $pos=strpos($fcontent,"\n",$last_pos);
        if($pos===false)$current=substr($fcontent,$last_pos);
        else{
            $current=substr($fcontent,$last_pos,$pos-$last_pos);
        }
        
        if(strlen(trim($current))>0){    
            $data=NER_callNER($url,$current);
            $data=json_decode($data,true);
            if($data!==null && is_array($data) && isset($data['status']) && $data['status']=="OK"){
                foreach($data['result'] as $ob){
                    $ob['start']=intval($ob['start'])+$last_pos;
                    $ob['end']=intval($ob['end'])+$last_pos;
                    $ob['id']="T".$id;
                    $id++;
                    $rdata[]="${ob['id']}\t${ob['type']} ${ob['start']} ${ob['end']}\t${ob['text']}";
                }
        
            }
        }
     
        if($pos===false)break;   
        $last_pos=$pos+1;
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