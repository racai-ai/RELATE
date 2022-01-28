<?php

namespace Modules\translate_s2s;

$cached_translation_system=null;

function getTranslationSystem($taskDesc){
    global $cached_translation_system;
    if($cached_translation_system===null){
        $found=false;
        if($taskDesc['system_translate']=="EN-RO"){
            $systems=TILDE_getRomanianSystems();
            foreach($systems as $sys){
                if(strcasecmp($sys['from'],"en")==0 && strcasecmp($sys['to'],"ro")==0){
                    $found=$sys;
                    break;
                }
            }
        }else if($taskDesc['system_translate']=="RO-EN"){
            $systems=TILDE_getRomanianSystems();
            foreach($systems as $sys){
                if(strcasecmp($sys['from'],"ro")==0 && strcasecmp($sys['to'],"en")==0){
                    $found=$sys;
                    break;
                }
            }
        }
        $cached_translation_system=$found;
    }
    
    return $cached_translation_system;
}


function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    $extensions=[
        "RO-EN" => "en.wav",
        "EN-RO" => "ro.wav"
    ];

    $path=$corpus->getFolderPath()."/audio/";
    $fnameOut=changeFileExtension($fnameOut,$extensions[$taskDesc['system_translate']]);
    $finalFile=$path.$fnameOut;

    echo "Destination for S2S $finalFile\n";
    @mkdir($path);        

    $contentIn=file_get_contents($data['fpath']);

    if($taskDesc['system_asr']=="RO DeepSpeech2")
        $result=ROBIN_runASR($contentIn,false,false,false);
    else if($taskDesc['system_asr']=="EN DeepSpeech2") 
        $result=ROBIN_runASR($contentIn,false,true,false);
    else if($taskDesc['system_asr']=="RO ROBIN Dev") 
        $result=ROBIN_runASR($contentIn,true,false,false);
    else if($taskDesc['system_asr']=="RO WAV2VEC2") 
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
    
    
    $models=[
    "MARCELL" => ["url"=>"http://127.0.0.1:5105/api/v1.0/punctuation"],
    ];

    if(isset($models[$taskDesc['system_punct']])){
        $text=restorePunctuationText($models[$taskDesc['system_punct']]["url"],$asr);
    }else $text=$asr;
    

    $translate="";
    $sysid=getTranslationSystem($taskDesc);
    if($sysid===false){
        echo "ERROR Retrieving translation system";
    }else{
        $sysid=$sysid['id'];
        $translate=TILDE_Translate($sysid,$text);
        $translate=trim($translate,'"');
    }

    $tts="";
    if($taskDesc['system_tts']=="RO RACAI SSLA")
        $tts=TTS_SSLA_runTTS($translate);
    else if($taskDesc['system_tts']=="RO RomanianTTS"){ 
		$tts=ROMANIANTTS_runTTS($translate);
		$tts=file_get_contents(trim($tts));
    }else if($taskDesc['system_tts']=="EN Mozilla TTS") 
		$tts=file_get_contents("http://127.0.0.1:7011/api/tts?text=".urlencode($translate));
    else // Default assume default system => should never get here since systems are selected from a dropdown
        $tts=TTS_SSLA_runTTS($translate);


    file_put_contents($finalFile,$tts);
    
    file_put_contents($corpus->getFolderPath()."/changed_audio.json",json_encode(["changed"=>time()]));            
}


?>