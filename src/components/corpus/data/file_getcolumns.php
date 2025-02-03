<?php

global $DirectoryAnnotated;

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['file']))die("Invalid call");

$useHeader=false;
if(isset($_REQUEST['useHeader']) && intval($_REQUEST['useHeader']==1))$useHeader=true;

$corpora=new Corpora();
$corpus=new Corpus($corpora,$_REQUEST['corpus']);
if(!$corpus->loadData())die("Invalid corpus");
if(!$corpus->hasRights("read"))die("Invalid corpus");

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


$meta=false;
if(!$statistics && !$zip_text && !$zip_bt && !$basicTagging && !$standoff && !$marcell_out && !$audio && !$goldann && !$goldstandoff){
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

}else if($audio){
        $dir=$corpus->getFolderPath();
        $dir.="/audio";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");

}else if($goldann){
        $dir=$corpus->getFolderPath();
        $dir.="/gold_ann";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");

}else if($goldstandoff){
        $dir=$corpus->getFolderPath();
        $dir.="/gold_standoff";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");

}else{
        $dir=$corpus->getFolderPath();
        $dir.="/files";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");
}

$columns=[];
if($basicTagging || $goldann){
    $conllup=new CONLLUP();
    $conllup->readFromFile($fpath);
    foreach($conllup->getColumns() as $col){
        $columns[]=["name"=>$col,"type"=>"string"];    
    }
}else if($meta!==false && $meta['type']=="csv"){
    $del=$meta['delimiter'];
    $ignoreRows=intval($meta['ignore_rows']);
    $enc=$meta['enclosure'];
    $esc=$meta['escape'];
    
    $fp=fopen($fpath,"r");
    for($i=0;$i<$ignoreRows;$i++)$line=fgets($fp);
    $data=fgetcsv($fp,0,$del,$enc,$esc);
    fclose($fp);
    for($i=1;$i<=count($data);$i++){
        $columns[]=["name"=>"C${i}","type"=>"string"];
    }
}else if(endsWith(strtolower($fpath),".csv")){
    $fp=fopen($fpath,"r");
    $data=fgetcsv($fp);
    fclose($fp);
    if($useHeader){
        for($i=0;$i<count($data);$i++){
            $columns[]=["name"=>$data[$i],"type"=>"string"];
        }
    }else{
        for($i=1;$i<=count($data);$i++){
            $columns[]=["name"=>"C${i}","type"=>"string"];
        }
    }
    
}

echo json_encode(["status"=>"OK","columns"=>$columns]);

?>