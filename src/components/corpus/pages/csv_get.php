<?php

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

$statistics=false;
if(startsWith($fname,"statistics/")){
    $statistics=true;
    $fname=substr($fname,strlen("statistics/"));
}

$standoff=false;
if(startsWith($fname,"standoff/")){
    $standoff=true;
    $fname=substr($fname,strlen("standoff/"));
}


if(!$statistics && !$basicTagging && !$standoff){
    $meta=$corpus->getFileMeta($fname);
    if($meta===false)die("Invalid file");
}

$lnum=0;
echo "[\n";
$first=true;

if($basicTagging){
    $fp=$corpus->openFileBasicTagging($fname);
    while(!feof($fp)){
        $line=fgets($fp);
        if($line===false || $line===null)break;

        $line=explode("\t",trim($line));
        
        $lnum++;
        
        if($line[0]===null)continue;
        
        if($first)$first=false;
        else echo ",\n";
        echo json_encode($line,JSON_FORCE_OBJECT);
    }
    fclose($fp);
}else if($standoff){
    $fp=$corpus->openFileStandoff($fname);
    while(!feof($fp)){
        $line=fgetcsv($fp);
        if($line===false || $line===null)break;

        $lnum++;
        
        if($line[0]===null)continue;
        
        if($first)$first=false;
        else echo ",\n";
        echo json_encode($line,JSON_FORCE_OBJECT);
    }
    fclose($fp);

}else if($statistics){
        $dir=$corpus->getFolderPath();
        $dir.="/statistics";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");

        $fp=fopen($fpath,"r");
    while(!feof($fp)){
        $line=fgetcsv($fp,0);
        if($line===false || $line===null)break;
        
        $lnum++;
        
        if($line[0]===null)continue;
        
        if($first)$first=false;
        else echo ",\n";
        echo json_encode($line,JSON_FORCE_OBJECT);
    }
    fclose($fp);
    
}else{
    $fp=$corpus->openFile($fname);
    while(!feof($fp)){
        $line=fgetcsv($fp,0,$meta['delimiter'],$meta['enclosure'],$meta['escape']);
        if($line===false || $line===null)break;
        
        $lnum++;
        
        if($line[0]===null)continue;
        
        if(strlen($meta['comment'])>0 && strncasecmp($meta['comment'],$line[0],strlen($meta['comment']))===0)continue;
        
        if(!empty($meta['ignore_rows']) && $lnum<=intval($meta['ignore_rows']))continue;
        
        if($first)$first=false;
        else echo ",\n";
        echo json_encode($line,JSON_FORCE_OBJECT);
    }
    fclose($fp);
}
echo "\n]";
?>