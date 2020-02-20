<?php

function setFileOwner($fpath){
    global $settings;

    if(isset($settings) && is_object($settings)){
        
        $ownerUser=$settings->get("owner_user");
        if($ownerUser!==false && !empty($ownerUser))
            @chown($fpath,$ownerUser);
        
        $ownerGroup=$settings->get("owner_group");
        if($ownerGroup!==false && !empty($ownerGroup))
            @chgrp($fpath,$ownerGroup);
            
    }
}

function storeFile($fpath,$content,$flags = 0 ){
    $newFile=false;
    if(!is_file($fpath))$newFile=true;
    
    file_put_contents($fpath,$content,$flags);
    
    if($newFile)setFileOwner($fpath);

}

function renameFile($path1,$path2){
    rename($path1,$path2);
    
    setFileOwner($path2);

}


function createFolder($path){
    $newFile=false;
    if(!is_dir($path))$newFile=true;

    @mkdir($path);
    
    if($newFile)setFileOwner($path);

}

function clearFolder($folder){
    $dh = opendir($folder);
    if($dh===false)return false;
    
    while (($file = readdir($dh)) !== false) {
        $fpath="$folder/$file";
        if(!is_file($fpath))continue;
        
        unlink($fpath);
    }
    closedir($dh);    

    return true;
}


?>