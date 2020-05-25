<?php

$data=[];
for($i=0;$i<1000;$i++){
	if(!isset($_REQUEST["k$i"]) || !isset($_REQUEST["v$i"]))break;
	$data[$_REQUEST["k$i"]]=$_REQUEST["v$i"];
}

if(count($data)==0)die("Invalid usage");

global $user;
foreach($data as $k=>$v){
		$user->setProfile($k,$v);
}
$user->saveProfile();

echo json_encode(array("status"=>"OK"));

?>