<?php
require_once "person.php";
require_once "persons.php";

if(!isset($_REQUEST['person']))die("Invalid call");

$persons=new Persons();
$per=new Person($persons,$_REQUEST['person']);
if(!$per->loadData())die("Invalid person");
if(!$per->hasRights("admin"))die("Invalid person");

echo json_encode($per->getUnavailabilityList());

?>