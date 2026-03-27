<?php
require_once "person.php";
require_once "persons.php";

function addError($reason){
  echo json_encode(["status"=>false,"reason"=>$reason]);
  die();
}

if(!isset($_REQUEST['data']))addError("Invalid call");

$data=json_decode($_REQUEST['data'],true);

if(!isset($data['name']))addError("Invalid data");
if(!isset($data['max_daily_hours']))addError("Invalid data");
if(count($data)!=2)addError("Invalid data");

$persons=new Persons();

$data['created_by']=$user->getUsername();
$data['created_date']=strftime("%Y-%m-%d");
$per=new Person($persons,$data['name'],$data);
if(!$per->saveData(false))addError("Can not save data");

echo json_encode(["status"=>true]);

?>