<?php
require_once "project.php";
require_once "projects.php";


function addError($reason){
  echo json_encode(["status"=>false,"reason"=>$reason]);
  die();
}

if(!isset($_REQUEST['data']))addError("Invalid call");

$data=json_decode($_REQUEST['data'],true);

if(!isset($data['name']))addError("Invalid data");
if(!isset($data['desc']))addError("Invalid data");
if(!isset($data['ignore']))addError("Invalid data");
if(count($data)!=3)addError("Invalid data");

$projects=new Projects();

$data['created_by']=$user->getUsername();
$data['created_date']=strftime("%Y-%m-%d");
$prj=new Project($projects,$data['name'],$data);
if(!$prj->saveData(false))addError("Can not save data");

echo json_encode(["status"=>true]);

?>