<?php

$HANDLERS=[];

function registerHandler($path,$fname,$isData,$rights=[]){
    global $HANDLERS;
    $HANDLERS[$path]=["fname"=>$fname,"isData"=>$isData,"rights"=>$rights];
}

?>