<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/proiecte.html");
	
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/resource.css");
    
    return $css;
}

function getPageJS(){
}

?>