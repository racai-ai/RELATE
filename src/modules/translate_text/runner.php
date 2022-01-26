<?php

namespace Modules\translate_text;

$cached_translation_system=null;

function getTranslationSystem($taskDesc){
    global $cached_translation_system;
    if($cached_translation_system===null){
        $found=false;
        if($taskDesc['system']=="EN-RO"){
            $systems=TILDE_getRomanianSystems();
            foreach($systems as $sys){
                if(strcasecmp($sys['from'],"en")==0 && strcasecmp($sys['to'],"ro")==0){
                    $found=$sys;
                    break;
                }
            }
        }else if($taskDesc['system']=="RO-EN"){
            $systems=TILDE_getRomanianSystems();
            foreach($systems as $sys){
                if(strcasecmp($sys['from'],"ro")==0 && strcasecmp($sys['to'],"en")==0){
                    $found=$sys;
                    break;
                }
            }
        }
        $cached_translation_system=$found;
    }
    
    return $cached_translation_system;
}

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){

    $extensions=[
        "RO-EN" => "en.txt",
        "EN-RO" => "ro.txt"
    ];

    $path=$corpus->getFolderPath()."/files/";
    $fnameOut=changeFileExtension($fnameOut,$extensions[$taskDesc['system']]);
    $finalFile=$path.$fnameOut;
    $finalFileMeta=$corpus->getFolderPath()."/meta/".$fnameOut.".meta";
    echo "Destination for Text Translation $finalFile\n";
    @mkdir($path);        

    $sysid=getTranslationSystem($taskDesc);
    if($sysid===false){
        echo "ERROR Retrieving translation system";
    }else{
        $sysid=$sysid['id'];
        $translate=TILDE_Translate($sysid,$contentIn);
        file_put_contents($finalFile,trim($translate,'"'));
        $meta=["name"=>$fnameOut,"corpus"=>$corpus->getName(),"type"=>"text","desc"=>"","created_by"=>"translation","created_date"=>strftime("%Y-%m-%d")];
        file_put_contents($finalFileMeta,json_encode($meta));
    }
    
    file_put_contents($corpus->getFolderPath()."/changed_files.json",json_encode(["changed"=>time()]));            
}


?>