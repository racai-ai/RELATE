<?php

namespace Modules\ttlchunker;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/".$settings->get("dir.annotated")."/";
    $finalFile=$path.$fnameOut;

    $conllup=new \CONLLUP();
    $conllup->readFromString($contentIn);
    
    foreach($conllup->getSentenceIterator() as $sent){
        $msd=[];
        foreach($sent->getTokenIterator() as $tok){
            $msd[]=$tok->get("XPOS");
        }
        $data=TTLChunker_chunkSentence($msd);

        $chunks=[];
        if(is_array($data) && isset($data['status']) && $data['status']=="OK" && isset($data['chunks']))
            $chunks=explode("\n",$data['chunks']);

        $n=0;
        foreach($sent->getTokenIterator() as $tok){
            $chunk="_";
            if($n<count($chunks))$chunk=$chunks[$n];
            $tok->set("XPOS",$chunk);
        }
    }

    $conllup->writeToFile($finalFile);
    
    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}


?>