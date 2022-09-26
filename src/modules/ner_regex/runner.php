<?php

namespace Modules\ner_regex;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/".$settings->get("dir.annotated")."/";
    $finalFile=$path.$fnameOut;
    if( (!isset($taskDesc['overwrite']) || $taskDesc['overwrite']===false) && is_file($finalFile)){
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
    
    $column=$taskDesc['column'];
    $regexFile=$corpus->getFolderPath()."/standoff/".$taskDesc['regex'];
    
    $regex=[];
    foreach(explode("\n",file_get_contents($regexFile)) as $line){
        $line=trim($line); if(strlen($line)==0 || $line[0]=="#")continue;
        $r=explode(" ",trim($line),2);
        if(!is_array($r) || count($r)!=2)continue;
        $regex[]=$r;
    }
    
    @mkdir($path);    
    
    $conllup=new \CONLLUP();
    $conllup->readFromString(file_get_contents($finalFile));
    foreach($conllup->getSentenceIterator() as $k_sent=>$sentence){
        $tokIt=$sentence->getTokenIterator();
        foreach($tokIt as $k_tok=>$token){
        
            $w=$token->get("FORM");
            $found=false;
            foreach($regex as $r){
                if(preg_match("/${r[1]}/",$w,$matches,PREG_OFFSET_CAPTURE)===1){
                    $found=$r[0]; break;
                }
            }
            if($found!==false){
                $token->set($column,"B-".$found);
            }else{
                if($token->get($column)===false)$token->set($column,"O");
            }
        
        }
    }
    
    $conllup->writeToFile($finalFile);

    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}


?>