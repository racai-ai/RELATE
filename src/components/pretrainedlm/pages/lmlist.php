<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/lmlist.html");
    
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/lmlist.css");
    
    return $css;
}

function getPageJS(){
}

?>