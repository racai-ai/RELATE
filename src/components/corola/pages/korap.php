<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/../iframe_common.html");
    $html=str_replace("{{src}}","http://korap.racai.ro/",$html);
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
}

?>