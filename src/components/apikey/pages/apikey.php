<?php

function generateRandomAPIKey($length = 30) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

function PageInit(){
    global $user,$regenerated;
    
    $regenerated=false;
    if(isset($_REQUEST['regenerate']) ){
         
         $akey=generateRandomAPIKey();
         $user->setProfile("deepnewsdefAPIKey",$akey);
         $user->saveProfile();

         $regenerated=true;
    }
    

}

function getPageContent(){
    global $user,$regenerated;

    $html=file_get_contents(realpath(dirname(__FILE__))."/apikey.html");

    if($regenerated){
        $html=str_replace("{{error}}","API Key was regenerated",$html);
    }else{
        $html=str_replace("{{error}}","",$html);
    }
    $html=str_replace("{{username}}",$user->getUsername(),$html);
    $html=str_replace("{{apikey}}",$user->getProfile("deepnewsdefAPIKey","Key not generated"),$html);
    
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/apikey.css");
    
    return $css;
}

function getPageJS(){
//    $js=file_get_contents(realpath(dirname(__FILE__))."/apikey.js");
    
//    return $js;
}

?>