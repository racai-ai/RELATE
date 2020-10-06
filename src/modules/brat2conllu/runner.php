<?php

namespace Modules\brat2conllu;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $path=$corpus->getFolderPath()."/".$settings->get("dir.annotated")."/";
    $finalFile=$path.$fnameOut;
    
    $path=$corpus->getFolderPath()."/files/";
    $textFile=$path.$fnameOut;
    $textFile=changeFileExtension($textFile,"txt");

    $path=$corpus->getFolderPath()."/standoff/";
    $annFile=$path.$fnameOut;
    $annFile=changeFileExtension($annFile,"ann");

    echo "Destination for brat2conllu $finalFile\n";
    
    if(!is_file($annFile)){echo "ANN file does not exist. SKIP"; return false;}
/*    if(is_file($finalFile)){
    
        $fmtime=filemtime($finalFile);
        $tctime=strtotime($taskDesc['created_date']);
        if($fmtime>$tctime && filesize($finalFile)>100){
            echo "SKIP $fout\n";
            return false;
        }
    }
*/    
    @mkdir($path);    
    
    $b2c=new \BRAT2CONLLU();
		$b2c->convertBrat2Conllu($contentIn,file_get_contents($textFile),file_get_contents($annFile),$finalFile);
    
    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
}


?>