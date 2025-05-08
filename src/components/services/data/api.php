<?php

//var_dump($_REQUEST);

if(!isset($_REQUEST['service'])){
	die(json_encode(["status"=>"ERROR","messages"=>["Invalid call"]]));
}

$services=new Services();
$srv=$services->getByName($_REQUEST['service']);
if($srv===false){
	die(json_encode(["status"=>"ERROR","messages"=>["Invalid call"]]));
}

$ret=json_encode($srv->run());

//var_dump($srv->getRunData());

die($ret);
