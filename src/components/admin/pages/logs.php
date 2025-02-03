<?php

function getPageContent(){
    global $settings;
    
    $nRunners=$settings->get("TaskRunners");
    $startButton=2;
    
    $html=file_get_contents(realpath(dirname(__FILE__))."/logs.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/common_loading.html");


    $runnerButtons="";
    for($nrunner=0;$nrunner<$nRunners;$nrunner++){
        $nbutton=$nrunner+$startButton;
        $runnerButtons.=
            "<button type=\"button\" id=\"bOutput${nbutton}\"  class=\"btn cur-p btn-secondary\"  onclick=\"showLog(${nbutton},'runner.${nrunner}');\">Runner ${nrunner}</button>\n";
    }

    $html=str_replace("{{LOADING}}",$loading,$html);
    $html=str_replace("{{RUNNER_BUTTONS}}",$runnerButtons,$html);
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
    global $settings;
    
    $nRunners=$settings->get("TaskRunners");

    $js=file_get_contents(realpath(dirname(__FILE__))."/logs.js");
    
    $js=str_replace("{{NUM_RUNNERS}}","$nRunners",$js);
    
    return $js;
}

function getPageAdditionalCSS(){
    return [];
}

function getPageAdditionalJS(){
    return [];
}


?>