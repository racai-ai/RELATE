<?php

function getPageContent(){
    global $user,$LIB_PATH;
    
    $downloads=json_decode(file_get_contents("${LIB_PATH}/../DB/downloads/downloads.json"),true);

    $html=file_get_contents(realpath(dirname(__FILE__))."/private.html");
    
    $dl="";
    foreach($downloads as $d){
        if(isset($d['rights']) && !$user->hasAccess($d['rights']))continue;

        $fsize=@filesize("${LIB_PATH}/../DB/downloads/".$d['file']);
        $unit="b";
        if($fsize>1024){$fsize/=1024; $unit="Kb";}
        if($fsize>1024){$fsize/=1024; $unit="Mb";}
        if($fsize>1024){$fsize/=1024; $unit="Gb";}
        if($fsize>1024){$fsize/=1024; $unit="Tb";}
        
        $fsize=sprintf("%.2f",$fsize);
        
        $dl.='<tr><td><a href="index.php?path=downloads/data&file='.$d['file'].'">'.$d['file']."</a><br/>${fsize} ${unit}</td><td>".htmlspecialchars($d['description'])."</td></tr>";
    }

    $html=str_replace("{{downloads}}",$dl,$html);

    return $html;
}

function getPageCSS(){
    $css=file_get_contents(realpath(dirname(__FILE__))."/private.css");
    
    return $css;
}

function getPageJS(){
//    $js=file_get_contents(realpath(dirname(__FILE__))."/asr.js");
    
//    return $js;
}

?>