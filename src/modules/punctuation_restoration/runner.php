<?php

namespace Modules\punctuation_restoration;

function runner($runner,$settings,$corpus,$taskDesc,$data,$contentIn,$fnameOut){

    $path=$corpus->getFolderPath()."/files/";
    $fnameOut=changeFileExtension($fnameOut,"txt");
    $finalFile=$path.$fnameOut;
    $finalFileMeta=$corpus->getFolderPath()."/meta/".$fnameOut.".meta";

    echo "Destination for Punctuation Restoration $finalFile\n";
    @mkdir($path);        

    $models=[
    "MARCELL" => ["url"=>"http://127.0.0.1:5105/api/v1.0/punctuation"],
    ];

    $text=restorePunctuationText($models[$taskDesc['system']]["url"],$contentIn);

    file_put_contents($finalFile,$text);
    
    $meta=["name"=>$fnameOut,"corpus"=>$corpus->getName(),"type"=>"text","desc"=>"","created_by"=>"PunctuationRestoration","created_date"=>strftime("%Y-%m-%d")];
    file_put_contents($finalFileMeta,json_encode($meta));
    
    file_put_contents($corpus->getFolderPath()."/changed_files.json",json_encode(["changed"=>time()]));            
    @chown($corpus->getFolderPath()."/changed_files.json",$settings->get("owner_user"));
    @chgrp($corpus->getFolderPath()."/changed_files.json",$settings->get("owner_group"));

}


?>