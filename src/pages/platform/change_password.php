<?php

function PageInit(){
    global $user,$changeErrorMessage,$changeOK;
    
    $errMsg="";  $changeOK=false;
    if(isset($_REQUEST['newpassword']) && isset($_REQUEST['password']) && isset($_REQUEST['password2']) ){
         
          if ($_REQUEST['newpassword']!=$_REQUEST['password2'])$errMsg.="Passwords do not match!<br/>";
          
          if(strlen($errMsg)==0){
              if (!$user->changePassword($_REQUEST['password'],$_REQUEST['newpassword'])) $errMsg.="Error changing password!<br/>";
              else $changeOK=true;
          }

    }
    
    $changeErrorMessage=$errMsg;

}

function getPageContent(){
    global $user,$changeErrorMessage,$changeOK;

    if($changeOK){
        $html=file_get_contents(realpath(dirname(__FILE__))."/change_password_ok.html");
    }else{
        $html=file_get_contents(realpath(dirname(__FILE__))."/change_password.html");
    }

    //$loading=file_get_contents(realpath(dirname(__FILE__))."/../teprolin/teprolin_common_loading.html");
    //$html=str_replace("{{loading}}",$loading,$html);

    $html=str_replace("{{error}}",$changeErrorMessage,$html);
    
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/change_password.css");
    
    return $css;
}

function getPageJS(){
//    $js=file_get_contents(realpath(dirname(__FILE__))."/asr.js");
    
//    return $js;
}

?>