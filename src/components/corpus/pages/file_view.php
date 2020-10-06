<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/file_view.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/../teprolin/teprolin_common_loading.html");

    $html=str_replace("{{loading}}",$loading,$html);
    $html=str_replace("{{CORPUS_NAME}}",$_REQUEST['corpus'],$html);
    $html=str_replace("{{CORPUS_FILE}}",$_REQUEST['file'],$html);
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
    $js=file_get_contents(realpath(dirname(__FILE__))."/file_view.js");
    
    $js=str_replace("{{CORPUS_NAME}}",$_REQUEST['corpus'],$js);
    $js=str_replace("{{CORPUS_FILE}}",$_REQUEST['file'],$js);
    
    return $js;
}


?>