<?php

function PageInit(){
    global $user;

    $user->logout();    
}

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/logout.html");
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
}

?>