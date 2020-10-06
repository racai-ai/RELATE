<?php

namespace Modules\goldnelist;

$firstRun=true;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    global $firstRun;
    
    $finalFile=$corpus->getFolderPath()."/gold_standoff/ne.gazetteer";
    
    if($firstRun===false){
    }else{
        file_put_contents($finalFile,"");
        $firstRun=false;
    }

    $NE=[];
    foreach(explode("\n",file_get_contents($finalFile)) as $line){
        $ldata=explode(" ",$line,2);
        if(count($ldata)!=2)continue;
        $NE[$ldata[1]]=$ldata[0];
    }    
    
    
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