<?php

namespace Modules\zip;

function runZip($pathIn,$pathOut,$fnameOut){
    global $runnerFolder,$corpus,$settings,$taskDesc;
    
    @mkdir($pathOut);    

    passthru("zip -r -j -1 ".escapeshellarg($pathOut."/".$fnameOut)." ".escapeshellarg($pathIn));
   
}

function runUnzip($fnameIn,$pathOut,$settings,$corpus,$taskDesc){
    
    @mkdir($pathOut);    

    passthru("unzip -j -o ".escapeshellarg($fnameIn)." -d ".escapeshellarg($pathOut));
   
    $dir_meta=$corpus->getFolderPath();
    $dir_meta.="/meta";
    @mkdir($dir_meta);

    $dir_standoff=$corpus->getFolderPath();
    $dir_standoff.="/standoff";
    @mkdir($dir_standoff);

    $dir_annotated=$corpus->getFolderPath();
    $dir_annotated.="/".$settings->get("dir.annotated");
    @mkdir($dir_annotated);

    $dh = opendir($pathOut);
    while (($file = readdir($dh)) !== false) {
        $pathFile=$pathOut."/".$file;
        if(!is_file($pathFile))continue;
        
        $pathMeta=$dir_meta."/".$file;
        $pathStandoff=$dir_standoff."/".$file;
        $pathAnnotated=$dir_annotated."/".$file;
        
        if(endsWith(strtolower($file),".txt")){
            if(!is_file($pathMeta)){
                file_put_contents($dir_meta."/".$file.".meta",json_encode([
                    'name' => $file,
                    'corpus' => $corpus->getData("name","unknown"),
                    'type' => 'text',
                    'desc' => '',
                    'created_by' => $taskDesc['created_by'],
                    'created_date' => $taskDesc['created_date']
                ]));
                
            }
        }else if(endsWith(strtolower($file),".conllu") || endsWith(strtolower($file),".conllup")){
            @rename($pathFile,$pathAnnotated);
        }else{
            @rename($pathFile,$pathStandoff);
        }
    }
    closedir($dh);
        
    file_put_contents($corpus->getFolderPath()."/changed_files.json",json_encode(["changed"=>time()]));            
    file_put_contents($corpus->getFolderPath()."/changed_standoff.json",json_encode(["changed"=>time()]));            
    file_put_contents($corpus->getFolderPath()."/changed_annotated.json",json_encode(["changed"=>time()]));            
    
}

function runUnzipAnnotated($fnameIn,$pathOut){
    global $runnerFolder,$corpus,$settings,$taskDesc;
    
    @mkdir($pathOut);    

    passthru("unzip -j -o ".escapeshellarg($fnameIn)." -d ".escapeshellarg($pathOut));
   
    file_put_contents($corpus->getFolderPath()."/changed_annotated.json",json_encode(["changed"=>time()]));            
}

function runnerZipText($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
   runZip($corpus->getFolderPath()."/files",$corpus->getFolderPath()."/zip_text",$taskDesc['fname']);
}

function runnerZipAnnotated($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
    runZip($corpus->getFolderPath()."/".$settings->get("dir.annotated"),$corpus->getFolderPath()."/zip_".$settings->get("dir.annotated"),$taskDesc['fname']);
}

function runnerUnzipText($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
       runUnzip($data['fpath'],$corpus->getFolderPath()."/files",$settings,$corpus,$taskDesc);
}

function runnerUnzipAnnotated($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
       runUnzip($data['fpath'],$corpus->getFolderPath()."/".$settings->get("dir.annotated"),$settings,$corpus,$taskDesc);
}

function runnerZipStandoff($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
   runZip($corpus->getFolderPath()."/standoff",$corpus->getFolderPath()."/zip_standoff",$taskDesc['fname']);
}

function runnerZipGoldStandoff($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
   runZip($corpus->getFolderPath()."/gold_standoff",$corpus->getFolderPath()."/zip_gold_standoff",$taskDesc['fname']);
}

function runnerZipGoldAnn($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){
   runZip($corpus->getFolderPath()."/gold_ann",$corpus->getFolderPath()."/zip_gold_ann",$taskDesc['fname']);
}


?>