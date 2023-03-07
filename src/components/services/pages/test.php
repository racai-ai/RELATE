<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/test.html");
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
}

?>