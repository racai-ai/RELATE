<?php

if(!isset($_FILES['asrfile']))
  die("Invalid usage");

$fname=$_FILES['asrfile']['tmp_name'];
$d=WAV_getDuration($fname);
if($d===false || $d>5*60){
    echo json_encode(["asr"=>"","error"=>true,"success"=>""]);
    die();
}

$fileContent = file_get_contents($fname);
$result=ROBIN_runASR($fileContent);
echo json_encode(["asr"=>$result['data'],"error"=>false,"success"=>$result['success']]);

?>