<?php

function PageInit(){
    global $user,$isError;
    
    
    $isError=false;
    if(isset($_REQUEST['username']) && isset($_REQUEST['password'])){
          $user->doLogin();
          if(!$user->isLoggedIn())$isError=true;
    }
}

function getPageContent(){
    global $user,$isError;

    if($user->isLoggedIn()){
        $html=file_get_contents(realpath(dirname(__FILE__))."/login_ok.html");
    }else{
        $html=file_get_contents(realpath(dirname(__FILE__))."/login.html");
    }

    //$loading=file_get_contents(realpath(dirname(__FILE__))."/../teprolin/teprolin_common_loading.html");
    //$html=str_replace("{{loading}}",$loading,$html);

    $errorMsg="";
    if($isError)$errorMsg="Invalid username/password";
    $html=str_replace("{{error}}",$errorMsg,$html);
    
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/login.css");
    
    return $css;
}

function getPageJS(){
//    $js=file_get_contents(realpath(dirname(__FILE__))."/asr.js");
    
//    return $js;
}

?>