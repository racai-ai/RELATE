<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/papers.html");
    
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/papers.css");
    
    return $css;
}

function getPageJS(){
}

?>