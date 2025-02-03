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

    if($FILE_LOCK===false){
        $FILE_LOCK=[];
        register_shutdown_function("UNLOCK_FILE");
    }
    $FILE_LOCK[]=$flock;
    return $flock;
}

function UNLOCK_FILE(){
    global $FILE_LOCK;

    if($FILE_LOCK!==false){
        foreach($FILE_LOCK as $flock){
            flock($flock,LOCK_UN);
            fclose($flock);
        }
        $FILE_LOCK=false;
    }
}

function UNLOCK_SINGLE_FILE($flock){
    global $FILE_LOCK;

    flock($flock,LOCK_UN);
    fclose($flock);


    if($FILE_LOCK!==false){
        foreach($FILE_LOCK as $k=>$clock){
            if($clock===$flock){unset($FILE_LOCK[$k]);break;}
        }
    }
}
