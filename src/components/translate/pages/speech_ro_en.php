<?php

$TILDE_found_sysid=false;

function getPageContent(){

		$systems_asr=[
				["name"=>"ROBIN ASR","value"=>"robin_asr_ro","default"=>true],
				["name"=>"ROBIN ASR Dev","value"=>"robin_asrdev_ro"]
		];
		$systems_correction=[
				["name"=>"No Correction","value"=>"none"],
				["name"=>"ROBIN Correction","value"=>"correction_1","default"=>true]
		];
		$systems_mt=[
				["name"=>"RO Presidency","value"=>"ro_presidency","default"=>true]
		];
		$systems_tts=[
				["name"=>"Mozilla EN TTS","value"=>"en_tts","default"=>true]
		];
		
		$str_systems_asr="";
		foreach($systems_asr as $s){
				$sel=((isset($s['default']) && $s['default']===true) ? (" selected=\"selected\"") : (""));
				$str_systems_asr.="<option value=\"${s['value']}\"${sel}>${s['name']}</option>\n";
		}				
		$str_systems_correction="";
		foreach($systems_correction as $s){
				$sel=((isset($s['default']) && $s['default']===true) ? (" selected=\"selected\"") : (""));
				$str_systems_correction.="<option value=\"${s['value']}\"${sel}>${s['name']}</option>\n";
		}
		$str_systems_mt="";
		foreach($systems_mt as $s){
				$sel=((isset($s['default']) && $s['default']===true) ? (" selected=\"selected\"") : (""));
				$str_systems_mt.="<option value=\"${s['value']}\"${sel}>${s['name']}</option>\n";
		}
		$str_systems_tts="";
		foreach($systems_tts as $s){
				$sel=((isset($s['default']) && $s['default']===true) ? (" selected=\"selected\"") : (""));
				$str_systems_tts.="<option value=\"${s['value']}\"${sel}>${s['name']}</option>\n";
		}


		global $TILDE_found_sysid;
    $systems=TILDE_getRomanianSystems();
    $found=false;
    foreach($systems as $sys){
        if(strcasecmp($sys['from'],"ro")==0 && strcasecmp($sys['to'],"en")==0){
            $found=$sys;
            break;
        }
    }
    
    if($found===false){
        $html=file_get_contents(realpath(dirname(__FILE__))."/translate_common_error.html");
        return $html;
    }
    
    $TILDE_found_sysid=$found;
    
    $html=file_get_contents(realpath(dirname(__FILE__))."/speech_ro.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common_loading.html");

    $html=str_replace("{{loading}}",$loading,$html);
    $html=str_replace("{{label}}","Introduceți un text în limba ROMÂNĂ",$html);
    $html=str_replace("{{sysid}}",$found['id'],$html);
    $html=str_replace("{{input}}","ro",$html);
    $html=str_replace("{{system_asr}}",$str_systems_asr,$html);
    $html=str_replace("{{system_correction}}",$str_systems_correction,$html);
    $html=str_replace("{{system_mt}}",$str_systems_mt,$html);
    $html=str_replace("{{system_tts}}",$str_systems_tts,$html);
    
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
    $js=str_replace("{{input}}","ro",$js);
    return $js;
}

function getPageAdditionalJS(){
    return [
				"extern/web_audio_recorder/WebAudioRecorder.min.js"
		];
}

?>