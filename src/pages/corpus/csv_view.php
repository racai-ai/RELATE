<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/csv_view.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/../teprolin/teprolin_common_loading.html");

    $html=str_replace("{{loading}}",$loading,$html);
    $html=str_replace("{{CORPUS_NAME}}",$_REQUEST['corpus'],$html);
    $html=str_replace("{{CORPUS_FILE}}",$_REQUEST['file'],$html);
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
    $js=file_get_contents(realpath(dirname(__FILE__))."/csv_view.js");
    
    $js=str_replace("{{CORPUS_NAME}}",$_REQUEST['corpus'],$js);
    $js=str_replace("{{CORPUS_FILE}}",$_REQUEST['file'],$js);
    
    return $js;
}

function getPageAdditionalCSS(){
    return ["extern/pqgrid-2.4.1/pqgrid.min.css"];
}

function getPageAdditionalJS(){
    return ["extern/pqgrid-2.4.1/pqgrid.min.js"];
}


?>