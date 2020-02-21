<?php

function runExportMarcell($pathIn){
    global $runnerFolder,$corpus,$settings,$taskDesc;
    
    @mkdir($pathOut);    

    @chdir("marcell");
    passthru($settings->get("tools.java.path")." -cp MarcellCorrection.jar ".escapeshellarg($pathIn));
    @chdir("..");
    
    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
    
}