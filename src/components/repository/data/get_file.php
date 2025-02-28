<?php

global $DirectoryAnnotated,$user;

if(!isset($_REQUEST['resource']))die("Invalid call");
if(!isset($_REQUEST['file']))die("Invalid call");

$repository=new Repository();
$res=$repository->getByName($_REQUEST['resource']);
if($res===false)die("Invalid call");

$fname=$_REQUEST['file'];
$files=$res->getFiles();
if(!isset($files[$fname]))die("Invalid call");

$fpath=$res->getFolderPath(false)."/".$fname;

header('Content-Description: File Transfer');
if(endsWith($fname,"pdf"))
	header('Content-Type: application/pdf');
else
	header('Content-Type: application/octet-stream');

/*if($viewFile)
	header('Content-Disposition: inline; filename="'.basename($fpath).'"');
else*/
	header('Content-Disposition: attachment; filename="'.basename($fpath).'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($fpath));
@ob_end_flush();
readfile($fpath);
exit;

