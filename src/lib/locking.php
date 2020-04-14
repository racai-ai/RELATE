<?php

$FILE_LOCK=false;

function LOCK_ON_FILE($fname){
    global $FILE_LOCK;
    
    if(!is_file($fname))file_put_contents($fname,"");
    
    $flock=fopen($fname,"r+b");
    if($flock===false){
        die("Already running\n");
    }
    $wouldblock=0;
    $r=flock($flock,LOCK_EX | LOCK_NB, $wouldblock );
    if($r===false || $wouldblock===1){
        fclose($flock);
        die("Already running 2\n");
    }

    $FILE_LOCK=$flock;
    
    register_shutdown_function("UNLOCK_FILE");
}

function UNLOCK_FILE(){
    global $FILE_LOCK;

    if($FILE_LOCK!==false){    
        flock($FILE_LOCK,LOCK_UN);
        fclose($FILE_LOCK);
        
        $FILE_LOCK=false;
    }
}
?>