<?php

function PageInit(){
    global $user,$registerErrorMessage, $settings;
    
    if(!$settings->get("RegistrationEnabled")){
        $registerErrorMessage="Registration disabled";
        return ;
    }
    
    
    $errMsg="";
    if(isset($_REQUEST['username']) && isset($_REQUEST['password']) && isset($_REQUEST['password2']) && isset($_REQUEST['name'])){
          $_REQUEST['username']=strtolower($_REQUEST['username']);
    
    
          $securimage = new Securimage();
          
          if ($securimage->check($_POST['captcha_code']) == false) $errMsg.="Invalid captch code provided!<br/>";
          
          if ($_REQUEST['password']!=$_REQUEST['password2'])$errMsg.="Passwords do not match!<br/>";
          
          if (!$user->isValidUsernameString($_REQUEST['username'])) $errMsg.="Invalid username!<br/>";
          
          if ($user->userExists($_REQUEST['username'])) $errMsg.="Username already exists!<br/>";
    
          if(strlen($errMsg)==0){
              if(!$user->create($_REQUEST))$errMsg.="Error creating account!<br/>";
          }
          
          if(strlen($errMsg)==0){
              $user->loadUser($_REQUEST['username']);
              $_SESSION['username']=$_REQUEST['username'];
          }

    }
    
    $registerErrorMessage=$errMsg;

}

function getPageContent(){
    global $user,$registerErrorMessage;

    if($user->isLoggedIn()){
        $html=file_get_contents(realpath(dirname(__FILE__))."/register_ok.html");
    }else{
        $html=file_get_contents(realpath(dirname(__FILE__))."/register.html");
    }

    //$loading=file_get_contents(realpath(dirname(__FILE__))."/../teprolin/teprolin_common_loading.html");
    //$html=str_replace("{{loading}}",$loading,$html);

    $html=str_replace("{{error}}",$registerErrorMessage,$html);
    
    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/register.css");
    
    return $css;
}

function getPageJS(){
//    $js=file_get_contents(realpath(dirname(__FILE__))."/asr.js");
    
//    return $js;
}

?>