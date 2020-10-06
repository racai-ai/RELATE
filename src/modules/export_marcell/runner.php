<?php

namespace Modules\export_marcell;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    global $LIB_PATH;
    
	$pathIn=$corpus->getFolderPath()."/".$settings->get("dir.annotated");

	$cwd=getcwd();
    @chdir("${LIB_PATH}/../modules/export_marcell/marcell");
    
    $cmd=$settings->get("tools.java.path")." -cp MarcellCorrection.jar Corrector ".escapeshellarg($pathIn);
    echo "RUNNING [$cmd]\n";
    passthru($cmd);

    file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
    
    $cmd="./marcell_xml_all.sh ".escapeshellarg($settings->get("tools.python.venv"))." ".escapeshellarg($corpus->getFolderPath());
    echo "RUNNING [$cmd]\n";
    passthru($cmd);
    
    @chdir($cwd);
    
    $marcellOut=$corpus->getFolderPath()."/marcell-out";
    @mkdir($marcellOut);
    echo "Deleting old files, if exist\n";
    @unlink("$marcellOut/ro-annotated.zip");
    @unlink("$marcellOut/ro-raw.zip");
    @unlink("$marcellOut/ro-xml.zip");
    
    echo "Compressing => ro-raw.zip\n";
    passthru("zip -r -j -9 ".escapeshellarg("$marcellOut/ro-raw.zip")." ".escapeshellarg($corpus->getFolderPath()."/files"));
    echo "Compressing => ro-annotated.zip\n";
    passthru("zip -r -j -9 ".escapeshellarg("$marcellOut/ro-annotated.zip")." ".escapeshellarg($corpus->getFolderPath()."/basic_tagging"));
    echo "Compressing => ro-xml.zip\n";
    passthru("zip -r -j -9 ".escapeshellarg("$marcellOut/ro-xml.zip")." ".escapeshellarg($corpus->getFolderPath()."/marcell-xml"));
}

?>