<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/../iframe_common.html");
    $html=str_replace("{{src}}","http://89.38.230.10:5555/",$html);
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
}

?>