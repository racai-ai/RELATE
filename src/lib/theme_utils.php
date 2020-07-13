<?php

function makeLink($path,$forceHTTP=false){
  global $PLATFORM;

  if($forceHTTP!==false){
      $ru=$_SERVER['REQUEST_URI'];
      if(strpos($ru,"?")!==false)$ru=substr($ru,0,strpos($ru,"?"));
      return "http://".$_SERVER['HTTP_HOST'].$ru."?path=$path";
      //var_dump($_SERVER);
      //die();
  }
  
  return "index.php?path=$path";
}

function makeMenuLink($menu){
  if(isset($menu['path'])){
      return makeLink($menu['path'],isset($menu['forceHTTP'])?$menu['forceHTTP']:false);
  }else return "javascript:void(0);";
}

?>