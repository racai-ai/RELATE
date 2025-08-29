<?php

require_once "holidays.php";

$hol=new Holidays();

echo json_encode($hol->getList());

?>