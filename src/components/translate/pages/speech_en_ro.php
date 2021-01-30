<?php

$TILDE_found_sysid=false;

function getPageContent(){
		global $TILDE_found_sysid;
    $systems=TILDE_getRomanianSystems();
    $found=false;
    foreach($systems as $sys){
        if(strcasecmp($sys['from'],"en")==0 && strcasecmp($sys['to'],"ro")==0){
            $found=$sys;
            break;
        }
    }
    
    if($found===false){
        $html=file_get_contents(realpath(dirname(__FILE__))."/translate_common_error.html");
        return $html;
    }
    
    $TILDE_found_sysid=$found;
    
    $html=file_get_contents(realpath(dirname(__FILE__))."/speech_en.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common_loading.html");

    $html=str_replace("{{loading}}",$loading,$html);
    $html=str_replace("{{label}}","Enter an ENGLISH text",$html);
    $html=str_replace("{{sysid}}",$found['id'],$html);
    $html=str_replace("{{input}}","en",$html);
    
    $analysisButton='';
    $html=str_replace("{{analysis_button}}",$analysisButton,$html);
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
		global $TILDE_found_sysid;
		
    $js=file_get_contents(realpath(dirname(__FILE__))."/speech.js");
    $js=str_replace("{{sysid}}",$TILDE_found_sysid['id'],$js);
    $js=str_replace("{{input}}","en",$js);
    return $js;
}

function getPageAdditionalJS(){
    return [
				"extern/web_audio_recorder/WebAudioRecorder.min.js"
		];
}

?>