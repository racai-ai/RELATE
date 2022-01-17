<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/wav2vec2.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common_loading.html");

    $html=str_replace("{{loading}}",$loading,$html);
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
    $js=file_get_contents(realpath(dirname(__FILE__))."/wav2vec2.js");
    
    return $js;
}

function getPageAdditionalJS(){
    return [
				"extern/web_audio_recorder/WebAudioRecorder.min.js"
		];
}


?>