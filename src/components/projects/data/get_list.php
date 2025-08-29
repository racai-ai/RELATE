<?php

require_once "project.php";
require_once "projects.php";

$projects=new Projects();

echo json_encode($projects->getList());

?>