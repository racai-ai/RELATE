<?php

function getPageContent(){
    return file_get_contents(realpath(dirname(__FILE__))."/we.html");
}

function getPageCSS(){
}

function getPageJS(){
    return file_get_contents(realpath(dirname(__FILE__))."/we.js");
}

?>