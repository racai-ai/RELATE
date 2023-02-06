<?php

function addError($reason){
  echo json_encode(["status"=>false,"reason"=>$reason]);
  die();
}

if(!isset($_REQUEST['data']))addError("Invalid call");

$data=json_decode($_REQUEST['data'],true);

if(!isset($data['name']))addError("Invalid data");
if(!isset($data['username']))addError("Invalid data");
if(!isset($data['password']))addError("Invalid data");
if(!isset($data['password2']))addError("Invalid data");
if(!isset($data['rights']))addError("Invalid data");
$edit=false;
if(count($data)==6){
	if(!isset($data['edit']))addError("Invalid data");
	$edit=$data['edit'];
}	
if(count($data)!=5 && count($data)!=6)addError("Invalid data");

if($data['password']!=$data['password2'])addError("Passwords do not match");

$u=new User();
if (!$u->isValidUsernameString($data['username'])) addError("Invalid username string");
if(!$edit){
	if($u->userExists($data['username'])) addError("Username already exists!");
	if(!$u->create($data))addError("Error creating account!");
	if(!$u->loadUser($data['username']))addError("Cannot load user after creation!");
}else{
	if(!$u->userExists($data['username'])) addError("Username does not exist!");
	if(!$u->loadUser($data['username']))addError("Cannot load user!");
	if(!$u->setProfile("name",$data['name']))addError("Cannot set profile data");
	if(!$u->writeProfile())addError("Cannot save user profile!");
	if(strlen($data['password'])>0)
		if(!$u->forceChangePassword($data['password']))addError("Cannot change password");
}

if(!$u->setRights(explode(",",$data['rights'])))addError("Cannot set user rights!");

echo json_encode(["status"=>true]);

?>