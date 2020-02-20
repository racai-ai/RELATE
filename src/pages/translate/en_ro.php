<?php

function getPageContent(){
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

    $html=file_get_contents(realpath(dirname(__FILE__))."/translate_common.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/../teprolin/teprolin_common_loading.html");
    
    $html=str_replace("{{loading}}",$loading,$html);
    $html=str_replace("{{label}}","Enter an ENGLISH text",$html);
    $html=str_replace("{{sysid}}",$found['id'],$html);
    
    $analysisButton='<button type="button"  class="btn cur-p btn-secondary" onclick="runAnalysis();">Analysis</button>';
    $html=str_replace("{{analysis_button}}",$analysisButton,$html);
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
    $js=file_get_contents(realpath(dirname(__FILE__))."/translate_common.js");
    return $js;
}

?>