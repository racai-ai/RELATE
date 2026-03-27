<?php
require_once "person.php";
require_once "persons.php";

if(!isset($_REQUEST['person']))die("Invalid call");
if(!isset($_REQUEST['date_start']))die("Invalid call");
if(!isset($_REQUEST['date_end']))die("Invalid call");
if(!isset($_REQUEST['type']))die("Invalid call");
if(!isset($_REQUEST['description']))die("Invalid call");

$persons=new Persons();
$per=new Person($persons,$_REQUEST['person']);
if(!$per->loadData())die("Invalid person");
if(!$per->hasRights("admin"))die("Invalid person");

$per->addUnavailability([
    "date_start"=>$_REQUEST['date_start'], 
    "date_end"=>$_REQUEST['date_end'], 
    "type"=>$_REQUEST['type'], 
    "description"=>$_REQUEST['description'], 
], $user);
//$per->saveData(true); // already saved

echo json_encode(["status"=>"OK"]);

?>