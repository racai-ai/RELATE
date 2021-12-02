<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/lrlist.html");
    
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/lrlist.css");
    
    return $css;
}

function getPageJS(){
}

?>