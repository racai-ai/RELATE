<?php
require_once "holidays.php";

function addError($reason){
  echo json_encode(["status"=>false,"reason"=>$reason]);
  die();
}

if(!isset($_REQUEST['date']))addError("Invalid call");
if(!isset($_REQUEST['description']))addError("Invalid call");

$hol=new Holidays();
$hol->add(["date"=>$_REQUEST['date'],"description"=>$_REQUEST['description']],$user);

echo json_encode(["status"=>true]);

?>