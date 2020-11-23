<?php

namespace Modules\eurovoc_class;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/".$settings->get("dir.annotated")."/";
    $finalFile=$path.$fnameOut;
    if(is_file($finalFile)){
        /*if(isset($taskDesc['overwrite']) && $taskDesc['overwrite']===false){
            echo "SKIP $fnameOut\n";
            return false;
        } */
    
        $fmtime=filemtime($finalFile);
        $tctime=strtotime($taskDesc['created_date']);
        if($fmtime>$tctime && filesize($finalFile)>100){
            echo "SKIP $fnameOut\n";
            return false;
        }
    }
    
    @mkdir($path);    
    
    $conllup=new \CONLLUP();
    $conllup->readFromString($contentIn);
    $data=EUROVOC_Classify($conllup->getText(),6,0.0,$runner->getRunnerId()+1);    
    if($data!==false){
				$mtids=EUROVOC_getMT($data);
				$domains=EUROVOC_getDomains($mtids);
				sort($domains);
        $conllup->addFileMetadataField("eurovoc_domains",implode("\t",$domains));
        $conllup->writeToFile($finalFile);
    }else{
        echo "ERROR $fnameOut\n";
        return false;
    }
    

    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}


?>