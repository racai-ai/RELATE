<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/classify.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common_loading.html");

    $html=str_replace("{{loading}}",$loading,$html);
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
    $js=file_get_contents(realpath(dirname(__FILE__))."/classify.js");
    
    return $js;
}

?>