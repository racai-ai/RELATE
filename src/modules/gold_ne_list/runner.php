<?php

namespace Modules\goldnelist;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $finalFile=$corpus->getFolderPath()."/gold_standoff/ne.gazetteer";
    
    echo "Destination for goldnelist $finalFile\n";
/*    if(is_file($finalFile)){
    
        $fmtime=filemtime($finalFile);
        $tctime=strtotime($taskDesc['created_date']);
        if($fmtime>$tctime && filesize($finalFile)>100){
            echo "SKIP $fout\n";
            return false;
        }
    }
*/    
    
    $NE=[];
    
    foreach(explode("\n",$contentIn) as $line){
				$data=explode("\t",$line);
				if(count($data)!==3)continue;
				list($type,$from,$to)=explode(" ",$data[1],3);
				$NE[$data[2]]=$type;
		}
		
		$fout=fopen($finalFile,"w");
		foreach($NE as $text=>$type)fwrite($fout,"$type $text\n");
		fclose($fout);
    
    file_put_contents($corpus->getFolderPath()."/changed_gold_standoff.json",json_encode(["changed"=>time()]));            
}


?>