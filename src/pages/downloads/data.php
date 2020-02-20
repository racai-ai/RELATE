<?php

if(!isset($_REQUEST['file']))die();


    $downloads=json_decode(file_get_contents("${LIB_PATH}/../DB/downloads/downloads.json"),true);

    foreach($downloads as $d){
        if(strcmp($d['file'],$_REQUEST['file'])===0){
            if(isset($d['rights']) && !$user->hasAccess($d['rights']))die("Not authorized");
            
            $user->writeHistory("DOWNLOAD",$d['file']);
            
            $fpath="${LIB_PATH}/../DB/downloads/".$d['file'];
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$d['file'].'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fpath));
            readfile($fpath);
            exit;            
        }
    }
    
    die();
?>