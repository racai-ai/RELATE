<?php

function getPageContent(){
    $html=file_get_contents(realpath(dirname(__FILE__))."/complete.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common_loading.html");
    $output=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common_output.html");
    
    $text="";
    if(isset($_REQUEST['text']))$text=$_REQUEST['text'];
    
    $html=str_replace("{{loading}}",$loading,$html);
    $html=str_replace("{{output}}",$output,$html);
    $html=str_replace("{{text}}",htmlentities($text),$html);
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common.css");
    return $css;
}

function getPageJS(){
    $js=file_get_contents(realpath(dirname(__FILE__))."/teprolin_common.js");
    $js.=file_get_contents(realpath(dirname(__FILE__))."/complete.js");
    return $js;
}

?>