<?php

namespace Modules\tts;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){

    $path=$corpus->getFolderPath()."/audio/";
    $fnameOut=changeFileExtension($fnameOut,"wav");
    $finalFile=$path.$fnameOut;
    echo "Destination for TTS $finalFile\n";
    @mkdir($path);        


    $tts="";
    if($taskDesc['system']=="RO RACAI SSLA")
        $tts=TTS_SSLA_runTTS($contentIn);
    else if($taskDesc['system']=="RO RomanianTTS"){ 
		$tts=ROMANIANTTS_runTTS($contentIn);
		$tts=file_get_contents(trim($tts));
    }else if($taskDesc['system']=="EN Mozilla TTS") 
		$tts=file_get_contents("http://127.0.0.1:7011/api/tts?text=".urlencode($data['text']));
    else // Default assume default system => should never get here since systems are selected from a dropdown
        $tts=TTS_SSLA_runTTS($contentIn);


    file_put_contents($finalFile,$tts);
    
    file_put_contents($corpus->getFolderPath()."/changed_audio.json",json_encode(["changed"=>time()]));            
    @chown($corpus->getFolderPath()."/changed_audio.json",$settings->get("owner_user"));
    @chgrp($corpus->getFolderPath()."/changed_audio.json",$settings->get("owner_group"));
}


?>