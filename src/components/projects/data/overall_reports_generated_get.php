<?php
require_once "project.php";
require_once "projects.php";
require_once "projects_overall.php";

$prj=new ProjectsOverall();
//if(!$prj->loadData())die("Invalid project");
//if(!$prj->hasRights("admin"))die("Invalid project");

echo json_encode($prj->getReportsGenerated());

?>