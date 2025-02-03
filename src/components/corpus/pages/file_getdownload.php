<?php

global $DirectoryAnnotated,$user;

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['file']))die("Invalid call");

$viewFile=false;
if(isset($_REQUEST['view']) && $_REQUEST['view']=='Y')$viewFile=true;

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

$zip_standoff=false;
if(startsWith($fname,"zip_standoff/")){
    $zip_standoff=true;
    $fname=substr($fname,strlen("zip_standoff/"));
}
$zip_gold_ann=false;
if(startsWith($fname,"zip_gold_ann/")){
    $zip_gold_ann=true;
    $fname=substr($fname,strlen("zip_gold_ann/"));
}
$zip_gold_standoff=false;
if(startsWith($fname,"zip_gold_standoff/")){
    $zip_gold_standoff=true;
    $fname=substr($fname,strlen("zip_gold_standoff/"));
}
$zip_audio=false;
if(startsWith($fname,"zip_audio/")){
    $zip_audio=true;
    $fname=substr($fname,strlen("zip_audio/"));
}
$zip_image=false;
if(startsWith($fname,"zip_image/")){
    $zip_image=true;
    $fname=substr($fname,strlen("zip_image/"));
}
$zip_video=false;
if(startsWith($fname,"zip_video/")){
    $zip_video=true;
    $fname=substr($fname,strlen("zip_video/"));
}


$marcell_out=false;
if(startsWith($fname,"marcell-out/")){
    $marcell_out=true;
    $fname=substr($fname,strlen("marcell-out/"));
}
$curlicat_out=false;
if(startsWith($fname,"curlicat-out/")){
    $curlicat_out=true;
    $fname=substr($fname,strlen("curlicat-out/"));
}

$audio=false;
if(startsWith($fname,"audio/")){
    $audio=true;
    $fname=substr($fname,strlen("audio/"));
}
$image=false;
if(startsWith($fname,"image/")){
    $image=true;
    $fname=substr($fname,strlen("image/"));
}
$video=false;
if(startsWith($fname,"video/")){
    $video=true;
    $fname=substr($fname,strlen("video/"));
}

$zip_bt=false;
if(startsWith($fname,"zip_$DirectoryAnnotated/")){
    $zip_bt=true;
    $fname=substr($fname,strlen("zip_$DirectoryAnnotated/"));
}

if( 
    !$statistics && !$zip_text && !$zip_bt && !$basicTagging && !$standoff && !$marcell_out && !$curlicat_out && 
    !$audio && !$goldann && !$goldstandoff && !$zip_standoff && !$zip_gold_ann && !$zip_gold_standoff && !$zip_audio &&
    !$image && !$video && !$zip_image && !$zip_video
){
        
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
}else if($zip_standoff){
        $dir=$corpus->getFolderPath();
        $dir.="/zip_standoff";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");
}else if($zip_gold_ann){
        $dir=$corpus->getFolderPath();
        $dir.="/zip_gold_ann";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");
}else if($zip_gold_standoff){
        $dir=$corpus->getFolderPath();
        $dir.="/zip_gold_standoff";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");
}else if($zip_audio){
        $dir=$corpus->getFolderPath();
        $dir.="/zip_audio";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");
}else if($zip_image){
        $dir=$corpus->getFolderPath();
        $dir.="/zip_image";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");
}else if($zip_video){
        $dir=$corpus->getFolderPath();
        $dir.="/zip_video";
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
}else if($curlicat_out){
        $dir=$corpus->getFolderPath();
        $dir.="/curlicat-out";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");

}else if($audio){
        $dir=$corpus->getFolderPath();
        $dir.="/audio";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");
        
        $user->setProfile("last_viewed_audio_".$_REQUEST['corpus'],$fname);
        $user->saveProfile();
        
        
}else if($image){
        $dir=$corpus->getFolderPath();
        $dir.="/image";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");
        
        $user->setProfile("last_viewed_image_".$_REQUEST['corpus'],$fname);
        $user->saveProfile();
        
}else if($video){
        $dir=$corpus->getFolderPath();
        $dir.="/video";
        $fpath=$dir."/$fname";
        if(!is_file($fpath))die("Invalid file");
        
        $user->setProfile("last_viewed_video_".$_REQUEST['corpus'],$fname);
        $user->saveProfile();
        

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
        
        $user->setProfile("last_viewed_file_".$_REQUEST['corpus'],$fname);
        $user->saveProfile();
}

    header('Content-Description: File Transfer');
	if(endsWith($fname,"pdf"))
		header('Content-Type: application/pdf');
	else
		header('Content-Type: application/octet-stream');
	
	if($viewFile)
		header('Content-Disposition: inline; filename="'.basename($fpath).'"');
	else
		header('Content-Disposition: attachment; filename="'.basename($fpath).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($fpath));
    @ob_end_flush();
    readfile($fpath);
    exit;

?>