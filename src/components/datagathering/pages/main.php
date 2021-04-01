<?php

$datagPIN=false;

function getPageContent(){
		global $modules,$LIB_PATH,$datagPIN;
	
    if(!isset($_REQUEST['pin'])){
        $html=file_get_contents(realpath(dirname(__FILE__))."/pin.html");
        return $html;
    }
    
    $pin=$_REQUEST['pin'];
    $allowed="abcdefghijklmnopqrstuvwxyz0123456789";
    $allowedArr=[];
    for($i=0;$i<strlen($allowed);$i++)$allowedArr[$allowed[$i]]=1;
    $ok=true;
    for($i=0;$i<strlen($pin);$i++)if(!isset($allowedArr[$pin[$i]]))$ok=false;
    if(strlen($pin)<4 || strlen($pin)>50 || !$ok || !is_file("$LIB_PATH/../DB/datag/$pin/metadata.json")){
        $html=file_get_contents(realpath(dirname(__FILE__))."/pin.html");
        return $html;
    }
    	
    $datagPIN=$pin;
    $html=file_get_contents(realpath(dirname(__FILE__))."/main.html");
    $loading=file_get_contents(realpath(dirname(__FILE__))."/loading.html");

    $html=str_replace("{{loading}}",$loading,$html);
    $html=str_replace("{{pin}}",$datagPIN,$html);
    
    return $html;
}

function getPageCSS(){
}

function getPageJS(){
    global $datagPIN;
    
    $js=file_get_contents(realpath(dirname(__FILE__))."/main.js");
    $js=str_replace("{{pin}}",$datagPIN,$js);
    
    return $js;
}

function getPageAdditionalCSS(){
    return ["extern/pqgrid-2.4.1/pqgrid.min.css"];
}

function getPageAdditionalJS(){
    return ["extern/pqgrid-2.4.1/pqgrid.min.js"];
}


?>