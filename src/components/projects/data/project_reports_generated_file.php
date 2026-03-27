<?php

require_once "project.php";
require_once "projects.php";

if(!isset($_REQUEST['project']))die("Invalid call");
if(!isset($_REQUEST['file']))die("Invalid call");

$projects=new Projects();
$prj=new Project($projects,$_REQUEST['project']);
if(!$prj->loadData())die("Invalid project");
if(!$prj->hasRights("admin"))die("Invalid project");

$fname=$_REQUEST['file'];
if(strpos($fname,'\\')!==false || strpos($fname,'/')!==false)die("Invalid call");
$fpath=$prj->getFolderPath()."/reports_gen/".$fname;
if(!is_file($fpath))die("Invalid call");

$viewFile=false;

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