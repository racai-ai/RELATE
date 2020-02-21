<?php

function runExportMarcell($pathIn){
    global $runnerFolder,$corpus,$settings,$taskDesc;
    
    @mkdir($pathOut);    

    @chdir("marcell");
    passthru($settings->get("tools.java.path")." -cp MarcellCorrection.jar ".escapeshellarg($pathIn));

    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
    
    passthru("./marcell_xml_all.sh ".escapeshellarg($settings->get("tools.python.venv"))." ".escapeshellarg($corpus->getFolderPath()));
    @chdir("..");
    
    $marcellOut=$corpus->getFolderPath()."/marcell-out";
    @mkdir($marcellOut);
    @unlink("$marcellOut/ro-annotated.zip");
    @unlink("$marcellOut/ro-raw.zip");
    @unlink("$marcellOut/ro-xml.zip");
    
    passthru("zip -r -j -9 ".escapeshellarg("$marcellOut/ro-raw.zip")." ".escapeshellarg($corpus->getFolderPath()."/files"));
    passthru("zip -r -j -9 ".escapeshellarg("$marcellOut/ro-annotated.zip")." ".escapeshellarg($corpus->getFolderPath()."/basic_tagging"));
    passthru("zip -r -j -9 ".escapeshellarg("$marcellOut/ro-xml.zip")." ".escapeshellarg($corpus->getFolderPath()."/marcell-xml"));
}
