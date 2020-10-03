<?php

namespace Modules\udpipe;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/".$settings->get("dir.annotated")."/";
    $finalFile=$path.$fnameOut;
    if(is_file($finalFile)){
        if(filesize($finalFile)>0 && isset($taskDesc['overwrite']) && $taskDesc['overwrite']===false){
            echo "SKIP $fnameOut\n";
            return false;
        }
    
        $fmtime=filemtime($finalFile);
        $tctime=strtotime($taskDesc['created_date']);
        if($fmtime>$tctime && filesize($finalFile)>100){
            echo "SKIP $fout\n";
            return false;
        }
    }
    
    @mkdir($path);    
    
    $lang=$corpus->getData("lang","en");
    
    $r=UDPIPE_call($contentIn,$lang,$runner->getRunnerId()+1);
    
    if($r!==false && $r!==null){
				$r=json_decode($r,true);
				if(isset($r['result']))file_put_contents($finalFile,\CONLLUP::$defaultGlobalColumns."\n".$r['result']);
		}
    
    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}


?>