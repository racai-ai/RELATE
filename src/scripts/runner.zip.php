<?php

function runZip($pathIn,$pathOut,$fnameOut){
    global $runnerFolder,$corpus,$settings,$taskDesc;
    
    @mkdir($pathOut);    

    passthru("zip -r -j -1 ".escapeshellarg($pathOut."/".$fnameOut)." ".escapeshellarg($pathIn));
   
    //file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
    
}