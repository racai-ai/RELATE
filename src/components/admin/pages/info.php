<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/info.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/common_loading.html");

    $html=str_replace("{{LOADING}}",$loading,$html);
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
    $js=file_get_contents(realpath(dirname(__FILE__))."/info.js");
    
    return $js;
}

function getPageAdditionalCSS(){
    return [];
}

function getPageAdditionalJS(){
    return [];
}


?>