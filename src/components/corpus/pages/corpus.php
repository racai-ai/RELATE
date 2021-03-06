<?php

function getPageContent(){
		global $user,$modules;
		
    if(!isset($_REQUEST['name']))return "";

    $corpora=new Corpora();
    $corpus=new Corpus($corpora,$_REQUEST['name']);
    if(!$corpus->loadData())die("Invalid corpus");

    $html=file_get_contents(realpath(dirname(__FILE__))."/corpus.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/corpus_common_loading.html");
    
    $hideaudiobutton=""; if(!$corpus->hasAudio())$hideaudiobutton="display:none";
    $hidegoldbutton=""; if(!$corpus->hasGoldAnnotations())$hidegoldbutton="display:none";
    $hidebratbutton=""; if(!$corpus->hasBratProfiles())$hidebratbutton="display:none";
    
    $modules_task_dialog=$modules->getTaskDialog($corpus);
    $html=str_replace("{{TASK-DIALOG}}",$modules_task_dialog,$html);
    
    $html=str_replace("{{CORPUS_NAME_HTML}}",htmlspecialchars($_REQUEST['name']),$html);
    $html=str_replace("{{CORPUS_NAME}}",$_REQUEST['name'],$html);
    $html=str_replace("{{CORPUS_LANG}}",$corpus->getData("lang",""),$html);
    $html=str_replace("{{RECORDER_NAME}}",$user->getProfileHTML("recorder_name",""),$html);
    $html=str_replace("{{LOADING}}",$loading,$html);
    $html=str_replace("{{hideaudiobutton}}",$hideaudiobutton,$html);
    $html=str_replace("{{hidegoldbutton}}",$hidegoldbutton,$html);
    $html=str_replace("{{hidebratbutton}}",$hidebratbutton,$html);
    
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/corpus.css");
    return $css;
}

function getPageJS(){
		global $user,$modules;

    $corpora=new Corpora();
    $corpus=new Corpus($corpora,$_REQUEST['name']);
    if(!$corpus->loadData())die("Invalid corpus");
    
    $hasAudio="false"; if($corpus->hasAudio())$hasAudio="true";
    $hasGold="false"; if($corpus->hasGoldAnnotations())$hasGold="true";
    $hasBrat="false"; if($corpus->hasBratProfiles())$hasBrat="true";
    $hidebratbutton=""; if(!$corpus->hasBratProfiles())$hidebratbutton="display:none";

    $js=file_get_contents(realpath(dirname(__FILE__))."/corpus.js");
    $js=str_replace("{{TASKS-BUTTONS}}",$modules->getTaskButtons($corpus),$js);
    $js=str_replace("{{TASKS-INIT}}",$modules->getTaskInit($corpus),$js);

    $js=str_replace("{{CORPUS_NAME}}",$_REQUEST['name'],$js);
    $js=str_replace("{{CORPUS_LANG}}",$corpus->getData("lang",""),$js);
    $js=str_replace("{{RECORDER_NAME}}",$user->getProfileJS("recorder_name",""),$js);
    $js=str_replace("{{HAS_AUDIO}}",$hasAudio,$js);
    $js=str_replace("{{HAS_GOLD}}",$hasGold,$js);
    $js=str_replace("{{HAS_BRAT}}",$hasBrat,$js);
    $js=str_replace("{{hidebratbutton}}",$hidebratbutton,$js);

    return $js;
}

function getPageAdditionalCSS(){
    return ["extern/pqgrid-2.4.1/pqgrid.min.css"];
}

function getPageAdditionalJS(){
    return [
				"extern/pqgrid-2.4.1/pqgrid.min.js",
				"extern/web_audio_recorder/WebAudioRecorder.min.js"
		];
}

?>