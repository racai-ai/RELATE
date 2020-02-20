<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/dev_doc.html");
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
}

?>