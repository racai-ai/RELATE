<?php

namespace Modules\export_curlicat;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    global $LIB_PATH;
    
	$pathIn=$corpus->getFolderPath()."/".$settings->get("dir.annotated");

	$cwd=getcwd();
    @chdir("${LIB_PATH}/../modules/export_curlicat/curlicat");
    
    $cmd=$settings->get("tools.java.path")." -cp CurlicatCorrection.jar Corrector ".escapeshellarg($pathIn);
    echo "RUNNING [$cmd]\n";
    passthru($cmd);

    //file_put_contents($corpus->getFolderPath()."/changed_basictagging.json",json_encode(["changed"=>time()]));            
    
    $cmd="./curlicat_xml_all.sh ".escapeshellarg($settings->get("tools.python.venv"))." ".escapeshellarg($corpus->getFolderPath());
    echo "RUNNING [$cmd]\n";
    passthru($cmd);
    
    @chdir($cwd);
    
    $curlicatOut=$corpus->getFolderPath()."/curlicat-out";
    @mkdir($curlicatOut);
    echo "Deleting old files, if exist\n";
    @unlink("$curlicatOut/ro-annotated.zip");
    @unlink("$curlicatOut/ro-raw.zip");
    @unlink("$curlicatOut/ro-xml.zip");
    
    echo "Compressing => ro-raw.zip\n";
    passthru("zip -r -j -9 ".escapeshellarg("$curlicatOut/ro-raw.zip")." ".escapeshellarg($corpus->getFolderPath()."/files"));
    echo "Compressing => ro-annotated.zip\n";
    passthru("zip -r -j -9 ".escapeshellarg("$curlicatOut/ro-annotated.zip")." ".escapeshellarg($corpus->getFolderPath()."/curlicat"));
    echo "Compressing => ro-xml.zip\n";
    passthru("zip -r -j -9 ".escapeshellarg("$curlicatOut/ro-xml.zip")." ".escapeshellarg($corpus->getFolderPath()."/curlicat-xml"));
}

?>