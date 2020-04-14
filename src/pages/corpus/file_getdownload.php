<?php

global $DirectoryAnnotated;

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['file']))die("Invalid call");


$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");

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

$zip_bt=false;
if(startsWith($fname,"zip_$DirectoryAnnotated/")){
    $zip_bt=true;
    $fname=substr($fname,strlen("zip_$DirectoryAnnotated/"));
}

if(!$statistics && !$zip_text && !$zip_bt && !$basicTagging && !$standoff && !$marcell_out){
    $meta=$corpus->getFileMeta($fname);
    if($meta===false)die("Invalid file");
}

if($basicTagging){
        $dir=$corpus->getFolderPath();
        $dir.="/$DirectoryAnnotated";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");
}else if($standoff){
        $fpath=$corpus->getFilePathStandoff($fname);
        if($fpath===false)die("Invalid file");
}else if($statistics){
        $dir=$corpus->getFolderPath();
        $dir.="/statistics";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");

}else if($zip_text){
        $dir=$corpus->getFolderPath();
        $dir.="/zip_text";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");

}else if($zip_bt){
        $dir=$corpus->getFolderPath();
        $dir.="/zip_$DirectoryAnnotated";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");
        
}else if($marcell_out){
        $dir=$corpus->getFolderPath();
        $dir.="/marcell-out";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");

}else{
        $dir=$corpus->getFolderPath();
        $dir.="/files";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");
}

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($fpath).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($fpath));
    @ob_end_flush();
    readfile($fpath);
    exit;

?>