<?php

if(!isset($_REQUEST['corpus']))die("Invalid call");
if(!isset($_REQUEST['file']))die("Invalid call");

$cname=$_REQUEST['corpus'];
$fname=$_REQUEST['file'];

$corpora=new Corpora();
$corpus=new Corpus($corpora,$cname);
if(!$corpus->loadData())die("Invalid corpus");

$meta=$corpus->getFileMeta($fname);
if($meta===false)die("Invalid file");

$dir=$corpus->getFolderPath();
$dir.="/files";
$fpath=$dir."/$fname";
if(!is_file($fpath))die("Invalid file");

$src=$corpus->getFolderPath()."/standoff/$fname";
$src=changeFileExtension($src,"ann");
$dst=$corpus->getFolderPath()."/gold_standoff/";
@mkdir($dst);
$dst.=$fname;
$dst=changeFileExtension($dst,"ann");
if(is_file($src)){
		file_put_contents($dst,file_get_contents($src));
		echo "ANN OK\n";
}else {
		file_put_contents($dst,""); // place empty ann
        echo "ANN Not Found\n";
}

$src=$corpus->getFolderPath()."/".$settings->get("dir.annotated")."/$fname";
$src=changeFileExtension($src,"conllup");

$srcText=$corpus->getFolderPath()."/files/$fname";

$srcAnn=$corpus->getFolderPath()."/standoff/$fname";
$srcAnn=changeFileExtension($srcAnn,"ann");

$dst=$corpus->getFolderPath()."/gold_ann/";
@mkdir($dst);
$dst.=$fname;
$dst=changeFileExtension($dst,"conllup");
if(is_file($src) && is_file($srcText) && is_file($srcAnn)){
		//file_put_contents($dst,file_get_contents($src));
    $b2c=new \BRAT2CONLLU();
		$b2c->convertBrat2Conllu(file_get_contents($src),file_get_contents($srcText),file_get_contents($srcAnn),$dst);
		
		echo "CONLLUP OK\n";
}else echo "CONLLUP Not Found\n";

?>