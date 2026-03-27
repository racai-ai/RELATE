<?php

require_once "person.php";
require_once "persons.php";

$persons=new Persons();

echo json_encode($persons->getList());

?>