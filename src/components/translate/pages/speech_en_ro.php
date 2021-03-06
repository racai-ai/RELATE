<?php

$TILDE_found_sysid=false;

function getPageContent(){

		$systems_asr=[
				["name"=>"EN DeepSpeech2","value"=>"en_deepspeech2","default"=>true],
				["name"=>"Mozilla DeepSpeech","value"=>"en_deepspeech_mozilla"]
		];
		$systems_correction=[
				["name"=>"No Correction","value"=>"none","default"=>true],
		];
		$systems_mt=[
				["name"=>"RO Presidency","value"=>"ro_presidency","default"=>true]
		];
		$systems_tts=[
				["name"=>"RomanianTTS","value"=>"romanian_tts","default"=>true],
				["name"=>"RACAI SSLA","value"=>"racai_ssla_tts"]
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
    $js=str_replace("{{input}}","en",$js);
    return $js;
}

function getPageAdditionalJS(){
    return [
				"extern/web_audio_recorder/WebAudioRecorder.min.js"
		];
}

?>