<?php

namespace Modules\asr;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){

    $path=$corpus->getFolderPath()."/files/";
    $fnameOut=changeFileExtension($fnameOut,"txt");
    $finalFile=$path.$fnameOut;
    $finalFileMeta=$corpus->getFolderPath()."/meta/".$fnameOut.".meta";
    echo "Destination for ASR $finalFile\n";
    @mkdir($path);        

    $contentIn=file_get_contents($data['fpath']);

    if($taskDesc['system']=="RO DeepSpeech2")
        $result=ROBIN_runASR($contentIn,false,false,false);
    else if($taskDesc['system']=="EN DeepSpeech2") 
        $result=ROBIN_runASR($contentIn,false,true,false);
    else if($taskDesc['system']=="RO ROBIN Dev") 
        $result=ROBIN_runASR($contentIn,true,false,false);
    else if($taskDesc['system']=="RO WAV2VEC2") 
        $result=ROBIN_runASR($contentIn,false,false,true);
    else // Default assume default system => should never get here since systems are selected from a dropdown
        $result=ROBIN_runASR($contentIn,false,false,false);

    $asr="";
    if(isset($result['transcription'])){
        $asr=$result['transcription'];
    }else{
    
        $trans="";
        foreach($result['transcriptions'] as $t){
            if(strlen($trans)>0)$trans.=" ";
            $trans.=$t['transcription'];
        }
    
        $asr=$trans;
    
    }


    file_put_contents($finalFile,$asr);
    $meta=["name"=>$fnameOut,"corpus"=>$corpus->getName(),"type"=>"text","desc"=>"","created_by"=>"ASR","created_date"=>strftime("%Y-%m-%d")];
    file_put_contents($finalFileMeta,json_encode($meta));
    
    file_put_contents($corpus->getFolderPath()."/changed_files.json",json_encode(["changed"=>time()]));            
}


?>