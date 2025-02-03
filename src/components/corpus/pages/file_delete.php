<?php

global $DirectoryAnnotated;

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['file']))die("Invalid call");


$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");
if(!$corpus->hasRights("readwrite"))die("Invalid corpus");

$fname=$_REQUEST['file'];
$basicTagging=false;
if(startsWith($fname,"basictagging/")){
    $basicTagging=true;
    $fname=substr($fname,strlen("basictagging/"));
}

$standoff=false;
if(startsWith($fname,"standoff/")){
    $standoff=true;
    $fname=substr($fname,strlen("standoff/"));
}

$goldann=false;
if(startsWith($fname,"goldann/")){
    $goldann=true;
    $fname=substr($fname,strlen("goldann/"));
}

$goldstandoff=false;
if(startsWith($fname,"goldstandoff/")){
    $goldstandoff=true;
    $fname=substr($fname,strlen("goldstandoff/"));
}


$statistics=false;
if(startsWith($fname,"statistics/")){
    $statistics=true;
    $fname=substr($fname,strlen("statistics/"));
}

$zip_text=false;
if(startsWith($fname,"zip_text/")){
    $zip_text=true;
    $fname=substr($fname,strlen("zip_text/"));
}

$marcell_out=false;
if(startsWith($fname,"marcell-out/")){
    $marcell_out=true;
    $fname=substr($fname,strlen("marcell-out/"));
}

$audio=false;
if(startsWith($fname,"audio/")){
    $audio=true;
    $fname=substr($fname,strlen("audio/"));
}

$zip_bt=false;
if(startsWith($fname,"zip_$DirectoryAnnotated/")){
    $zip_bt=true;
    $fname=substr($fname,strlen("zip_$DirectoryAnnotated/"));
}

if(!$statistics && !$zip_text && !$zip_bt && !$basicTagging && !$standoff && !$marcell_out && !$audio && !$goldann && !$goldstandoff){
    $meta=$corpus->getFileMeta($fname);
    if($meta===false)die("Invalid file");
}

if($basicTagging){
        $dir=$corpus->getFolderPath();
        $srcDir="$DirectoryAnnotated";
        $fpath="$dir/$srcDir/$fname";
        if(!is_file($fpath))die("Invalid file");
}else if($standoff){
        $fpath=$corpus->getFilePathStandoff($fname);
        if($fpath===false)die("Invalid file");
}else if($statistics){
        $dir=$corpus->getFolderPath();
        $srcDir="statistics";
        $fpath="$dir/$srcDir/$fname";
        if(!is_file($fpath))die("Invalid file");

}else if($zip_text){
        $dir=$corpus->getFolderPath();
        $srcDir="zip_text";
        $fpath="$dir/$srcDir/$fname";
        if(!is_file($fpath))die("Invalid file");

}else if($zip_bt){
        $dir=$corpus->getFolderPath();
        $srcDir="/zip_$DirectoryAnnotated";
        $fpath="$dir/$srcDir/$fname";
        if(!is_file($fpath))die("Invalid file");
        
}else if($marcell_out){
        $dir=$corpus->getFolderPath();
        $srcDir="marcell-out";
        $fpath="$dir/$srcDir/$fname";
        if(!is_file($fpath))die("Invalid file");

}else if($audio){
        $dir=$corpus->getFolderPath();
        $srcDir="audio";
        $fpath="$dir/$srcDir/$fname";
        if(!is_file($fpath))die("Invalid file");

}else if($goldann){
        $dir=$corpus->getFolderPath();
        $srcDir="gold_ann";
        $fpath="$dir/$srcDir/$fname";
        if(!is_file($fpath))die("Invalid file");

}else if($goldstandoff){
        $dir=$corpus->getFolderPath();
        $srcDir="gold_standoff";
        $fpath="$dir/$srcDir/$fname";
        if(!is_file($fpath))die("Invalid file");

}else{
        $dir=$corpus->getFolderPath();
        $srcDir="files";
        $fpath="$dir/$srcDir/$fname";
        if(!is_file($fpath))die("Invalid file");
}


$trash=$corpus->getFolderPath()."/trash";
@mkdir($trash);
$trash.="/$srcDir";
@mkdir($trash);

$fpath_trash=$trash."/$fname".".".date("YmdHis") ;
rename($fpath,$fpath_trash);

echo json_encode(["status"=>"OK"]);
