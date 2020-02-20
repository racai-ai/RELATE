<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/queryp.html");
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
    $js=file_get_contents(realpath(dirname(__FILE__))."/queryp.js");
    return $js;
}

?>