<?php

namespace Modules\iate_eurovoc;

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
    
    $data=IATE_EUROVOC_Annotate($contentIn,$runner->getRunnerId()+1);
    if(strlen($data)>1000 || strlen($data)>strlen($contentIn)){
        $conllup=new \CONLLUP();
        $conllup->readFromString($contentIn);
        $eurovoc=new \CONLLUP();
        $eurovoc->readFromString($data);
        $eurovocIt=$eurovoc->getTokenIterator();
        $eurovocIt->rewind();
        $c1=$conllup->getNumColumns();
        $c2=$c1+1;
        foreach($conllup->getTokenIterator() as $tok){
            $toke=$eurovocIt->current();
            $tok->set("RELATE:IATE",$toke->get("C${c1}"));
            $tok->set("RELATE:EUROVOC",$toke->get("C${c2}"));
        }
        $conllup->writeToFile($finalFile);
    }else{
        echo "ERROR $fnameOut\n";
        return false;
    }
    

    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}


?>